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
 * Shopping cart operation observer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * Get customer wishlist model instance
     *
     * @param   int $customerId
     * @return  Mage_Wishlist_Model_Wishlist || false
     */
    protected function _getWishlist($customerId)
    {
        if (!$customerId) {
            return false;
        }
        return Mage::getModel('wishlist/wishlist')->loadByCustomer($customerId, true);
    }

    /**
     * Check move quote item to wishlist request
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_Wishlist_Model_Observer
     */
    public function processCartUpdateBefore($observer)
    {
        $cart = $observer->getEvent()->getCart();
        $data = $observer->getEvent()->getInfo();
        $productIds = array();

        $wishlist = $this->_getWishlist($cart->getQuote()->getCustomerId());
        if (!$wishlist) {
            return $this;
        }

        /**
         * Collect product ids marked for move to wishlist
         */
        foreach ($data as $itemId => $itemInfo) {
            if (!empty($itemInfo['wishlist'])) {
                if ($item = $cart->getQuote()->getItemById($itemId)) {
                    $productId = $item->getProductId();

                    $wishlist->addNewItem($productId, $item->getBuyRequest());
                    
                    $productIds[] = $productId;
                    $cart->getQuote()->removeItem($itemId);
                }
            }
        }

        if (!empty($productIds)) {
            $wishlist->save();
            Mage::helper('wishlist')->calculate();
        }
        return $this;
    }

    public function processAddToCart($observer)
    {
        $request = $observer->getEvent()->getRequest();
        $sharedWishlist = Mage::getSingleton('checkout/session')->getSharedWishlist();
        $messages = Mage::getSingleton('checkout/session')->getWishlistPendingMessages();
        $urls = Mage::getSingleton('checkout/session')->getWishlistPendingUrls();
        $wishlistIds = Mage::getSingleton('checkout/session')->getWishlistIds();
        $singleWishlistId = Mage::getSingleton('checkout/session')->getSingleWishlistId();

        if ($singleWishlistId) {
            $wishlistIds = array($singleWishlistId);
        }

        if (count($wishlistIds) && $request->getParam('wishlist_next')){
            $wishlistId = array_shift($wishlistIds);

            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                $wishlist = Mage::getModel('wishlist/wishlist')
                        ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
            } else if ($sharedWishlist) {
                $wishlist = Mage::getModel('wishlist/wishlist')->loadByCode($sharedWishlist);
            } else {
                return;
            }


            $wishlist->getItemCollection()->load();

            foreach($wishlist->getItemCollection() as $wishlistItem){
                if ($wishlistItem->getId() == $wishlistId)
                    $wishlistItem->delete();
            }
            Mage::getSingleton('checkout/session')->setWishlistIds($wishlistIds);
            Mage::getSingleton('checkout/session')->setSingleWishlistId(null);
        }

        if ($request->getParam('wishlist_next') && count($urls)) {
            $url = array_shift($urls);
            $message = array_shift($messages);

            Mage::getSingleton('checkout/session')->setWishlistPendingUrls($urls);
            Mage::getSingleton('checkout/session')->setWishlistPendingMessages($messages);

            Mage::getSingleton('checkout/session')->addError($message);

            $observer->getEvent()->getResponse()->setRedirect($url);
            Mage::getSingleton('checkout/session')->setNoCartRedirect(true);
        }
    }

    /**
     * Customer login processing
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Wishlist_Model_Observer
     */
    public function customerLogin(Varien_Event_Observer $observer)
    {
        Mage::helper('wishlist')->calculate();

        return $this;
    }

    /**
     * Customer logout processing
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Wishlist_Model_Observer
     */
    public function customerLogout(Varien_Event_Observer $observer)
    {
        Mage::getSingleton('customer/session')->setWishlistItemCount(0);

        return $this;
    }

}
