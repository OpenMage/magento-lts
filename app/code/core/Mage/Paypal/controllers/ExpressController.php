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
 * Express Checkout Controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_ExpressController extends Mage_Core_Controller_Front_Action
{
    /**
     * Setting right header of response if session died
     *
     */
    protected function _expireAjax()
    {
        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

    /**
     * Get singleton with paypal express order transaction information
     *
     * @return Mage_Paypal_Model_Express
     */
    public function getExpress()
    {
        return Mage::getSingleton('paypal/express');
    }

    /**
     * When there's an API error
     */
    public function errorAction()
    {
        $this->_redirect('checkout/cart');
    }

    /**
     * When user camcels transaction on paypal site and return back in store
     *
     */
    public function cancelAction()
    {
        $this->_redirect('checkout/cart');
    }

    /**
     * When a customer clicks Paypal button on shopping cart
     */
    public function shortcutAction()
    {
        $this->getExpress()->shortcutSetExpressCheckout();
        $this->getResponse()->setRedirect($this->getExpress()->getRedirectUrl());
    }

    public function editAction()
    {
        $this->getResponse()->setRedirect($this->getExpress()->getApi()->getPaypalUrl());
    }

    /**
     * Return here from Paypal before final payment (continue)
     *
     */
    public function returnAction()
    {
        $this->getExpress()->returnFromPaypal();
        $this->getResponse()->setRedirect($this->getExpress()->getRedirectUrl());
    }

    /**
     * Return here from Paypal after final payment (commit) or after on-site order review
     *
     */
    public function reviewAction()
    {
        $payment = Mage::getSingleton('checkout/session')->getQuote()->getPayment();
        if ($payment && $payment->getPaypalPayerId()) {
            $this->loadLayout();
            $this->_initLayoutMessages('paypal/session');
            $this->renderLayout();
        } else {
            $this->_redirect('checkout/cart');
        }
    }

    /**
     * Get PayPal Onepage checkout model
     *
     * @return Mage_Paypal_Model_Express_Onepage
     */
    public function getReview()
    {
        return Mage::getSingleton('paypal/express_review');
    }

    /**
     * Action for ajax request to save selected shipping and return html block
     *
     */
    public function saveShippingMethodAction()
    {
        if ($this->getRequest()->getParam('ajax')) {
            $this->_expireAjax();
        }

        if (!$this->getRequest()->isPost()) {
            return;
        }

        $data = $this->getRequest()->getParam('shipping_method', '');
        $result = $this->getReview()->saveShippingMethod($data);

        if ($this->getRequest()->getParam('ajax')) {
            $this->loadLayout('paypal_express_review_details');
            $this->getResponse()->setBody($this->getLayout()->getBlock('root')->toHtml());
        } else {
            $this->_redirect('paypal/express/review');
        }
    }

    /**
     * Action executed when 'Place Order' button pressed on review page
     *
     */
    public function saveOrderAction()
    {
        /**
         * 1- create order
         * 2- place order (call doexpress checkout)
         * 3- save order
         */
        $error_message = '';
        $payPalSession = Mage::getSingleton('paypal/session');

        try {
            $address = $this->getReview()->getQuote()->getShippingAddress();
            if (!$address->getShippingMethod()) {
                if ($shippingMethod = $this->getRequest()->getParam('shipping_method')) {
                    $this->getReview()->saveShippingMethod($shippingMethod);
                } else if (!$this->getReview()->getQuote()->getIsVirtual()) {
                    $payPalSession->addError(Mage::helper('paypal')->__('Please select a valid shipping method'));
                    $this->_redirect('paypal/express/review');
                    return;
                }
            }

            $billing = $this->getReview()->getQuote()->getBillingAddress();
            $shipping = $this->getReview()->getQuote()->getShippingAddress();

            $convertQuote = Mage::getModel('sales/convert_quote');
            /* @var $convertQuote Mage_Sales_Model_Convert_Quote */
            $order = Mage::getModel('sales/order');
            /* @var $order Mage_Sales_Model_Order */

            if ($this->getReview()->getQuote()->isVirtual()) {
                $order = $convertQuote->addressToOrder($billing);
            } else {
                $order = $convertQuote->addressToOrder($shipping);
            }

            $order->setBillingAddress($convertQuote->addressToOrderAddress($billing));
            $order->setShippingAddress($convertQuote->addressToOrderAddress($shipping));
            $order->setPayment($convertQuote->paymentToOrderPayment($this->getReview()->getQuote()->getPayment()));

            foreach ($this->getReview()->getQuote()->getAllItems() as $item) {
                $order->addItem($convertQuote->itemToOrderItem($item));
            }

            /**
             * We can use configuration data for declare new order status
             */
            Mage::dispatchEvent('checkout_type_onepage_save_order', array('order'=>$order, 'quote'=>$this->getReview()->getQuote()));

            //customer checkout from shopping cart page
            if (!$order->getCustomerEmail()) {
                $order->setCustomerEmail($shipping->getEmail());
            }

            $order->place();

            if (isset($customer) && $customer && $this->getReview()->getQuote()->getCheckoutMethod()==Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER) {
                $customer->save();
                $customer->setDefaultBilling($customerBilling->getId());
                $customerShippingId = isset($customerShipping) ? $customerShipping->getId() : $customerBilling->getId();
                $customer->setDefaultShipping($customerShippingId);
                $customer->save();

                $order->setCustomerId($customer->getId())
                    ->setCustomerEmail($customer->getEmail())
                    ->setCustomerPrefix($customer->getPrefix())
                    ->setCustomerFirstname($customer->getFirstname())
                    ->setCustomerMiddlename($customer->getMiddlename())
                    ->setCustomerLastname($customer->getLastname())
                    ->setCustomerSuffix($customer->getSuffix())
                    ->setCustomerGroupId($customer->getGroupId())
                    ->setCustomerTaxClassId($customer->getTaxClassId());

                $billing->setCustomerId($customer->getId())->setCustomerAddressId($customerBilling->getId());
                $shipping->setCustomerId($customer->getId())->setCustomerAddressId($customerShippingId);
            }

        } catch (Mage_Core_Exception $e){
            $error_message = $e->getMessage();
        } catch (Exception $e){
            if (isset($order)) {
                $error_message = $order->getErrors();
            } else {
                $error_message = $e->getMessage();
            }
        }

        if ($error_message) {
            $payPalSession->addError($e->getMessage());
            $this->_redirect('paypal/express/review');
            return;
        }

        $order->save();

        $this->getReview()->getQuote()->setIsActive(false);
        $this->getReview()->getQuote()->save();

        $orderId = $order->getIncrementId();
        $this->getReview()->getCheckout()->setLastQuoteId($this->getReview()->getQuote()->getId());
        $this->getReview()->getCheckout()->setLastSuccessQuoteId($this->getReview()->getQuote()->getId());
        $this->getReview()->getCheckout()->setLastOrderId($order->getId());
        $this->getReview()->getCheckout()->setLastRealOrderId($order->getIncrementId());

        $order->sendNewOrderEmail();

        $payPalSession->unsExpressCheckoutMethod();

        $this->_redirect('checkout/onepage/success');
    }

    /**
     * Method to update order if customer used PayPal Express
     * as payment method not a separate checkout from shopping cart
     *
     */
    public function updateOrderAction() {
        $error_message = '';
        $payPalSession = Mage::getSingleton('paypal/session');

        $order = Mage::getModel('sales/order')->load(Mage::getSingleton('checkout/session')->getLastOrderId());

        if ($order->getId()) {
            $comment = null;
            if ($order->canInvoice() && $this->getExpress()->getPaymentAction() == Mage_Paypal_Model_Api_Abstract::PAYMENT_TYPE_SALE) {
                $invoice = $order->prepareInvoice();
                $invoice->register()->capture();
                Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder())
                    ->save();

                $orderState = Mage_Sales_Model_Order::STATE_PROCESSING;
                $orderStatus = $this->getExpress()->getConfigData('order_status');
                $comment = Mage::helper('paypal')->__('Invoice #%s created', $invoice->getIncrementId());
            } else {
                $this->getExpress()->placeOrder($order->getPayment());

                $orderState = Mage_Sales_Model_Order::STATE_PROCESSING;
                $orderStatus = $this->getExpress()->getConfigData('order_status');
            }

            if (!$orderStatus) {
                $orderStatus = $order->getConfig()->getStateDefaultStatus($orderState);
            }
            if (!$comment) {
                $comment = Mage::helper('paypal')->__('Customer returned from PayPal site.');
            }

            $order->setState($orderState, $orderStatus, $comment, $notified = true);
            $order->save();

            Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false);
            Mage::getSingleton('checkout/session')->getQuote()->save();

            $order->sendNewOrderEmail();
        }

        $payPalSession->unsExpressCheckoutMethod();

        $this->_redirect('checkout/onepage/success');
    }

}