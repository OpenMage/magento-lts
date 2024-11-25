<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Checkout observer model
 *
 * @category   Mage
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
