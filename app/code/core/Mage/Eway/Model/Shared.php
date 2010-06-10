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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * eWAY Shared Model
 *
 * @category   Mage
 * @package    Mage_Eway
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eway_Model_Shared extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'eway_shared';

    protected $_isGateway               = false;
    protected $_canAuthorize            = false;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

    protected $_formBlockType = 'eway/shared_form';
    protected $_paymentMethod = 'shared';

    protected $_order;

    /**
     * Get order model
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->_order) {
            $paymentInfo = $this->getInfoInstance();
            $this->_order = Mage::getModel('sales/order')
                            ->loadByIncrementId($paymentInfo->getOrder()->getRealOrderId());
        }
        return $this->_order;
    }

    /**
     * Get Customer Id
     *
     * @return string
     */
    public function getCustomerId()
    {
        return Mage::getStoreConfig('payment/' . $this->getCode() . '/customer_id');
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
            Mage::throwException(Mage::helper('eway')->__('Selected currency code (%s) is not compatible with eWAY.', $currency_code));
        }
        return $this;
    }

    public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('eway/' . $this->_paymentMethod . '/redirect');
    }

    /**
     * prepare params array to send it to gateway page via POST
     *
     * @return array
     */
    public function getFormFields()
    {
        $billing = $this->getOrder()->getBillingAddress();
        $fieldsArr = array();
        $invoiceDesc = '';
        $lengs = 0;
        foreach ($this->getOrder()->getAllItems() as $item) {
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
        $paymentInfo = $this->getInfoInstance();
        $fieldsArr['ewayCustomerID'] = $this->getCustomerId();
        $fieldsArr['ewayTotalAmount'] = ($this->getOrder()->getBaseGrandTotal()*100);
        $fieldsArr['ewayCustomerFirstName'] = $billing->getFirstname();
        $fieldsArr['ewayCustomerLastName'] = $billing->getLastname();
        $fieldsArr['ewayCustomerEmail'] = $this->getOrder()->getCustomerEmail();
        $fieldsArr['ewayCustomerAddress'] = trim($formatedAddress);
        $fieldsArr['ewayCustomerPostcode'] = $billing->getPostcode();
//        $fieldsArr['ewayCustomerInvoiceRef'] = '';
        $fieldsArr['ewayCustomerInvoiceDescription'] = $invoiceDesc;
        $fieldsArr['eWAYSiteTitle '] = Mage::app()->getStore()->getFrontendName();
        $fieldsArr['eWAYAutoRedirect'] = 1;
        $fieldsArr['ewayURL'] = Mage::getUrl('eway/' . $this->_paymentMethod . '/success', array('_secure' => true));
        $fieldsArr['eWAYTrxnNumber'] = $paymentInfo->getOrder()->getRealOrderId();
        $fieldsArr['ewayOption1'] = '';
        $fieldsArr['ewayOption2'] = Mage::helper('core')->encrypt($fieldsArr['eWAYTrxnNumber']);
        $fieldsArr['ewayOption3'] = '';

        $request = '';
        foreach ($fieldsArr as $k=>$v) {
            $request .= '<' . $k . '>' . $v . '</' . $k . '>';
        }

        $this->_debug(array('request' => $request));

        return $fieldsArr;
    }

    /**
     * Get url of eWAY Shared Payment
     *
     * @return string
     */
    public function getEwaySharedUrl()
    {
         if (!$url = Mage::getStoreConfig('payment/eway_shared/api_url')) {
             $url = 'https://www.eway.com.au/gateway/payment.asp';
         }
         return $url;
    }

    /**
     * @deprecated after 1.4.1.0
     *
     * @return string
     */
    public function getDebug()
    {
        return $this->getDebugFlag();
    }

    public function capture(Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
            ->setTransactionId($this->getTransactionId())
            ->setIsTransactionClosed(0);

        return $this;
    }

    public function cancel(Varien_Object $payment)
    {
        $payment->setStatus(self::STATUS_DECLINED)
            ->setTransactionId($this->getTransactionId())
            ->setIsTransactionClosed(1);

        return $this;
    }

    /**
     * parse response POST array from gateway page and return payment status
     *
     * @return bool
     */
    public function parseResponse()
    {
        $response = $this->getResponse();

        $this->_debug(array('result' => $response));

        if ($response['ewayTrxnStatus'] == 'True') {
            return true;
        }
        return false;
    }

    /**
     * Return redirect block type
     *
     * @return string
     */
    public function getRedirectBlockType()
    {
        return $this->_redirectBlockType;
    }

    /**
     * Return payment method type string
     *
     * @return string
     */
    public function getPaymentMethodType()
    {
        return $this->_paymentMethod;
    }

    /**
     * Define if debugging is enabled
     *
     * @return bool
     */
    public function getDebugFlag()
    {
        return $this->getConfigData('debug_flag');
    }
}
