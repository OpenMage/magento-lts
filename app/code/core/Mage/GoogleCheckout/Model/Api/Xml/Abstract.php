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
 * @category   Mage
 * @package    Mage_GoogleCheckout
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

set_include_path(get_include_path().PS.Mage::getBaseDir('lib').DS.'googlecheckout');

require_once('googleresponse.php');
require_once('googlemerchantcalculations.php');
require_once('googleresult.php');
require_once('googlerequest.php');

abstract class Mage_GoogleCheckout_Model_Api_Xml_Abstract extends Varien_Object
{
    public function log($text, $nl=true)
    {
        error_log(print_r($text,1).($nl?"\n":''), 3, Mage::getBaseDir('log').DS.'callback.log');
        return $this;
    }

    public function __()
    {
        $args = func_get_args();
        $expr = new Mage_Core_Model_Translate_Expr(array_shift($args), 'Mage_GoogleCheckout');
        array_unshift($args, $expr);
        return Mage::app()->getTranslator()->translate($args);
    }

    public function getMerchantId()
    {
        if (!$this->hasData('merchant_id')) {
            $this->setData('merchant_id', Mage::getStoreConfig('google/checkout/merchant_id'));
        }
        return $this->getData('merchant_id');
    }

    public function getMerchantKey()
    {
        if (!$this->hasData('merchant_key')) {
            $this->setData('merchant_key', Mage::getStoreConfig('google/checkout/merchant_key'));
        }
        return $this->getData('merchant_key');
    }

    public function getServerType()
    {
        if (!$this->hasData('server_type')) {
            $this->setData('server_type', Mage::getStoreConfig('google/checkout/sandbox') ? "sandbox" : "");
        }
        return $this->getData('server_type');
    }

    public function getLocale()
    {
        if (!$this->hasData('locale')) {
            $this->setData('locale', Mage::getStoreConfig('google/checkout/locale'));
        }
        return $this->getData('locale');
    }

    public function getCurrency()
    {
        if (!$this->hasData('currency')) {
            $this->setData('currency', Mage::app()->getStore()->getBaseCurrencyCode());
            //$this->setData('currency', $this->getLocale()=='en_US' ? 'USD' : 'GBP');
        }
        return $this->getData('currency');
    }

    /**
     * Google Checkout Request instance
     *
     * @return GoogleRequest
     */
    public function getGRequest()
    {
        if (!$this->hasData('g_request')) {
            $this->setData('g_request', new GoogleRequest(
                $this->getMerchantId(),
                $this->getMerchantKey(),
                $this->getServerType(),
                $this->getCurrency()
            ));

            //Setup the log file
            $logDir = Mage::getBaseDir('log');
            $this->getData('g_request')->SetLogFiles(
                $logDir.DS.'googleerror.log',
                $logDir.DS.'googlemessage.log',
                L_ALL
            );
        }
        return $this->getData('g_request');
    }

    /**
     * Google Checkout Response instance
     *
     * @return GoogleResponse
     */
    public function getGResponse()
    {
        if (!$this->hasData('g_response')) {
            $this->setData('g_response', new GoogleResponse(
                $this->getMerchantId(),
                $this->getMerchantKey()
            ));

            //Setup the log file
            $logDir = Mage::getBaseDir('log');
            $this->getData('g_response')->SetLogFiles(
                $logDir.DS.'googleerror.log',
                $logDir.DS.'googlemessage.log',
                L_ALL
            );
        }
        return $this->getData('g_response');
    }

    protected function _getBaseApiUrl()
    {
        $url = 'https://';
        if ($this->getServerType()=='sandbox') {
            $url .= 'sandbox.google.com/checkout/api/checkout/v2/';
        } else {
            $url .= 'checkout.google.com/api/checkout/v2/';
        }
        return $url;
    }

    abstract protected function _getApiUrl();

    public function _call($xml)
    {
        $auth = 'Basic '.base64_encode($this->getMerchantId().':'.$this->getMerchantKey());

        $headers = array(
            'Authorization: '.$auth,
            'Content-Type: application/xml;charset=UTF-8',
            'Accept: application/xml;charset=UTF-8',
        );

        $url = $this->_getApiUrl();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\r\n".$xml;

        if (Mage::getStoreConfig('google/checkout/debug')) {
            $debug = Mage::getModel('googlecheckout/api_debug');
            $debug->setDir('out')->setUrl($url)->setRequestBody($xml)->save();
        }

        $http = new Varien_Http_Adapter_Curl();
        $http->write('POST', $url, '1.1', $headers, $xml);
        $response = $http->read();
        $response = preg_split('/^\r?$/m', $response, 2);
        $response = trim($response[1]);

        if (!empty($debug)) {
            $debug->setResponseBody($response)->save();
        }

        $result = @simplexml_load_string($response);
        if (!$result) {
            $result = simplexml_load_string('<error><error-message>Invalid response from Google Checkout server</error-message></error>');
        }
        if ($result->getName()=='error') {
            $this->setError($this->__('Google Checkout: %s', (string)$result->{'error-message'}));
            $this->setWarnings((array)$result->{'warning-messages'});
        } else {
            $this->unsError()->unsWarnings();
        }

        $this->setResult($result);

        return $result;
    }

    protected function _getCallbackUrl()
    {
        return Mage::getUrl('googlecheckout/api', array('_secure'=>true));
    }
}
