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
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order payment information
 */
class Mage_Sales_Model_Order_Payment extends Mage_Payment_Model_Info
{
    /**
     * Order model object
     *
     * @var Mage_Sales_Model_Order
     */
    protected $_order;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('sales/order_payment');
    }

    /**
     * Declare order model object
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  Mage_Sales_Model_Order_Payment
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        return $this;
    }

    /**
     * Retrieve order model object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * Check order payment capture action availability
     *
     * @return unknown
     */
    public function canCapture()
    {
        /**
         * @todo checking amounts
         */
        return $this->getMethodInstance()->canCapture();
    }

    public function canRefund()
    {
        return $this->getMethodInstance()->canRefund();
    }

    public function canRefundPartialPerInvoice()
    {
        return $this->getMethodInstance()->canRefundPartialPerInvoice();
    }

    public function canCapturePartial()
    {
        return $this->getMethodInstance()->canCapturePartial();
    }

    /**
     * Place payment information
     *
     * This method is called when order will be placed
     *
     * @return Mage_Sales_Model_Order_Payment
     */
    public function place()
    {
        Mage::dispatchEvent('sales_order_payment_place_start', array('payment' => $this));

        $this->setAmountOrdered($this->getOrder()->getTotalDue());
        $this->setBaseAmountOrdered($this->getOrder()->getBaseTotalDue());

        $this->setShippingAmount($this->getOrder()->getShippingAmount());
        $this->setBaseShippingAmount($this->getOrder()->getBaseShippingAmount());

        $methodInstance = $this->getMethodInstance()->setStore($this->getOrder()->getStoreId());

        $orderState = Mage_Sales_Model_Order::STATE_NEW;
        $orderStatus= false;

        $stateObject = new Varien_Object();

        /**
         * validating payment method again
         */
        $methodInstance->validate();
        if ($action = $methodInstance->getConfigData('payment_action')) {
            /**
             * Run action declared for payment method in configuration
             */

            if ($methodInstance->isInitializeNeeded()) {
                $methodInstance->initialize($action, $stateObject);
            } else {
                switch ($action) {
                    case Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE:
                    case Mage_Paypal_Model_Api_Abstract::PAYMENT_TYPE_AUTH:
                        $methodInstance->authorize($this, $this->getOrder()->getBaseTotalDue());

                        $this->setAmountAuthorized($this->getOrder()->getTotalDue());
                        $this->setBaseAmountAuthorized($this->getOrder()->getBaseTotalDue());

                        $orderState = Mage_Sales_Model_Order::STATE_PROCESSING;
                        break;
                    case Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE:
                    case Mage_Paypal_Model_Api_Abstract::PAYMENT_TYPE_SALE:
                        $invoice = $this->_invoice();

                        $this->setAmountAuthorized($this->getOrder()->getTotalDue());
                        $this->setBaseAmountAuthorized($this->getOrder()->getBaseTotalDue());

                        $orderState = Mage_Sales_Model_Order::STATE_PROCESSING;
                        break;
                    default:
                        break;
                }
            }
        }

        $orderIsNotified = null;
        if ($stateObject->getState() && $stateObject->getStatus()) {
            $orderState      = $stateObject->getState();
            $orderStatus     = $stateObject->getStatus();
            $orderIsNotified = $stateObject->getIsNotified();
        } else {
            /*
             * this flag will set if the order went to as authorization under fraud service for payflowpro
             */
            if ($this->getFraudFlag()) {
                $orderStatus = $methodInstance->getConfigData('fraud_order_status');
                $orderState = Mage_Sales_Model_Order::STATE_HOLDED;
            } else {
                /**
                 * Change order status if it specified
                 */
                $orderStatus = $methodInstance->getConfigData('order_status');
            }

            if (!$orderStatus) {
                $orderStatus = $this->getOrder()->getConfig()->getStateDefaultStatus($orderState);
            }
        }

        $this->getOrder()->setState($orderState);
        $this->getOrder()->addStatusToHistory(
            $orderStatus,
            $this->getOrder()->getCustomerNote(),
            (null !== $orderIsNotified ? $orderIsNotified : $this->getOrder()->getCustomerNoteNotify())
        );

        Mage::dispatchEvent('sales_order_payment_place_end', array('payment' => $this));

        return $this;
    }

    /**
     * Capture payment
     *
     * @return Mage_Sales_Model_Order_Payment
     */
    public function capture($invoice)
    {
        if (is_null($invoice)) {
            $invoice = $this->_invoice();
        }

        Mage::dispatchEvent('sales_order_payment_capture', array('payment' => $this, 'invoice' => $invoice));

        $this->getMethodInstance()
            ->setStore($this->getOrder()->getStoreId())
            ->capture($this, sprintf('%.2f', $invoice->getBaseGrandTotal()));
        $this->getMethodInstance()->processInvoice($invoice, $this);
        return $this;
    }


    /**
     * Register payment fact
     *
     * @param   unknown_type $invoice
     * @return  unknown
     */
    public function pay($invoice)
    {
        $this->setAmountPaid($this->getAmountPaid()+$invoice->getGrandTotal());
        $this->setBaseAmountPaid($this->getBaseAmountPaid()+$invoice->getBaseGrandTotal());

        $this->setShippingCaptured($this->getShippingCaptured()+$invoice->getShippingAmount());
        $this->setBaseShippingCaptured($this->getBaseShippingCaptured()+$invoice->getBaseShippingAmount());

        Mage::dispatchEvent('sales_order_payment_pay', array('payment' => $this, 'invoice' => $invoice));

        return $this;
    }

    /**
     * Cancel invoice
     *
     * @param   unknown_type $invoice
     * @return  unknown
     */
    public function cancelInvoice($invoice)
    {
        $this->setAmountPaid($this->getAmountPaid()-$invoice->getGrandTotal());
        $this->setBaseAmountPaid($this->getBaseAmountPaid()-$invoice->getBaseGrandTotal());

        $this->setShippingCaptured($this->getShippingCaptured()-$invoice->getShippingAmount());
        $this->setBaseShippingCaptured($this->getBaseShippingCaptured()-$invoice->getBaseShippingAmount());

        Mage::dispatchEvent('sales_order_payment_cancel_invoice', array('payment' => $this, 'invoice' => $invoice));

        return $this;
    }

    /**
     * Create new invoice with maximum qty for invoice for each item
     * register this invoice and capture
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    protected function _invoice()
    {
        $invoice = $this->getOrder()->prepareInvoice();

        $invoice->register();
        if ($this->getMethodInstance()->canCapture()) {
            $invoice->capture();
        }

        $this->getOrder()->addRelatedObject($invoice);
        return $invoice;
    }

    /**
     * Check order payment void availability
     *
     * @return bool
     */
    public function canVoid(Varien_Object $document)
    {
        return $this->getMethodInstance()->canVoid($document);
    }

    public function void(Varien_Object $document)
    {
        $this->getMethodInstance()
            ->setStore($this->getOrder()->getStoreId())
            ->processBeforeVoid($document, $this);
        //$this->getMethodInstance()->void($document);
        $this->getMethodInstance()->void($this);

        Mage::dispatchEvent('sales_order_payment_void', array('payment' => $this, 'invoice' => $document));

        return $this;
    }

    public function refund($creditmemo)
    {
        if ($this->getMethodInstance()->canRefund() && $creditmemo->getDoTransaction()) {
            $this->setCreditmemo($creditmemo);
            if ($creditmemo->getInvoice()) {
                $this->getMethodInstance()->setStore($this->getOrder()->getStoreId());
                $this->getMethodInstance()->processBeforeRefund($creditmemo->getInvoice(), $this);
                $this->getMethodInstance()->refund($this, $creditmemo->getBaseGrandTotal());
                $this->getMethodInstance()->processCreditmemo($creditmemo, $this);
            }
        }

        $this->setAmountRefunded($this->getAmountRefunded()+$creditmemo->getGrandTotal());
        $this->setBaseAmountRefunded($this->getBaseAmountRefunded()+$creditmemo->getBaseGrandTotal());

        $this->setShippingRefunded($this->getShippingRefunded()+$creditmemo->getShippingAmount());
        $this->setBaseShippingRefunded($this->getBaseShippingRefunded()+$creditmemo->getBaseShippingAmount());

        Mage::dispatchEvent('sales_order_payment_refund', array('payment' => $this, 'creditmemo' => $creditmemo));

        return $this;
    }

    public function cancelCreditmemo($creditmemo)
    {
        $this->setAmountRefunded($this->getAmountRefunded()-$creditmemo->getGrandTotal());
        $this->setBaseAmountRefunded($this->getBaseAmountRefunded()-$creditmemo->getBaseGrandTotal());

        $this->setShippingRefunded($this->getShippingRefunded()-$creditmemo->getShippingAmount());
        $this->setBaseShippingRefunded($this->getBaseShippingRefunded()-$creditmemo->getBaseShippingAmount());

        Mage::dispatchEvent('sales_order_payment_cancel_creditmemo', array('payment' => $this, 'creditmemo' => $creditmemo));

        return $this;
    }

    public function cancel()
    {
        $this->getMethodInstance()
            ->setStore($this->getOrder()->getStoreId())
            ->cancel($this);

        Mage::dispatchEvent('sales_order_payment_cancel', array('payment' => $this));

        return $this;
    }
}