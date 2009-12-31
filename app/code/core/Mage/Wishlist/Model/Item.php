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
 * @package     Mage_Wishlist
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Wishlist item model
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Model_Item extends Mage_Core_Model_Abstract
{
    const EXCEPTION_CODE_NOT_SALABLE            = 901;
    const EXCEPTION_CODE_HAS_REQUIRED_OPTIONS   = 902;
    const EXCEPTION_CODE_IS_GROUPED_PRODUCT     = 903;

   /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'wishlist_item';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getItem() in this case
     *
     * @var string
     */
    protected $_eventObject = 'item';

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('wishlist/item');
    }

    /**
     * Retrieve resource instance wrapper
     *
     * @return Mage_Wishlist_Model_Mysql4_Item
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Validate wish list item data
     *
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function validate()
    {
        if (!$this->getWishlistId()) {
            Mage::throwException(Mage::helper('wishlist')->__('Can\'t specify wishlist'));
        }
        if (!$this->getProductId()) {
            Mage::throwException(Mage::helper('wishlist')->__('Can\'t specify product'));
        }

        return true;
    }

    /**
     * Check required data
     *
     * @return Mage_Wishlist_Model_Item
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        // validate required item data
        $this->validate();

        // set current store id if it is not defined
        if (is_null($this->getStoreId())) {
            $this->setStoreId(Mage::app()->getStore()->getId());
        }

        // set current date if added at data is not defined
        if (is_null($this->getAddedAt())) {
            $this->setAddedAt(Mage::getSingleton('core/date')->gmtDate());
        }

        return $this;
    }

    /**
     * Retrieve wishlist item data as array
     *
     * @deprecated since 1.4.0.0
     * @return array
     */
    public function getDataForSave()
    {
        $data = array();
        $data['product_id']  = $this->getProductId();
        $data['wishlist_id'] = $this->getWishlistId();
        $data['added_at']    = $this->getAddedAt() ? $this->getAddedAt() : Mage::getSingleton('core/date')->gmtDate();
        $data['description'] = $this->getDescription();
        $data['store_id']    = $this->getStoreId() ? $this->getStoreId() : Mage::app()->getStore()->getId();

        return $data;
    }

    /**
     * Load item by product, wishlist and shared stores
     *
     * @param int $wishlistId
     * @param int $productId
     * @param array $sharedStores
     * @return Mage_Wishlist_Model_Item
     */
    public function loadByProductWishlist($wishlistId, $productId, $sharedStores)
    {
        $this->_getResource()->loadByProductWishlist($this, $wishlistId, $productId, $sharedStores);
        $this->_afterLoad();
        $this->setOrigData();

        return $this;
    }

    /**
     * Retrieve item product instance
     *
     * @throws Mage_Core_Exception
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        $product = $this->_getData('product');
        if (is_null($product)) {
            if (!$this->getProductId()) {
                Mage::throwException(Mage::helper('wishlist')->__('Can\'t specify product'));
            }

            $product = Mage::getModel('catalog/product')
                ->load($this->getProductId());

            $this->setData('product', $product);
        }
        return $product;
    }

    /**
     * Add or Move item product to shopping cart
     *
     * Return true if product was successful added or exception with code
     * Return false for disabled or unvisible products
     *
     * @throws Mage_Core_Exception
     * @param Mage_Checkout_Model_Cart $cart
     * @param bool $delete  delete the item after successful add to cart
     * @return bool
     */
    public function addToCart(Mage_Checkout_Model_Cart $cart, $delete = false)
    {
        $product = $this->getProduct();

        if (Mage_Catalog_Model_Product_Type::TYPE_GROUPED == $product->getTypeId()) {
            throw new Mage_Core_Exception(null, self::EXCEPTION_CODE_IS_GROUPED_PRODUCT);
        }

        $product->setQty(1);
        $storeId = $this->getStoreId();

        if ($product->getStatus() != Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
            return false;
        }

        if (!$product->isVisibleInSiteVisibility()) {
            if ($product->getStoreId() == $storeId) {
                return false;
            }
            $urlData = Mage::getResourceSingleton('catalog/url')
                ->getRewriteByProductStore(array($product->getId() => $storeId));
            if (!isset($urlData[$product->getId()])) {
                return false;
            }
            $product->setUrlDataObject(new Varien_Object($urlData));
            $visibility = $product->getUrlDataObject()->getVisibility();
            if (!in_array($visibility, $product->getVisibleInSiteVisibilities())) {
                return false;
            }
        }

        if (!$product->isSalable()) {
            throw new Mage_Core_Exception(null, self::EXCEPTION_CODE_NOT_SALABLE);
        }

        if ($product->getTypeInstance(true)->hasRequiredOptions($product)) {
            throw new Mage_Core_Exception(null, self::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS);
        }

        $cart->addProduct($product);
        if (!$product->isVisibleInSiteVisibility()) {
            $cart->getQuote()->getItemByProduct($product)->setStoreId($storeId);
        }

        if ($delete) {
            $this->delete();
        }

        return true;
    }

    /**
     * Retrieve Product View Page URL
     *
     * If product has required options add special key to URL
     *
     * @return string
     */
    public function getProductUrl()
    {
        $product = $this->getProduct();
        $query   = array();

        if ($product->getTypeInstance(true)->hasRequiredOptions($product)) {
            $query['options'] = 'cart';
        }

        return $product->getUrlModel()->getUrl($product, array('_query' => $query));
    }
}
