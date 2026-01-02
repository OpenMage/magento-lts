<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Quote payment information
 *
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Resource_Quote_Payment            _getResource()
 * @method string                                             getAdditionalData()
 * @method string                                             getCcCidEnc()
 * @method int                                                getCcExpMonth()
 * @method int                                                getCcExpYear()
 * @method string                                             getCcLast4()
 * @method string                                             getCcNumberEnc()
 * @method string                                             getCcOwner()
 * @method string                                             getCcSsIssue()
 * @method string                                             getCcSsOwner()
 * @method int                                                getCcSsStartMonth()
 * @method int                                                getCcSsStartYear()
 * @method string                                             getCcType()
 * @method Mage_Sales_Model_Resource_Quote_Payment_Collection getCollection()
 * @method int                                                getCustomerPaymentId()
 * @method string                                             getCybersourceToken()
 * @method string                                             getIdealIssuerId()
 * @method string                                             getIdealIssuerList()
 * @method string                                             getMethod()
 * @method string                                             getPaypalCorrelationId()
 * @method string                                             getPaypalPayerId()
 * @method string                                             getPaypalPayerStatus()
 * @method string                                             getPoNumber()
 * @method int                                                getQuoteId()
 * @method Mage_Sales_Model_Resource_Quote_Payment            getResource()
 * @method Mage_Sales_Model_Resource_Quote_Payment_Collection getResourceCollection()
 * @method int                                                getStoreId()
 * @method $this                                              setAdditionalData(string $value)
 * @method $this                                              setCcCid(string $value)
 * @method $this                                              setCcCidEnc(string $value)
 * @method $this                                              setCcExpMonth(int $value)
 * @method $this                                              setCcExpYear(int $value)
 * @method $this                                              setCcLast4(string $value)
 * @method $this                                              setCcNumber(string $value)
 * @method $this                                              setCcNumberEnc(string $value)
 * @method $this                                              setCcOwner(string $value)
 * @method $this                                              setCcSsIssue(string $value)
 * @method $this                                              setCcSsOwner(string $value)
 * @method $this                                              setCcSsStartMonth(int $value)
 * @method $this                                              setCcSsStartYear(int $value)
 * @method $this                                              setCcType(string $value)
 * @method $this                                              setCustomerPaymentId(int $value)
 * @method $this                                              setCybersourceToken(string $value)
 * @method $this                                              setIdealIssuerId(string $value)
 * @method $this                                              setIdealIssuerList(string $value)
 * @method $this                                              setMethod(string $value)
 * @method $this                                              setPaypalCorrelationId(string $value)
 * @method $this                                              setPaypalPayerId(string $value)
 * @method $this                                              setPaypalPayerStatus(string $value)
 * @method $this                                              setPoNumber(string $value)
 * @method $this                                              setQuoteId(int $value)
 * @method $this                                              setStoreId(int $value)
 */
class Mage_Sales_Model_Quote_Payment extends Mage_Payment_Model_Info
{
    protected $_eventPrefix = 'sales_quote_payment';

    protected $_eventObject = 'payment';

    protected $_quote;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sales/quote_payment');
    }

    /**
     * Declare quote model instance
     *
     * @return $this
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
     * @return $this
     * @throws Mage_Core_Exception
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
        } catch (Mage_Core_Exception) {
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
