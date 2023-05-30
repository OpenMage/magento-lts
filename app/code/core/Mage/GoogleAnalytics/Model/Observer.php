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
     * Create Google Analytics block for success page view
     *
     * @deprecated after 1.3.2.3 Use setGoogleAnalyticsOnOrderSuccessPageView() method instead
     * @param Varien_Event_Observer $observer
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function order_success_page_view($observer)
    {
        $this->setGoogleAnalyticsOnOrderSuccessPageView($observer);
    }

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
            Mage::getSingleton('core/session')->setRemovedProductCart($productRemoved->getId());
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
            Mage::getSingleton('core/session')->setAddedProductCart($productAdded->getId());
        }
    }
}
