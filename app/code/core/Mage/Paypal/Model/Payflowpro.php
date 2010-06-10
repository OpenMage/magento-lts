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
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Payflow Pro payment gateway model
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Paypal_Model_Payflowpro extends  Mage_Payment_Model_Method_Cc
{
    const TRXTYPE_AUTH_ONLY         = 'A';
    const TRXTYPE_SALE              = 'S';
    const TRXTYPE_CREDIT            = 'C';
    const TRXTYPE_DELAYED_CAPTURE   = 'D';
    const TRXTYPE_DELAYED_VOID      = 'V';
    const TRXTYPE_DELAYED_VOICE     = 'F';
    const TRXTYPE_DELAYED_INQUIRY   = 'I';

    const TENDER_AUTOMATED          = 'A';
    const TENDER_CC                 = 'C';
    const TENDER_PINLESS_DEBIT      = 'D';
    const TENDER_ECHEK              = 'E';
    const TENDER_TELECHECK          = 'K';
    const TENDER_PAYPAL             = 'P';

    const RESPONSE_DELIM_CHAR = ',';

    const TRANSACTION_URL = 'https://payflowpro.paypal.com/transaction';
    const TRANSACTION_URL_TEST_MODE = 'https://pilot-payflowpro.paypal.com/transaction';

    protected $_clientTimeout = 45;

    const RESPONSE_CODE_APPROVED = 0;
    const RESPONSE_CODE_FRAUDSERVICE_FILTER = 126;
    const RESPONSE_CODE_DECLINED = 12;
    const RESPONSE_CODE_CAPTURE_ERROR = 111;

    protected $_code = Mage_Paypal_Model_Config::METHOD_PAYFLOWPRO;

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
    protected $_isProxy = false;

    /**
     * Fields that should be replaced in debug with '***'
     *
     * @var array
     */
    protected $_debugReplacePrivateDataKeys = array('user', 'pwd', 'acct',
                                                    'expdate', 'cvv2');

    /**
     * 3 = Authorisation approved
     * 6 = Settlement pending (transaction is scheduled to be settled)
     * 9 = Authorisation captured
     */
    protected $_validVoidTransState = array(3,6,9);

    /**
     * Centinel cardinal fields map
     *
     * @var string
     */
    protected $_centinelFieldMap = array(
        'centinel_mpivendor' => 'MPIVENDOR3DS',
        'centinel_authstatus'      => 'AUTHSTATUS3DS',
        'centinel_cavv'          => 'CAVV',
        'centinel_eci'      => 'ECI',
        'centinel_xid'           => 'XID',
    );

    /**
     * Check whether payment method can be used
     * @param Mage_Sales_Model_Quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        $storeId = Mage::app()->getStore($this->getStore())->getId();
        $config = Mage::getModel('paypal/config')->setStoreId($storeId);
        if ($config->isMethodAvailable($this->getCode()) && parent::isAvailable($quote)) {
            return true;
        }
        return false;
    }

    /**
     * Custom getter for payment configuration
     *
     * @param string $field
     * @param int $storeId
     * @return mixed
     */
    public function getConfigData($field, $storeId = null)
    {
        $value = null;
        switch ($field)
        {
            case 'url':
                $value = $this->getTransactionUrl();
                break;
            default:
                $value = parent::getConfigData($field, $storeId);
        }
        return $value;
    }

    /**
     * Getter for URL to perform Payflow requests, based on test mode by default
     *
     * @param bool $testMode Ability to specify test mode using
     * @return string
     */
    public function getTransactionUrl($testMode = null)
    {
        $testMode = is_null($testMode) ? $this->getConfigData('sandbox_flag') : (bool)$testMode;
        if ($testMode) {
            return self::TRANSACTION_URL_TEST_MODE;
        }
        return self::TRANSACTION_URL;
    }

    /**
     * Payment action getter compatible with payment model
     *
     * @see Mage_Sales_Model_Payment::place()
     * @return string
     */
    public function getConfigPaymentAction()
    {
        switch ($this->getConfigData('payment_action')) {
            case Mage_Paypal_Model_Config::PAYMENT_ACTION_AUTH:
                return Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE;
            case Mage_Paypal_Model_Config::PAYMENT_ACTION_SALE:
                return Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE;
        }
    }

    public function authorize(Varien_Object $payment, $amount)
    {
        $error = false;
        if($amount>0){
            $payment->setTrxtype(self::TRXTYPE_AUTH_ONLY);
            $payment->setAmount($amount);

            $request = $this->_buildRequest($payment);
            $result = $this->_postRequest($request);
            $payment->setCcTransId($result->getPnref())
                ->setTransactionId($result->getPnref())
                ->setIsTransactionClosed(0);

            switch ($result->getResultCode()){
                case self::RESPONSE_CODE_APPROVED:
                     $payment->setStatus(self::STATUS_APPROVED);
                     break;

                case self::RESPONSE_CODE_FRAUDSERVICE_FILTER:
                    $payment->setIsTransactionPending(true);
                    break;

                default:
                    if ($result->getRespmsg()) {
                        $error = $result->getRespmsg();
                    }
                    else {
                        $error = Mage::helper('paypal')->__('Error in authorizing the payment.');
                    }
                break;
            }
        }else{
            $error = Mage::helper('paypal')->__('Invalid amount for authorization.');
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
            /*
            for payment already captured, we need to send the transaction type as sale
            */
            if ($payment->getOrder()->getTotalPaid()>0) {
                $payment->setTrxtype(self::TRXTYPE_SALE);
            } else {
                $payment->setTrxtype(self::TRXTYPE_DELAYED_CAPTURE);
            }
            $payment->setTransactionId($payment->getCcTransId());
            $request = $this->_buildBasicRequest($payment);
        } else {
            $payment->setTrxtype(self::TRXTYPE_SALE);
            $payment->setAmount($amount);
            $request = $this->_buildRequest($payment);
        }

        $result = $this->_postRequest($request);
        switch ($result->getResultCode()){
            case self::RESPONSE_CODE_APPROVED:
                 $payment->setStatus(self::STATUS_APPROVED);
                 //$payment->setCcTransId($result->getPnref());
                 $payment->setLastTransId($result->getPnref());
                 $payment->setTransactionId($result->getPnref());
                 break;

            case self::RESPONSE_CODE_FRAUDSERVICE_FILTER:
                $payment->setIsTransactionPending(true);
                break;

            default:
                if ($result->getRespmsg()) {
                    $error = $result->getRespmsg();
                } else {
                    $error = Mage::helper('paypal')->__('Error in capturing the payment.');
                }
            break;
        }
        if ($error !== false) {
            Mage::throwException($error);
        }
        return $this;
    }

    /**
      * canVoid
      *
      * @access public
      * @param string $payment Varien_Object object
      * @return Mage_Payment_Model_Abstract
      * @desc checking the transaction id is valid or not and transction id was not settled
      */
    public function canVoid(Varien_Object $payment)
    {
        if($payment->getCcTransId()){
            $payment->setTrxtype(self::TRXTYPE_DELAYED_INQUIRY);
            $payment->setTransactionId($payment->getCcTransId());
            $request=$this->_buildBasicRequest($payment);
            $result = $this->_postRequest($request);

            if($result->getResultCode()==self::RESPONSE_CODE_APPROVED){
                if($result->getTransstate()>1000){
                    $payment->setStatus(self::STATUS_ERROR);
                    $payment->setStatusDescription(Mage::helper('paypal')->__('Voided transaction.'));
                }elseif(in_array($result->getTransstate(),$this->_validVoidTransState)){
                     $payment->setStatus(self::STATUS_VOID);
                }
            }else{
                $payment->setStatus(self::STATUS_ERROR);
                $payment->setStatusDescription($result->getRespmsg()?
                    $result->getRespmsg():
                    Mage::helper('paypal')->__('Error in retrieving the transaction.'));
            }
        }else{
            $payment->setStatus(self::STATUS_ERROR);
            $payment->setStatusDescription(Mage::helper('paypal')->__('Invalid transaction ID.'));
        }

        return $this;
    }

     /**
      * void
      *
      * @access public
      * @param string $payment Varien_Object object
      * @return Mage_Payment_Model_Abstract
      */
    public function void(Varien_Object $payment)
    {
         $error = false;
         if($payment->getParentTransactionId()){
            $payment->setTrxtype(self::TRXTYPE_DELAYED_VOID);
            $payment->setTransactionId($payment->getParentTransactionId());
            $request=$this->_buildBasicRequest($payment);
            $result = $this->_postRequest($request);

            if($result->getResultCode()==self::RESPONSE_CODE_APPROVED){
                 $payment->setStatus(self::STATUS_SUCCESS);
                 $payment->setCcTransId($result->getPnref());
                 $payment->setTransactionId($result->getPnref());
            }else{
                $payment->setStatus(self::STATUS_ERROR);
                $error = $result->getRespmsg();
            }
         }else{
            $payment->setStatus(self::STATUS_ERROR);
            $error = Mage::helper('paypal')->__('Invalid transaction ID.');
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
        if(($payment->getRefundTransactionId() && $amount>0)){
            $payment->setTransactionId($payment->getRefundTransactionId());
            $payment->setTrxtype(self::TRXTYPE_CREDIT);

            $request=$this->_buildBasicRequest($payment);

            $request->setAmt(round($amount,2));
            $result = $this->_postRequest($request);

            if($result->getResultCode()==self::RESPONSE_CODE_APPROVED){
                 $payment->setStatus(self::STATUS_SUCCESS);
                 $payment->setCcTransId($result->getPnref());
            }else{
                $error = ($result->getRespmsg()?
                    $result->getRespmsg():
                    Mage::helper('paypal')->__('Error in refunding the payment.'));

            }
        }else{
            $error = Mage::helper('paypal')->__('Error in refunding the payment.');
        }

        if ($error !== false) {
            Mage::throwException($error);
        }
        return $this;

    }

    protected function _postRequest(Varien_Object $request)
    {
        $debugData = array('request' => $request->getData());

        $client = new Varien_Http_Client();
        $result = $this->_getResultObject();

        $_config = array(
                        'maxredirects'=>5,
                        'timeout'=>30,
                    );

        $_isProxy = $this->getConfigData('use_proxy', false);
        if($_isProxy){
            $_config['proxy'] = $this->getConfigData('proxy_host') . ':' . $this->getConfigData('proxy_port');//http://proxy.shr.secureserver.net:3128',
            $_config['httpproxytunnel'] = true;
            $_config['proxytype'] = CURLPROXY_HTTP;
        }

        $uri = $this->getConfigData('url');
        $client->setUri($uri)
            ->setConfig($_config)
            ->setMethod(Zend_Http_Client::POST)
            ->setParameterPost($request->getData())
            ->setHeaders('X-VPS-VIT-CLIENT-CERTIFICATION-ID: 33baf5893fc2123d8b191d2d011b7fdc')
            ->setHeaders('X-VPS-Request-ID: ' . $request->getRequestId())
            ->setHeaders('X-VPS-CLIENT-TIMEOUT: ' . $this->_clientTimeout);

        try {
           /**
            * we are sending request to payflow pro without url encoding
            * so we set up _urlEncodeBody flag to false
            */
            $response = $client->setUrlEncodeBody(false)->request();
        }
        catch (Exception $e) {
            $result->setResponseCode(-1)
                ->setResponseReasonCode($e->getCode())
                ->setResponseReasonText($e->getMessage());

            $debugData['result'] = $result->getData();
            $this->_debug($debugData);
            throw $e;
        }



        $response = strstr($response->getBody(), 'RESULT');
        $valArray = explode('&', $response);

        foreach($valArray as $val) {
                $valArray2 = explode('=', $val);
                $result->setData(strtolower($valArray2[0]), $valArray2[1]);
        }

        $result->setResultCode($result->getResult())
                ->setRespmsg($result->getRespmsg());

        $debugData['result'] = $result->getData();
        $this->_debug($debugData);

        return $result;
    }

    protected function _buildRequest(Varien_Object $payment)
    {
        if( !$payment->getTrxtype() ) {
            $payment->setTrxtype(self::TRXTYPE_AUTH_ONLY);
        }

        if( !$payment->getTender() ) {
            $payment->setTender(self::TENDER_CC);
        }

        $request = $this->_getRequestObject()
            ->setUser($this->getConfigData('user'))
            ->setVendor($this->getConfigData('vendor'))
            ->setPartner($this->getConfigData('partner'))
            ->setPwd($this->getConfigData('pwd'))
            ->setTender($payment->getTender())
            ->setTrxtype($payment->getTrxtype())
            ->setVerbosity($this->getConfigData('verbosity'))
            ->setRequestId($this->_generateRequestId());

        if ($this->getIsCentinelValidationEnabled()){
            $params = array();
            $params = $this->getCentinelValidator()->exportCmpiData($params);
            $request = Varien_Object_Mapper::accumulateByMap($params, $request, $this->_centinelFieldMap);
        }

        if($payment->getAmount()){
            $request->setAmt(round($payment->getAmount(),2));
            $request->setCurrency($payment->getOrder()->getBaseCurrencyCode());
        }

        switch ($request->getTender()) {
            case self::TENDER_CC:
                    if($payment->getCcNumber()){
                        $request
                            //->setComment1($payment->getCcOwner())
                            ->setAcct($payment->getCcNumber())
                            ->setExpdate(sprintf('%02d',$payment->getCcExpMonth()).substr($payment->getCcExpYear(),-2,2))
                            ->setCvv2($payment->getCcCid());
                    }
                break;
        }

        $order = $payment->getOrder();
        if(!empty($order)){
            $billing = $order->getBillingAddress();
            if (!empty($billing)) {
                $request->setFirstname($billing->getFirstname())
                    ->setLastname($billing->getLastname())
                    ->setStreet($billing->getStreet(1))
                    ->setCity($billing->getCity())
                    ->setState($billing->getRegion())
                    ->setZip($billing->getPostcode())
                    ->setCountry($billing->getCountry())
                    ->setEmail($payment->getOrder()->getCustomerEmail());
            }
            $shipping = $order->getShippingAddress();
            if (!empty($shipping)) {
                $request->setShiptofirstname($shipping->getFirstname())
                    ->setShiptolastname($shipping->getLastname())
                    ->setShiptostreet($shipping->getStreet(1))
                    ->setShiptocity($shipping->getCity())
                    ->setShiptostate($shipping->getRegion())
                    ->setShiptozip($shipping->getPostcode())
                    ->setShiptocountry($shipping->getCountry());
            }
        }
        return $request;
    }

    protected function _generateRequestId()
    {
        return Mage::helper('core')->uniqHash();
    }

     /**
      * Prepare base request
      *
      * @access public
      * @return object which was set with all basic required information
      */
    protected function _buildBasicRequest(Varien_Object $payment)
    {
        if( !$payment->getTender() ) {
            $payment->setTender(self::TENDER_CC);
        }

        $request = $this->_getRequestObject()
            ->setUser($this->getConfigData('user'))
            ->setVendor($this->getConfigData('vendor'))
            ->setPartner($this->getConfigData('partner'))
            ->setPwd($this->getConfigData('pwd'))
            ->setTender($payment->getTender())
            ->setTrxtype($payment->getTrxtype())
            ->setVerbosity($this->getConfigData('verbosity'))
            ->setRequestId($this->_generateRequestId())
            ->setOrigid($payment->getTransactionId());
        return $request;
    }

     /**
      * Return generic object instance for API requests
      *
      * @return Varien_Object
      */
    protected function _getRequestObject()
    {
        $request = new Varien_Object();
        return $request;
    }

     /**
      * Return wrapper object instance for API response results
      *
      * @return Varien_Object
      */
    protected function _getResultObject()
    {
        $result = new Varien_Object();
        return $result;
    }
}
