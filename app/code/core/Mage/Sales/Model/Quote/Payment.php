<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Quote payment information
 *
 * @category   Mage
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Resource_Quote_Payment _getResource()
 * @method Mage_Sales_Model_Resource_Quote_Payment getResource()
 * @method Mage_Sales_Model_Resource_Quote_Payment_Collection getCollection()
 *
 * @method string getAdditionalData()
 * @method $this setAdditionalData(string $value)
 *
 * @method $this setCcCid(string $value)
 * @method string getCcCidEnc()
 * @method $this setCcCidEnc(string $value)
 * @method int getCcExpMonth()
 * @method $this setCcExpMonth(int $value)
 * @method int getCcExpYear()
 * @method $this setCcExpYear(int $value)
 * @method string getCcLast4()
 * @method $this setCcLast4(string $value)
 * @method $this setCcNumber(string $value)
 * @method string getCcNumberEnc()
 * @method $this setCcNumberEnc(string $value)
 * @method string getCcOwner()
 * @method $this setCcOwner(string $value)
 * @method string getCcSsIssue()
 * @method $this setCcSsIssue(string $value)
 * @method string getCcSsOwner()
 * @method $this setCcSsOwner(string $value)
 * @method int getCcSsStartMonth()
 * @method $this setCcSsStartMonth(int $value)
 * @method int getCcSsStartYear()
 * @method $this setCcSsStartYear(int $value)
 * @method string getCcType()
 * @method $this setCcType(string $value)
 * @method string getCreatedAt()
 * @method $this setCreatedAt(string $value)
 * @method int getCustomerPaymentId()
 * @method $this setCustomerPaymentId(int $value)
 * @method string getCybersourceToken()
 * @method $this setCybersourceToken(string $value)
 *
 * @method string getIdealIssuerId()
 * @method $this setIdealIssuerId(string $value)
 * @method string getIdealIssuerList()
 * @method $this setIdealIssuerList(string $value)
 *
 * @method string getMethod()
 * @method $this setMethod(string $value)
 *
 * @method string getPaypalCorrelationId()
 * @method $this setPaypalCorrelationId(string $value)
 * @method string getPaypalPayerId()
 * @method $this setPaypalPayerId(string $value)
 * @method string getPaypalPayerStatus()
 * @method $this setPaypalPayerStatus(string $value)
 * @method string getPoNumber()
 * @method $this setPoNumber(string $value)
 *
 * @method int getQuoteId()
 * @method $this setQuoteId(int $value)
 *
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 *
 * @method string getUpdatedAt()
 * @method $this setUpdatedAt(string $value)
 */
class Mage_Sales_Model_Quote_Payment extends Mage_Payment_Model_Info
{
    protected $_eventPrefix = 'sales_quote_payment';
    protected $_eventObject = 'payment';

    protected $_quote;

    protected function _construct()
    {
        $this->_init('sales/quote_payment');
    }

    /**
     * Declare quote model instance
     *
     * @return  $this
     */
    public function setQuote(Mage_Sales_Model_Quote $quote)
    {
        $this->_quote = $quote;
        if ($this->getQuoteId() != $quote->getId()) {
            $this->setQuoteId($quote->getId());
        }
        return $this;
    }

    /**
     * Retrieve quote model instance
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_quote;
    }

    /**
     * Import data array to payment method object,
     * Method calls quote totals collect because payment method availability
     * can be related to quote totals
     *
     * @throws  Mage_Core_Exception
     * @return  $this
     */
    public function importData(array $data)
    {
        $data = new Varien_Object($data);
        Mage::dispatchEvent(
            $this->_eventPrefix . '_import_data_before',
            [
                $this->_eventObject => $this,
                'input' => $data,
            ],
        );

        $this->setMethod($data->getMethod());
        $method = $this->getMethodInstance();

        /**
         * Payment availability related with quote totals.
         * We have to recollect quote totals before checking
         */
        $this->getQuote()->collectTotals();

        if (!$method->isAvailable($this->getQuote())
            || !$method->isApplicableToQuote($this->getQuote(), $data->getChecks())
        ) {
            Mage::throwException(Mage::helper('sales')->__('The requested Payment Method is not available.'));
        }

        $method->assignData($data);
        /*
        * validating the payment data
        */
        $method->validate();
        return $this;
    }

    /**
     * Prepare object for save
     *
     * @inheritDoc
     */
    protected function _beforeSave()
    {
        if ($this->getQuote()) {
            $this->setQuoteId($this->getQuote()->getId());
        }
        try {
            $method = $this->getMethodInstance();
        } catch (Mage_Core_Exception $e) {
            return parent::_beforeSave();
        }
        $method->prepareSave();
        return parent::_beforeSave();
    }

    /**
     * Checkout redirect URL getter
     *
     * @return string
     */
    public function getCheckoutRedirectUrl()
    {
        $method = $this->getMethodInstance();
        if ($method) {
            return $method->getCheckoutRedirectUrl();
        }
        return '';
    }

    /**
     * Checkout order place redirect URL getter
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        $method = $this->getMethodInstance();
        if ($method) {
            return $method->getOrderPlaceRedirectUrl();
        }
        return '';
    }

    /**
     * Retrieve payment method model object
     *
     * @return Mage_Payment_Model_Method_Abstract
     */
    public function getMethodInstance()
    {
        $method = parent::getMethodInstance();
        return $method->setStore($this->getQuote()->getStore());
    }
}
