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
 * @category   Mage
 * @package    Mage_Paygate
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Paygate_Model_Authorizenet extends Mage_Payment_Model_Method_Cc
{
    const CGI_URL = 'https://secure.authorize.net/gateway/transact.dll';

    const REQUEST_METHOD_CC     = 'CC';
    const REQUEST_METHOD_ECHECK = 'ECHECK';

    const REQUEST_TYPE_AUTH_CAPTURE = 'AUTH_CAPTURE';
    const REQUEST_TYPE_AUTH_ONLY    = 'AUTH_ONLY';
    const REQUEST_TYPE_CAPTURE_ONLY = 'CAPTURE_ONLY';
    const REQUEST_TYPE_CREDIT       = 'CREDIT';
    const REQUEST_TYPE_VOID         = 'VOID';
    const REQUEST_TYPE_PRIOR_AUTH_CAPTURE = 'PRIOR_AUTH_CAPTURE';

    const ECHECK_ACCT_TYPE_CHECKING = 'CHECKING';
    const ECHECK_ACCT_TYPE_BUSINESS = 'BUSINESSCHECKING';
    const ECHECK_ACCT_TYPE_SAVINGS  = 'SAVINGS';

    const ECHECK_TRANS_TYPE_CCD = 'CCD';
    const ECHECK_TRANS_TYPE_PPD = 'PPD';
    const ECHECK_TRANS_TYPE_TEL = 'TEL';
    const ECHECK_TRANS_TYPE_WEB = 'WEB';

    const RESPONSE_DELIM_CHAR = ',';

    const RESPONSE_CODE_APPROVED = 1;
    const RESPONSE_CODE_DECLINED = 2;
    const RESPONSE_CODE_ERROR    = 3;
    const RESPONSE_CODE_HELD     = 4;

    protected $_code  = 'authorizenet';

    /**
     * Availability options
     */
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc = false;

    /**
     * Send authorize request to gateway
     *
     * @param   Varien_Object $payment
     * @param   decimal $amount
     * @return  Mage_Paygate_Model_Authorizenet
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        $error = false;

        if($amount>0){
            $payment->setAnetTransType(self::REQUEST_TYPE_AUTH_ONLY);
            $payment->setAmount($amount);

            $request= $this->_buildRequest($payment);
            $result = $this->_postRequest($request);

            $payment->setCcApproval($result->getApprovalCode())
                ->setLastTransId($result->getTransactionId())
                ->setCcTransId($result->getTransactionId())
                ->setCcAvsStatus($result->getAvsResultCode())
                ->setCcCidStatus($result->getCardCodeResponseCode());

            switch ($result->getResponseCode()) {
                case self::RESPONSE_CODE_APPROVED:
                    $payment->setStatus(self::STATUS_APPROVED);
                    break;
                case self::RESPONSE_CODE_DECLINED:
                    $error = Mage::helper('paygate')->__('Payment authorization transaction has been declined.');
                    break;
                default:
                    $error = Mage::helper('paygate')->__('Payment authorization error.');
                    break;
            }
        }else{
            $error = Mage::helper('paygate')->__('Invalid amount for authorization.');
        }

        if ($error !== false) {
            Mage::throwException($error);
        }
        return $this;
    }


    public function capture(Varien_Object $payment, $amount)
    {
        $error = false;

        if ($payment->getCcTransId()) {
            $payment->setAnetTransType(self::REQUEST_TYPE_PRIOR_AUTH_CAPTURE);
        } else {
            $payment->setAnetTransType(self::REQUEST_TYPE_AUTH_CAPTURE);
        }

        $payment->setAmount($amount);

        $request= $this->_buildRequest($payment);
        $result = $this->_postRequest($request);

        if ($result->getResponseCode() == self::RESPONSE_CODE_APPROVED) {
            $payment->setStatus(self::STATUS_APPROVED);
            //$payment->setCcTransId($result->getTransactionId());
            $payment->setLastTransId($result->getTransactionId());
        }
        else {
            if ($result->getResponseReasonText()) {
                $error = $result->getResponseReasonText();
            }
            else {
                $error = Mage::helper('paygate')->__('Error in capturing the payment');
            }
        }

        if ($error !== false) {
            Mage::throwException($error);
        }

        return $this;
    }


    /**
     * void
     *
 * @author      Magento Core Team <core@magentocommerce.com>
     * @access public
     * @param string $payment Varien_Object object
     * @return Mage_Payment_Model_Abstract
     */
    public function void(Varien_Object $payment)
    {
        $error = false;
        if($payment->getVoidTransactionId()){
            $payment->setAnetTransType(self::REQUEST_TYPE_VOID);
            $request = $this->_buildRequest($payment);
                        $request->setXTransId($payment->getVoidTransactionId());
            $result = $this->_postRequest($request);
            if($result->getResponseCode()==self::RESPONSE_CODE_APPROVED){
                 $payment->setStatus(self::STATUS_SUCCESS );
            }
            else{
                $payment->setStatus(self::STATUS_ERROR);
                $error = $result->getResponseReasonText();
            }
        }else{
            $payment->setStatus(self::STATUS_ERROR);
            $error = Mage::helper('paygate')->__('Invalid transaction id');
        }
        if ($error !== false) {
            Mage::throwException($error);
        }
        return $this;
    }

    /**
     * refund the amount with transaction id
     *
     * @access public
     * @param string $payment Varien_Object object
     * @return Mage_Payment_Model_Abstract
     */
    public function refund(Varien_Object $payment, $amount)
    {
        $error = false;
        if ($payment->getRefundTransactionId() && $amount>0) {
            $payment->setAnetTransType(self::REQUEST_TYPE_CREDIT);
            $request = $this->_buildRequest($payment);
            $request->setXTransId($payment->getRefundTransactionId());
            $result = $this->_postRequest($request);

            if ($result->getResponseCode()==self::RESPONSE_CODE_APPROVED) {
                $payment->setStatus(self::STATUS_SUCCESS);
            } else {
                $error = $result->getResponseReasonText();
            }

        } else {
            $error = Mage::helper('paygate')->__('Error in refunding the payment');
        }

        if ($error !== false) {
            Mage::throwException($error);
        }
        return $this;
    }

    /**
     * Prepare request to gateway
     *
     * @link http://www.authorize.net/support/AIM_guide.pdf
     * @param Mage_Sales_Model_Document $order
     * @return unknown
     */
    protected function _buildRequest(Varien_Object $payment)
    {
        $order = $payment->getOrder();

        $this->setStore($order->getStoreId());

        if (!$payment->getAnetTransMethod()) {
            $payment->setAnetTransMethod(self::REQUEST_METHOD_CC);
        }

        $request = Mage::getModel('paygate/authorizenet_request')
            ->setXVersion(3.1)
            ->setXDelimData('True')
            ->setXDelimChar(self::RESPONSE_DELIM_CHAR)
            ->setXRelayResponse('False');

        if ($order && $order->getIncrementId()) {
            $request->setXInvoiceNum($order->getIncrementId());
        }

        $request->setXTestRequest($this->getConfigData('test') ? 'TRUE' : 'FALSE');

        $request->setXLogin($this->getConfigData('login'))
            ->setXTranKey($this->getConfigData('trans_key'))
            ->setXType($payment->getAnetTransType())
            ->setXMethod($payment->getAnetTransMethod());

        if($payment->getAmount()){
            $request->setXAmount($payment->getAmount(),2);
            $request->setXCurrencyCode($order->getBaseCurrencyCode());
        }
        switch ($payment->getAnetTransType()) {
            case self::REQUEST_TYPE_CREDIT:
            case self::REQUEST_TYPE_VOID:
            case self::REQUEST_TYPE_PRIOR_AUTH_CAPTURE:
                $request->setXTransId($payment->getCcTransId());
                break;

            case self::REQUEST_TYPE_CAPTURE_ONLY:
                $request->setXAuthCode($payment->getCcAuthCode());
                break;
        }

        if (!empty($order)) {
            $billing = $order->getBillingAddress();
            if (!empty($billing)) {
                $request->setXFirstName($billing->getFirstname())
                    ->setXLastName($billing->getLastname())
                    ->setXCompany($billing->getCompany())
                    ->setXAddress($billing->getStreet(1))
                    ->setXCity($billing->getCity())
                    ->setXState($billing->getRegion())
                    ->setXZip($billing->getPostcode())
                    ->setXCountry($billing->getCountry())
                    ->setXPhone($billing->getTelephone())
                    ->setXFax($billing->getFax())
                    ->setXCustId($billing->getCustomerId())
                    ->setXCustomerIp($order->getRemoteIp())
                    ->setXCustomerTaxId($billing->getTaxId())
                    ->setXEmail($billing->getEmail())
                    ->setXEmailCustomer($this->getConfigData('email_customer'))
                    ->setXMerchantEmail($this->getConfigData('merchant_email'));
            }

            $shipping = $order->getShippingAddress();
            if (!empty($shipping)) {
                $request->setXShipToFirstName($shipping->getFirstname())
                    ->setXShipToLastName($shipping->getLastname())
                    ->setXShipToCompany($shipping->getCompany())
                    ->setXShipToAddress($shipping->getStreet(1))
                    ->setXShipToCity($shipping->getCity())
                    ->setXShipToState($shipping->getRegion())
                    ->setXShipToZip($shipping->getPostcode())
                    ->setXShipToCountry($shipping->getCountry());
            }

            $request->setXPoNum($payment->getPoNumber())
                ->setXTax($order->getTaxAmount())
                ->setXFreight($order->getShippingAmount());
        }

        switch ($payment->getAnetTransMethod()) {
            case self::REQUEST_METHOD_CC:
                if($payment->getCcNumber()){
                    $request->setXCardNum($payment->getCcNumber())
                        ->setXExpDate(sprintf('%02d-%04d', $payment->getCcExpMonth(), $payment->getCcExpYear()))
                        ->setXCardCode($payment->getCcCid());
                }
                break;

            case self::REQUEST_METHOD_ECHECK:
                $request->setXBankAbaCode($payment->getEcheckRoutingNumber())
                    ->setXBankName($payment->getEcheckBankName())
                    ->setXBankAcctNum($payment->getEcheckAccountNumber())
                    ->setXBankAcctType($payment->getEcheckAccountType())
                    ->setXBankAcctName($payment->getEcheckAccountName())
                    ->setXEcheckType($payment->getEcheckType());
                break;
        }

        return $request;
    }

    protected function _postRequest(Varien_Object $request)
    {
        $result = Mage::getModel('paygate/authorizenet_result');

        $client = new Varien_Http_Client();

        $uri = $this->getConfigData('cgi_url');
        $client->setUri($uri ? $uri : self::CGI_URL);
        $client->setConfig(array(
            'maxredirects'=>0,
            'timeout'=>30,
            //'ssltransport' => 'tcp',
        ));
        $client->setParameterPost($request->getData());
        $client->setMethod(Zend_Http_Client::POST);

        if ($this->getConfigData('debug')) {
            foreach( $request->getData() as $key => $value ) {
                $requestData[] = strtoupper($key) . '=' . $value;
            }

            $requestData = join('&', $requestData);

            $debug = Mage::getModel('paygate/authorizenet_debug')
                ->setRequestBody($requestData)
                ->setRequestSerialized(serialize($request->getData()))
                ->setRequestDump(print_r($request->getData(),1))
                ->save();
        }

        try {
            $response = $client->request();
        } catch (Exception $e) {
            $result->setResponseCode(-1)
                ->setResponseReasonCode($e->getCode())
                ->setResponseReasonText($e->getMessage());

            if (!empty($debug)) {
                $debug
                    ->setResultSerialized(serialize($result->getData()))
                    ->setResultDump(print_r($result->getData(),1))
                    ->save();
            }
            Mage::throwException(
                Mage::helper('paygate')->__('Gateway request error: %s', $e->getMessage())
            );
        }

        $responseBody = $response->getBody();

        $r = explode(self::RESPONSE_DELIM_CHAR, $responseBody);

        if ($r) {
            $result->setResponseCode((int)str_replace('"','',$r[0]))
                ->setResponseSubcode((int)str_replace('"','',$r[1]))
                ->setResponseReasonCode((int)str_replace('"','',$r[2]))
                ->setResponseReasonText($r[3])
                ->setApprovalCode($r[4])
                ->setAvsResultCode($r[5])
                ->setTransactionId($r[6])
                ->setInvoiceNumber($r[7])
                ->setDescription($r[8])
                ->setAmount($r[9])
                ->setMethod($r[10])
                ->setTransactionType($r[11])
                ->setCustomerId($r[12])
                ->setMd5Hash($r[37])
                ->setCardCodeResponseCode($r[39]);
        } else {
             Mage::throwException(
                Mage::helper('paygate')->__('Error in payment gateway')
            );
        }

        if (!empty($debug)) {
            $debug
                ->setResponseBody($responseBody)
                ->setResultSerialized(serialize($result->getData()))
                ->setResultDump(print_r($result->getData(),1))
                ->save();
        }

        return $result;
    }
}
