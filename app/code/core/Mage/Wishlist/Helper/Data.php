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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Wishlist Data Helper
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Customer Wishlist instance
     *
     * @var Mage_Wishlist_Model_Wishlist
     */
    protected $_wishlist = null;

    /**
     * Wishlist Product Items Collection
     *
     * @var Mage_Wishlist_Model_Mysql4_Product_Collection
     */
    protected $_productCollection = null;

    /**
     * Retrieve customer login status
     *
     * @return bool
     */
    protected function _isCustomerLogIn()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }

    /**
     * Retrieve logged in customer
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function _getCurrentCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    /**
     * Retrieve wishlist by logged in customer
     *
     * @return Mage_Wishlist_Model_Wishlist
     */
    public function getWishlist()
    {
        if (is_null($this->_wishlist)) {
            $this->_wishlist = Mage::getModel('wishlist/wishlist')
                ->loadByCustomer($this->_getCurrentCustomer());
        }
        return $this->_wishlist;
    }

    /**
     * Retrieve wishlist items availability
     *
     * @return bool
     */
    public function hasItems()
    {
        return $this->getItemCount() > 0;
    }

    /**
     * Retrieve wishlist item count
     *
     * @return int
     */
    public function getItemCount()
    {
        if (!Mage::getSingleton('customer/session')->hasWishlistItemCount()) {
            $this->calculate();
        }

        return Mage::getSingleton('customer/session')->getWishlistItemCount();
    }

    /**
     * Retrieve wishlist product items collection
     *
     * alias for getProductCollection
     *
     * @return Mage_Wishlist_Model_Mysql4_Product_Collection
     */
    public function getItemCollection()
    {
        return $this->getProductCollection();
    }

    /**
     * Retrieve wishlist product items collection
     *
     * @return Mage_Wishlist_Model_Mysql4_Product_Collection
     */
    public function getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $this->_productCollection = $this->getWishlist()
                ->getProductCollection();

            Mage::getSingleton('catalog/product_visibility')
                ->addVisibleInSiteFilterToCollection($this->_productCollection);
        }
        return $this->_productCollection;
    }

    /**
     * Retrieve Item Store for URL
     *
     * @param Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item $item
     * @return Mage_Core_Model_Store
     */
    protected function _getUrlStore($item)
    {
        $storeId = null;
        if ($item instanceof Mage_Catalog_Model_Product) {
            if ($item->isVisibleInSiteVisibility()) {
                $storeId = $item->getStoreId();
            }
            else if ($item->hasUrlDataObject()) {
                $storeId = $item->getUrlDataObject()->getStoreId();
            }
        }
        return Mage::app()->getStore($storeId);
    }

    /**
     * Retrieve URL for removing item from wishlist
     *
     * @param Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item $item
     * @return string
     */
    public function getRemoveUrl($item)
    {
        return $this->_getUrl('wishlist/index/remove', array(
            'item' => $item->getWishlistItemId()
        ));
    }

    /**
     * Retrieve url for adding product to wishlist
     *
     * @param Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item $product
     * @return  string|boolean
     */
    public function getAddUrl($item)
    {
        return $this->getAddUrlWithParams($item);
    }

    /**
     * Retrieve url for adding product to wishlist with params
     *
     * @param Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item $product
     * @param array $param
     * @return  string|boolean
     */
    public function getAddUrlWithParams($item, array $params = array())
    {
        $productId = null;
        if ($item instanceof Mage_Catalog_Model_Product) {
            $productId = $item->getEntityId();
        }
        if ($item instanceof Mage_Wishlist_Model_Item) {
            $productId = $item->getProductId();
        }

        if ($productId) {
            $params['product'] = $productId;
            return $this->_getUrlStore($item)->getUrl('wishlist/index/add', $params);
        }

        return false;
    }

    /**
     * Retrieve URL for adding item to shoping cart
     *
     * @param Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item $item
     * @return  string
     */
    public function getAddToCartUrl($item)
    {
        $urlParamName = Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;
        $continueUrl  = Mage::helper('core')->urlEncode(Mage::getUrl('*/*/*', array(
            '_current'      => true,
            '_use_rewrite'  => true,
            '_store_to_url' => true,
        )));

        return $this->_getUrlStore($item)->getUrl('wishlist/index/cart', array(
            'item'          => $item->getWishlistItemId(),
            $urlParamName   => $continueUrl
        ));
    }

    /**
     * Retrieve url for adding item to shoping cart with b64 referer
     *
     * @deprecated
     * @param   Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item $item
     * @return  string
     */
    public function getAddToCartUrlBase64($item)
    {
        return $this->getAddToCartUrl($item);
    }

    /**
     * Retrieve customer wishlist url
     *
     * @return string
     */
    public function getListUrl()
    {
        return $this->_getUrl('wishlist');
    }

    /**
     * Check is allow wishlist module
     *
     * @return bool
     */
    public function isAllow()
    {
        if ($this->isModuleOutputEnabled() && Mage::getStoreConfig('wishlist/general/active')) {
            return true;
        }
        return false;
    }

    /**
     * Check is allow wishlist action in shopping cart
     *
     * @return bool
     */
    public function isAllowInCart()
    {
        return $this->isAllow() && $this->_isCustomerLogIn();
    }

    /**
     * Retrieve customer name
     *
     * @return string
     */
    public function getCustomerName()
    {
        return $this->_getCurrentCustomer()->getName();
    }

    /**
     * Retrieve RSS URL
     *
     * @return string
     */
    public function getRssUrl()
    {
        $customer = $this->_getCurrentCustomer();
        $key = $customer->getId().','.$customer->getEmail();
        return $this->_getUrl('rss/index/wishlist', array('data' => Mage::helper('core')->urlEncode($key), '_secure' => false));
    }

    /**
     * Is allow RSS
     *
     * @return bool
     */
    public function isRssAllow()
    {
        if (Mage::getStoreConfig('rss/wishlist/active')) {
            return true;
        }
        return false;
    }

    /**
     * Retrieve default empty comment message
     *
     * @return string
     */
    public function defaultCommentString()
    {
        return $this->__('Please, enter your comments...');
    }

    /**
     * Calculate count of wishlist items and put value to customer session.
     * Method called after wishlist modifications and trigger 'wishlist_items_renewed' event.
     *
     * @return Mage_Wishlist_Helper_Data
     */
    public function calculate()
    {
        if (!$this->_isCustomerLogIn()) {
            $count = 0;
        }
        else {
            $count = $this->getProductCollection()
                ->getSize();
        }
        Mage::getSingleton('customer/session')->setWishlistItemCount($count);
        Mage::dispatchEvent('wishlist_items_renewed');
        return $this;
    }
}
