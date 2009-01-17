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
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * NVP API wrappers model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Model_Api_Nvp extends Mage_Paypal_Model_Api_Abstract
{
    public function getApiEndpoint()
    {
        if (!$this->getData('api_endpoint')) {
            if ($this->getSandboxFlag()) {
                $default = 'https://api-3t.sandbox.paypal.com/nvp';
            } else {
                $default = 'https://api-3t.paypal.com/nvp';
            }
            return $this->getConfigData('api_endpoint', $default);
        }
        return $this->getData('api_endpoint');
    }

    public function getPaypalUrl()
    {
        if (!$this->hasPaypalUrl()) {
            if ($this->getSandboxFlag()) {
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

    public function getVersion()
    {
        return '3.0';
    }

    /**
     * SetExpressCheckout API call
     *
     * An express checkout transaction starts with a token, that
     * identifies to PayPal your transaction
     * In this example, when the script sees a token, the script
     * knows that the buyer has already authorized payment through
     * paypal.  If no token was found, the action is to send the buyer
     * to PayPal to first authorize payment
     */
    public function callSetExpressCheckout()
    {
        //------------------------------------------------------------------------------------------------------------------------------------
        // Construct the parameter string that describes the SetExpressCheckout API call

        $nvpArr = array(
            'PAYMENTACTION' => $this->getPaymentType(),
            'AMT'           => $this->getAmount(),
            'CURRENCYCODE'  => $this->getCurrencyCode(),
            'RETURNURL'     => $this->getReturnUrl(),
            'CANCELURL'     => $this->getCancelUrl(),
            'INVNUM'        => $this->getInvNum()
        );
        $this->setUserAction(self::USER_ACTION_CONTINUE);

        // for mark SetExpressCheckout API call
        if ($a = $this->getShippingAddress()) {
            $nvpArr = array_merge($nvpArr, array(
                'ADDROVERRIDE'      => 1,
                'SHIPTONAME'        => $a->getName(),
                'SHIPTOSTREET'      => $a->getStreet(1),
                'SHIPTOSTREET2'     => $a->getStreet(2),
                'SHIPTOCITY'        => $a->getCity(),
                'SHIPTOSTATE'       => $a->getRegionCode(),
                'SHIPTOCOUNTRYCODE' => $a->getCountry(),
                'SHIPTOZIP'         => $a->getPostcode(),
                'PHONENUM'          => $a->getTelephone(),
            ));
            $this->setUserAction(self::USER_ACTION_COMMIT);
        }

        //'---------------------------------------------------------------------------------------------------------------
        //' Make the API call to PayPal
        //' If the API call succeded, then redirect the buyer to PayPal to begin to authorize payment.
        //' If an error occured, show the resulting errors
        //'---------------------------------------------------------------------------------------------------------------
        $resArr = $this->call('SetExpressCheckout', $nvpArr);

        if (false===$resArr) {
            return false;
        }

        $this->setToken($resArr['TOKEN']);
        $this->setRedirectUrl($this->getPaypalUrl());
        return $resArr;
    }

    /*
    '-------------------------------------------------------------------------------------------
    ' Purpose:  Prepares the parameters for the GetExpressCheckoutDetails API Call.
    '
    ' Inputs:
    '       None
    ' Returns:
    '       The NVP Collection object of the GetExpressCheckoutDetails Call Response.
    '-------------------------------------------------------------------------------------------
    */
    function callGetExpressCheckoutDetails()
    {
        //'--------------------------------------------------------------
        //' At this point, the buyer has completed authorizing the payment
        //' at PayPal.  The function will call PayPal to obtain the details
        //' of the authorization, incuding any shipping information of the
        //' buyer.  Remember, the authorization is not a completed transaction
        //' at this state - the buyer still needs an additional step to finalize
        //' the transaction
        //'--------------------------------------------------------------

        //'---------------------------------------------------------------------------
        //' Build a second API request to PayPal, using the token as the
        //'  ID to get the details on the payment authorization
        //'---------------------------------------------------------------------------
        $nvpArr = array(
            'TOKEN' => $this->getToken(),
        );

        //'---------------------------------------------------------------------------
        //' Make the API call and store the results in an array.
        //' If the call was a success, show the authorization details, and provide
        //'     an action to complete the payment.
        //' If failed, show the error
        //'---------------------------------------------------------------------------
        $resArr = $this->call('GetExpressCheckoutDetails', $nvpArr);
        if (false===$resArr) {
            return false;
        }

        $this->setPayerId($resArr['PAYERID']);
        $this->setCorrelationId($resArr['CORRELATIONID']);
        $this->setPayerStatus($resArr['PAYERSTATUS']);
        if (isset($resArr['ADDRESSID'])) {
            $this->setAddressId($resArr['ADDRESSID']);
        }
        $this->setAddressStatus($resArr['ADDRESSSTATUS']);
        $this->setPaypalPayerEmail($resArr['EMAIL']);

        if (!$this->getShippingAddress()) {
            $this->setShippingAddress(Mage::getModel('customer/address'));
        }
        $a = $this->getShippingAddress();
        $a->setEmail($resArr['EMAIL']);
        $a->setFirstname($resArr['FIRSTNAME']);
        $a->setLastname($resArr['LASTNAME']);
        $street = array($resArr['SHIPTOSTREET']);
        if (isset($resArr['SHIPTOSTREET2'])) {
            $street[] = $resArr['SHIPTOSTREET2'];
        }
        $a->setStreet($street);
        $a->setCity($resArr['SHIPTOCITY']);
        $a->setRegion(isset($resArr['SHIPTOSTATE']) ? $resArr['SHIPTOSTATE'] : '');
        $a->setPostcode($resArr['SHIPTOZIP']);
        $a->setCountry($resArr['SHIPTOCOUNTRYCODE']);
        $a->setTelephone(Mage::helper('paypal')->__('N/A'));

        return $resArr;
    }

    /*
    '-------------------------------------------------------------------------------------------------------------------------------------------
    ' Purpose:  Prepares the parameters for the GetExpressCheckoutDetails API Call.
    '
    ' Returns:
    '       The NVP Collection object of the GetExpressCheckoutDetails Call Response.
    '--------------------------------------------------------------------------------------------------------------------------------------------
    */
    public function callDoExpressCheckoutPayment()
    {
        /* Gather the information to make the final call to
           finalize the PayPal payment.  The variable nvpstr
           holds the name value pairs
           */

        $nvpArr = array(
            'TOKEN'         => $this->getToken(),
            'PAYERID'       => $this->getPayerId(),
            'PAYMENTACTION' => $this->getPaymentType(),
            'AMT'           => $this->getAmount(),
            'CURRENCYCODE'  => $this->getCurrencyCode(),
            'IPADDRESS'     => $this->getServerName(),
            'BUTTONSOURCE'  => $this->getButtonSourceEc(),
        );

         /* Make the call to PayPal to finalize payment
            If an error occured, show the resulting errors
            */
        $resArr = $this->call('DoExpressCheckoutPayment', $nvpArr);

        if (false===$resArr) {
            return false;
        }

        $this->setTransactionId($resArr['TRANSACTIONID']);
        $this->setAmount($resArr['AMT']);

        return $resArr;
    }

    public function callDoDirectPayment()
    {
        $p = $this->getPayment();
        $a = $this->getBillingAddress();
        if ($this->getShippingAddress()) {
            $s = $this->getShippingAddress();
        } else {
            $s = $a;
        }

        $nvpArr = array(
            'PAYMENTACTION'  => $this->getPaymentType(),
            'AMT'            => $this->getAmount(),
            'CURRENCYCODE'   => $this->getCurrencyCode(),
            'BUTTONSOURCE'   => $this->getButtonSourceDp(),
            'INVNUM'         => $this->getInvNum(),

            'CREDITCARDTYPE' => $this->getCcTypeName($p->getCcType()),
            'ACCT'           => $p->getCcNumber(),
            'EXPDATE'        => sprintf('%02d%02d', $p->getCcExpMonth(), $p->getCcExpYear()),
            'CVV2'           => $p->getCcCid(),

            'FIRSTNAME'      => $a->getFirstname(),
            'LASTNAME'       => $a->getLastname(),
            'STREET'         => $a->getStreet(1),
            'CITY'           => $a->getCity(),
            'STATE'          => $a->getRegionCode(),
            'ZIP'            => $a->getPostcode(),
            'COUNTRYCODE'    => 'US', // only US supported for direct payment
            'EMAIL'          => $this->getEmail(),

            'SHIPTONAME'     => $s->getName(),
            'SHIPTOSTREET'   => $s->getStreet(1),
            'SHIPTOSTREET2'   => $s->getStreet(2),
            'SHIPTOCITY'     => $s->getCity(),
            'SHIPTOSTATE'    => ($s->getRegionCode() ? $s->getRegionCode() : $s->getRegion()),
            'SHIPTOZIP'      => $s->getPostcode(),
            'SHIPTOCOUNTRYCODE' => $s->getCountry(),
        );

#echo "<pre>".print_r($nvpArr,1)."</pre>"; die;
        $resArr = $this->call('DoDirectPayment', $nvpArr);

        if (false===$resArr) {
            return false;
        }

        $this->setTransactionId($resArr['TRANSACTIONID']);
        $this->setAmount($resArr['AMT']);
        $this->setAvsCode($resArr['AVSCODE']);
        $this->setCvv2Match($resArr['CVV2MATCH']);

        return $resArr;
    }

    public function callDoReauthorization()
    {
        $nvpArr = array(
            'AUTHORIZATIONID' => $this->getAuthorizationId(),
            'AMT'             => $this->getAmount(),
            'CURRENCYCODE'    => $this->getCurrencyCode(),
        );

        $resArr = $this->call('DoReauthorization', $nvpArr);

        if (false===$resArr) {
            return false;
        }

        $this->setAuthorizationId($resArr['AUTHORIZATIONID']);

        return $resArr;
    }

    public function callDoCapture()
    {
        $nvpArr = array(
            'AUTHORIZATIONID' => $this->getAuthorizationId(),
            'COMPLETETYPE'    => $this->getCompleteType(),
            'AMT'             => $this->getAmount(),
            'CURRENCYCODE'    => $this->getCurrencyCode(),
            'NOTE'            => $this->getNote(),
            'INVNUM'          => $this->getInvNum()
        );

        $resArr = $this->call('DoCapture', $nvpArr);

        if (false===$resArr) {
            return false;
        }

        $this->setAuthorizationId($resArr['AUTHORIZATIONID']);
        $this->setTransactionId($resArr['TRANSACTIONID']);
        $this->setPaymentStatus($resArr['PAYMENTSTATUS']);
        $this->setCurrencyCode($resArr['CURRENCYCODE']);
        $this->setAmount($resArr['AMT']);

        return $resArr;
    }

    public function callDoVoid()
    {
        $nvpArr = array(
            'AUTHORIZATIONID' => $this->getAuthorizationId(),
            'NOTE'            => $this->getNote(),
        );

        $resArr = $this->call('DoVoid', $nvpArr);

        if (false===$resArr) {
            return false;
        }

        $this->setAuthorizationId($resArr['AUTHORIZATIONID']);

        return $resArr;
    }

    public function callGetTransactionDetails()
    {
        $nvpArr = array(
            'TRANSACTIONID' => $this->getTransactionId(),
        );

        $resArr = $this->call('GetTransactionDetails', $nvpArr);

        if (false===$resArr) {
            return false;
        }

        $this->setPayerEmail($resArr['RECEIVEREMAIL']);
        $this->setPayerId($resArr['PAYERID']);
        $this->setFirstname($resArr['FIRSTNAME']);
        $this->setLastname($resArr['LASTNAME']);
        $this->setTransactionId($resArr['TRANSACTIONID']);
        $this->setParentTransactionId($resArr['PARENTTRANSACTIONID']);
        $this->setCurrencyCode($resArr['CURRENCYCODE']);
        $this->setAmount($resArr['AMT']);
        $this->setPaymentStatus($resArr['PAYERSTATUS']);

        return $resArr;
    }

    public function callRefundTransaction()
    {
        $nvpArr = array(
            'TRANSACTIONID' => $this->getTransactionId(),
            'REFUNDTYPE'    => $this->getRefundType(),
            'CURRENCYCODE'  => $this->getCurrencyCode(),
            'NOTE'          => $this->getNote(),
        );
        if ($this->getRefundType()===self::REFUND_TYPE_PARTIAL) {
            $nvpArr['AMT'] = $this->getAmount();
        }

        $resArr = $this->call('RefundTransaction', $nvpArr);

        if (false===$resArr) {
            return false;
        }

        $this->setTransactionId($resArr['REFUNDTRANSACTIONID']);
        $this->setAmount($resArr['GROSSREFUNDAMT']);

        return $resArr;
    }

    /**
     * Function to perform the API call to PayPal using API signature
     *
     * @param $methodName string is name of API  method.
     * @param $nvpArr array NVP params array
     * @return array|boolean an associtive array containing the response from the server or false in case of error.
     */
    public function call($methodName, array $nvpArr)
    {
        $nvpArr = array_merge(array(
            'METHOD'    => $methodName,
            'VERSION'   => $this->getVersion(),
            'USER'      => $this->getApiUserName(),
            'PWD'       => $this->getApiPassword(),
            'SIGNATURE' => $this->getApiSignature(),
        ), $nvpArr);

        $nvpReq = '';
        foreach ($nvpArr as $k=>$v) {
            $nvpReq .= '&'.$k.'='.urlencode($v);
        }
        $nvpReq = substr($nvpReq, 1);
        if ($this->getDebug()) {
            $debug = Mage::getModel('paypal/api_debug')
                ->setApiEndpoint($this->getApiEndpoint())
                ->setRequestBody($nvpReq)
                ->save();
        }

        $http = new Varien_Http_Adapter_Curl();
        $config = array('timeout' => 30);
        if ($this->getUseProxy()) {
            $config['proxy'] = $this->getProxyHost(). ':' . $this->getProxyPort();
        }
        $http->setConfig($config);
        $http->write(Zend_Http_Client::POST, $this->getApiEndpoint(), '1.1', array(), $nvpReq);
        $response = $http->read();
        $response = preg_split('/^\r?$/m', $response, 2);
        $response = trim($response[1]);

        if ($this->getDebug()) {
            $debug->setResponseBody($response)->save();
        }

        $nvpReqArray = $this->deformatNVP($nvpReq);
        $this->getSession()->setNvpReqArray($nvpReqArray);

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

        //converting NVPResponse to an Associative Array
        $nvpResArray = $this->deformatNVP($response);
        $this->getSession()
            ->setLastCallMethod($methodName)
            ->setResHash($nvpResArray);

        $ack = strtoupper($nvpResArray['ACK']);

        if ($ack == 'SUCCESS' || $ack=='SUCCESSWITHWARNING') {
            $this->unsError();
            return $nvpResArray;
        }


        $errorArr = array(
            'type' => 'API',
            'ack' => $ack,
        );
        if (isset($nvpResArray['VERSION'])) {
            $errorArr['version'] = $nvpResArray['VERSION'];
        }
        if (isset($nvpResArray['CORRELATIONID'])) {
            $errorArr['correlation_id'] = $nvpResArray['CORRELATIONID'];
        }
        for ($i=0; isset($nvpResArray['L_SHORTMESSAGE'.$i]); $i++) {
            $errorArr['code'] = $nvpResArray['L_ERRORCODE'.$i];
            $errorArr['short_message'] = $nvpResArray['L_SHORTMESSAGE'.$i];
            $errorArr['long_message'] = $nvpResArray['L_LONGMESSAGE'.$i];
        }
        $this->setError($errorArr);
        $this->setRedirectUrl($this->getApiErrorUrl());
        return false;
    }

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

}
