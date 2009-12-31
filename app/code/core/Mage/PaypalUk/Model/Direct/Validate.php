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
 * @package     Mage_PaypalUk
 * @copyright   Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * 3D Secure validation library for PayflowUk Pro Direct
 */
include_once '3Dsecure/CentinelClient.php';

/**
 * 3D Secure Validation Model for PayPal Direct
 */
class Mage_PaypalUk_Model_Direct_Validate extends Mage_Core_Model_Abstract
{
    protected $_thinClient = null;

    /**
     * Return Centinel thin client object
     *
     * @return CentinelClient
     */
    public function getThinClient()
    {
        if (empty($this->_thinClient)) {
            $this->_thinClient = new CentinelClient();
        }
        return $this->_thinClient;
    }

    /**
     * Return Centinel Api version
     *
     * @return string
     */
    public function getVersion()
    {
        return '1.7';
    }

    /**
     * Return centinel processor id, based on config data for given payment
     *
     * @return string
     */
    public function getProcessorId()
    {
        return $this->getConfigData('centinel_processor_id');
    }

    /**
     * Return centinel merchant id, based on config data for given payment
     *
     * @return string
     */
    public function getMerchantId()
    {
        return $this->getConfigData('centinel_merchant_id');
    }

    /**
     * Return centinel transaction password, based on config data for given payment
     *
     * @return string
     */
    public function getTransactionPwd()
    {
        return $this->getConfigData('centinel_password');
    }

    /**
     * Return centinel map url, based on config data for given payment
     *
     * @return string
     */
    public function getMapUrl()
    {
        return $this->getConfigData('centinel_maps_url');
    }

    /**
     * Return centinel timeout connect , based on config data for give payment
     *
     * @return string
     */
    public function getTimeoutConnect()
    {
        return $this->getConfigData('centinel_timeout_connect');
    }

    /**
     * Return centinel timeout read, based on config data for give payment
     *
     * @return string
     */
    public function getTimeoutRead()
    {
        return $this->getConfigData('centinel_timeout_read');
    }

    /**
     * Return transaction type. according centinel documetation it should be "C"
     *
     * @return "C"
     */
    public function getTransactionType()
    {
        return 'C';
    }

    /**
     * Return term url, witch is used for return after card holder process validation
     *
     * @return string
     */
    public function getTermUrl()
    {
        $formKey = Mage::getSingleton('core/session')->getFormKey();
        if (Mage::app()->getStore()->isAdmin()) {
            return Mage::getUrl('*/paypaluk_direct/termValidate', array('_secure' => true, '_current' => true, 'form_key' => $formKey));
        } else {
            return Mage::getUrl('paypaluk/direct/termValidate', array('_secure' => true, 'form_key' => $formKey));
        }
    }

    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Return quoter namespace
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return Mage::getSingleton('adminhtml/session_quote')->getQuote();
        } else {
            return $this->getCheckout()->getQuote();
        }
    }

    /**
     * Return payment model
     *
     * @return Mage_Paypal_Model_Direct
     */
    public function getPayPal()
    {
        return Mage::getSingleton('paypaluk/direct');
    }

     /**
     * Retrieve information from cardinal lib configuration
     *
     * @param   string $field
     * @return  mixed
     */
    public function getConfigData($field, $storeId = null)
    {
        return $this->getPayPal()->getConfigData($field, $storeId);
    }

    /**
     * Return paypal session model
     *
     * @return Mage_Paypal_Model_Session
     */
    public function getSession()
    {
        return $this->getPayPal()->getSession();
    }

    /**
     * Set AcsUrl in session, url used for redirect customer to cardholder verification page.
     *
     * @return Mage_Paypal_Model_Direct_Validate
     */
    public function setAcsUrl($url)
    {
        $this->getSession()->setData('centinel_asc_url', $url);
        return $this;
    }

    /**
     * Get card holder url from session
     *
     * @return string
     */
    public function getAcsUrl()
    {
        return $this->getSession()->getData('centinel_asc_url');
    }

    /**
     * Set Enrolled status in session, url to verify lookup status
     *
     * @return Mage_Paypal_Model_Direct_Validate
     */
    public function setEnrolled($enrolled)
    {
        $this->getSession()->setData('centinel_enrolled', $enrolled);
        return $this;
    }

    /**
     * Return lookup status from session
     *
     * @return string
     */
    public function getEnrolled()
    {
        return $this->getSession()->getData('centinel_enrolled');
    }

    /**
     * Set Payload status in session, encrypted data, result of centinel api lookup method call
     *
     * @return Mage_Paypal_Model_Direct_Validate
     */
    public function setPayload($payLoad)
    {
        $this->getSession()->setData('centinel_payload', $payLoad);
        return $this;
    }

    /**
     * Return payload from session
     *
     * @return string
     */
    public function getPayload()
    {
        return $this->getSession()->getData('centinel_payload');
    }

    /**
     * Set Electronic Commerce indicator in session
     *
     * @return Mage_Paypal_Model_Direct_Validate
     */
    public function setEciFlag($eciFlag)
    {
        $this->getSession()->setData('centinel_eci_flag', $eciFlag);
        return $this;
    }

    /**
     * Return Electronic Commerce indicator from session
     *
     * @return string
     */
    public function getEciFlag()
    {
        return $this->getSession()->getData('centinel_eci_flag');
    }

    /**
     * Set outcome of the issuer authentication in session
     *
     * @return Mage_Paypal_Model_Direct_Validate
     */
    public function setPaResStatus($status)
    {
        $this->getSession()->setData('centinel_pa_res_status', $status);
        return $this;
    }

    /**
     * Get outcome of the issuer authentication
     *
     * @return string
     */
    public function getPaResStatus()
    {
        return $this->getSession()->getData('centinel_pa_res_status');
    }

    /**
     * Set status of authntication elgibility
     *
     * @return Mage_Paypal_Model_Direct_Validate
     */
    public function setSignature($signature)
    {
        $this->getSession()->setData('centinel_signature', $signature);
        return $this;
    }

    /**
     * Return status of authntication elgibility
     *
     * @return string
     */
    public function getSignature()
    {
        return $this->getSession()->getData('centinel_signature');
    }

    /**
     * Set cavv, result of api authentiocation in session
     * A random sequence of characters. This is encoded authentication.
     *
     * @return Mage_Paypal_Model_Direct_Validate
     */
    public function setCavv($cavv)
    {
        $this->getSession()->setData('centinel_cavv', $cavv);
        return $this;
    }

    /**
     * Return Cavv value from session
     *
     * @return string
     */
    public function getCavv()
    {
        return $this->getSession()->getData('centinel_cavv');
    }

    /**
     * Set transaction identifier from authentication in session
     *
     * @return Mage_Paypal_Model_Direct_Validate
     */
    public function setXid($xid)
    {
        $this->getSession()->setData('centinel_xid', $xid);
        return $this;
    }

    /**
     * Get transaction identifier from session
     *
     * @return string
     */
    public function getXid()
    {
        return $this->getSession()->getData('centinel_xid');
    }

    /**
     * Set centinel transaction id. Transaction id within Centinel
     *
     * @return Mage_Paypal_Model_Direct_Validate
     */
    public function setTransactionId($transactionId)
    {
        $this->getSession()->setData('centinel_transaction_id', $transactionId);
        return $this;
    }

    /**
     * Return internal Centinel transaction id from session
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->getSession()->getData('centinel_transaction_id');
    }

    /**
     * Set Authentication information from cardholder to session.
     * it will be passed to centinel authentication request
     *
     * @return Mage_Paypal_Model_Direct_Validate
     */
    public function setPaResPayload($payloadRes)
    {
        $this->getSession()->setData('centinel_pa_res_payload', $payloadRes);
        return $this;
    }

    /**
     * Return cardholder payload information from session
     *
     * @return string
     */
    public function getPaResPayload()
    {
        return $this->getSession()->getData('centinel_pa_res_payload');
    }

    /**
     * Call centinel api lookup method
     *
     * @return Mage_Paypal_Model_Direct_Validate
     */
    public function callLookup()
    {
        $payment = $this->getQuote()->getPayment();

        $currencyIso = "";
        try {
            $currencyIso = Mage::helper('paypal/currency')->getIso4217CurrencyCode($this->getQuote()->getBaseCurrencyCode());
        }catch (Mage_Core_Exception $e) {
            $this->setErrorNo(1);
            $this->setErrorDesc($e);
            return $this;
        }

        $month = strlen($this->getCcExpMonth())==1 ? '0' . $this->getCcExpMonth() : $this->getCcExpMonth();
        $lookUpArray = array(
            'Amount' => round($this->getQuote()->getBaseGrandTotal()*100),
            'CurrencyCode' => $currencyIso,
            'CardNumber' =>  $this->getCcNumber(),
            'CardExpMonth'=> $month,
            'CardExpYear' =>  $this->getCcExpYear()
        );

        if (!$this->getQuote()->getReservedOrderId()) {
            $this->getQuote()->reserveOrderId();
        }
        $lookUpArray['OrderNumber'] = $this->getQuote()->getReservedOrderId();

        $clientResponse = $this->call('cmpi_lookup', $lookUpArray);

        $this->setEnrolled($clientResponse->getValue('Enrolled'));
        $this->setErrorNo($clientResponse->getValue('ErrorNo'));
        $this->setErrorDesc($clientResponse->getValue('ErrorDesc'));
        $this->setEciFlag($clientResponse->getValue('EciFlag'));
        $this->setAcsUrl($clientResponse->getValue('ACSUrl'));
        $this->setPayload($clientResponse->getValue('Payload'));
        $this->setOrderId($clientResponse->getValue('OrderId'));
        $this->setTransactionId($clientResponse->getValue('TransactionId'));
        $this->setAuthenticationPath($clientResponse->getValue('AuthenticationPath'));
        $this->setTermUrl($this->getTermUrl());
        return $this;
    }

    /**
     * Call centinel api authentication method
     *
     * @return Mage_Paypal_Model_Direct_Validate
     */
    public function callAuthentication()
    {
        $authArray = array(
            'TransactionId' => $this->getTransactionId(),
            'PAResPayload'  => $this->getPaResPayload(),
        );

        $clientResponse = $this->call('cmpi_authenticate', $authArray);
        $this->setErrorNo($clientResponse->getValue('ErrorNo'));
        $this->setErrorDesc($clientResponse->getValue('ErrorDesc'));
        $this->setPaResStatus($clientResponse->getValue('PAResStatus'));
        $this->setCavv($clientResponse->getValue('Cavv'));
        $this->setSignature($clientResponse->getValue('SignatureVerification'));
        $this->setEciFlag($clientResponse->getValue('EciFlag'));
        $this->setXid($clientResponse->getValue('Xid'));
        return $this;
    }

    /**
     * Call centinel api methods by given method name and data
     *
     * @param $method string
     * @param $data array
     *
     * @return CentinelClient
     */
    public function call($method, $data)
    {
        $thinData = array(
            'MsgType'           => $method,
            'Version'           => $this->getVersion(),
            'ProcessorId'       => $this->getProcessorId(),
            'MerchantId'        => $this->getMerchantId(),
            'TransactionPwd'    => Mage::helper('core')->decrypt($this->getTransactionPwd()),
            'TransactionType'   => $this->getTransactionType(),
        );

        $thinClient = $this->getThinClient();
        $thinData = array_merge($thinData, $data);
        if (count($thinData) > 0) {
            foreach($thinData AS $key => $val) {
                $thinClient->add($key, $val);
            }
        }

        $thinClient->sendHttp($this->getMapUrl(), $this->getTimeoutConnect(), $this->getTimeoutRead());
        return $thinClient;
    }
}
