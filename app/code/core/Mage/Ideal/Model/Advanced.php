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
 * @package     Mage_Ideal
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * iDEAL Advanced Checkout Model
 *
 * @category    Mage
 * @package     Mage_Ideal
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Ideal_Model_Advanced extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'ideal_advanced';
    protected $_formBlockType = 'ideal/advanced_form';
    protected $_infoBlockType = 'ideal/advanced_info';
    protected $_allowCurrencyCode = array('EUR', 'GBP', 'USD', 'CAD', 'SHR', 'NOK', 'SEK', 'DKK');

    protected $_isGateway               = false;
    protected $_canAuthorize            = false;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

    protected $_issuersList = null;

    public function canUseCheckout()
    {
        if ($this->getIssuerList() && parent::canUseCheckout()) {
            return true;
        } else {
            return false;
        }
    }

    public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('ideal/advanced/redirect', array('_secure' => true));
    }

    /**
     * Get iDEAL API Model
     *
     * @return Mage_Ideal_Api_Advanced
     */
    public function getApi()
    {
        return Mage::getSingleton('ideal/api_advanced');
    }

    public function getIssuerList($saveAttrbute = false)
    {
        if ($this->_issuersList == null) {
            $request = new Mage_Ideal_Model_Api_Advanced_DirectoryRequest();
            $response = $this->getApi()->processRequest($request, $this->getDebug());
            if ($response) {
                $this->_issuersList = $response->getIssuerList();
                return $this->_issuersList;
            } else {
                $this->_issuersList = null;
                $this->setError($this->getApi()->getError());
                return false;
            }
        } else {
            $this->getInfoInstance()
                ->setIdealIssuerList(serialize($this->_issuersList))
                ->save();
            return $this->_issuersList;
        }
    }

    /**
     * validate the currency code is avaialable to use for iDEAL Advanced or not
     *
     * @return bool
     */
    public function validate()
    {
        parent::validate();
        $paymentInfo = $this->getInfoInstance();
        if ($paymentInfo instanceof Mage_Sales_Model_Order_Payment) {
            $currency_code = $paymentInfo->getOrder()->getBaseCurrencyCode();
        } else {
            $currency_code = $paymentInfo->getQuote()->getBaseCurrencyCode();
        }

        if (!in_array($currency_code,$this->_allowCurrencyCode)) {
            Mage::throwException(Mage::helper('ideal')->__('Selected currency code (%s) is not compatible with iDEAL', $currency_code));
        }

        return $this;
    }

    /**
     * Preapre and send transaction request
     *
     * @param Mage_Sales_Model_Order $order
     * @param string $issuerId
     * @return Mage_Ideal_Model_Api_Advanced_AcquirerTrxResponse
     */
    public function sendTransactionRequest(Mage_Sales_Model_Order $order, $issuerId)
    {
        $request = new Mage_Ideal_Model_Api_Advanced_AcquirerTrxRequest();
        $request->setIssuerId($issuerId);
        $request->setPurchaseId($order->getIncrementId());
        $request->setEntranceCode(Mage::helper('ideal')->encrypt($order->getIncrementId()));
        //we need to be sure that we sending number without decimal part
        $request->setAmount(floor($order->getBaseGrandTotal()*100));
        $response = $this->getApi()->processRequest($request, $this->getDebug());
        return $response;
    }

    /**
     * Prepare and send transaction status request
     *
     * @param string $transactionId
     * @return Mage_Ideal_Model_Api_Advanced_AcquirerStatusResponse
     */
    public function getTransactionStatus($transactionId)
    {
        $request = new Mage_Ideal_Model_Api_Advanced_AcquirerStatusRequest();
        $request->setTransactionId($transactionId);
        $response = $this->getApi()->processRequest($request, $this->getDebug());
        return $response;
    }

    public function capture(Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
            ->setLastTransId($this->getTransactionId());

        return $this;
    }

    public function cancel(Varien_Object $payment)
    {
        $payment->setStatus(self::STATUS_DECLINED);

        return $this;
    }

    /**
     * Executes by cron and check transactions status
     * for every iDEAL order that was created in last hour
     */
    public function transactionStatusCheck($shedule = null)
    {
        $gmtStamp = Mage::getModel('core/date')->gmtTimestamp();
        $to = $this->getConfigData('cron_start') > 0?$this->getConfigData('cron_start'):1;
        $to = date('Y-m-d H:i:s', $gmtStamp - $to * 3600);

        $from = $this->getConfigData('cron_end') > 0?$this->getConfigData('cron_end'):1;
        $from = date('Y-m-d H:i:s', $gmtStamp - $from * 86400);

        $paymentCollection = Mage::getModel('sales/order_payment')->getCollection()
            ->addAttributeToFilter('last_trans_id', array('neq' => ''))
            ->addAttributeToFilter('method', $this->_code)
            ->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to, 'datetime' => true))
            ->addAttributeToFilter('ideal_transaction_checked', array('neq' => '1'));

        $order = Mage::getModel('sales/order');
        foreach($paymentCollection->getItems() as $item) {
            $order->reset();
            $order->load($item->getParentId());
            $response = $this->getTransactionStatus($item->getLastTransId());

            if ($response->getTransactionStatus() == Mage_Ideal_Model_Api_Advanced::STATUS_SUCCESS) {
                if ($order->canInvoice()) {
                    $invoice = $order->prepareInvoice();
                    $invoice->register()->capture();
                    Mage::getModel('core/resource_transaction')
                        ->addObject($invoice)
                        ->addObject($invoice->getOrder())
                        ->save();

                    $order->addStatusToHistory($order->getStatus(), Mage::helper('ideal')->__('Transaction Status Update: finished successfully'));
                }
            } else if ($response->getTransactionStatus() == Mage_Ideal_Model_Api_Advanced::STATUS_CANCELLED) {
                $order->cancel();
                $order->addStatusToHistory($order->getStatus(), Mage::helper('ideal')->__('Transaction Status Update: cancelled by customer'));
            } else {
                $order->cancel();
                $order->addStatusToHistory($order->getStatus(), Mage::helper('ideal')->__('Transaction Status Update: rejected by iDEAL'));
            }

            $order->getPayment()->setIdealTransactionChecked(1);
            $order->save();
        }
    }

    /**
     * Get debug flag
     *
     * @return boolean
     */
    public function getDebug()
    {
        return $this->getConfigData('debug_flag');
    }
}
