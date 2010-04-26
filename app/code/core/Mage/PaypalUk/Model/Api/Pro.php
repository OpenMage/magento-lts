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

/**
 * Payflow Pro payment gateway model
 *
 * @category    Mage
 * @package     Mage_Paygate
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_PaypalUk_Model_Api_Pro extends  Mage_PaypalUk_Model_Api_Abstract
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

    const ACIONT_SET_EXPRESS        = 'S';
    const ACIONT_GET_EXPRESS        = 'G';
    const ACIONT_DO_EXPRESS         = 'D';

    const RESPONSE_DELIM_CHAR = ',';

    protected $_clientTimeout = 45;

    const RESPONSE_CODE_APPROVED = 0;
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

    /**
     * Fields that should be replaced in debug with '***'
     *
     * @var array
     */
    protected $_debugReplacePrivateDataKeys = array('ACCT', 'EXPDATE', 'CVV2',
                                                    'CARDISSUE', 'CARDSTART',
                                                    'CREDITCARDTYPE', 'USER',
                                                    'PWD');

    /*
    * 3 = Authorisation approved
    * 6 = Settlement pending (transaction is scheduled to be settled)
    * 9 =  Authorisation captured
    */
    protected $_validVoidTransState = array(3,6,9);

    /**
     * Additional information fields map use for centinel params
     *
     * @var string
     */
    protected $_additionalInformationFieldMap = array(
        'centinel_mpivendor' => 'MPIVENDOR3DS',
        'centinel_authstatus' => 'AUTHSTATUS3DS',
        'centinel_cavv' => 'CAVV',
        'centinel_eci' => 'ECI',
        'centinel_xid' => 'XID',
        'centinel_vpas_result' => 'vpas', // must be 'VPAS' but self::postRequest return Varien_Object
        'centinel_eci_result' => 'ecisubmitted3ds' // must be 'ECISUBMITTED3DS' but self::postRequest return Varien_Object
    );

/*********************** DIRECT PAYMENT ***************************/
    public function callDoDirectPayment()
    {
        $p = $this->getPayment();
        $a = $this->getBillingAddress();
        if ($this->getShippingAddress()) {
            $s = $this->getShippingAddress();
        } else {
            $s = $a;
        }

        $proArr = array(
            'TENDER'        => self::TENDER_CC,
            'AMT'           => $this->getAmount(),
            'BUTTONSOURCE'   => $this->getButtonSourceDp(),
        );

        if($this->getTrxtype()==self::TRXTYPE_AUTH_ONLY || $this->getTrxtype()==self::TRXTYPE_SALE){
            $proArr = array_merge(array(
                'ACCT'      => $p->getCcNumber(),
                'EXPDATE'   => sprintf('%02d',$p->getCcExpMonth()).substr($p->getCcExpYear(),-2,2),
                'CVV2'      => $p->getCcCid(),
                'CURRENCY'      => $this->getCurrencyCode(),
                'EMAIL'     => $p->getOrder()->getCustomerEmail(),

                'FIRSTNAME' => $a->getFirstname(),
                'LASTNAME'  => $a->getLastname(),
                'STREET'    => $a->getStreet(1),
                'CITY'      => $a->getCity(),
                'STATE'     => $a->getRegionCode(),
                'ZIP'       => $a->getPostcode(),
                'COUNTRY'   => $a->getCountry(),

                'SHIPTOFIRSTNAME' => $s->getFirstname(),
                'SHIPTOLASTNAME' => $s->getLastname(),
                'SHIPTOSTREET' => $s->getStreet(1),
                'SHIPTOSTREET2' => $s->getStreet(2),
                'SHIPTOCITY' => $s->getCity(),
                'SHIPTOSTATE' => $s->getRegion(),
                'SHIPTOZIP' => $s->getPostcode(),
                'SHIPTOCOUNTRY' => $s->getCountry(),
            ), $proArr);

            if($p->getCcSsIssue()){
                $proArr = array_merge(array(
                'CARDISSUE'    => $p->getCcSsIssue(),
                ), $proArr);
            }
            if($p->getCcSsStartYear() || $p->getCcSsStartMonth()){
                $proArr = array_merge(array(
                'CARDSTART'    => sprintf('%02d',$p->getCcSsStartMonth()).substr($p->getCcSsStartYear(),-2,2),
                ), $proArr);
            }

        }else{
            $proArr = array_merge(array(
                'ORIGID'    => $this->getTransactionId(),
            ), $proArr);
        }

        $proArr = Varien_Object_Mapper::accumulateByMap($this->getAdditionalInformation(), $proArr, $this->_additionalInformationFieldMap);

        $result = $this->postRequest($proArr);

        if ($result && $result->getResultCode()==self::RESPONSE_CODE_APPROVED) {
             $this->setTransactionId($result->getPnref());
             $this->setAvsZip($result->getAvszip());
             $this->setAvsCode($result->getAvszip());
             $this->setCvv2Match($result->getCvv2match());

            $resultAdditionalInformation = $this->getAdditionalInformation();
            $resultAdditionalInformation = Varien_Object_Mapper::accumulateByMap($result, $resultAdditionalInformation, array_flip($this->_additionalInformationFieldMap));
            $this->setAdditionalInformation($resultAdditionalInformation);
        } else {
            $errorArr['code'] = $result->getResultCode();
            $errorArr['message'] = $result->getRespmsg();
            $this->setError($errorArr);
            return false;
         }

        return $this;
    }


/*********************** EXPRESS PAYMENT ***************************/
    public function getPaypalUrl()
    {
        if (!$this->hasPaypalUrl()) {
            $url=$this->getApiUrl();
            if (stripos($url,'pilot') || stripos($url,'test')) {
                $default = 'https://www.sandbox.paypal.com/';
            } else {
                $default = 'https://www.paypal.com/cgi-bin/';
            }
            $default .= 'webscr?cmd=_express-checkout&useraction='.$this->getUserAction().'&token=';

            $url = $this->getConfigData('paypal_url', $default);
        } else {
            $url = $this->getData('paypal_url');
        }
        return $url . $this->getToken();
    }

    public function callSetExpressCheckout()
    {
        $proArr = array(
            'TENDER'        => self::TENDER_PAYPAL,
            'AMT'           => $this->getAmount(),
            'ACTION'        => self::ACIONT_SET_EXPRESS,
            'CURRENCY'      => $this->getCurrencyCode(),
            "RETURNURL"     => $this->getReturnUrl(),
            'CANCELURL'     => $this->getCancelUrl(),
        );

        $this->setUserAction(self::USER_ACTION_CONTINUE);

         // for mark SetExpressCheckout API call
        if ($a = $this->getShippingAddress()) {
            $proArr = array_merge($proArr, array(
                'ADDROVERRIDE'      => 1,
                'SHIPTONAME'        => $a->getName(),
                'SHIPTOSTREET'      => $a->getStreet(1),
                'SHIPTOSTREET2'     => $a->getStreet(2),
                'SHIPTOCITY'        => $a->getCity(),
                'SHIPTOSTATE'       => $a->getRegionCode(),
                'SHIPTOCOUNTRY'     => $a->getCountry(),
                'SHIPTOZIP'         => $a->getPostcode(),
                'PHONENUM'          => $a->getTelephone(),
            ));
            $this->setUserAction(self::USER_ACTION_COMMIT);
        }

        $result = $this->postRequest($proArr);

        if ($result && $result->getResultCode()==self::RESPONSE_CODE_APPROVED) {
            $this->setToken($result->getToken());
            $this->setRedirectUrl($this->getPaypalUrl());
        } else if ($result) {
            $errorArr['code'] = $result->getResultCode();
            $errorArr['message'] = $result->getRespmsg();
            $this->setError($errorArr);
            return false;
        } else {
            return false;
        }

         return $this;
    }

    public function callGetExpressCheckoutDetails()
    {
        $proArr = array(
            'TENDER'        => self::TENDER_PAYPAL,
            'ACTION'        => self::ACIONT_GET_EXPRESS,
            'TOKEN'         => $this->getToken(),
        );

        $result = $this->postRequest($proArr);

        if ($result && $result->getResultCode()==self::RESPONSE_CODE_APPROVED) {
            $this->setPayerId($result->getPayerid());
            $this->setCorrelationId($result->getCorrelationid());
            $this->setPayerStatus($result->getPayerstatus());
            $this->setPaypalPayerEmail($result->getEmail());

            //$this->setAddressId($result->getAddressId());
           //$this->setAddressStatus($result->getAddressStatus());

            if (!$this->getShippingAddress()) {
                $this->setShippingAddress(Mage::getModel('customer/address'));
            }
//print_r($result->getData());
            $a = $this->getShippingAddress();
            $a->setEmail($result->getEmail());
            $a->setFirstname($result->getFirstname());
            $a->setLastname($result->getLastname());
            $street = array($result->getShiptostreet());
            if ($result->getShiptostreet2()) {
                $street[] = $result->getShiptostreet2();
            }
            $a->setStreet($street);
            $a->setCity($result->getShiptocity());
            $a->setRegion($result->getShiptostate());
            $a->setPostcode($result->getShiptozip());
            $a->setCountry($result->getShiptocountry());
            $a->setTelephone(Mage::helper('paypalUk')->__('N/A'));
//echo "<hr>";
//print_r($a->getData());
//echo "<hr>";
//print_r($this->getData());
         } else {
            $errorArr['code'] = $result->getResultCode();
            $errorArr['message'] = $result->getRespmsg();
            $this->setError($errorArr);
            return false;
         }

         return $this;
    }

    public function callDoExpressCheckoutPayment()
    {
         /* Gather the information to make the final call to
           finalize the PayPal payment.  The variable nvpstr
           holds the name value pairs
           */

        $proArr = array(
            'TENDER'        => self::TENDER_PAYPAL,
            'ACTION'        => self::ACIONT_DO_EXPRESS,
            'TOKEN'         => $this->getToken(),
            'PAYERID'       => $this->getPayerId(),
            'AMT'           => $this->getAmount(),
            'CURRENCY'      => $this->getCurrencyCode(),
            'BUTTONSOURCE'  => $this->getButtonSourceEc(),
        );

        $result = $this->postRequest($proArr);

         if ($result && $result->getResultCode()==self::RESPONSE_CODE_APPROVED) {
             $this->setTransactionId($result->getPnref());
          } else {
            //$errorArr['code'] = $result->getResultCode();
            //$errorArr['message'] = $result->getRespmsg();
            //$this->setError($errorArr);
            return false;
         }

         return $this;
    }


/*********************** EXPRESS & DIRECT PAYMENT ***************************/
    /*'----------------------------------------------------------------------------------
     * This function will take NVPString and convert it to an Associative Array and it will decode the response.
      * It is usefull to search for a particular key and displaying arrays.
      * @nvpstr is NVPString.
      * @nvpArray is Associative Array.
       ----------------------------------------------------------------------------------
    */
    public function deformatNVP($nvpstr)
    {
        $intial=0;
        $nvpArray = array();

        $nvpstr = strpos($nvpstr, "\r\n\r\n")!==false ? substr($nvpstr, strpos($nvpstr, "\r\n\r\n")+4) : $nvpstr;

        while(strlen($nvpstr)) {
            //postion of Key
            $keypos= strpos($nvpstr,'=');
            //position of value
            $valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);

            /*getting the Key and Value values and storing in a Associative Array*/
            $keyval=substr($nvpstr,$intial,$keypos);
            $valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
            //decoding the respose
            $nvpArray[urldecode($keyval)] =urldecode( $valval);
            $nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
         }
        return $nvpArray;
    }

    public function postRequest(array $proArr)
    {
        $proArr = array_merge(array(
            'PARTNER'   => $this->getPartner(),
            'USER'      => $this->getApiUser(),
            'VENDOR'    => $this->getApiVendor(),
            'PWD'       => $this->getApiPassword(),
            'TRXTYPE'   => $this->getTrxtype(),
            'REQUEST_ID'=> $this->_generateRequestId()
        ), $proArr);

        $proReq = '';
        $proReqDebug = '';
        foreach ($proArr as $k=>$v) {
            //payflow gateway doesn't allow urlencoding.
            //$proReq .= '&'.$k.'='.urlencode($v);
            $proReq .= '&'.$k.'='.$v;
            $proReqDebug .= '&'.$k.'=';
            if (in_array($k, $this->_debugReplacePrivateDataKeys)) {
                $proReqDebug .=  '***';
            } else {
                $proReqDebug .=  $v;
            }
        }
        $proReq = substr($proReq, 1);
        $proReqDebug = substr($proReqDebug, 1);

        if ($this->getDebug()) {
            $debug = Mage::getModel('paypaluk/api_debug')
                ->setRequestBody($proReqDebug)
                ->save();
        }
        $http = new Varien_Http_Adapter_Curl();
        $config = array('timeout' => 30);
        $http->setConfig($config);
        $http->write(Zend_Http_Client::POST, $this->getApiUrl(), '1.1', array(), $proReq);
        $response = $http->read();
        $response = preg_split('/^\r?$/m', $response, 2);
        $response = trim($response[1]);

        if ($this->getDebug()) {
            $debug->setResponseBody($response)->save();
        }

        if ($http->getErrno()) {
            $http->close();
            $this->setError(array(
                'type'=>'CURL',
                'code'=>$http->getErrno(),
                'message'=>$http->getError()
            ));
            $this->setRedirectUrl($this->getApiErrorUrl());
            return false;
        }
        $http->close();

        $result = Mage::getModel('paypaluk/api_result');
        $valArray = $this->deformatNVP($response);

        foreach($valArray as $k=>$v) {
            $result->setData(strtolower($k), $v);
        }
        $result->setResultCode($result->getResult())
               ->setRespmsg($result->getRespmsg());

        /*
        $client = new Varien_Http_Client();
        $uri = $this->getApiUrl();
        $client->setUri($uri)
               ->setConfig(array(
                    'maxredirects'=>5,
                    'timeout'=>30,
                ))
            ->setMethod(Zend_Http_Client::POST)
            ->setParameterPost($proArr)
            ->setHeaders('X-VPS-VIT-CLIENT-CERTIFICATION-ID: 33baf5893fc2123d8b191d2d011b7fdc')
            ->setHeaders('X-VPS-Request-ID: ' . $this->_generateRequestId())
            ->setHeaders('X-VPS-CLIENT-TIMEOUT: ' . $this->_clientTimeout)
            ->setUrlEncodeBody(false);

        $result = Mage::getModel('paypaluk/api_result');

        try {
            $response = $client->request();
            $response = strstr($response->getBody(), 'RESULT');
            $valArray = explode('&', $response);
            foreach($valArray as $val) {
                    $valArray2 = explode('=', $val);
                    $result->setData(strtolower($valArray2[0]), $valArray2[1]);
            }

            $result->setResultCode($result->getResult())
                    ->setRespmsg($result->getRespmsg());
            if ($this->getDebug()) {
                $debug->setResponseBody($response)
                    ->save();
            }

        } catch (Exception $e) {
            $result->setResultCode(-1)
                    ->setRespmsg($e->getMessage());
        }
        */

        return $result;
    }

    protected function _generateRequestId()
    {
        return md5(microtime() . rand(0, time()));
    }

    /**
      * canVoid
      *
      * @access public
      * @desc checking the transaction id is valid or not and transction id was not settled
      */
    public function canVoid()
    {
        $payment = $this->getPayment();

        $proArr = array(
            'TENDER'        => self::TENDER_CC,
            'ORIGID'        => $this->getTransactionId(),
        );

        $this->getTrxtype(self::TRXTYPE_DELAYED_INQUIRY);
        $result = $this->postRequest($proArr);

        if ($result && $result->getResultCode()==self::RESPONSE_CODE_APPROVED) {
            if ($result->getTransstate()>1000) {
                $errorArr['code'] = $result->getResultCode();
                $errorArr['message'] = Mage::helper('paypalUk')->__('Voided transaction');
                $this->setError($errorArr);
                return false;
            } elseif(in_array($result->getTransstate(),$this->_validVoidTransState)) {
                 $this->setStatus(Mage_Payment_Model_Method_Abstract::STATUS_VOID);
                 return $this;
            }
        }

        $errorArr['code'] = $result->getResultCode();
        $errorArr['message'] = $result->getRespmsg() ? $result->getRespmsg() : Mage::helper('paypalUk')->__('Error in inquriing the transaction');
        $this->setError($errorArr);
        return false;
    }

     /**
      * void
      *
      * @access public
      */
    public function void()
    {
        $payment = $this->getPayment();

        $proArr = array(
            'TENDER'        => self::TENDER_CC,
            'ORIGID'        => $this->getTransactionId(),
        );
        $this->getTrxtype(self::TRXTYPE_DELAYED_VOID);
        $result = $this->postRequest($proArr);

        if ($result && $result->getResultCode()==self::RESPONSE_CODE_APPROVED) {
            $this->setTransactionId($result->getPnref());
            return $this;
        }
        $errorArr['code'] = $result->getResultCode();
        $errorArr['message'] = $result->getRespmsg() ? $result->getRespmsg() : Mage::helper('paypalUk')->__('Error in voiding the transaction');
        $this->setError($errorArr);
        return false;
    }


     /**
      * refund the amount with transaction id
      *
      * @access public
      */
    public function refund()
    {
        $payment = $this->getPayment();

        $proArr = array(
            'TENDER'        => self::TENDER_CC,
            'ORIGID'        => $this->getTransactionId(),
            'AMT'           => $this->getAmount()
        );
        $this->getTrxtype(self::TRXTYPE_CREDIT);
        $result = $this->postRequest($proArr);

        if ($result && $result->getResultCode()==self::RESPONSE_CODE_APPROVED) {
            $this->setTransactionId($result->getPnref());
            return $this;
        }

        $errorArr['code'] = $result->getResultCode();
        $errorArr['message'] = $result->getRespmsg() ? $result->getRespmsg() : Mage::helper('paypalUk')->__('Error in voiding the transaction');
        $this->setError($errorArr);
        return false;
    }
}
