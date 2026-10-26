<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Account dashboard sidebar
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Block_Account_Dashboard_Sidebar extends Mage_Core_Block_Template
{
    protected $_cartItemsCount;

    /**
     * @var Mage_Wishlist_Model_Wishlist
     */
    protected $_wishlist;

    protected $_compareItems;

    /**
     * @return string
     */
    public function getShoppingCartUrl()
    {
        return Mage::getUrl('checkout/cart');
    }

    /**
     * @return int
     */
    public function getCartItemsCount()
    {
        if (!$this->_cartItemsCount) {
            $this->_cartItemsCount = Mage::getModel('sales/quote')
                ->setId(Mage::getModel('checkout/session')->getQuote()->getId())
                ->getItemsCollection()
                ->getSize();
        }

        return $this->_cartItemsCount;
    }

    /**
     * @return Mage_Wishlist_Model_Resource_Item_Collection
     */
    public function getWishlist()
    {
        if (!$this->_wishlist) {
            $this->_wishlist = Mage::getModel('wishlist/wishlist')
                ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer());
            $this->_wishlist->getItemCollection()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('price')
                ->addAttributeToSelect('small_image')
                ->addAttributeToFilter('store_id', ['in' => $this->_wishlist->getSharedStoreIds()])
                ->addAttributeToSort('added_at', 'desc')
                ->setCurPage(1)
                ->setPageSize(3)
                ->load();
        }

        return $this->_wishlist->getItemCollection();
    }

    /**
     * @return int
     */
    public function getWishlistCount()
    {
        return $this->getWishlist()->getSize();
    }

    /**
     * @param  Mage_Wishlist_Model_Item $wishlistItem
     * @return string
     */
    public function getWishlistAddToCartLink($wishlistItem)
    {
        return Mage::getUrl('wishlist/index/cart', ['item' => $wishlistItem->getId()]);
    }

    /**
     * @return Mage_Catalog_Model_Resource_Product_Compare_Item_Collection
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getCompareItems()
    {
        if (!$this->_compareItems) {
            $this->_compareItems = Mage::getResourceModel('catalog/product_compare_item_collection')
                ->setStoreId(Mage::app()->getStore()->getId());
            $this->_compareItems->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
            $this->_compareItems
               ->addAttributeToSelect('name')
               ->useProductItem()
               ->load();
        }

        return $this->_compareItems;
    }

    /**
     * @return string
     */
    public function getCompareJsObjectName()
    {
        return 'dashboardSidebarCompareJsObject';
    }

    /**
     * @return string
     */
    public function getCompareRemoveUrlTemplate()
    {
        return $this->getUrl('catalog/product_compare/remove', ['product' => '#{id}']);
    }

    /**
     * @return string
     */
    public function getCompareAddUrlTemplate()
    {
        return $this->getUrl('catalog/product_compare/add', ['product' => '#{id}']);
    }

    /**
     * @return string
     */
    public function getCompareUrl()
    {
        return $this->getUrl('catalog/product_compare');
    }
}
