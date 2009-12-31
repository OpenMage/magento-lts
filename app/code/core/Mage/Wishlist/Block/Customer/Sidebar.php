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
 * @category   Mage
 * @package    Mage_Wishlist
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Wishlist sidebar block
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Wishlist_Block_Customer_Sidebar extends Mage_Catalog_Block_Product_Abstract
{
    protected  $_wishlist = null;

    public function getWishlistItems()
    {
        return $this->getWishlist()->getProductCollection();
    }

    public function getWishlist()
    {
        if(is_null($this->_wishlist)) {
            $this->_wishlist = Mage::getModel('wishlist/wishlist')
                ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer());

            $collection = $this->_wishlist->getProductCollection()
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                //->addAttributeToFilter('store_id', array('in'=>$this->_wishlist->getSharedStoreIds()))
                ->addStoreFilter()
                ->addAttributeToSort('added_at', 'desc')
                ->setCurPage(1)
                ->setPageSize(3)
                ->addUrlRewrite();

            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInSiteFilterToCollection($collection);
        }

        return $this->_wishlist;
    }

    protected function _toHtml()
    {
        if( sizeof($this->getWishlistItems()->getItems()) > 0 ){
            return parent::_toHtml();
        } else {
            return '';
        }
    }

    public function getCanDisplayWishlist()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }

    public function getRemoveItemUrl($item)
    {
        return $this->getUrl('wishlist/index/remove',array('item'=>$item->getWishlistItemId()));
    }

    public function getAddToCartItemUrl($item)
    {
        return Mage::helper('wishlist')->getAddToCartUrlBase64($item);
    }
}
