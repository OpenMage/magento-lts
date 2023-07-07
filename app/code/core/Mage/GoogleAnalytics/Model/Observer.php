<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_GoogleAnalytics
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Google Analytics module observer
 *
 * @category   Mage
 * @package    Mage_GoogleAnalytics
 */
class Mage_GoogleAnalytics_Model_Observer
{
    /**
     * Add order information into GA block to render on checkout success pages
     *
     * @param Varien_Event_Observer $observer
     */
    public function setGoogleAnalyticsOnOrderSuccessPageView(Varien_Event_Observer $observer)
    {
        $orderIds = $observer->getEvent()->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }
        $block = Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('google_analytics');
        if ($block) {
            $block->setOrderIds($orderIds);
        }
    }

    /**
     * Add 'removed item' from cart into session for GA4 block to render event on cart view
     *
     * @param Varien_Event_Observer $observer
     */
    public function removeItemFromCartGoogleAnalytics(Varien_Event_Observer $observer)
    {
        $productRemoved = $observer->getEvent()->getQuoteItem()->getProduct();
        if ($productRemoved) {
            $_removedProducts = Mage::getSingleton('core/session')->getRemovedProductsCart() ?: [];
            $_removedProducts[] = $productRemoved->getId();
            $_removedProducts = array_unique($_removedProducts);
            Mage::getSingleton('core/session')->setRemovedProductsCart($_removedProducts);
        }
    }

    /**
     * Add 'added item' to cart into session for GA4 block to render event on cart view
     *
     * @param Varien_Event_Observer $observer
     */
    public function addItemToCartGoogleAnalytics(Varien_Event_Observer $observer)
    {
        $productAdded = $observer->getEvent()->getQuoteItem()->getProduct();
        if ($productAdded) {
            // Fix double add to cart for configurable products, skip child product
            if ($productAdded->getParentProductId()) {
                return;
            }
            $_addedProducts = Mage::getSingleton('core/session')->getAddedProductsCart() ?: [];
            $_addedProducts[] = $productAdded->getParentItem() ? $productAdded->getParentItem()->getId() : $productAdded->getId();
            $_addedProducts = array_unique($_addedProducts);
            Mage::getSingleton('core/session')->setAddedProductsCart($_addedProducts);
        }
    }
}
