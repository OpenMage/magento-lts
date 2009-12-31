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
 * @package     Mage_Eway
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * eWAY Direct Model
 *
 * @category   Mage
 * @package    Mage_Eway
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eway_Model_Direct extends Mage_Payment_Model_Method_Cc
{
    protected $_code  = 'eway_direct';

    protected $_isGateway               = true;
    protected $_canAuthorize            = false;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc               = true;

    protected $_formBlockType = 'eway/form';
    protected $_infoBlockType = 'eway/info';

    /**
     * Get debug flag
     *
     * @return string
     */
    public function getDebug()
    {
        return Mage::getStoreConfig('payment/eway_direct/debug_flag');
    }

    /**
     * Get flag to use CCV or not
     *
     * @return string
     */
    public function getUseccv()
    {
        return Mage::getStoreConfig('payment/eway_direct/useccv');
    }

    /**
     * Get api url of eWAY Direct payment
     *
     * @return string
     */
    public function getApiGatewayUrl()
    {
        $value = Mage::getStoreConfig('payment/eway_direct/api_url');
        if (!$value || $value === false) {
            return 'https://www.eway.com.au/gateway/xmlpayment.asp';
        }
        return $value;
    }

    /**
     * Get Customer Id
     *
     * @return string
     */
    public function getCustomerId()
    {
        return Mage::getStoreConfig('payment/eway_direct/customer_id');
    }

    /**
     * Get currency that accepted by eWAY account
     *
     * @return string
     */
    public function getAccepteCurrency()
    {
        return Mage::getStoreConfig('payment/' . $this->getCode() . '/currency');
    }

    public function validate()
    {
        parent::validate();
        $paymentInfo = $this->getInfoInstance();
        if ($paymentInfo instanceof Mage_Sales_Model_Order_Payment) {
            $currency_code = $paymentInfo->getOrder()->getBaseCurrencyCode();
        } else {
            $currency_code = $paymentInfo->getQuote()->getBaseCurrencyCode();
        }
        if ($currency_code != $this->getAccepteCurrency()) {
            Mage::throwException(Mage::helper('eway')->__('Selected currency code ('.$currency_code.') is not compatible with eWAY'));
        }
        return $this;
    }

    public function capture(Varien_Object $payment, $amount)
    {
        $this->setAmount($amount)
            ->setPayment($payment);

        $result = $this->callDoDirectPayment($payment)!==false;

        if ($result) {
            $payment->setStatus(self::STATUS_APPROVED)
                ->setLastTransId($this->getTransactionId());
        } else {
            $e = $this->getError();
            if (isset($e['message'])) {
                $message = Mage::helper('eway')->__('There has been an error processing your payment.') . $e['message'];
            } else {
                $message = Mage::helper('eway')->__('There has been an error processing your payment. Please try later or contact us for help.');
            }
            Mage::throwException($message);
        }
        return $this;
    }

    public function cancel(Varien_Object $payment)
    {
        $payment->setStatus(self::STATUS_DECLINED);
        return $this;
    }

    /**
     * prepare params to send to gateway
     *
     * @return bool | array
     */
    public function callDoDirectPayment()
    {
        $payment = $this->getPayment();
        $billing = $payment->getOrder()->getBillingAddress();

        $invoiceDesc = '';
        $lengs = 0;
        foreach ($payment->getOrder()->getAllItems() as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            if (Mage::helper('core/string')->strlen($invoiceDesc.$item->getName()) > 10000) {
                break;
            }
            $invoiceDesc .= $item->getName() . ', ';
        }
        $invoiceDesc = Mage::helper('core/string')->substr($invoiceDesc, 0, -2);

        $address = clone $billing;
        $address->unsFirstname();
        $address->unsLastname();
        $address->unsPostcode();
        $formatedAddress = '';
        $tmpAddress = explode(' ', str_replace("\n", ' ', trim($address->format('text'))));
        foreach ($tmpAddress as $part) {
            if (strlen($part) > 0) $formatedAddress .= $part . ' ';
        }
//        $this->getQuote()->reserveOrderId();
        $xml = "<ewaygateway>";
        $xml .= "<ewayCustomerID>" . $this->getCustomerId() . "</ewayCustomerID>";
        $xml .= "<ewayTotalAmount>" . ($this->getAmount()*100) . "</ewayTotalAmount>";
        $xml .= "<ewayCardHoldersName>" . htmlentities(trim($payment->getCcOwner()), ENT_QUOTES, 'UTF-8') . "</ewayCardHoldersName>";
        $xml .= "<ewayCardNumber>" . $payment->getCcNumber() . "</ewayCardNumber>";
        $xml .= "<ewayCardExpiryMonth>" . $payment->getCcExpMonth() . "</ewayCardExpiryMonth>";
        $xml .= "<ewayCardExpiryYear>" . $payment->getCcExpYear() . "</ewayCardExpiryYear>";
        $xml .= "<ewayTrxnNumber>" . '' . "</ewayTrxnNumber>";
        $xml .= "<ewayCustomerInvoiceDescription>" . htmlentities(trim($invoiceDesc), ENT_QUOTES, 'UTF-8') . "</ewayCustomerInvoiceDescription>";
        $xml .= "<ewayCustomerFirstName>" . htmlentities(trim($billing->getFirstname()), ENT_QUOTES, 'UTF-8') . "</ewayCustomerFirstName>";
        $xml .= "<ewayCustomerLastName>" . htmlentities(trim($billing->getLastname()), ENT_QUOTES, 'UTF-8') . "</ewayCustomerLastName>";
        $xml .= "<ewayCustomerEmail>" . htmlentities(trim($payment->getOrder()->getCustomerEmail()), ENT_QUOTES, 'UTF-8') . "</ewayCustomerEmail>";
        $xml .= "<ewayCustomerAddress>" . htmlentities(trim($formatedAddress), ENT_QUOTES, 'UTF-8') . "</ewayCustomerAddress>";
        $xml .= "<ewayCustomerPostcode>" . htmlentities(trim($billing->getPostcode()), ENT_QUOTES, 'UTF-8') . "</ewayCustomerPostcode>";
//        $xml .= "<ewayCustomerInvoiceRef>" . $this->getQuote()->getReservedOrderId() . "</ewayCustomerInvoiceRef>";
        $xml .= "<ewayCustomerInvoiceRef>" . '' . "</ewayCustomerInvoiceRef>";

        if ($this->getUseccv()) {
            $xml .= "<ewayCVN>" . $payment->getCcCid() . "</ewayCVN>";
        }

        $xml .= "<ewayOption1>" . '' . "</ewayOption1>";
        $xml .= "<ewayOption2>" . '' . "</ewayOption2>";
        $xml .= "<ewayOption3>" . '' . "</ewayOption3>";


        if (Mage::getStoreConfig('payment/eway_direct/use_anti_fraud')) {
            $xml .= "<ewayCustomerIPAddress>". Mage::helper('core/http')->getRemoteAddr() ."</ewayCustomerIPAddress>";
            $xml .= "<ewayCustomerBillingCountry>". $billing->getCountryId() ."</ewayCustomerBillingCountry>";
        }

        $xml .= "</ewaygateway>";

        $resultArr = $this->call($xml);

        if ($resultArr === false) {
            return false;
        }

        $this->setTransactionId($resultArr['ewayTrxnNumber']);

        return $resultArr;
    }

    /**
     * Send params to gateway
     *
     * @param string $xml
     * @return bool | array
     */
    public function call($xml)
    {
        if ($this->getDebug()) {
            $debug = Mage::getModel('eway/api_debug')
                ->setRequestBody($xml)
                ->save();
        }

        $http = new Varien_Http_Adapter_Curl();
        $config = array('timeout' => 30);

        $http->setConfig($config);
        $http->write(Zend_Http_Client::POST, $this->getApiGatewayUrl(), '1.1', array(), $xml);
        $response = $http->read();

        $response = preg_split('/^\r?$/m', $response, 2);
        $response = trim($response[1]);

        if ($this->getDebug()) {
            $debug->setResponseBody($response)->save();
        }

        if ($http->getErrno()) {
            $http->close();
            $this->setError(array(
                'message' => $http->getError()
            ));
            return false;
        }
        $http->close();

        $parsedResArr = $this->parseXmlResponse($response);

        if ($parsedResArr['ewayTrxnStatus'] == 'True') {
            $this->unsError();
            return $parsedResArr;
        }

        if (isset($parsedResArr['ewayTrxnError'])) {
            $this->setError(array(
                'message' => $parsedResArr['ewayTrxnError']
            ));
        }

        return false;
    }

    /**
     * parse response of gateway
     *
     * @param string $xmlResponse
     * @return array
     */
    public function parseXmlResponse($xmlResponse)
    {
        $xmlObj = simplexml_load_string($xmlResponse);
        $newResArr = array();
        foreach ($xmlObj as $key => $val) {
            $newResArr[$key] = (string)$val;
        }

        return $newResArr;
    }

}
