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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract model for product type implementation
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Catalog_Model_Product_Type_Abstract
{

    /**
     * Product model instance
     *
     * @var Mage_Catalog_Model_Product
     */
    protected $_product;
    protected $_typeId;
    protected $_setAttributes;
    protected $_editableAttributes;
    protected $_isComposite = false;
    protected $_storeFilter     = null;

    const CALCULATE_CHILD = 0;
    const CALCULATE_PARENT = 1;

    /**
     * values for shipment type (invoice etc)
     *
     */
    const SHIPMENT_SEPARATELY = 1;
    const SHIPMENT_TOGETHER = 0;

    /**
     * Specify type instance product
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_Catalog_Model_Product_Type_Abstract
     */
    public function setProduct($product)
    {
        $this->_product = $product;
        return $this;
    }

    /**
     * Specify type identifier
     *
     * @param   string $typeId
     * @return  Mage_Catalog_Model_Product_Type_Abstract
     */
    public function setTypeId($typeId)
    {
        $this->_typeId = $typeId;
        return $this;
    }

    /**
     * Retrieve catalog product object
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->_product;
    }

    /**
     * Return relation info about used products for specific type instance
     *
     * @return Varien_Object Object with information data
     */
    public function getRelationInfo()
    {
        return new Varien_Object();
    }

    /**
     * Get array of product set attributes
     *
     * @return array
     */
    public function getSetAttributes()
    {
        if (is_null($this->_setAttributes)) {
            $attributes = $this->getProduct()->getResource()
                ->loadAllAttributes($this->getProduct())
                ->getAttributesByCode();
            $this->_setAttributes = array();
            foreach ($attributes as $attribute) {
                if ($attribute->isInSet($this->getProduct()->getAttributeSetId())) {
                    $attribute->setDataObject($this->getProduct());
                    $this->_setAttributes[$attribute->getAttributeCode()] = $attribute;
                }
            }

            uasort($this->_setAttributes, array($this, 'attributesCompare'));
        }
        return $this->_setAttributes;
    }

    public function attributesCompare($attribute1, $attribute2)
    {
        $sortPath      = 'attribute_set_info/' . $this->getProduct()->getAttributeSetId() . '/sort';
        $groupSortPath = 'attribute_set_info/' . $this->getProduct()->getAttributeSetId() . '/group_sort';

        $sort1 =  ($attribute1->getData($groupSortPath) * 1000) + ($attribute1->getData($sortPath) * 0.0001);
        $sort2 =  ($attribute2->getData($groupSortPath) * 1000) + ($attribute2->getData($sortPath) * 0.0001);

        if ($sort1 > $sort2) {
            return 1;
        } elseif ($sort1 < $sort2) {
            return -1;
        }

        return 0;
    }

    /**
     * Retrieve product type attributes
     *
     * @return array
     */
    public function getEditableAttributes()
    {
        if (is_null($this->_editableAttributes)) {
            $this->_editableAttributes = array();
            foreach ($this->getSetAttributes() as $attributeCode => $attribute) {
                if (!is_array($attribute->getApplyTo())
                    || count($attribute->getApplyTo())==0
                    || in_array($this->getProduct()->getTypeId(), $attribute->getApplyTo())) {
                    $this->_editableAttributes[$attributeCode] = $attribute;
                }
            }
        }
        return $this->_editableAttributes;
    }

    /**
     * Retrieve product attribute by identifier
     *
     * @param   int $attributeId
     * @return  Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getAttributeById($attributeId)
    {
        foreach ($this->getSetAttributes() as $attribute) {
            if ($attribute->getId() == $attributeId) {
                return $attribute;
            }
        }
        return null;
    }

    /**
     * Check is virtual product
     *
     * @return bool
     */
    public function isVirtual()
    {
        return false;
    }

    /**
     * Check is product available for sale
     *
     * @return bool
     */
    public function isSalable()
    {
        $salable = $this->getProduct()->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_ENABLED;
        if ($salable && $this->getProduct()->hasData('is_salable')) {
            return $this->getProduct()->getData('is_salable');
        }
        return $salable;
    }

    /**
     * Initialize product(s) for add to cart process
     *
     * @param   Varien_Object $buyRequest
     * @return  array|string
     */
    public function prepareForCart(Varien_Object $buyRequest)
    {
        $product = $this->getProduct();
        /* @var Mage_Catalog_Model_Product $product */

        // try to add custom options
        $options = $this->_prepareOptionsForCart($buyRequest->getOptions());
        if (is_string($options)) {
            return $options;
        }
        // try to found super product configuration
        // (if product was buying within grouped product)
        $superProductConfig = $buyRequest->getSuperProductConfig();
        if (!empty($superProductConfig['product_id'])
            && !empty($superProductConfig['product_type'])) {
            $superProductId = (int) $superProductConfig['product_id'];
            if ($superProductId) {
                if (!$superProduct = Mage::registry('used_super_product_'.$superProductId)) {
                    $superProduct = Mage::getModel('catalog/product')->load($superProductId);
                    Mage::register('used_super_product_'.$superProductId, $superProduct);
                }
                if ($superProduct->getId()) {
                    $assocProductIds = $superProduct->getTypeInstance()->getAssociatedProductIds();
                    if (in_array($product->getId(), $assocProductIds)) {
                        $productType = $superProductConfig['product_type'];
                        $product->addCustomOption('product_type', $productType, $superProduct);

                        $buyRequest->setData('super_product_config', array(
                                'product_type'  => $productType,
                                'product_id'    => $superProduct->getId()
                            )
                        );
                    }
                }
            }
        }

        $product->addCustomOption('info_buyRequest', serialize($buyRequest->getData()));

        if ($options) {
            $optionIds = array_keys($options);
            $product->addCustomOption('option_ids', implode(',', $optionIds));
            foreach ($options as $optionId => $optionValue) {
                $product->addCustomOption('option_'.$optionId, $optionValue);
            }
        }
        // set quantity in cart
        $product->setCartQty($buyRequest->getQty());

        return array($product);
    }

    /**
     * Check custom defined options for product
     *
     * @param   array $options
     * @return  array || string
     */
    protected function _prepareOptionsForCart($options)
    {
        $newOptions = array();

        foreach ($this->getProduct()->getOptions() as $_option) {
            /* @var $_option Mage_Catalog_Model_Product_Option */
            if (!isset($options[$_option->getId()]) && $_option->getIsRequire() && !$this->getProduct()->getSkipCheckRequiredOption()) {
                return Mage::helper('catalog')->__('Please specify the product required option(s)');
            }
            if (!isset($options[$_option->getId()])) {
                continue;
            }
            if ($_option->getGroupByType($_option->getType()) == Mage_Catalog_Model_Product_Option::OPTION_GROUP_TEXT) {
                $options[$_option->getId()] = trim($options[$_option->getId()]);
                if (strlen($options[$_option->getId()]) == 0 && $_option->getIsRequire()) {
                    return Mage::helper('catalog')->__('Please specify the product required option(s)');
                }
                if (strlen($options[$_option->getId()]) > $_option->getMaxCharacters() && $_option->getMaxCharacters() > 0) {
                    return Mage::helper('catalog')->__('Length of text is too long');
                }
                if (strlen($options[$_option->getId()]) == 0) continue;
            }
            if ($_option->getGroupByType($_option->getType()) == Mage_Catalog_Model_Product_Option::OPTION_GROUP_FILE) {

            }
            if ($_option->getGroupByType($_option->getType()) == Mage_Catalog_Model_Product_Option::OPTION_GROUP_DATE) {

            }

            if ($_option->getGroupbyType($_option->getType()) == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
                if (($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN
                    || $_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_RADIO)
                    && strlen($options[$_option->getId()])== 0) {
                    continue;
                }
                $valuesCollection = $_option->getOptionValuesByOptionId(
                        $options[$_option->getId()], $this->getProduct()->getStoreId()
                    )->load();

                if ($valuesCollection->count() != count($options[$_option->getId()])) {
                    return Mage::helper('catalog')->__('Please specify the product required option(s)');
                }
            }
            if (is_array($options[$_option->getId()])) {
                $options[$_option->getId()] = implode(',', $options[$_option->getId()]);
            }
            $newOptions[$_option->getId()] = $options[$_option->getId()];
        }

        return $newOptions;
    }

    /**
     * Check if product can be bought
     *
     * @return Mage_Catalog_Model_Product_Type_Abstract
     * @throws Mage_Core_Exception
     */
    public function checkProductBuyState()
    {
        if (!$this->getProduct()->getSkipCheckRequiredOption()) {
            foreach ($this->getProduct()->getOptions() as $option) {
                if ($option->getIsRequire() && (!$this->getProduct()->getCustomOption('option_'.$option->getId())
                || strlen($this->getProduct()->getCustomOption('option_'.$option->getId())->getValue()) == 0)) {
                    Mage::throwException(
                        Mage::helper('catalog')->__('Product has required options')
                    );
                    break;
                }
            }
        }

        return $this;
    }

    /**
     * Prepare additional options/information for order item which will be
     * created from this product
     *
     * @return attay
     */
    public function getOrderOptions()
    {
        $optionArr = array();
        if ($info = $this->getProduct()->getCustomOption('info_buyRequest')) {
            $optionArr['info_buyRequest'] = unserialize($info->getValue());
        }

        if ($optionIds = $this->getProduct()->getCustomOption('option_ids')) {
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                if ($option = $this->getProduct()->getOptionById($optionId)) {
                    $formatedValue = '';
                    $optionGroup = $option->getGroupByType($option->getType());
                    $optionValue = $this->getProduct()->getCustomOption('option_'.$option->getId())->getValue();
                    if ($option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX
                        || $option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE) {
                        foreach (explode(',', $optionValue) as $value) {
                            $formatedValue .= $option->getValueById($value)->getTitle() . ', ';
                        }
                        $formatedValue = Mage::helper('core/string')->substr($formatedValue, 0, -2);
                    } elseif ($optionGroup == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
                        $formatedValue = $option->getValueById($optionValue)->getTitle();
                    } else {
                        $formatedValue = $optionValue;
                    }
                    $optionArr['options'][] = array(
                        'label' => $option->getTitle(),
                        'value' => $formatedValue,
                        'option_id' => $option->getId(),
                        'option_value' => $optionValue
                    );
                }
            }
        }

        if ($productTypeConfig = $this->getProduct()->getCustomOption('product_type')) {
            $optionArr['super_product_config'] = array(
                'product_code'  => $productTypeConfig->getCode(),
                'product_type'  => $productTypeConfig->getValue(),
                'product_id'    => $productTypeConfig->getProductId()
            );
        }

        return $optionArr;
    }

    /**
     * Save type related data
     *
     * @return Mage_Catalog_Model_Product_Type_Abstract
     */
    public function save()
    {
        return $this;
    }

    /**
     * Before save type related data
     *
     * @return unknown
     */
    public function beforeSave()
    {
        $this->getProduct()->canAffectOptions(true);
        return $this;
    }

    /**
     * Check if product is composite (grouped, configurable, etc)
     *
     * @return bool
     */
    public function isComposite()
    {
        return $this->_isComposite;
    }

    /**
     * Default action to get sku of product
     *
     * @return string
     */
    public function getSku()
    {
        $skuDelimiter = '-';
        $sku = $this->getProduct()->getData('sku');
        if ($optionIds = $this->getProduct()->getCustomOption('option_ids')) {
            $optionIds = split(',', $optionIds->getValue());
            foreach ($optionIds as $optionId) {
                $productOption = $this->getProduct()->getOptionById($optionId);
                if ($productOption = $this->getProduct()->getOptionById($optionId)) {
                    $optionValue   = $this->getProduct()->getCustomOption('option_' . $optionId)->getValue();

                    if ($productOption->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX
                        || $productOption->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE) {
                        foreach(split(',', $optionValue) as $value) {
                            if ($optionSku = $productOption->getValueById($value)->getSku()) {
                                $sku .= $skuDelimiter . $optionSku;
                            }
                        }
                        $optionSku = null;
                    }
                    elseif ($productOption->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
                        $optionSku = $productOption->getValueById($optionValue)->getSku();
                    }
                    else {
                        $optionSku = $productOption->getSku();
                    }

                    if (!empty($optionSku)) {
                        $sku .= $skuDelimiter . $optionSku;
                    }
                }
            }
        }
        return $sku;
    }

    /**
     * Default action to get weight of product
     *
     * @return decimal
     */
    public function getWeight()
    {
        return $this->getProduct()->getData('weight');
    }

    /**
     * Return true if product has options
     *
     * @return bool
     */
    public function hasOptions()
    {
        if ($this->getProduct()->getHasOptions()) {
            return true;
        }
        return false;
    }

    /**
     * Method is needed for specific actions to change given quote options values
     * according current product type logic
     * Example: the cataloginventory validation of decimal qty can change qty to int,
     * so need to change quote item qty option value too.
     *
     * @param array         $options
     * @param Varien_Object $option
     * @param mixed         $value
     *
     * @return object       Mage_Catalog_Model_Product_Type_Abstract
     */
    public function updateQtyOption($options, Varien_Object $option, $value)
    {
        return $this;
    }

    /**
     * Check if product has required options
     *
     * @return bool
     */
    public function hasRequiredOptions()
    {
        if ($this->getProduct()->getRequiredOptions()) {
            return true;
        }
        return false;
    }

    /**
     * Retrive store filter for associated products
     *
     * @return int|Mage_Core_Model_Store
     */
    public function getStoreFilter()
    {
        return $this->_storeFilter;
    }

    /**
     * Set store filter for associated products
     *
     * @param $store int|Mage_Core_Model_Store
     * @return Mage_Catalog_Model_Product_Type_Configurable
     */
    public function setStoreFilter($store=null)
    {
        $this->_storeFilter = $store;
        return $this;
    }

    /**
     * Allow for updates of chidren qty's
     * (applicable for complicated product types. As default returns false)
     *
     * @return boolean false
     */
    public function getForceChildItemQtyChanges()
    {
        return false;
    }

    /**
     * Prepare Quote Item Quantity
     *
     * @param mixed $qty
     * @return float
     */
    public function prepareQuoteItemQty($qty)
    {
        return floatval($qty);
    }

    /**
     * Implementation of product specify logic of which product needs to be assigned to option.
     * For example if product which was added to option already removed from catalog.
     *
     * @param Mage_Catalog_Model_Product $optionProduct
     * @param Mage_Sales_Model_Quote_Item_Option $option
     * @return Mage_Catalog_Model_Product_Type_Abstract
     */
    public function assignProductToOption($optionProduct, $option)
    {
        if ($optionProduct) {
            $option->setProduct($optionProduct);
        } else {
            $option->setProduct($this->getProduct());
        }

        return $this;
    }
}
