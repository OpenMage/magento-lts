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
 * @package    Mage_AmazonPayments
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * AmazonPayments ASP API Model
 *
 * @category   Mage
 * @package    Mage_AmazonPayments
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_AmazonPayments_Model_Api_Asp extends Mage_AmazonPayments_Model_Api_Asp_Abstract
{
    /**
     * collect shipping address to IPN notification request 
     */
    protected $_collectShippingAddress = 0;

    /**
     * IPN notification request model path 
     */
    protected $_ipnRequest = 'amazonpayments/api_asp_ipn_request';
    
    /**
     * FPS model path 
     */
    protected $_fpsModel = 'amazonpayments/api_asp_fps';

    /**
     * Get singleton with AmazonPayments ASP API FPS Model
     *
     * @return Mage_AmazonPayments_Model_Api_Asp_Fps
     */
    protected function _getFps()
    {
        return Mage::getSingleton($this->_fpsModel)->setStoreId($this->getStoreId());
    }
    
    /**
     * Get singleton with AmazonPayments ASP IPN notification request Model
     *
     * @return Mage_AmazonPayments_Model_Api_Asp_Ipn_Request
     */
    protected function _getIpnRequest()
    {
        return Mage::getSingleton($this->_ipnRequest);
    }
    
    
    /**
     * Return Amazon Simple Pay payment url
     *
     * @return string
     */
    public function getPayUrl () 
    {
        if ($this->_isSandbox()) {
            return $this->_getConfig('pay_service_url_sandbox');
        }
        return $this->_getConfig('pay_service_url');
    } 
    
    /**
     * Return Amazon Simple Pay payment params
     *
     * @param string $referenceId
     * @param string $amountValue
     * @param string $currencyCode
     * @param string $abandonUrl
     * @param string $returnUrl
     * @param string $ipnUrl
     * @return array
     */
    public function getPayParams($referenceId, $amountValue, $currencyCode, $abandonUrl, $returnUrl, $ipnUrl) 
    {
        $amount = Mage::getSingleton('amazonpayments/api_asp_amount')
            ->setValue($amountValue)
            ->setCurrencyCode($currencyCode);
        
        $requestParams = array();
        $requestParams['referenceId'] = $referenceId;
        $requestParams['amount'] = $amount->toString(); 
        $requestParams['description'] = $this->_getConfig('pay_description');

        $requestParams['accessKey'] = $this->_getConfig('access_key');
        $requestParams['processImmediate'] = $this->_getConfig('pay_process_immediate');
        $requestParams['immediateReturn'] = $this->_getConfig('pay_immediate_return');
        $requestParams['collectShippingAddress'] = $this->_collectShippingAddress;
        $requestParams['abandonUrl'] = $abandonUrl;
        $requestParams['returnUrl'] = $returnUrl;
        $requestParams['ipnUrl'] = $ipnUrl;
        
        $signature = $this->_getSignatureForArray($requestParams, $this->_getConfig('secret_key'));
        $requestParams['signature'] = $signature;

        return $requestParams;
    }
    
    /**
     * process notification request
     *
     * @param array $requestParams
     * @return Mage_AmazonPayments_Model_Api_Asp_Ipn_Request
     */
    public function processNotification($requestParams) 
    {
        $requestSignature = false;
        
        if (isset($requestParams['signature'])) {
            $requestSignature = $requestParams['signature'];
            unset($requestParams['signature']);
        }
        
        $originalSignature = $this->_getSignatureForArray($requestParams, $this->_getConfig('secret_key'));
        if ($requestSignature != $originalSignature) {
            Mage::throwException(Mage::helper('amazonpayments')->__('Request signed an incorrect or missing signature'));
        }
        
        $ipnRequest = $this->_getIpnRequest();
        
        if(!$ipnRequest->init($requestParams)) {
            Mage::throwException(Mage::helper('amazonpayments')->__('Request is not a valid IPN request'));
        }
        
        return $ipnRequest;
    }

    /**
     * cancel payment through FPS api
     *
     * @param string $transactionId
     * @return Mage_AmazonPayments_Model_Api_Asp_Fps_Response_Abstract
     */
    public function cancel($transactionId) 
    {
        $fps = $this->_getFps();

        $request = $fps->getRequest(Mage_AmazonPayments_Model_Api_Asp_Fps::ACTION_CODE_CANCEL)
            ->setTransactionId($transactionId)
            ->setDescription($this->_getConfig('cancel_description'));
            
        $response = $fps->process($request);
        return $response; 
    }
    
    /**
     * capture payment through FPS api
     *
     * @param string $transactionId
     * @param string $amount
     * @param string $currencyCode
     * @return Mage_AmazonPayments_Model_Api_Asp_Fps_Response_Abstract
     */
    public function capture($transactionId, $amount, $currencyCode) 
    {
        $fps = $this->_getFps();
        $amount = $this->_getAmount()
            ->setValue($amount)
            ->setCurrencyCode($currencyCode);
                        
        $request = $fps->getRequest(Mage_AmazonPayments_Model_Api_Asp_Fps::ACTION_CODE_SETTLE)
            ->setTransactionId($transactionId)
            ->setAmount($amount);

        $response = $fps->process($request);
        return $response; 
    }

    /**
     * capture payment through FPS api
     *
     * @param string $transactionId
     * @param string $amount
     * @param string $currencyCode
     * @param string $referenceId
     * @return Mage_AmazonPayments_Model_Api_Asp_Fps_Response_Abstract
     */
    public function refund($transactionId, $amount, $currencyCode, $referenceId) 
    {
        $fps = $this->_getFps();

        $amount = $this->_getAmount()
            ->setValue($amount)
            ->setCurrencyCode($currencyCode);
        
        $request = $fps->getRequest(Mage_AmazonPayments_Model_Api_Asp_Fps::ACTION_CODE_REFUND)
            ->setTransactionId($transactionId)
            ->setReferenceId($referenceId)
            ->setAmount($amount)
            ->setDescription($this->_getConfig('refund_description'));

        $response = $fps->process($request);
        return $response; 
    }
}
