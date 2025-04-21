<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog products compare block
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Block_Product_Compare_List extends Mage_Catalog_Block_Product_Compare_Abstract
{
    /**
     * Product Compare items collection
     *
     * @var Mage_Catalog_Model_Resource_Product_Compare_Item_Collection|null
     */
    protected $_items;

    /**
     * Compare Products comparable attributes cache
     *
     * @var array|null
     */
    protected $_attributes;

    /**
     * Flag which allow/disallow to use link for as low as price
     *
     * @var bool
     */
    protected $_useLinkForAsLowAs = false;

    /**
     * Customer id
     *
     * @var null|int
     */
    protected $_customerId = null;

    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_noform';

    /**
     * Retrieve url for adding product to wishlist with params
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getAddToWishlistUrl($product)
    {
        return $this->getAddToWishlistUrlCustom($product);
    }

    /**
     * Preparing layout
     *
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(Mage::helper('catalog')->__('Products Comparison List') . ' - ' . $headBlock->getDefaultTitle());
        }
        return parent::_prepareLayout();
    }

    /**
     * Retrieve Product Compare items collection
     *
     * @return Mage_Catalog_Model_Resource_Product_Compare_Item_Collection
     */
    public function getItems()
    {
        if (is_null($this->_items)) {
            Mage::helper('catalog/product_compare')->setAllowUsedFlat(false);

            $this->_items = Mage::getResourceModel('catalog/product_compare_item_collection')
                ->useProductItem(true)
                ->setStoreId(Mage::app()->getStore()->getId());

            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                $this->_items->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
            } elseif ($this->_customerId) {
                $this->_items->setCustomerId($this->_customerId);
            } else {
                $this->_items->setVisitorId(Mage::getSingleton('log/visitor')->getId());
            }

            $this->_items
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->loadComparableAttributes()
                ->addMinimalPrice()
                ->addTaxPercents();

            Mage::getSingleton('catalog/product_visibility')
                ->addVisibleInSiteFilterToCollection($this->_items);
        }

        return $this->_items;
    }

    /**
     * Retrieve Product Compare Attributes
     *
     * @return Mage_Eav_Model_Entity_Attribute_Abstract[]
     */
    public function getAttributes()
    {
        if (is_null($this->_attributes)) {
            $this->_attributes = $this->getItems()->getComparableAttributes();
        }

        return $this->_attributes;
    }

    /**
     * Retrieve Product Attribute Value
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return string
     */
    public function getProductAttributeValue($product, $attribute)
    {
        if (!$product->hasData($attribute->getAttributeCode())) {
            return Mage::helper('catalog')->__('N/A');
        }

        if ($attribute->getSourceModel()
            || in_array($attribute->getFrontendInput(), ['select','boolean','multiselect'])
        ) {
            //$value = $attribute->getSource()->getOptionText($product->getData($attribute->getAttributeCode()));
            $value = $attribute->getFrontend()->getValue($product);
        } else {
            $value = $product->getData($attribute->getAttributeCode());
        }
        return ((string) $value == '') ? Mage::helper('catalog')->__('No') : $value;
    }

    /**
     * Retrieve Print URL
     *
     * @return string
     */
    public function getPrintUrl()
    {
        return $this->getUrl('*/*/*', ['_current' => true, 'print' => 1]);
    }

    /**
     * Setter for customer id
     *
     * @param int $id
     * @return $this
     */
    public function setCustomerId($id)
    {
        $this->_customerId = $id;
        return $this;
    }

    /**
     * Retrieve url for adding product to wishlist with params with or without Form Key
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool $addFormKey
     * @return string
     */
    public function getAddToWishlistUrlCustom($product, $addFormKey = true)
    {
        $continueUrl = Mage::helper('core')->urlEncode($this->getUrl('customer/account'));
        $params = [
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $continueUrl,
        ];

        /** @var Mage_Wishlist_Helper_Data $helper */
        $helper = $this->helper('wishlist');

        if (!$addFormKey) {
            return $helper->getAddUrlWithCustomParams($product, $params, false);
        }

        return $helper->getAddUrlWithParams($product, $params);
    }
}
