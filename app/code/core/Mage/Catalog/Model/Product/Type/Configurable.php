<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Configurable product type implementation
 *
 * This type builds in product attributes and existing simple products
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Type_Configurable extends Mage_Catalog_Model_Product_Type_Abstract
{
    const TYPE_CODE = 'configurable';
    /**
     * Attributes which used for configurable product
     *
     * @var array
     */
    protected $_usedProductAttributeIds = null;
    protected $_usedProductAttributes   = null;
    protected $_configurableAttributes  = null;
    protected $_usedProductIds  = null;
    protected $_usedProducts    = null;

    protected $_isComposite = true;

    /**
     * Retrieve product type attributes
     *
     * @return array
     */
    public function getEditableAttributes()
    {
        if (is_null($this->_editableAttributes)) {
            $this->_editableAttributes = parent::getEditableAttributes();
            foreach ($this->_editableAttributes as $index => $attribute) {
                if ($this->getUsedProductAttributeIds()
                    && in_array($attribute->getAttributeId(), $this->getUsedProductAttributeIds())) {
                    unset($this->_editableAttributes[$index]);
                }
            }
        }
        return $this->_editableAttributes;
    }

    /**
     * Checkin attribute availability for create superproduct
     *
     * @param   Mage_Eav_Model_Entity_Attribute $attribute
     * @return  bool
     */
    public function canUseAttribute(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        $allow = $attribute->getIsGlobal() == Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL
            && $attribute->getIsVisible()
            && $attribute->getIsConfigurable()
            && $attribute->usesSource();

        return $allow;
    }

    /**
     * Declare attribute identifiers used for asign subproducts
     *
     * @param   array $ids
     * @return  Mage_Catalog_Model_Product_Type_Configurable
     */
    public function setUsedProductAttributeIds($ids)
    {
        $this->_usedProductAttributes = array();
        $this->_configurableAttributes= array();

        foreach ($ids as $attributeId) {
            $this->_usedProductAttributes[] = $this->getAttributeById($attributeId);
            $this->_configurableAttributes[]= Mage::getModel('catalog/product_type_configurable_attribute')
                ->setProductAttribute($this->getAttributeById($attributeId));
        }
        $this->_usedProductAttributeIds = $ids;
        return $this;
    }

    /**
     * Retrieve identifiers of used product attributes
     *
     * @return array
     */
    public function getUsedProductAttributeIds()
    {
        if (is_null($this->_usedProductAttributeIds)) {
            $this->_usedProductAttributeIds = array();
            foreach ($this->getUsedProductAttributes() as $attribute) {
                $this->_usedProductAttributeIds[] = $attribute->getId();
            }
        }
        return $this->_usedProductAttributeIds;
    }

    /**
     * Retrieve used product attributes
     *
     * @return array
     */
    public function getUsedProductAttributes()
    {
        if (is_null($this->_usedProductAttributes)) {
            $this->_usedProductAttributes = array();
            foreach ($this->getConfigurableAttributes() as $attribute) {
                $this->_usedProductAttributes[$attribute->getProductAttribute()->getId()] = $attribute->getProductAttribute();
            }
        }
        return $this->_usedProductAttributes;
    }

    /**
     * Retrieve configurable attrbutes data
     *
     * @return array
     */
    public function getConfigurableAttributes()
    {
        Varien_Profiler::start('CONFIGURABLE:'.__METHOD__);
        if (is_null($this->_configurableAttributes)) {
            $this->_configurableAttributes = $this->getConfigurableAttributeCollection()
                ->orderByPosition()
                ->load();

        }
        Varien_Profiler::stop('CONFIGURABLE:'.__METHOD__);
        return $this->_configurableAttributes;
    }

    public function getConfigurableAttributesAsArray()
    {
        $res = array();
        foreach ($this->getConfigurableAttributes() as $attribute) {
            $label = $attribute->getLabel() ? $attribute->getLabel() : $attribute->getProductAttribute()->getFrontend()->getLabel();
            $res[] = array(
               'id'            => $attribute->getId(),
               'label'         => $label,
               'position'      => $attribute->getPosition(),
               'values'        => $attribute->getPrices() ? $attribute->getPrices() : array(),
               'attribute_id'  => $attribute->getProductAttribute()->getId(),
               'attribute_code'=> $attribute->getProductAttribute()->getAttributeCode(),
               'frontend_label'=> $attribute->getProductAttribute()->getFrontend()->getLabel(),
            );
        }
        return $res;
    }

    public function getConfigurableAttributeCollection()
    {
        return Mage::getResourceModel('catalog/product_type_configurable_attribute_collection')
            ->setProductFilter($this->getProduct());
    }


    /**
     * Retrieve subproducts identifiers
     *
     * @return array
     */
    public function getUsedProductIds()
    {
        if (is_null($this->_usedProductIds)) {
            $this->_usedProductIds = array();
            foreach ($this->getUsedProducts() as $product) {
                $this->_usedProductIds[] = $product->getId();
            }
        }
        return $this->_usedProductIds;
    }

    /**
     * Retrieve array of "subproducts"
     *
     * @return array
     */
    public function getUsedProducts($requiredAttributeIds=null)
    {
        Varien_Profiler::start('CONFIGURABLE:'.__METHOD__);
        if (is_null($this->_usedProducts)) {
            if (is_null($requiredAttributeIds) && is_null($this->_configurableAttributes)) {
                // If used products load before attributes, we will load attributes.
                $this->getConfigurableAttributes();
                // After attributes loading products loaded too.
                return $this->_usedProducts;
            }

            $this->_usedProducts = array();
            $collection = $this->getUsedProductCollection()
                ->addAttributeToSelect('*')
                ->addFilterByRequiredOptions();

            if (is_array($requiredAttributeIds)) {
                foreach ($requiredAttributeIds as $attributeId) {
                    $attribute = $this->getAttributeById($attributeId);
                    if (!is_null($attribute))
                        $collection->addAttributeToFilter($attribute->getAttributeCode(), array('notnull'=>1));
                }
            }

            foreach ($collection as $product) {
                $this->_usedProducts[] = $product;
            }
        }
        Varien_Profiler::stop('CONFIGURABLE:'.__METHOD__);
        return $this->_usedProducts;
    }

    /**
     * Retrieve related products collection
     *
     * @return unknown
     */
    public function getUsedProductCollection()
    {
        $collection = Mage::getResourceModel('catalog/product_type_configurable_product_collection')
            ->setProductFilter($this->getProduct());
        if (!is_null($this->getStoreFilter())) {
            $collection->addStoreFilter($this->getStoreFilter());
        }
        return $collection;
    }

    public function beforeSave()
    {
        parent::beforeSave();

        $this->getProduct()->canAffectOptions(false);

        if ($this->getProduct()->getCanSaveConfigurableAttributes()) {
            $this->getProduct()->canAffectOptions(true);
            if ($data = $this->getProduct()->getConfigurableAttributesData()) {
                if (!empty($data)) {
                    foreach ($data as $attribute) {
                        if (!empty($attribute['values'])) {
                            $this->getProduct()->setTypeHasOptions(true);
                            $this->getProduct()->setTypeHasRequiredOptions(true);
                            break;
                        }
                    }
                }
            }
        }
    }

    /**
     * Save configurable product depended data
     *
     * @return Mage_Catalog_Model_Product_Type_Configurable
     */
    public function save()
    {
        parent::save();
        /**
         * Save Attributes Information
         */
        if ($data = $this->getProduct()->getConfigurableAttributesData()) {
            foreach ($data as $attributeData) {
                $id = isset($attributeData['id']) ? $attributeData['id'] : null;
                $attribute = Mage::getModel('catalog/product_type_configurable_attribute')
                   ->setData($attributeData)
                   ->setId($id)
                   ->setStoreId($this->getProduct()->getStoreId())
                   ->setProductId($this->getProduct()->getId())
                   ->save();
            }
        }

        /**
         * Save product relations
         */
        $data = $this->getProduct()->getConfigurableProductsData();
        if (is_array($data)) {
            $productIds = array_keys($data);
            Mage::getResourceModel('catalog/product_type_configurable')
                ->saveProducts($this->getProduct()->getId(), $productIds);
        }
        return $this;
    }

    /**
     * Check is product available for sale
     *
     * @return bool
     */
    public function isSalable()
    {
        $salable = $this->getProduct()->getIsSalable();
        if (!is_null($salable) && !$salable) {
            return $salable;
        }

        $salable = false;
        foreach ($this->getUsedProducts() as $product) {
            $salable = $salable || $product->isSalable();
        }
        return $salable;
    }

    /**
     * Retrieve used product by attribute values
     *  $attrbutesInfo = array(
     *      $attributeId => $attributeValue
     *  )
     * @param   array $attrbutesInfo
     * @return
     */
    public function getProductByAttributes($attributesInfo)
    {
        foreach ($this->getUsedProducts() as $product) {
            $checkRes = true;
            foreach ($attributesInfo as $attributeId => $attributeValue) {
                $code = $this->getAttributeById($attributeId)->getAttributeCode();
                if ($product->getData($code) != $attributeValue) {
                    $checkRes = false;
                }
            }
            if ($checkRes) {
                return $product;
            }
        }
        return null;
    }

    public function getSelectedAttributesInfo()
    {
        $attributes = array();
        Varien_Profiler::start('CONFIGURABLE:'.__METHOD__);
        if ($attributesOption = $this->getProduct()->getCustomOption('attributes')) {
            $data = unserialize($attributesOption->getValue());
            $usedAttributes = $this->getUsedProductAttributes();

            foreach ($data as $attributeId => $attributeValue) {
                if (isset($usedAttributes[$attributeId])) {
                    $attribute = $usedAttributes[$attributeId];
                    $label = $attribute->getFrontend()->getLabel();

                    if ($attribute->getSourceModel()) {
                        $value = $attribute->getSource()->getOptionText($attributeValue);
                    }
                    else {
                        $value = '';
                    }

                    $attributes[] = array('label'=>$label, 'value'=>$value);
                }
            }
        }
        Varien_Profiler::stop('CONFIGURABLE:'.__METHOD__);
        return $attributes;
    }

    /**
     * Initialize product(s) for add to cart process
     *
     * @param   Varien_Object $buyRequest
     * @return  unknown
     */
    public function prepareForCart(Varien_Object $buyRequest)
    {
        if ($attributes = $buyRequest->getSuperAttribute()) {
            $result = parent::prepareForCart($buyRequest);
            if (is_array($result)) {
                $product = $this->getProduct();
                /**
                 * $attributes = array($attributeId=>$attributeValue)
                 */
                if ($subProduct = $this->getProductByAttributes($attributes)) {
                    $product->addCustomOption('attributes', serialize($attributes));
                    $product->addCustomOption('product_qty_'.$subProduct->getId(), 1, $subProduct);
                    $product->addCustomOption('simple_product', $subProduct->getId(), $subProduct);

                    $subProduct->setParentProductId($product->getId())
                        ->setCartQty(1);

                    $result[] = $subProduct;

                    return $result;
                }
            }
        }
        return Mage::helper('catalog')->__('Please specify the product option(s)');
    }

    /**
     * Prepare additional options/information for order item which will be
     * created from this product
     *
     * @return attay
     */
    public function getOrderOptions()
    {
        $options = parent::getOrderOptions();
        $options['attributes_info'] = $this->getSelectedAttributesInfo();
        if ($simpleOption = $this->getProduct()->getCustomOption('simple_product')) {
            $options['simple_name'] = $simpleOption->getProduct()->getName();
            $options['simple_sku']  = $simpleOption->getProduct()->getSku();
        }

        $options['product_calculations'] = self::CALCULATE_PARENT;
        $options['shipment_type'] = self::SHIPMENT_TOGETHER;

        return $options;
    }

    /**
     * Check is virtual product
     *
     * @return bool
     */
    public function isVirtual()
    {
        if ($productOption = $this->getProduct()->getCustomOption('simple_product')) {
            if ($product = $productOption->getProduct()) {
                /* @var $product Mage_Catalog_Model_Product */
                return $product->getTypeInstance()->isVirtual();
            }
        }
        return parent::isVirtual();
    }

    /**
     * Return true if product has options
     *
     * @return bool
     */
    public function hasOptions()
    {
        if ($this->getProduct()->getOptions()) {
            return true;
        }

        $attributes = $this->getConfigurableAttributes();
        if (count($attributes)) {
            foreach ($attributes as $key => $attribute) {
                /** @var Mage_Catalog_Model_Product_Type_Configurable_Attribute $attribute */
                if ($attribute->getData('prices')) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Return product weight based on simple product
     * weight or configurable product weight
     *
     * @return decimal
     */
    public function getWeight()
    {
        if ($this->getProduct()->hasCustomOptions() && ($simpleProductOption = $this->getProduct()->getCustomOption('simple_product'))) {
            $simpleProduct = $simpleProductOption->getProduct();
            return $simpleProduct->getWeight();
        } else {
            return $this->getProduct()->getData('weight');
        }
    }
}
