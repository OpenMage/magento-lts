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
 * @package     Mage_Flo2Cash
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Flo2Cash Payment Gateway Model
 *
 * @category    Mage
 * @package     Mage_Flo2Cash
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Flo2Cash_Model_Web extends Mage_Payment_Model_Method_Cc
{
    const WSDL_URL_DEMO = 'http://demo.flo2cash.co.nz/ws/paynzws.asmx?wsdl';
    const WSDL_URL_LIVE = 'https://secure.flo2cash.co.nz/ws/paynzws.asmx?wsdl';

    protected $_code  = 'flo2cash_web';

    protected $_allowCurrencyCode = array('NZD');

    /**
     * Availability options
     */
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc               = false;

    protected $_ccTypesConvert = array(
        'VI'    => 'VISA',
        'MC'    => 'MC',
        'DICL'  => 'DINERS',
        'AE'    => 'AMEX'
    );

    const TRANSACTION_TYPE_PURCHASE = 1;
    const TRANSACTION_TYPE_REFUND = 2;
    const TRANSACTION_TYPE_AUTHORISE = 3;
    const TRANSACTION_TYPE_CAPTURE = 4;

    const TRANSACTION_STATUS_ACCEPTED = 1;
    const TRANSACTION_STATUS_DECLINED = 2;

    protected $_formBlockType = 'flo2cash/form';
    protected $_infoBlockType = 'flo2cash/info';

    /**
     * Get Account Id for selected payment action
     *
     * @return string
     */
    public function getAccountId()
    {
        if ($this->getConfigData('payment_action') == self::ACTION_AUTHORIZE_CAPTURE) {
            $acountId = $this->getConfigData('payzn_purchase_account_id');
        } else {
            $acountId = $this->getConfigData('payzn_account_id');
        }
        return $acountId;
    }

    /**
     * validate the currency code is avaialable to use for Flo2Cash Basic or not
     *
     * @return bool
     */
    public function validate()
    {
        parent::validate();
        $paymentInfo = $this->getInfoInstance();
        if ($paymentInfo instanceof Mage_Sales_Model_Order_Payment) {
            $currency_code = $paymentInfo->getOrder()->getBaseCurrencyCode();
        } else {
            $currency_code = $paymentInfo->getQuote()->getBaseCurrencyCode();
        }
        if (!in_array($currency_code, $this->_allowCurrencyCode)) {
            Mage::throwException(Mage::helper('flo2cash')->__('Selected currency code (%s) is not compatible with Flo2Cash', $currency_code));
        }
        return $this;
    }

    public function authorize(Varien_Object $payment, $amount)
    {
        $txnDetails = $this->_prepareTxnDetails($payment, $amount);

        $response = $this->_sendRequest($txnDetails);

        if ($response['txn_status'] == self::TRANSACTION_STATUS_DECLINED) {
            Mage::throwException(Mage::helper('flo2cash')->__('Payment transaction has been declined.'));
        }

        $payment->setStatus(self::STATUS_APPROVED);
        $payment->setCcTransId($response['transaction_id']);
        $payment->setFlo2cashAccountId($response['paynz_account_id']);

        return $this;
    }

    public function capture(Varien_Object $payment, $amount)
    {
        $txnDetails = $this->_prepareTxnDetails($payment, $amount);

        $response = $this->_sendRequest($txnDetails);

        if ($response['txn_status'] == self::TRANSACTION_STATUS_DECLINED) {
            Mage::throwException(Mage::helper('flo2cash')->__('Payment transaction has been declined.'));
        }

        $payment->setStatus(self::STATUS_APPROVED);
        $payment->setLastTransId($response['transaction_id']);
        $payment->setFlo2cashAccountId($response['paynz_account_id']);

        return $this;
    }

    public function void(Varien_Object $payment)
    {
        $payment->setStatus(self::STATUS_SUCCESS );
        return $this;
    }

    public function refund(Varien_Object $payment, $amount)
    {
        if ($payment->getRefundTransactionId() && $amount>0) {

            $transId = $payment->getCcTransId();
            //if transaction type was purchase (authorize & capture)
            if (is_null($transId)) {
                $transId = $payment->getLastTransId();
            }

            $txnDetails = array(
                'txn_type' => self::TRANSACTION_TYPE_REFUND,
                'refund_transaction_id' => $transId,
                'paynz_account_id' => $payment->getFlo2cashAccountId(),
                'amount' => sprintf('%.2f', $amount)
            );
        } else {
            Mage::throwException(Mage::helper('flo2cash')->__('Error in refunding the payment.'));
        }

        $response = $this->_sendRequest($txnDetails);

        if ($response['txn_status'] == self::TRANSACTION_STATUS_DECLINED) {
            Mage::throwException(Mage::helper('flo2cash')->__('Payment transaction has been declined.'));
        }

        $payment->setLastTransId($response['transaction_id']);

        return $this;
    }

    /**
     * Sending SOAP request to gateway
     *
     * @param array $txnDetails
     * @return void
     */
    protected function _sendRequest($txnDetails)
    {
        if ($this->getConfigData('demo_mode')) {
            $url = self::WSDL_URL_DEMO;
        } else {
            $url = self::WSDL_URL_LIVE;
        }

        $client = new SoapClient($url);

        $parameters = array(
            'username' => $this->getConfigData('username'),
            'password' => $this->getConfigData('password'),
            'txn_details' => $txnDetails
        );

        try {
            $response = $client->ProcessPayment($parameters);

            if ($this->getConfigData('debug_flag')) {
                $debug = Mage::getModel('flo2cash/api_debug')
                    ->setRequestBody(print_r($parameters, true))
                    ->setResponseBody(print_r($response, true))
                    ->save();
            }
            return (array)$response->ProcessPaymentResult;
        } catch (SoapFault $e) {
            if ($this->getConfigData('debug_flag')) {
                $debug = Mage::getModel('flo2cash/api_debug')
                    ->setRequestBody(print_r($parameters, true))
                    ->setException($e->getMessage())
                    ->save();
            }

            if (strpos($e->getMessage(), ' ---> ') !== FALSE) {
                list($title, $error) = explode(' ---> ', $e->getMessage());
            } else {
                $error = $e->getMessage();
            }

            Mage::throwException(Mage::helper('flo2cash')->__('Gateway returned an error message: %s', $error));
        }
    }

    /**
     * Preapare basic paramters for transaction
     *
     * @param Varien_Object $payment
     * @param decimal $amount
     * @return array
     */
    protected function _prepareTxnDetails(Varien_Object $payment, $amount)
    {
        if ($payment->getCcTransId()) {
            $txnDetails = array(
                'txn_type' => self::TRANSACTION_TYPE_CAPTURE,
                'capture_transaction_id' => $payment->getCcTransId()
            );
        } else {
            $billingAddress = $payment->getOrder()->getBillingAddress();

            if ($payment->getOrder()->getCustomerEmail()) {
                $customerEmail = $payment->getOrder()->getCustomerEmail();
            } elseif ($billingAddress->getEmail()) {
                $customerEmail = $billingAddress->getEmail();
            } else {
                $customerEmail = '';
            }

            $txnDetails = array(
                'card_holder_name' => $payment->getCcOwner(),
                'card_number' => $payment->getCcNumber(),
                'card_type' => $this->_convertCcType($payment->getCcType()),
                'card_expiry' => sprintf('%02d', $payment->getCcExpMonth()).substr($payment->getCcExpYear(), 2, 2),
                'card_csc' => $payment->getCcCid(),
                'customer_email' => $customerEmail
            );

            if ($this->getConfigData('payment_action') == self::ACTION_AUTHORIZE) {
                $txnDetails['txn_type'] = self::TRANSACTION_TYPE_AUTHORISE;
            } else {
                $txnDetails['txn_type'] = self::TRANSACTION_TYPE_PURCHASE;
            }
        }

        $accountId = $payment->getFlo2cashAccountId();
        //if transaction type is authorize & capture or only authorize
        if (is_null($accountId)) {
            $accountId = $this->getAccountId();
        }

        $txnDetails = array_merge($txnDetails, array(
            //'txn_reference' => $payment->getOrder()->getIncrementId(),
            'merchant_reference' => $payment->getOrder()->getIncrementId(),
            'paynz_account_id' => $accountId,
            'amount' => sprintf('%.2f', $amount),
        ));

        return $txnDetails;
    }

    /**
     * Converst CC Types Code from Magento to Flo2Cash
     *
     * @param string $magentoCcType
     * @return string
     */
    protected function _convertCcType($magentoCcType = 'VI')
    {
        return $this->_ccTypesConvert[$magentoCcType];
    }
}
