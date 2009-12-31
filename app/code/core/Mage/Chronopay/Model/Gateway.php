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
 * @package     Mage_Chronopay
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * ChronoPay Gateway Model
 *
 * @category   Mage
 * @package    Mage_Chronopay
 * @name       Mage_Chronopay_Model_Gateway
 * @author	   Magento Core Team <core@magentocommerce.com>
 */

class Mage_Chronopay_Model_Gateway extends Mage_Payment_Model_Method_Cc
{
    const CGI_URL = 'https://secure.chronopay.com/gateway.cgi';

    const OPCODE_CHARGING               = 1;
    const OPCODE_REFUND                 = 2;
    const OPCODE_AUTHORIZE              = 4;
    const OPCODE_VOID_AUTHORIZE         = 5;
    const OPCODE_CONFIRM_AUTHORIZE      = 6;
    const OPCODE_CUSTOMER_FUND_TRANSFER = 8;

    protected $_code  = 'chronopay_gateway';

    protected $_formBlockType = 'chronopay/form';
    protected $_infoBlockType = 'chronopay/info';

    /**
     * Availability options
     */
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = true;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc               = false;

    /**
     *  Return ip address of customer
     *
     *  @return	  string
     */
    protected function _getIp ()
    {
        return Mage::helper('core/http')->getRemoteAddr();
    }

    /**
     *  Return shared secret key from config
     *
     *  @return	  string
     */
    protected function _getSharedSecret ()
    {
        return $this->getConfigData('shared_secret');
    }

    public function authorize(Varien_Object $payment, $amount)
    {
        $payment->setAmount($amount);
        $payment->setOpcode(self::OPCODE_AUTHORIZE);

        $request = $this->_buildRequest($payment);
        $result = $this->_postRequest($request);

        if (!$result->getError()) {
            $payment->setStatus(self::STATUS_APPROVED);
            $payment->setCcTransId($result->getTransaction());
        } else {
            Mage::throwException($result->getError());
        }

        return $this;
    }

    public function capture(Varien_Object $payment, $amount)
    {
        $payment->setAmount($amount);
        if ($payment->getCcTransId()) {
            $this->setTransactionId($payment->getCcTransId());
            $payment->setOpcode(self::OPCODE_CONFIRM_AUTHORIZE);
        } else {
            $payment->setOpcode(self::OPCODE_CHARGING);
        }

        $request = $this->_buildRequest($payment);
        $result = $this->_postRequest($request);

        if (!$result->getError()) {
            $payment->setStatus(self::STATUS_APPROVED);
            $payment->setLastTransId($result->getTransaction());
        } else {
            Mage::throwException($result->getError());
        }

        return $this;
    }

    public function refund(Varien_Object $payment, $amount)
    {
        $payment->setAmount($amount);
        $payment->setOpcode(self::OPCODE_REFUND);

        $this->setTransactionId($payment->getRefundTransactionId());

        $request = $this->_buildRequest($payment);
        $result = $this->_postRequest($request);

        $payment->setStatus(self::STATUS_APPROVED);
        $payment->setLastTransId($result->getTransaction());

        return $this;
    }

    /**
     *  Building request array
     *
     *  @param    Varien_Object
     *  @return	  array
     */
    protected function _buildRequest(Varien_Object $payment)
    {
        $order = $payment->getOrder();
        $billing = $order->getBillingAddress();
        $streets = $billing->getStreet();
        $street = isset($streets[0]) && $streets[0] != ''
                  ? $streets[0]
                  : (isset($streets[1]) && $streets[1] != '' ? $streets[1] : '');

        $request = Mage::getModel('chronopay/gateway_request')
            ->setOpcode($payment->getOpcode())
            ->setProductId($this->getConfigData('product_id'));

        switch ($request->getOpcode()) {
            case self::OPCODE_CUSTOMER_FUND_TRANSFER :
                $request->setCustomer($order->getCustomerId())
                    ->setAmount(sprintf('%.2f', $payment->getAmount()))
                    ->setCurrency($order->getBaseCurrencyCode());
                break;
            case self::OPCODE_CHARGING :
            case self::OPCODE_REFUND :
            case self::OPCODE_AUTHORIZE :
            case self::OPCODE_VOID_AUTHORIZE :
                $request->setFname($billing->getFirstname())
                    ->setLname($billing->getLastname())
                    ->setCardholder($payment->getCcOwner())
                    ->setZip($billing->getPostcode())
                    ->setStreet($street)
                    ->setCity($billing->getCity())
                    ->setState($billing->getRegionModel()->getCode())
                    ->setCountry($billing->getCountryModel()->getIso3Code())
                    ->setEmail($order->getCustomerEmail())
                    ->setPhone($billing->getTelephone())
                    ->setIp($this->_getIp())
                    ->setCardNo($payment->getCcNumber())
                    ->setCvv($payment->getCcCid())
                    ->setExpirey($payment->getCcExpYear())
                    ->setExpirem(sprintf('%02d', $payment->getCcExpMonth()))
                    ->setAmount(sprintf('%.2f', $payment->getAmount()))
                    ->setCurrency($order->getBaseCurrencyCode());
                break;
            default :
                Mage::throwException(
                    Mage::helper('chronopay')->__('Invalid operation code')
                );
                break;
        }

        $request->setShowTransactionId(1);

        if ($this->getTransactionId()) {
            $request->setTransaction($this->getTransactionId());
        }


        $hash = $this->_getHash($request);
        $request->setHash($hash);
        return $request;
    }

    /**
     *  Send request to gateway
     *
     *  @param    Mage_Chronopay_Model_Gateway_Request
     *  @return	  mixed
     */
    protected function _postRequest(Mage_Chronopay_Model_Gateway_Request $request)
    {
        $result = Mage::getModel('chronopay/gateway_result');

        $client = new Varien_Http_Client();

        $url = $this->getConfigData('cgi_url');
        $client->setUri($url ? $url : self::CGI_URL);
        $client->setConfig(array(
            'maxredirects' => 0,
            'timeout' => 30,
        ));
        $client->setParameterPost($request->getData());
        $client->setMethod(Zend_Http_Client::POST);

        if ($this->getConfigData('debug_flag')) {
            $debug = Mage::getModel('chronopay/api_debug')
                ->setRequestBody($client->getUri() . "\n" . print_r($request->getData(), 1))
                ->save();
        }

        try {
            $response = $client->request();
            $body = $response->getRawBody();

            if (preg_match('/(T\|(.+)\|[\r\n]{0,}){0,1}(Y\|(.+)?|\|)|(N\|(.+[\r\n]{0,}.+){0,})/', $body, $matches)) {

                $transactionId = $matches[2];
                $message = isset($matches[4])?trim($matches[4], '|'):'';

                if (isset($matches[5], $matches[6])) {
                    $result->setError($matches[6]);
                    Mage::throwException($matches[6]);
                }

                if ($message == 'Completed') {
                    $result->setTransaction($request->getTransaction());
                }

                if (strlen($transactionId)) {
                    $result->setTransaction($transactionId);
                }

                if (!$result->getTransaction()) {
                    Mage::throwException(Mage::helper('chronopay')->__('Transaction ID is invalid'));
                }
            } else {
                Mage::throwException(Mage::helper('chronopay')->__('Invalid response format'));
            }

            if ($this->getConfigData('debug_flag')) {
                $debug->setResponseBody($body)->save();
            }

        } catch (Exception $e) {
            $result->setResponseCode(-1)
                ->setResponseReasonCode($e->getCode())
                ->setResponseReasonText($e->getMessage());


            $exceptionMsg = Mage::helper('chronopay')->__('Gateway request error: %s', $e->getMessage());

            if ($this->getConfigData('debug_flag')) {
                $debug->setResponseBody($body)->save();
            }

            Mage::throwException($exceptionMsg);
        }
        return $result;
    }

    /**
     *  Generate MD5 hash for transaction checksum
     *
     *  @param    Mage_Chronopay_Model_Gateway_Request
     *  @return	  string MD5
     */
    protected function _getHash(Mage_Chronopay_Model_Gateway_Request $request)
    {
        $hashArray = array(
            $this->_getSharedSecret(),
            $request->getOpcode(),
            $request->getProductId()
        );

        switch ($request->getOpcode()) {
            case self::OPCODE_CHARGING :
            case self::OPCODE_AUTHORIZE :
                $hashArray[] = $request->getFname();
                $hashArray[] = $request->getLname();
                $hashArray[] = $request->getStreet();
                $hashArray[] = $this->_getIp();
                $hashArray[] = $request->getCardNo();
                $hashArray[] = $request->getAmount();
                break;

            case self::OPCODE_VOID_AUTHORIZE :
            case self::OPCODE_CONFIRM_AUTHORIZE :
                $hashArray[] = $request->getTransaction();
                break;

            case self::OPCODE_REFUND :
                $hashArray[] = $request->getTransaction();
                $hashArray[] = $request->getAmount();
                break;

            case self::OPCODE_CUSTOMER_FUND_TRANSFER :
                $hashArray[] = $request->getCustomer();
                $hashArray[] = $request->getTransaction();
                $hashArray[] = $request->getAmount();
                break;

            default :
                Mage::throwException(
                    Mage::helper('chronopay')->__('Invalid operation code')
                );
                break;
        }

        return md5(implode('', $hashArray));
    }

}
