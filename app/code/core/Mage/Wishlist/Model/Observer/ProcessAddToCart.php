<?php

declare(strict_types=1);

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
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart operation observer
 *
 * @category   Mage
 * @package    Mage_Wishlist
 */
class Mage_Wishlist_Model_Observer_ProcessAddToCart extends Mage_Core_Model_Abstract implements Mage_Core_Observer_Interface
{
    /**
     * Customer logout processing
     * @throws Mage_Core_Model_Store_Exception
     */
    public function execute(Varien_Event_Observer $observer): void
    {
        /** @var Mage_Core_Controller_Request_Http $request */
        $request = $observer->getEvent()->getDataByKey('request');
        $sharedWishlist = $this->getCheckoutSession()->getSharedWishlist();
        $messages = $this->getCheckoutSession()->getWishlistPendingMessages();
        $urls = $this->getCheckoutSession()->getWishlistPendingUrls();
        $wishlistIds = $this->getCheckoutSession()->getWishlistIds();
        $singleWishlistId = $this->getCheckoutSession()->getSingleWishlistId();

        if ($singleWishlistId) {
            $wishlistIds = [$singleWishlistId];
        }

        if (!empty($wishlistIds) && $request->getParam('wishlist_next')) {
            $wishlistId = array_shift($wishlistIds);

            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                $wishlist = Mage::getModel('wishlist/wishlist')
                    ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
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

            /** @var Mage_Core_Controller_Response_Http $response */
            $response = $observer->getEvent()->getDataByKey('response');
            $response->setRedirect($url);
            $this->getCheckoutSession()->setNoCartRedirect(true);
        }
    }

    protected function getCheckoutSession(): Mage_Checkout_Model_Session
    {
        return Mage::getSingleton('checkout/session');
    }
}
