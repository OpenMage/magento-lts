<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2018-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart operation observer
 *
 * @category   Mage
 * @package    Mage_Wishlist
 */
class Mage_Wishlist_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * Get customer wishlist model instance
     *
     * @param   int $customerId
     * @return  Mage_Wishlist_Model_Wishlist|false
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
        $productIds = [];

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
                    $productId  = $item->getProductId();
                    $buyRequest = $item->getBuyRequest();

                    if (isset($itemInfo['qty']) && is_numeric($itemInfo['qty'])) {
                        $buyRequest->setQty($itemInfo['qty']);
                    }
                    $wishlist->addNewItem($productId, $buyRequest);

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

    /**
     * @param Varien_Event_Observer $observer
     */
    public function processAddToCart($observer)
    {
        /** @var Mage_Core_Controller_Request_Http $request */
        $request = $observer->getEvent()->getRequest();
        $sharedWishlist     = $this->getCheckoutSession()->getSharedWishlist();
        $messages           = $this->getCheckoutSession()->getWishlistPendingMessages();
        $urls               = $this->getCheckoutSession()->getWishlistPendingUrls();
        $wishlistIds        = $this->getCheckoutSession()->getWishlistIds();
        $singleWishlistId   = $this->getCheckoutSession()->getSingleWishlistId();

        if ($singleWishlistId) {
            $wishlistIds = [$singleWishlistId];
        }

        if (!empty($wishlistIds) && $request->getParam('wishlist_next')) {
            $wishlistId = array_shift($wishlistIds);

            if ($this->getCustomerSession()->isLoggedIn()) {
                $wishlist = Mage::getModel('wishlist/wishlist')
                        ->loadByCustomer($this->getCustomerSession()->getCustomer(), true);
            } elseif ($sharedWishlist) {
                $wishlist = Mage::getModel('wishlist/wishlist')->loadByCode($sharedWishlist);
            } else {
                return;
            }

            $wishlist->getItemCollection()->load();

            foreach ($wishlist->getItemCollection() as $wishlistItem) {
                if ($wishlistItem->getId() == $wishlistId) {
                    $wishlistItem->delete();
                }
            }
            $this->getCheckoutSession()->setWishlistIds($wishlistIds);
            $this->getCheckoutSession()->setSingleWishlistId(null);
        }

        if ($request->getParam('wishlist_next') && !empty($urls)) {
            $url = array_shift($urls);
            $message = array_shift($messages);

            $this->getCheckoutSession()->setWishlistPendingUrls($urls);
            $this->getCheckoutSession()->setWishlistPendingMessages($messages);

            $this->getCheckoutSession()->addError($message);

            $observer->getEvent()->getResponse()->setRedirect($url);
            $this->getCheckoutSession()->setNoCartRedirect(true);
        }
    }

    /**
     * Customer login processing
     *
     * @return $this
     */
    public function customerLogin(Varien_Event_Observer $observer)
    {
        Mage::helper('wishlist')->calculate();

        return $this;
    }

    /**
     * Customer logout processing
     *
     * @return $this
     */
    public function customerLogout(Varien_Event_Observer $observer)
    {
        $this->getCustomerSession()->setWishlistItemCount(0);

        return $this;
    }
}
