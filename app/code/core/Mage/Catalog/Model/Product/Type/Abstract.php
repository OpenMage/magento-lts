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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
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
     * @deprecated if use as singleton
     * @var Mage_Catalog_Model_Product
     */
    protected $_product;

    /**
     * Product type instance id
     *
     * @var string
     */
    protected $_typeId;

    /**
     * @deprecated
     *
     * @var array
     */
    protected $_setAttributes;

    /**
     * @deprecated
     *
     * @var array
     */
    protected $_editableAttributes;

    /**
     * Is a compotite product type
     *
     * @var bool
     */
    protected $_isComposite = false;

    /**
     * Whether product quantity is fractional number or not
     *
     * @var bool
     */
    protected $_canUseQtyDecimals  = true;

    /**
     * @deprecated
     *
     * @var int
     */
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
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct($product = null)
    {
        if (is_object($product)) {
            return $product;
        }
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
     * Retrieve Required children ids
     * Return grouped array, ex array(
     *   group => array(ids)
     * )
     *
     * @param int $parentId
     * @param bool $required
     * @return array
     */
    public function getChildrenIds($parentId, $required = true)
    {
        return array();
    }

    /**
     * Retrieve parent ids array by requered child
     *
     * @param int|array $childId
     * @return array
     */
    public function getParentIdsByChild($childId)
    {
        return array();
    }

    /**
     * Get array of product set attributes
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getSetAttributes($product = null)
    {
        return $this->getProduct($product)->getResource()
            ->loadAllAttributes($this->getProduct($product))
            ->getSortedAttributes($this->getProduct($product)->getAttributeSetId());
    }

    /**
     * Compare attribues sorting
     *
     * @param Mage_Catalog_Model_Entity_Attribute $attribute1
     * @param Mage_Catalog_Model_Entity_Attribute $attribute2
     * @return int
     */
    public function attributesCompare($attribute1, $attribute2)
    {
        $sort1 =  ($attribute1->getGroupSortPath() * 1000) + ($attribute1->getSortPath() * 0.0001);
        $sort2 =  ($attribute2->getGroupSortPath() * 1000) + ($attribute2->getSortPath() * 0.0001);

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
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getEditableAttributes($product = null)
    {
        $cacheKey = '_cache_editable_attributes';
        if (!$this->getProduct($product)->hasData($cacheKey)) {
            $editableAttributes = array();
            foreach ($this->getSetAttributes($product) as $attributeCode => $attribute) {
                if (!is_array($attribute->getApplyTo())
                    || count($attribute->getApplyTo())==0
                    || in_array($this->getProduct($product)->getTypeId(), $attribute->getApplyTo())) {
                    $editableAttributes[$attributeCode] = $attribute;
                }
            }
            $this->getProduct($product)->setData($cacheKey, $editableAttributes);
        }
        return $this->getProduct($product)->getData($cacheKey);
    }

    /**
     * Retrieve product attribute by identifier
     *
     * @param   int $attributeId
     * @return  Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getAttributeById($attributeId, $product = null)
    {
        foreach ($this->getSetAttributes($product) as $attribute) {
            if ($attribute->getId() == $attributeId) {
                return $attribute;
            }
        }
        return null;
    }

    /**
     * Check is virtual product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function isVirtual($product = null)
    {
        return false;
    }

    /**
     * Check is product available for sale
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function isSalable($product = null)
    {
        $salable = $this->getProduct($product)->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_ENABLED;
        if ($salable && $this->getProduct($product)->hasData('is_salable')) {
            $salable = $this->getProduct($product)->getData('is_salable');
        }
        elseif ($salable && $this->isComposite()) {
            $salable = null;
        }

        return $salable;
    }

    /**
     * Initialize product(s) for add to cart process
     *
     * @param   Varien_Object $buyRequest
     * @param Mage_Catalog_Model_Product $product
     * @return  array|string
     */
    public function prepareForCart(Varien_Object $buyRequest, $product = null)
    {
        $product = $this->getProduct($product);
        /* @var Mage_Catalog_Model_Product $product */
        // try to add custom options
        try {
            $options = $this->_prepareOptionsForCart($buyRequest, $product);
        } catch (Mage_Core_Exception $e) {
            return $e->getMessage();
        }

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
                    $assocProductIds = $superProduct->getTypeInstance(true)->getAssociatedProductIds($superProduct);
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
     * Retrieve message for specify option(s)
     *
     * @return string
     */
    public function getSpecifyOptionMessage()
    {
        return Mage::helper('catalog')->__('Please specify the product\'s required option(s).');
    }

    /**
     * Check custom defined options for product
     *
     * @param Varien_Object $buyRequest
     * @param Mage_Catalog_Model_Product $product
     * @return  array || string
     */
    protected function _prepareOptionsForCart(Varien_Object $buyRequest, $product = null)
    {
        $transport = new StdClass;
        $transport->options = array();
        foreach ($this->getProduct($product)->getOptions() as $_option) {
            /* @var $_option Mage_Catalog_Model_Product_Option */
            $group = $_option->groupFactory($_option->getType())
                ->setOption($_option)
                ->setProduct($this->getProduct($product))
                ->setRequest($buyRequest)
                ->validateUserValue($buyRequest->getOptions());

            $preparedValue = $group->prepareForCart();
            if ($preparedValue !== null) {
                $transport->options[$_option->getId()] = $preparedValue;
            }
        }
        Mage::dispatchEvent('catalog_product_type_prepare_cart_options', array(
            'transport' => $transport, 'buy_request' => $buyRequest, 'product' => $product
        ));
        return $transport->options;
    }

    /**
     * Check if product can be bought
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Product_Type_Abstract
     * @throws Mage_Core_Exception
     */
    public function checkProductBuyState($product = null)
    {
        if (!$this->getProduct($product)->getSkipCheckRequiredOption()) {
            foreach ($this->getProduct($product)->getOptions() as $option) {
                if ($option->getIsRequire() && (!$this->getProduct($product)->getCustomOption('option_'.$option->getId())
                || strlen($this->getProduct($product)->getCustomOption('option_'.$option->getId())->getValue()) == 0)) {
                    Mage::throwException(
                        Mage::helper('catalog')->__('The product has required options')
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
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getOrderOptions($product = null)
    {
        $optionArr = array();
        if ($info = $this->getProduct($product)->getCustomOption('info_buyRequest')) {
            $optionArr['info_buyRequest'] = unserialize($info->getValue());
        }

        if ($optionIds = $this->getProduct($product)->getCustomOption('option_ids')) {
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                if ($option = $this->getProduct($product)->getOptionById($optionId)) {

                    $quoteItemOption = $this->getProduct($product)->getCustomOption('option_'.$option->getId());

                    $group = $option->groupFactory($option->getType())
                        ->setOption($option)
                        ->setProduct($this->getProduct())
                        ->setQuoteItemOption($quoteItemOption);

                    $optionArr['options'][] = array(
                        'label' => $option->getTitle(),
                        'value' => $group->getFormattedOptionValue($quoteItemOption->getValue()),
                        'print_value' => $group->getPrintableOptionValue($quoteItemOption->getValue()),
                        'option_id' => $option->getId(),
                        'option_type' => $option->getType(),
                        'option_value' => $quoteItemOption->getValue(),
                        'custom_view' => $group->isCustomizedView()
                    );
                }
            }
        }

        if ($productTypeConfig = $this->getProduct($product)->getCustomOption('product_type')) {
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
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Product_Type_Abstract
     */
    public function save($product = null)
    {
        return $this;
    }

    /**
     * Before save type related data
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Product_Type_Abstract
     */
    public function beforeSave($product = null)
    {
        $this->getProduct($product)->canAffectOptions(true);
        return $this;
    }

    /**
     * Check if product is composite (grouped, configurable, etc)
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function isComposite($product = null)
    {
        return $this->_isComposite;
    }

    /**
     * Check if product qty is fractional number
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function canUseQtyDecimals()
    {
        return $this->_canUseQtyDecimals;
    }

    /**
     * Default action to get sku of product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getSku($product = null)
    {
        $skuDelimiter = '-';
        $sku = $this->getProduct($product)->getData('sku');
        if ($optionIds = $this->getProduct($product)->getCustomOption('option_ids')) {
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                if ($option = $this->getProduct($product)->getOptionById($optionId)) {

                    $quoteItemOption = $this->getProduct($product)->getCustomOption('option_'.$optionId);

                    $group = $option->groupFactory($option->getType())
                        ->setOption($option)->setListener(new Varien_Object());

                    if ($optionSku = $group->getOptionSku($quoteItemOption->getValue(), $skuDelimiter)) {
                        $sku .= $skuDelimiter . $optionSku;
                    }

                    if ($group->getListener()->getHasError()) {
                        $this->getProduct($product)
                                ->setHasError(true)
                                ->setMessage(
                                    $group->getListener()->getMessage()
                                );
                    }

                }
            }
        }
        return $sku;
    }

    /**
     * Default action to get weight of product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return decimal
     */
    public function getWeight($product = null)
    {
        return $this->getProduct($product)->getData('weight');
    }

    /**
     * Return true if product has options
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function hasOptions($product = null)
    {
        if ($this->getProduct($product)->getHasOptions()) {
            return true;
        }
        if ($this->getProduct($product)->isRecurring()) {
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
    public function updateQtyOption($options, Varien_Object $option, $value, $product = null)
    {
        return $this;
    }

    /**
     * Check if product has required options
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function hasRequiredOptions($product = null)
    {
        if ($this->getProduct($product)->getRequiredOptions()) {
            return true;
        }
        return false;
    }

    /**
     * Retrive store filter for associated products
     *
     * @return int|Mage_Core_Model_Store
     */
    public function getStoreFilter($product = null)
    {
        $cacheKey = '_cache_instance_store_filter';
        return $this->getProduct($product)->getData($cacheKey);
    }

    /**
     * Set store filter for associated products
     *
     * @param $store int|Mage_Core_Model_Store
     * @return Mage_Catalog_Model_Product_Type_Configurable
     */
    public function setStoreFilter($store=null, $product = null)
    {
        $cacheKey = '_cache_instance_store_filter';
        $this->getProduct($product)->setData($cacheKey, $store);
        return $this;
    }

    /**
     * Allow for updates of chidren qty's
     * (applicable for complicated product types. As default returns false)
     *
     * @return boolean false
     */
    public function getForceChildItemQtyChanges($product = null)
    {
        return false;
    }

    /**
     * Prepare Quote Item Quantity
     *
     * @param mixed $qty
     * @return float
     */
    public function prepareQuoteItemQty($qty, $product = null)
    {
        return floatval($qty);
    }

    /**
     * Implementation of product specify logic of which product needs to be assigned to option.
     * For example if product which was added to option already removed from catalog.
     *
     * @param Mage_Catalog_Model_Product $optionProduct
     * @param Mage_Sales_Model_Quote_Item_Option $option
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Product_Type_Abstract
     */
    public function assignProductToOption($optionProduct, $option, $product = null)
    {
        if ($optionProduct) {
            $option->setProduct($optionProduct);
        } else {
            $option->setProduct($this->getProduct($product));
        }

        return $this;
    }

    /**
     * Setting specified product type variables
     *
     * @param array $config
     * @return Mage_Catalog_Model_Product_Type_Abstract
     */
    public function setConfig($config)
    {
        if (isset($config['composite'])) {
            $this->_isComposite = (bool) $config['composite'];
        }

        if (isset($config['can_use_qty_decimals'])) {
            $this->_canUseQtyDecimals = (bool) $config['can_use_qty_decimals'];
        }

        return $this;
    }

    /**
     * Retrieve additional searchable data from type instance
     * Using based on product id and store_id data
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getSearchableData($product = null)
    {
        $product    = $this->getProduct($product);
        $searchData = array();
        if ($product->getHasOptions()){
            $searchData = Mage::getSingleton('catalog/product_option')
                ->getSearchableData($product->getId(), $product->getStoreId());
        }

        return $searchData;
    }

    /**
     * Retrieve products divided into groups required to purchase
     * At least one product in each group has to be purchased
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getProductsToPurchaseByReqGroups($product = null)
    {
        $product = $this->getProduct($product);
        if ($this->isComposite($product)) {
            return array();
        }
        return array(array($product));
    }
}
