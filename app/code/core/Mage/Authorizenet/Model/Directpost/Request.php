<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Authorizenet
 */

/**
 * Authorize.net request model for DirectPost model.
 *
 * @package    Mage_Authorizenet
 */
class Mage_Authorizenet_Model_Directpost_Request extends Varien_Object
{
    protected $_transKey = null;

    /**
     * Hexadecimal signature key.
     *
     * @var string
     */
    protected $_signatureKey = '';

    /**
     * Return merchant transaction key.
     * Needed to generate MD5 sign.
     *
     * @return string
     */
    protected function _getTransactionKey()
    {
        return $this->_transKey;
    }

    /**
     * Set merchant transaction key.
     * Needed to generate MD5 sign.
     *
     * @param string $transKey
     * @return $this
     */
    protected function _setTransactionKey($transKey)
    {
        $this->_transKey = $transKey;
        return $this;
    }

    /**
     * Generates the MD5 fingerprint for request.
     *
     * @param string $merchantApiLoginId
     * @param string $merchantTransactionKey
     * @param string $amount
     * @param string $fpSequence An invoice number or random number.
     * @param string $fpTimestamp
     * @return string The fingerprint.
     */
    public function generateRequestSign($merchantApiLoginId, $merchantTransactionKey, $amount, $currencyCode, $fpSequence, $fpTimestamp)
    {
        return hash_hmac(
            'md5',
            $merchantApiLoginId . '^' .
            $fpSequence . '^' .
            $fpTimestamp . '^' .
            $amount . '^' .
            $currencyCode,
            $merchantTransactionKey,
        );
    }

    /**
     * Set paygate data to request.
     *
     * @return $this
     */
    public function setConstantData(Mage_Authorizenet_Model_Directpost $paymentMethod)
    {
        $this->setXVersion('3.1')
            ->setXDelimData('FALSE')
            ->setXRelayResponse('TRUE');

        $this->setXTestRequest($paymentMethod->getConfigData('test') ? 'TRUE' : 'FALSE');

        $this->setXLogin($paymentMethod->getConfigData('login'))
            ->setXType('AUTH_ONLY')
            ->setXMethod(Mage_Paygate_Model_Authorizenet::REQUEST_METHOD_CC)
            ->setXRelayUrl($paymentMethod->getRelayUrl());

        $this->_setTransactionKey($paymentMethod->getConfigData('trans_key'));
        $this->_setSignatureKey($paymentMethod->getConfigData('signature_key'));
        return $this;
    }

    /**
     * Set entity data to request
     *
     * @return $this
     */
    public function setDataFromOrder(Mage_Sales_Model_Order $order, Mage_Authorizenet_Model_Directpost $paymentMethod)
    {
        $payment = $order->getPayment();

        $this->setXFpSequence($order->getQuoteId());
        $this->setXInvoiceNum($order->getIncrementId());
        $this->setXAmount($payment->getBaseAmountAuthorized());
        $this->setXCurrencyCode($order->getBaseCurrencyCode());
        $this->setXTax(sprintf('%.2F', $order->getBaseTaxAmount()))
            ->setXFreight(sprintf('%.2F', $order->getBaseShippingAmount()));

        //need to use strval() because NULL values IE6-8 decodes as "null" in JSON in JavaScript, but we need "" for null values.
        $billing = $order->getBillingAddress();
        if (!empty($billing)) {
            $this->setXFirstName((string) $billing->getFirstname())
                ->setXLastName((string) $billing->getLastname())
                ->setXCompany((string) $billing->getCompany())
                ->setXAddress((string) $billing->getStreet(1))
                ->setXCity((string) $billing->getCity())
                ->setXState((string) $billing->getRegion())
                ->setXZip((string) $billing->getPostcode())
                ->setXCountry((string) $billing->getCountry())
                ->setXPhone((string) $billing->getTelephone())
                ->setXFax((string) $billing->getFax())
                ->setXCustId((string) $billing->getCustomerId())
                ->setXCustomerIp((string) $order->getRemoteIp())
                ->setXCustomerTaxId((string) $billing->getTaxId())
                ->setXEmail((string) $order->getCustomerEmail())
                ->setXEmailCustomer((string) $paymentMethod->getConfigData('email_customer'))
                ->setXMerchantEmail((string) $paymentMethod->getConfigData('merchant_email'));
        }

        $shipping = $order->getShippingAddress();
        if (!empty($shipping)) {
            $this->setXShipToFirstName((string) $shipping->getFirstname())
                ->setXShipToLastName((string) $shipping->getLastname())
                ->setXShipToCompany((string) $shipping->getCompany())
                ->setXShipToAddress((string) $shipping->getStreet(1))
                ->setXShipToCity((string) $shipping->getCity())
                ->setXShipToState((string) $shipping->getRegion())
                ->setXShipToZip((string) $shipping->getPostcode())
                ->setXShipToCountry((string) $shipping->getCountry());
        }

        $this->setXPoNum((string) $payment->getPoNumber());

        return $this;
    }

    /**
     * Set sign hash into the request object.
     * All needed fields should be placed in the object fist.
     *
     * @return $this
     */
    public function signRequestData()
    {
        $fpTimestamp = (string) time();
        $signatureKey = $this->_getSignatureKey();
        if (!empty($signatureKey)) {
            $hash = $this->_generateSha2RequestSign(
                $this->getXLogin(),
                $this->_getSignatureKey(),
                $this->getXAmount(),
                $this->getXCurrencyCode(),
                $this->getXFpSequence(),
                $fpTimestamp,
            );
        } else {
            $hash = $this->generateRequestSign(
                $this->getXLogin(),
                $this->_getTransactionKey(),
                $this->getXAmount(),
                $this->getXCurrencyCode(),
                $this->getXFpSequence(),
                $fpTimestamp,
            );
        }
        $this->setXFpTimestamp($fpTimestamp);
        $this->setXFpHash($hash);
        return $this;
    }

    /**
     * Generates the SHA2 fingerprint for request.
     *
     * @param string $merchantApiLoginId
     * @param string $merchantSignatureKey
     * @param string $amount
     * @param string $currencyCode
     * @param string $fpSequence An invoice number or random number.
     * @param string $fpTimestamp
     * @return string The fingerprint.
     */
    protected function _generateSha2RequestSign(
        $merchantApiLoginId,
        $merchantSignatureKey,
        $amount,
        $currencyCode,
        $fpSequence,
        $fpTimestamp
    ) {
        $message = $merchantApiLoginId . '^' . $fpSequence . '^' . $fpTimestamp . '^' . $amount . '^' . $currencyCode;

        return strtoupper(hash_hmac('sha512', $message, pack('H*', $merchantSignatureKey)));
    }

    /**
     * Return merchant hexadecimal signature key.
     *
     * Needed to generate SHA2 sign.
     *
     * @return string
     */
    protected function _getSignatureKey()
    {
        return $this->_signatureKey;
    }

    /**
     * Set merchant hexadecimal signature key.
     *
     * Needed to generate SHA2 sign.
     *
     * @param string $signatureKey
     */
    protected function _setSignatureKey($signatureKey)
    {
        $this->_signatureKey = $signatureKey;
    }
}
