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
 * Wishlist Product Items abstract Block
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Wishlist_Block_Abstract extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * Wishlist Product Items Collection
     *
     * @var Mage_Wishlist_Model_Mysql4_Product_Collection
     */
    protected $_collection;

    /**
     * Wishlist Model
     *
     * @var Mage_Wishlist_Model_Wishlist
     */
    protected $_wishlist;

    /**
     * Retrieve Wishlist Data Helper
     *
     * @return Mage_Wishlist_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('wishlist');
    }

    /**
     * Retrieve Customer Session instance
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Retrieve Wishlist model
     *
     * @return Mage_Wishlist_Model_Wishlist
     */
    protected function _getWishlist()
    {
        if (is_null($this->_wishlist)) {
            if (Mage::registry('shared_wishlist')) {
                $this->_wishlist = Mage::registry('shared_wishlist');
            }
            elseif (Mage::registry('wishlist')) {
                $this->_wishlist = Mage::registry('wishlist');
            }
            else {
                $this->_wishlist = Mage::getModel('wishlist/wishlist');
                if ($this->_getCustomerSession()->isLoggedIn()) {
                    $this->_wishlist->loadByCustomer($this->_getCustomerSession()->getCustomer());
                }
            }
        }
        return $this->_wishlist;
    }

    /**
     * Prepare additional conditions to collection
     *
     * @param Mage_Wishlist_Model_Mysql4_Product_Collection $collection
     * @return Mage_Wishlist_Block_Customer_Wishlist
     */
    protected function _prepareCollection($collection)
    {
        return $this;
    }

    /**
     * Retrieve Wishlist Product Items collection
     *
     * @return Mage_Wishlist_Model_Mysql4_Product_Collection
     */
    public function getWishlistItems()
    {
        if (is_null($this->_collection)) {
            $attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
            $this->_collection = $this->_getWishlist()
                ->getProductCollection()
                ->addAttributeToSelect($attributes)
                ->addStoreFilter()
                ->addUrlRewrite();

            Mage::getSingleton('catalog/product_visibility')
                ->addVisibleInSiteFilterToCollection($this->_collection);

            $this->_prepareCollection($this->_collection);
        }

        return $this->_collection;
    }

    /**
     * Back compatibility retrieve wishlist product items
     *
     * @deprecated
     * @return Mage_Wishlist_Model_Mysql4_Product_Collection
     */
    public function getWishlist()
    {
        return $this->getWishlistItems();
    }

    /**
     * Retrieve URL for Removing item from wishlist
     *
     * @param Mage_Catalog_Model_Product $item
     * @return string
     */
    public function getItemRemoveUrl($product)
    {
        return $this->_getHelper()->getRemoveUrl($product);
    }

    /**
     * Retrieve Add Item to shopping cart URL
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getItemAddToCartUrl($product)
    {
        return $this->_getHelper()->getAddToCartUrl($product);
    }

    /**
     * Retrieve URL for adding Product to wishlist
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getAddToWishlistUrl($product)
    {
        return $this->_getHelper()->getAddUrl($product);
    }

    /**
     * Retrieve Escaped Description for Wishlist Item
     *
     * @param Mage_Catalog_Model_Product $item
     * @return string
     */
    public function getEscapedDescription($item)
    {
        if ($item->getWishlistItemDescription()) {
            return $this->htmlEscape($item->getWishlistItemDescription());
        }
        return '&nbsp;';
    }

    /**
     * Check Wishlist item has description
     *
     * @param Mage_Catalog_Model_Product $item
     * @return bool
     */
    public function hasDescription($item)
    {
        return trim($item->getWishlistItemDescription()) != '';
    }

    /**
     * Retrieve formated Date
     *
     * @param string $date
     * @return string
     */
    public function getFormatedDate($date)
    {
        return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
    }

    /**
     * Check is the wishlist has a salable product(s)
     *
     * @return bool
     */
    public function isSaleable()
    {
        foreach ($this->getWishlistItems() as $item) {
            if ($item->isSaleable()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve wishlist loaded items count
     *
     * @return int
     */
    public function getWishlistItemsCount()
    {
        return $this->getWishlistItems()->count();
    }

    /**
     * Check is the wishlist has items
     *
     * @return bool
     */
    public function hasWishlistItems()
    {
        return $this->getWishlistItemsCount() > 0;
    }
}
