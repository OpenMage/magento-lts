<?php

declare(strict_types=1);

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
 * @method Mage_Sales_Model_Resource_Quote_Payment_Collection getCollection()
 * @method Mage_Sales_Model_Resource_Quote_Payment            getResource()
 * @method Mage_Sales_Model_Resource_Quote_Payment_Collection getResourceCollection()
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
    #[Override]
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
    #[Override]
    public function getMethodInstance()
    {
        $method = parent::getMethodInstance();
        return $method->setStore($this->getQuote()->getStore());
    }

    public function getAdditionalData(): ?string
    {
        $value = $this->_getData('additional_data');
        return $value !== null ? (string) $value : null;
    }

    public function setAdditionalData(string $value): static
    {
        return $this->setData('additional_data', $value);
    }

    public function getCcCidEnc(): ?string
    {
        $value = $this->_getData('cc_cid_enc');
        return $value !== null ? (string) $value : null;
    }

    public function setCcCidEnc(string $value): static
    {
        return $this->setData('cc_cid_enc', $value);
    }

    public function setCcCid(string $value): static
    {
        return $this->setData('cc_cid', $value);
    }

    public function getCcExpMonth(): ?int
    {
        $value = $this->_getData('cc_exp_month');
        return $value !== null ? (int) $value : null;
    }

    public function setCcExpMonth(int $value): static
    {
        return $this->setData('cc_exp_month', $value);
    }

    public function getCcExpYear(): ?int
    {
        $value = $this->_getData('cc_exp_year');
        return $value !== null ? (int) $value : null;
    }

    public function setCcExpYear(int $value): static
    {
        return $this->setData('cc_exp_year', $value);
    }

    public function getCcLast4(): ?string
    {
        $value = $this->_getData('cc_last4');
        return $value !== null ? (string) $value : null;
    }

    public function setCcLast4(string $value): static
    {
        return $this->setData('cc_last4', $value);
    }

    public function getCcNumberEnc(): ?string
    {
        $value = $this->_getData('cc_number_enc');
        return $value !== null ? (string) $value : null;
    }

    public function setCcNumberEnc(string $value): static
    {
        return $this->setData('cc_number_enc', $value);
    }

    public function setCcNumber(string $value): static
    {
        return $this->setData('cc_number', $value);
    }

    public function getCcOwner(): ?string
    {
        $value = $this->_getData('cc_owner');
        return $value !== null ? (string) $value : null;
    }

    public function setCcOwner(string $value): static
    {
        return $this->setData('cc_owner', $value);
    }

    public function getCcSsIssue(): ?string
    {
        $value = $this->_getData('cc_ss_issue');
        return $value !== null ? (string) $value : null;
    }

    public function setCcSsIssue(string $value): static
    {
        return $this->setData('cc_ss_issue', $value);
    }

    public function getCcSsOwner(): ?string
    {
        $value = $this->_getData('cc_ss_owner');
        return $value !== null ? (string) $value : null;
    }

    public function setCcSsOwner(string $value): static
    {
        return $this->setData('cc_ss_owner', $value);
    }

    public function getCcSsStartMonth(): ?int
    {
        $value = $this->_getData('cc_ss_start_month');
        return $value !== null ? (int) $value : null;
    }

    public function setCcSsStartMonth(int $value): static
    {
        return $this->setData('cc_ss_start_month', $value);
    }

    public function getCcSsStartYear(): ?int
    {
        $value = $this->_getData('cc_ss_start_year');
        return $value !== null ? (int) $value : null;
    }

    public function setCcSsStartYear(int $value): static
    {
        return $this->setData('cc_ss_start_year', $value);
    }

    public function getCcType(): ?string
    {
        $value = $this->_getData('cc_type');
        return $value !== null ? (string) $value : null;
    }

    public function setCcType(string $value): static
    {
        return $this->setData('cc_type', $value);
    }

    public function getCustomerPaymentId(): ?int
    {
        $value = $this->_getData('customer_payment_id');
        return $value !== null ? (int) $value : null;
    }

    public function setCustomerPaymentId(int $value): static
    {
        return $this->setData('customer_payment_id', $value);
    }

    public function getCybersourceToken(): ?string
    {
        $value = $this->_getData('cybersource_token');
        return $value !== null ? (string) $value : null;
    }

    public function setCybersourceToken(string $value): static
    {
        return $this->setData('cybersource_token', $value);
    }

    public function getIdealIssuerId(): ?string
    {
        $value = $this->_getData('ideal_issuer_id');
        return $value !== null ? (string) $value : null;
    }

    public function setIdealIssuerId(string $value): static
    {
        return $this->setData('ideal_issuer_id', $value);
    }

    public function getIdealIssuerList(): ?string
    {
        $value = $this->_getData('ideal_issuer_list');
        return $value !== null ? (string) $value : null;
    }

    public function setIdealIssuerList(string $value): static
    {
        return $this->setData('ideal_issuer_list', $value);
    }

    public function getMethod(): ?string
    {
        $value = $this->_getData('method');
        return $value !== null ? (string) $value : null;
    }

    public function setMethod(string $value): static
    {
        return $this->setData('method', $value);
    }

    public function getPaypalCorrelationId(): ?string
    {
        $value = $this->_getData('paypal_correlation_id');
        return $value !== null ? (string) $value : null;
    }

    public function setPaypalCorrelationId(string $value): static
    {
        return $this->setData('paypal_correlation_id', $value);
    }

    public function getPaypalPayerId(): ?string
    {
        $value = $this->_getData('paypal_payer_id');
        return $value !== null ? (string) $value : null;
    }

    public function setPaypalPayerId(string $value): static
    {
        return $this->setData('paypal_payer_id', $value);
    }

    public function getPaypalPayerStatus(): ?string
    {
        $value = $this->_getData('paypal_payer_status');
        return $value !== null ? (string) $value : null;
    }

    public function setPaypalPayerStatus(string $value): static
    {
        return $this->setData('paypal_payer_status', $value);
    }

    public function getPoNumber(): ?string
    {
        $value = $this->_getData('po_number');
        return $value !== null ? (string) $value : null;
    }

    public function setPoNumber(string $value): static
    {
        return $this->setData('po_number', $value);
    }

    public function getQuoteId(): int
    {
        return (int) $this->_getData('quote_id');
    }

    public function setQuoteId(int $value): static
    {
        return $this->setData('quote_id', $value);
    }

    public function getStoreId(): int
    {
        return (int) $this->_getData('store_id');
    }

    public function setStoreId(int $value): static
    {
        return $this->setData('store_id', $value);
    }
}
