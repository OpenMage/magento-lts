<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 */

/**
 * Checkout observer model
 *
 * @package    Mage_Checkout
 */
class Mage_Checkout_Model_Observer
{
    public function unsetAll()
    {
        Mage::getSingleton('checkout/session')->unsetAll();
    }

    public function loadCustomerQuote()
    {
        try {
            Mage::getSingleton('checkout/session')->loadCustomerQuote();
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('checkout/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('checkout/session')->addException(
                $e,
                Mage::helper('checkout')->__('Load customer quote error'),
            );
        }
    }

    public function salesQuoteSaveAfter(Varien_Event_Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        /** @var Mage_Sales_Model_Quote $quote */
        if ($quote->getIsCheckoutCart()) {
            Mage::getSingleton('checkout/session')->getQuoteId($quote->getId());
        }
    }
}
