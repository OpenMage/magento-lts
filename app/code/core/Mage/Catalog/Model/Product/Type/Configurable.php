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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Configurable product type implementation
 *
 * This type builds in product attributes and existing simple products
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Type_Configurable extends Mage_Catalog_Model_Product_Type_Abstract
{
    const TYPE_CODE = 'configurable';

    /**
     * Cache key for Used Product Attribute Ids
     *
     * @var string
     */
    protected $_usedProductAttributeIds = '_cache_instance_used_product_attribute_ids';

    /**
     * Cache key for Used Product Attributes
     *
     * @var string
     */
    protected $_usedProductAttributes   = '_cache_instance_used_product_attributes';

    /**
     * Cache key for Used Attributes
     *
     * @var string
     */
    protected $_usedAttributes          = '_cache_instance_used_attributes';

    /**
     * Cache key for configurable attributes
     *
     * @var string
     */
    protected $_configurableAttributes  = '_cache_instance_configurable_attributes';

    /**
     * Cache key for Used product ids
     *
     * @var string
     */
    protected $_usedProductIds          = '_cache_instance_product_ids';

    /**
     * Cache key for used products
     *
     * @var string
     */
    protected $_usedProducts            = '_cache_instance_products';

    /**
     * Product is composite
     *
     * @var bool
     */
    protected $_isComposite             = true;

    /**
     * Product is configurable
     *
     * @var bool
     */
    protected $_canConfigure            = true;

    /**
     * Product attributes to include on the children of configurable products
     *
     * @var string
     */
    const XML_PATH_PRODUCT_CONFIGURABLE_CHILD_ATTRIBUTES = 'frontend/product/configurable/child/attributes';

    /**
     * Return relation info about used products
     *
     * @return Varien_Object Object with information data
     */
    public function getRelationInfo()
    {
        $info = new Varien_Object();
        $info->setTable('catalog/product_super_link')
            ->setParentFieldName('parent_id')
            ->setChildFieldName('product_id');
        return $info;
    }

    /**
     * Retrieve Required children ids
     * Return grouped array, ex array(
     *   group => array(ids)
     * )
     *
     * @param  int $parentId
     * @param  bool $required
     * @return array
     */
    public function getChildrenIds($parentId, $required = true)
    {
        return Mage::getResourceSingleton('catalog/product_type_configurable')
            ->getChildrenIds($parentId, $required);
    }

    /**
     * Retrieve parent ids array by required child
     *
     * @param  int|array $childId
     * @return array
     */
    public function getParentIdsByChild($childId)
    {
        return Mage::getResourceSingleton('catalog/product_type_configurable')
            ->getParentIdsByChild($childId);
    }

    /**
     * Retrieve product type attributes
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getEditableAttributes($product = null)
    {
        if (is_null($this->_editableAttributes)) {
            $this->_editableAttributes = parent::getEditableAttributes($product);
            foreach ($this->_editableAttributes as $index => $attribute) {
                if ($this->getUsedProductAttributeIds($product)
                    && in_array($attribute->getAttributeId(), $this->getUsedProductAttributeIds($product))) {
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
            && $attribute->usesSource()
            && $attribute->getIsUserDefined();

        return $allow;
    }

    /**
     * Declare attribute identifiers used for assign subproducts
     *
     * @param   array $ids
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_Catalog_Model_Product_Type_Configurable
     */
    public function setUsedProductAttributeIds($ids, $product = null)
    {
        $usedProductAttributes  = array();
        $configurableAttributes = array();

        foreach ($ids as $attributeId) {
            $usedProductAttributes[]  = $this->getAttributeById($attributeId);
            $configurableAttributes[] = Mage::getModel('catalog/product_type_configurable_attribute')
                ->setProductAttribute($this->getAttributeById($attributeId));
        }
        $this->getProduct($product)->setData($this->_usedProductAttributes, $usedProductAttributes);
        $this->getProduct($product)->setData($this->_usedProductAttributeIds, $ids);
        $this->getProduct($product)->setData($this->_configurableAttributes, $configurableAttributes);

        return $this;
    }

    /**
     * Retrieve identifiers of used product attributes
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getUsedProductAttributeIds($product = null)
    {
        if (!$this->getProduct($product)->hasData($this->_usedProductAttributeIds)) {
            $usedProductAttributeIds = array();
            foreach ($this->getUsedProductAttributes($product) as $attribute) {
                $usedProductAttributeIds[] = $attribute->getId();
            }
            $this->getProduct($product)->setData($this->_usedProductAttributeIds, $usedProductAttributeIds);
        }
        return $this->getProduct($product)->getData($this->_usedProductAttributeIds);
    }

    /**
     * Retrieve used product attributes
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getUsedProductAttributes($product = null)
    {
        if (!$this->getProduct($product)->hasData($this->_usedProductAttributes)) {
            $usedProductAttributes = array();
            $usedAttributes        = array();
            foreach ($this->getConfigurableAttributes($product) as $attribute) {
                if (!is_null($attribute->getProductAttribute())) {
                    $id = $attribute->getProductAttribute()->getId();
                    $usedProductAttributes[$id] = $attribute->getProductAttribute();
                    $usedAttributes[$id]        = $attribute;
                }
            }
            $this->getProduct($product)->setData($this->_usedAttributes, $usedAttributes);
            $this->getProduct($product)->setData($this->_usedProductAttributes, $usedProductAttributes);
        }
        return $this->getProduct($product)->getData($this->_usedProductAttributes);
    }

    /**
     * Retrieve configurable attributes data
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getConfigurableAttributes($product = null)
    {
        Varien_Profiler::start('CONFIGURABLE:'.__METHOD__);
        if (!$this->getProduct($product)->hasData($this->_configurableAttributes)) {
            $configurableAttributes = $this->getConfigurableAttributeCollection($product)
                ->orderByPosition()
                ->load();
            $this->getProduct($product)->setData($this->_configurableAttributes, $configurableAttributes);
        }
        Varien_Profiler::stop('CONFIGURABLE:'.__METHOD__);
        return $this->getProduct($product)->getData($this->_configurableAttributes);
    }

    /**
     * Retrieve Configurable Attributes as array
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getConfigurableAttributesAsArray($product = null)
    {
        $res = array();
        foreach ($this->getConfigurableAttributes($product) as $attribute) {
            $res[] = array(
                'id'             => $attribute->getId(),
                'label'          => $attribute->getLabel(),
                'use_default'    => $attribute->getUseDefault(),
                'position'       => $attribute->getPosition(),
                'values'         => $attribute->getPrices() ? $attribute->getPrices() : array(),
                'attribute_id'   => $attribute->getProductAttribute()->getId(),
                'attribute_code' => $attribute->getProductAttribute()->getAttributeCode(),
                'frontend_label' => $attribute->getProductAttribute()->getFrontend()->getLabel(),
                'store_label'    => $attribute->getProductAttribute()->getStoreLabel(),
            );
        }
        return $res;
    }

    /**
     * Retrieve configurable attribute collection
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Type_Configurable_Attribute_Collection
     */
    public function getConfigurableAttributeCollection($product = null)
    {
        return Mage::getResourceModel('catalog/product_type_configurable_attribute_collection')
            ->setProductFilter($this->getProduct($product));
    }


    /**
     * Retrieve subproducts identifiers
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getUsedProductIds($product = null)
    {
        if (!$this->getProduct($product)->hasData($this->_usedProductIds)) {
            $usedProductIds = array();
            foreach ($this->getUsedProducts(null, $product) as $product) {
                $usedProductIds[] = $product->getId();
            }
            $this->getProduct($product)->setData($this->_usedProductIds, $usedProductIds);
        }
        return $this->getProduct($product)->getData($this->_usedProductIds);
    }

    /**
     * Retrieve array of "subproducts"
     *
     * @param  array
     * @param  Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getUsedProducts($requiredAttributeIds = null, $product = null)
    {
        Varien_Profiler::start('CONFIGURABLE:'.__METHOD__);
        if (!$this->getProduct($product)->hasData($this->_usedProducts)) {
            if (is_null($requiredAttributeIds)
                and is_null($this->getProduct($product)->getData($this->_configurableAttributes))) {
                // If used products load before attributes, we will load attributes.
                $this->getConfigurableAttributes($product);
                // After attributes loading products loaded too.
                Varien_Profiler::stop('CONFIGURABLE:'.__METHOD__);
                return $this->getProduct($product)->getData($this->_usedProducts);
            }

            $usedProducts = array();
            $collection = $this->getUsedProductCollection($product)
                ->addFilterByRequiredOptions();

            // Provides a mechanism for attaching additional attributes to the children of configurable products
            // Will primarily have affect on the configurable product view page
            $childAttributes = Mage::getConfig()->getNode(self::XML_PATH_PRODUCT_CONFIGURABLE_CHILD_ATTRIBUTES);

            if ($childAttributes) {
                $childAttributes = $childAttributes->asArray();
                $childAttributes = array_keys($childAttributes);

                $collection->addAttributeToSelect($childAttributes);
            }

            if (is_array($requiredAttributeIds)) {
                foreach ($requiredAttributeIds as $attributeId) {
                    $attribute = $this->getAttributeById($attributeId, $product);
                    if (!is_null($attribute))
                        $collection->addAttributeToFilter($attribute->getAttributeCode(), array('notnull'=>1));
                }
            }

            foreach ($collection as $item) {
                $usedProducts[] = $item;
            }

            $this->getProduct($product)->setData($this->_usedProducts, $usedProducts);
        }
        Varien_Profiler::stop('CONFIGURABLE:'.__METHOD__);
        return $this->getProduct($product)->getData($this->_usedProducts);
    }

    /**
     * Retrieve related products collection
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Type_Configurable_Product_Collection
     */
    public function getUsedProductCollection($product = null)
    {
        $collection = Mage::getResourceModel('catalog/product_type_configurable_product_collection')
            ->setFlag('require_stock_items', true)
            ->setFlag('product_children', true)
            ->setProductFilter($this->getProduct($product));
        if (!is_null($this->getStoreFilter($product))) {
            $collection->addStoreFilter($this->getStoreFilter($product));
        }

        return $collection;
    }

    /**
     * Before save process
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Product_Type_Configurable
     */
    public function beforeSave($product = null)
    {
        parent::beforeSave($product);

        $this->getProduct($product)->canAffectOptions(false);

        if ($this->getProduct($product)->getCanSaveConfigurableAttributes()) {
            $this->getProduct($product)->canAffectOptions(true);
            $data = $this->getProduct($product)->getConfigurableAttributesData();
            if (!empty($data)) {
                foreach ($data as $attribute) {
                    if (!empty($attribute['values'])) {
                        $this->getProduct($product)->setTypeHasOptions(true);
                        $this->getProduct($product)->setTypeHasRequiredOptions(true);
                        break;
                    }
                }
            }
        }
        foreach ($this->getConfigurableAttributes($product) as $attribute) {
            $this->getProduct($product)->setData($attribute->getProductAttribute()->getAttributeCode(), null);
        }

        return $this;
    }

    /**
     * Save configurable product depended data
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Product_Type_Configurable
     */
    public function save($product = null)
    {
        parent::save($product);
        /**
         * Save Attributes Information
         */
        if ($data = $this->getProduct($product)->getConfigurableAttributesData()) {
            foreach ($data as $attributeData) {
                $id = isset($attributeData['id']) ? $attributeData['id'] : null;
                Mage::getModel('catalog/product_type_configurable_attribute')
                   ->setData($attributeData)
                   ->setId($id)
                   ->setStoreId($this->getProduct($product)->getStoreId())
                   ->setProductId($this->getProduct($product)->getId())
                   ->save();
            }
        }

        /**
         * Save product relations
         */
        $data = $this->getProduct($product)->getConfigurableProductsData();
        if (is_array($data)) {
            $productIds = array_keys($data);
            Mage::getResourceModel('catalog/product_type_configurable')
                ->saveProducts($this->getProduct($product), $productIds);
        }
        return $this;
    }

    /**
     * Check is product available for sale
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function isSalable($product = null)
    {
        $salable = parent::isSalable($product);

        if ($salable !== false) {
            $salable = false;
            if (!is_null($product)) {
                $this->setStoreFilter($product->getStoreId(), $product);
            }
            foreach ($this->getUsedProducts(null, $product) as $child) {
                if ($child->isSalable()) {
                    $salable = true;
                    break;
                }
            }
        }

        return $salable;
    }

    /**
     * Check whether the product is available for sale
     * is alias to isSalable for compatibility
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function getIsSalable($product = null)
    {
        return $this->isSalable($product);
    }

    /**
     * Retrieve used product by attribute values
     *  $attrbutesInfo = array(
     *      $attributeId => $attributeValue
     *  )
     *
     * @param  array $attributesInfo
     * @param  Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Product|null
     */
    public function getProductByAttributes($attributesInfo, $product = null)
    {
        if (is_array($attributesInfo) && !empty($attributesInfo)) {
            $productCollection = $this->getUsedProductCollection($product)->addAttributeToSelect('name');
            foreach ($attributesInfo as $attributeId => $attributeValue) {
                $productCollection->addAttributeToFilter($attributeId, $attributeValue);
            }
            $productObject = $productCollection->getFirstItem();
            if ($productObject->getId()) {
                return $productObject;
            }

            foreach ($this->getUsedProducts(null, $product) as $productObject) {
                $checkRes = true;
                foreach ($attributesInfo as $attributeId => $attributeValue) {
                    $code = $this->getAttributeById($attributeId, $product)->getAttributeCode();
                    if ($productObject->getData($code) != $attributeValue) {
                        $checkRes = false;
                    }
                }
                if ($checkRes) {
                    return $productObject;
                }
            }
        }
        return null;
    }

    /**
     * Retrieve Selected Attributes info
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getSelectedAttributesInfo($product = null)
    {
        $attributes = array();
        Varien_Profiler::start('CONFIGURABLE:'.__METHOD__);
        if ($attributesOption = $this->getProduct($product)->getCustomOption('attributes')) {
            $data = unserialize($attributesOption->getValue());
            $this->getUsedProductAttributeIds($product);

            $usedAttributes = $this->getProduct($product)->getData($this->_usedAttributes);

            foreach ($data as $attributeId => $attributeValue) {
                if (isset($usedAttributes[$attributeId])) {
                    $attribute = $usedAttributes[$attributeId];
                    $label = $attribute->getLabel();
                    $value = $attribute->getProductAttribute();
                    if ($value->getSourceModel()) {
                        $value = $value->getSource()->getOptionText($attributeValue);
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
     * Prepare product and its configuration to be added to some products list.
     * Perform standard preparation process and then add Configurable specific options.
     *
     * @param Varien_Object $buyRequest
     * @param Mage_Catalog_Model_Product $product
     * @param string $processMode
     * @return array|string
     */
    protected function _prepareProduct(Varien_Object $buyRequest, $product, $processMode)
    {
        $attributes = $buyRequest->getSuperAttribute();
        if ($attributes || !$this->_isStrictProcessMode($processMode)) {
            if (!$this->_isStrictProcessMode($processMode)) {
                if (is_array($attributes)) {
                    foreach ($attributes as $key => $val) {
                        if (empty($val)) {
                            unset($attributes[$key]);
                        }
                    }
                } else {
                    $attributes = array();
                }
            }

            $result = parent::_prepareProduct($buyRequest, $product, $processMode);
            if (is_array($result)) {
                $product = $this->getProduct($product);
                /**
                 * $attributes = array($attributeId=>$attributeValue)
                 */
                $subProduct = true;
                if ($this->_isStrictProcessMode($processMode)) {
                    foreach($this->getConfigurableAttributes($product) as $attributeItem){
                        /* @var $attributeItem Varien_Object */
                        $attrId = $attributeItem->getData('attribute_id');
                        if(!isset($attributes[$attrId]) || empty($attributes[$attrId])) {
                            $subProduct = null;
                            break;
                        }
                    }
                }
                if( $subProduct ) {
                    $subProduct = $this->getProductByAttributes($attributes, $product);
                }

                if ($subProduct) {
                    $product->addCustomOption('attributes', serialize($attributes));
                    $product->addCustomOption('product_qty_'.$subProduct->getId(), 1, $subProduct);
                    $product->addCustomOption('simple_product', $subProduct->getId(), $subProduct);

                    $_result = $subProduct->getTypeInstance(true)->_prepareProduct(
                        $buyRequest,
                        $subProduct,
                        $processMode
                    );
                    if (is_string($_result) && !is_array($_result)) {
                        return $_result;
                    }

                    if (!isset($_result[0])) {
                        return Mage::helper('checkout')->__('Cannot add the item to shopping cart');
                    }

                    /**
                     * Adding parent product custom options to child product
                     * to be sure that it will be unique as its parent
                     */
                    if ($optionIds = $product->getCustomOption('option_ids')) {
                        $optionIds = explode(',', $optionIds->getValue());
                        foreach ($optionIds as $optionId) {
                            if ($option = $product->getCustomOption('option_' . $optionId)) {
                                $_result[0]->addCustomOption('option_' . $optionId, $option->getValue());
                            }
                        }
                    }

                    $_result[0]->setParentProductId($product->getId())
                        // add custom option to simple product for protection of process
                        //when we add simple product separately
                        ->addCustomOption('parent_product_id', $product->getId());
                    if ($this->_isStrictProcessMode($processMode)) {
                        $_result[0]->setCartQty(1);
                    }
                    $result[] = $_result[0];
                    return $result;
                } else if (!$this->_isStrictProcessMode($processMode)) {
                    return $result;
                }
            }
        }

        return $this->getSpecifyOptionMessage();
    }

    /**
     * Check if product can be bought
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Product_Type_Configurable
     * @throws Mage_Core_Exception
     */
    public function checkProductBuyState($product = null)
    {
        parent::checkProductBuyState($product);
        $product = $this->getProduct($product);
        $option = $product->getCustomOption('info_buyRequest');
        if ($option instanceof Mage_Sales_Model_Quote_Item_Option) {
            $buyRequest = new Varien_Object(unserialize($option->getValue()));
            $attributes = $buyRequest->getSuperAttribute();
            if (is_array($attributes)) {
                foreach ($attributes as $key => $val) {
                    if (empty($val)) {
                        unset($attributes[$key]);
                    }
                }
            }
            if (empty($attributes)) {
                Mage::throwException($this->getSpecifyOptionMessage());
            }
        }
        return $this;
    }

    /**
     * Retrieve message for specify option(s)
     *
     * @return string
     */
    public function getSpecifyOptionMessage()
    {
        return Mage::helper('catalog')->__('Please specify the product\'s option(s).');
    }

    /**
     * Prepare additional options/information for order item which will be
     * created from this product
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getOrderOptions($product = null)
    {
        $options = parent::getOrderOptions($product);
        $options['attributes_info'] = $this->getSelectedAttributesInfo($product);
        if ($simpleOption = $this->getProduct($product)->getCustomOption('simple_product')) {
            $options['simple_name'] = $simpleOption->getProduct($product)->getName();
            $options['simple_sku']  = $simpleOption->getProduct($product)->getSku();
        }

        $options['product_calculations'] = self::CALCULATE_PARENT;
        $options['shipment_type'] = self::SHIPMENT_TOGETHER;

        return $options;
    }

    /**
     * Check is virtual product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function isVirtual($product = null)
    {
        if ($productOption = $this->getProduct($product)->getCustomOption('simple_product')) {
            if ($optionProduct = $productOption->getProduct()) {
                /* @var $optionProduct Mage_Catalog_Model_Product */
                return $optionProduct->isVirtual();
            }
        }
        return parent::isVirtual($product);
    }

    /**
     * Return true if product has options
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function hasOptions($product = null)
    {
        if ($this->getProduct($product)->getOptions()) {
            return true;
        }

        $attributes = $this->getConfigurableAttributes($product);
        if (count($attributes)) {
            foreach ($attributes as $attribute) {
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
     * @param  Mage_Catalog_Model_Product $product
     * @return decimal
     */
    public function getWeight($product = null)
    {
        if ($this->getProduct($product)->hasCustomOptions() &&
            ($simpleProductOption = $this->getProduct($product)->getCustomOption('simple_product'))
        ) {
            $simpleProduct = $simpleProductOption->getProduct($product);
            if ($simpleProduct) {
                return $simpleProduct->getWeight();
            }
        }

        return $this->getProduct($product)->getData('weight');
    }

    /**
     * Implementation of product specify logic of which product needs to be assigned to option.
     * For example if product which was added to option already removed from catalog.
     *
     * @param  Mage_Catalog_Model_Product|null $optionProduct
     * @param  Mage_Sales_Model_Quote_Item_Option $option
     * @param  Mage_Catalog_Model_Product|null $product
     * @return Mage_Catalog_Model_Product_Type_Configurable
     */
    public function assignProductToOption($optionProduct, $option, $product = null)
    {
        if ($optionProduct) {
            $option->setProduct($optionProduct);
        } else {
            $option->getItem()->setHasConfigurationUnavailableError(true);
        }
        return $this;
    }

    /**
     * Retrieve products divided into groups required to purchase
     * At least one product in each group has to be purchased
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getProductsToPurchaseByReqGroups($product = null)
    {
        $product = $this->getProduct($product);
        return array($this->getUsedProducts(null, $product));
    }

    /**
     * Get sku of product
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getSku($product = null)
    {
        $simpleOption = $this->getProduct($product)->getCustomOption('simple_product');
        if($simpleOption) {
            $optionProduct = $simpleOption->getProduct($product);
            $simpleSku = null;
            if ($optionProduct) {
                $simpleSku =  $simpleOption->getProduct($product)->getSku();
            }
            $sku = parent::getOptionSku($product, $simpleSku);
        } else {
            $sku = parent::getSku($product);
        }

        return $sku;
    }

    /**
     * Prepare selected options for configurable product
     *
     * @param  Mage_Catalog_Model_Product $product
     * @param  Varien_Object $buyRequest
     * @return array
     */
    public function processBuyRequest($product, $buyRequest)
    {
        $superAttribute = $buyRequest->getSuperAttribute();
        $superAttribute = (is_array($superAttribute)) ? array_filter($superAttribute, 'intval') : array();

        $options = array('super_attribute' => $superAttribute);

        return $options;
    }

    /**
     * Check if Minimum Advertise Price is enabled at least in one option
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int $visibility
     * @return bool|null
     */
    public function isMapEnabledInOptions($product, $visibility = null)
    {
        return null;
    }

    /**
     * Prepare and retrieve options values with product data
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getConfigurableOptions($product)
    {
        return Mage::getResourceSingleton('catalog/product_type_configurable')
            ->getConfigurableOptions($product, $this->getUsedProductAttributes($product));
    }
}
