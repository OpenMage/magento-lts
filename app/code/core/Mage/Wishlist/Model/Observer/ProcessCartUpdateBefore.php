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
class Mage_Wishlist_Model_Observer_ProcessCartUpdateBefore extends Mage_Core_Model_Abstract implements Mage_Core_Observer_Interface
{
    /**
     * Customer logout processing
     * @throws Throwable
     */
    public function execute(Varien_Event_Observer $observer): self
    {
        $cart = $observer->getEvent()->getDataByKey('cart');
        $info = $observer->getEvent()->getDataByKey('info');
        $productIds = [];

        $wishlist = $this->_getWishlist($cart->getQuote()->getCustomerId());
        if (!$wishlist) {
            return $this;
        }

        /**
         * Collect product ids marked for move to wishlist
         */
        foreach ($info as $itemId => $itemInfo) {
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
}
