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
 * @package     Mage_GoogleAnalytics
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
    public function order_success_page_view($observer)
    {
        $this->setGoogleAnalyticsOnOrderSuccessPageView($observer);
    }

    /**
     * Create Google Analytics block for success page view
     *
     * @param Varien_Event_Observer $observer
     */
    public function setGoogleAnalyticsOnOrderSuccessPageView(Varien_Event_Observer $observer)
    {
        $quoteId = Mage::getSingleton('checkout/session')->getLastQuoteId();
        $analyticsBlock = Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('google_analytics');
        if ($quoteId && ($analyticsBlock instanceof Mage_Core_Block_Abstract)) {
            $quote = Mage::getModel('sales/quote')->load($quoteId);
            $analyticsBlock->setQuote($quote);
        }
    }
}
