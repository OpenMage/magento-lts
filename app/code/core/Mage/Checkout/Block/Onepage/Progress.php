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
 * One page checkout status
 *
 * @category   Mage
 * @package    Mage_Checkout
 */
class Mage_Checkout_Block_Onepage_Progress extends Mage_Checkout_Block_Onepage_Abstract
{
    /**
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getBilling()
    {
        return $this->getQuote()->getBillingAddress();
    }

    /**
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getShipping()
    {
        return $this->getQuote()->getShippingAddress();
    }

    /**
     * @return string
     */
    public function getShippingMethod()
    {
        return $this->getQuote()->getShippingAddress()->getShippingMethod();
    }

    /**
     * @return string
     */
    public function getShippingDescription()
    {
        return $this->getQuote()->getShippingAddress()->getShippingDescription();
    }

    /**
     * @return float
     */
    public function getShippingAmount()
    {
        /*$amount = $this->getQuote()->getShippingAddress()->getShippingAmount();
        $filter = Mage::app()->getStore()->getPriceFilter();
        return $filter->filter($amount);*/
        //return $this->helper('checkout')->formatPrice(
        //    $this->getQuote()->getShippingAddress()->getShippingAmount()
        //);
        return $this->getQuote()->getShippingAddress()->getShippingAmount();
    }

    /**
     * @return string
     */
    public function getPaymentHtml()
    {
        return $this->getChildHtml('payment_info');
    }

    /**
     * Get is step completed. if is set 'toStep' then all steps after him is not completed.
     *
     * @param string $currentStep
     *  @see: Mage_Checkout_Block_Onepage_Abstract::_getStepCodes() for allowed values
     * @return bool
     */
    public function isStepComplete($currentStep)
    {
        $stepsRevertIndex = array_flip($this->_getStepCodes());

        $toStep = $this->getRequest()->getParam('toStep');

        if (empty($toStep) || !isset($stepsRevertIndex[$currentStep])) {
            return $this->getCheckout()->getStepData($currentStep, 'complete');
        }

        if ($stepsRevertIndex[$currentStep] > $stepsRevertIndex[$toStep]) {
            return false;
        }

        return $this->getCheckout()->getStepData($currentStep, 'complete');
    }

    /**
     * Get quote shipping price including tax
     * @return float
     */
    public function getShippingPriceInclTax()
    {
        $inclTax = $this->getQuote()->getShippingAddress()->getShippingInclTax();
        return $this->formatPrice($inclTax);
    }

    /**
     * @return string
     */
    public function getShippingPriceExclTax()
    {
        return $this->formatPrice($this->getQuote()->getShippingAddress()->getShippingAmount());
    }

    /**
     * @param float $price
     * @return string
     */
    public function formatPrice($price)
    {
        return $this->getQuote()->getStore()->formatPrice($price);
    }
}
