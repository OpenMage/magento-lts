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
 * @package     Mage_Paygate
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Payflow Pro payment gateway model
 *
 * @category    Mage
 * @package     Mage_Paygate
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Paygate_Model_Payflow_Pro extends  Mage_Payment_Model_Method_Cc
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

    protected $_clientTimeout = 45;

    const RESPONSE_CODE_APPROVED = 0;
    const RESPONSE_CODE_FRAUDSERVICE_FILTER = 126;
    const RESPONSE_CODE_DECLINED = 12;
    const RESPONSE_CODE_CAPTURE_ERROR = 111;

    protected $_code = 'verisign';

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

    public function authorize(Varien_Object $payment, $amount)
    {
        $error = false;
        if($amount>0){
            $payment->setTrxtype(self::TRXTYPE_AUTH_ONLY);
            $payment->setAmount($amount);

            $request = $this->_buildRequest($payment);
            $result = $this->_postRequest($request);
            $payment->setCcTransId($result->getPnref());

            switch ($result->getResultCode()){
                case self::RESPONSE_CODE_APPROVED:
                     $payment->setStatus(self::STATUS_APPROVED);
                     break;

                case self::RESPONSE_CODE_FRAUDSERVICE_FILTER:
                    $payment->setFraudFlag(true);
                    break;

                default:
                    if ($result->getRespmsg()) {
                        $error = $result->getRespmsg();
                    }
                    else {
                        $error = Mage::helper('paygate')->__('Error in authorizing the payment');
                    }
                break;
            }
        }else{
            $error = Mage::helper('paygate')->__('Invalid amount for authorization');
        }

        if ($error !== false) {
            Mage::throwException($error);
        }
        return $this;
    }

    /**
     * Check capture availability
     * To avoid capture already voided transactions, allow only one capture thus the method
     * cannot make capture partially
     *
     * @return bool
     */
    public function canCapture()
    {
        if ($this->getInfoInstance()->getOrder()->getBaseSubtotalInvoiced() > 0) {
            return false;
        }
        return true;
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
                 break;

            case self::RESPONSE_CODE_FRAUDSERVICE_FILTER:
                $payment->setFraudFlag(true);
                break;

            default:
                if ($result->getRespmsg()) {
                    $error = $result->getRespmsg();
                }
                else {
                    $error = Mage::helper('paygate')->__('Error in capturing the payment');
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
                    $payment->setStatusDescription(Mage::helper('paygate')->__('Voided transaction'));
                }elseif(in_array($result->getTransstate(),$this->_validVoidTransState)){
                     $payment->setStatus(self::STATUS_VOID);
                }
            }else{
                $payment->setStatus(self::STATUS_ERROR);
                $payment->setStatusDescription($result->getRespmsg()?
                    $result->getRespmsg():
                    Mage::helper('paygate')->__('Error in retrieving the transaction'));
            }
        }else{
            $payment->setStatus(self::STATUS_ERROR);
            $payment->setStatusDescription(Mage::helper('paygate')->__('Invalid transaction id'));
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
         if($payment->getVoidTransactionId()){
            $payment->setTrxtype(self::TRXTYPE_DELAYED_VOID);
            $payment->setTransactionId($payment->getVoidTransactionId());
            $request=$this->_buildBasicRequest($payment);
            $result = $this->_postRequest($request);

            if($result->getResultCode()==self::RESPONSE_CODE_APPROVED){
                 $payment->setStatus(self::STATUS_SUCCESS);
                 $payment->setCcTransId($result->getPnref());
            }else{
                $payment->setStatus(self::STATUS_ERROR);
                $error = $result->getRespmsg();
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
                    Mage::helper('paygate')->__('Error in refunding the payment.'));

            }
        }else{
            $error = Mage::helper('paygate')->__('Error in refunding the payment');
        }

        if ($error !== false) {
            Mage::throwException($error);
        }
        return $this;

    }

    protected function _postRequest(Varien_Object $request)
    {
        if ($this->getConfigData('debug')) {
            $requestDebug = clone $request;


            foreach ($this->_debugReplacePrivateDataKeys as $key) {
                if ($requestDebug->hasData($key)) {
                    $requestDebug->setData($key, '***');
                }
            }

            foreach( $requestDebug->getData() as $key => $value ) {
                $value = (string)$value;
                $requestData[] = strtoupper($key) . '[' . strlen($value) . ']=' . $value;
            }

            $requestData = join('&', $requestData);

            $debug = Mage::getModel('paygate/authorizenet_debug')
                ->setRequestBody($requestData)
                ->setRequestSerialized(serialize($requestDebug->getData()))
                ->setRequestDump(print_r($requestDebug->getData(),1))
                ->save();
        }

        $client = new Varien_Http_Client();

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
            ->setHeaders('X-VPS-CLIENT-TIMEOUT: ' . $this->_clientTimeout)
        ;

        /*
        * we are sending request to payflow pro without url encoding
        * so we set up _urlEncodeBody flag to false
        */
        $response = $client->setUrlEncodeBody(false)
                           ->request();

        $result = Mage::getModel('paygate/payflow_pro_result');

        $response = strstr($response->getBody(), 'RESULT');
        $valArray = explode('&', $response);

        foreach($valArray as $val) {
                $valArray2 = explode('=', $val);
                $result->setData(strtolower($valArray2[0]), $valArray2[1]);
        }

        $result->setResultCode($result->getResult())
                ->setRespmsg($result->getRespmsg());

        if (!empty($debug)) {
            $debug
                ->setResponseBody($response)
                ->setResultSerialized(serialize($result->getData()))
                ->setResultDump(print_r($result->getData(),1))
                ->save();
        }

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

        $request = Mage::getModel('paygate/payflow_pro_request')
            ->setUser($this->getConfigData('user'))
            ->setVendor($this->getConfigData('vendor'))
            ->setPartner($this->getConfigData('partner'))
            ->setPwd($this->getConfigData('pwd'))
            ->setTender($payment->getTender())
            ->setTrxtype($payment->getTrxtype())
            ->setVerbosity($this->getConfigData('verbosity'))
            ->setRequestId($this->_generateRequestId())
            ;

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

        $request = Mage::getModel('paygate/payflow_pro_request')
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
}
