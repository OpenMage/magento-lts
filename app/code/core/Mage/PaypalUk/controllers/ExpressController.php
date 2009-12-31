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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Express Checkout Controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_PaypalUk_ExpressController extends Mage_Core_Controller_Front_Action
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
        return Mage::getSingleton('paypaluk/express');
    }

    /**
     * When a customer clicks Paypal button on shopping cart
     *
     */
    public function shortcutAction()
    {
        /*
        * set the express checkout
        * retrieve token
        */
        $this->getExpress()->shortcutSetExpressCheckout();
        /*
        * rediret to payapl
        */
        $this->getResponse()->setRedirect($this->getExpress()->getRedirectUrl());
    }

    /**
     * Redirect to paypal account, to edit transaction detail
     */
    public function editAction()
    {
        $this->getResponse()->setRedirect($this->getExpress()->getApi()->getPaypalUrl());
    }

    /*
    * when a user click on cancel on paypal need to redirect them to shopping cart
    */
    public function cancelAction()
    {
        $this->_redirect('checkout/cart');
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
            $this->_initLayoutMessages('paypaluk/session');
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
        return Mage::getSingleton('paypaluk/express_review');
    }

    /*
    when customer choose shipping method,loading this action
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
            $this->loadLayout('paypaluk_express_review_details');
            $this->getResponse()->setBody($this->getLayout()->getBlock('root')->toHtml());
        } else {
            $this->_redirect('paypaluk/express/review');
        }
    }

    /**
     * Action executed when 'Place Order' button pressed on review page
     *
     */
    public function saveOrderAction()
    {
        /*
        * 1- create order
        * 2- place order (call doexpress checkout)
        * 3- save order
        */
        $error_message = '';
        $payPalSession = Mage::getSingleton('paypaluk/session');

        try {
            $address = $this->getReview()->getQuote()->getShippingAddress();
            if (!$address->getShippingMethod()) {
                if ($shippingMethod = $this->getRequest()->getParam('shipping_method')) {
                    $this->getReview()->saveShippingMethod($shippingMethod);
                 } else if (!$this->getReview()->getQuote()->getIsVirtual()) {
                    $payPalSession->addError(Mage::helper('paypal')->__('Please select a valid shipping method'));
                    $this->_redirect('paypaluk/express/review');
                    return;
                }
            }
            $service = Mage::getModel('sales/service_quote', $this->getReview()->getQuote());
            $order = $service->submit();
        } catch (Mage_Core_Exception $e){
            $payPalSession->addError($e->getMessage());
            $this->_redirect('paypaluk/express/review');
            return;
        } catch (Exception $e){
            $payPalSession->addError($e->getMessage());
            $this->_redirect('paypaluk/express/review');
            return;
        }

        if ($order->hasInvoices() && $this->getExpress()->canSendEmailCopy()) {
            foreach ($order->getInvoiceCollection() as $invoice) {
                $invoice->sendEmail()->setEmailSent(true);
            }
        }
        $order->sendNewOrderEmail();

        $this->getReview()->getQuote()->setIsActive(false);
        $this->getReview()->getQuote()->save();

        $orderId = $order->getIncrementId();
        $this->getReview()->getCheckout()->setLastQuoteId($this->getReview()->getQuote()->getId());
        $this->getReview()->getCheckout()->setLastSuccessQuoteId($this->getReview()->getQuote()->getId());
        $this->getReview()->getCheckout()->setLastOrderId($order->getId());
        $this->getReview()->getCheckout()->setLastRealOrderId($order->getIncrementId());

        $payPalSession->unsExpressCheckoutMethod();

        $this->_redirect('checkout/onepage/success');
    }

    /**
     * When there's an API error
     *
     */
    public function errorAction()
    {
        $this->_redirect('checkout/cart');
    }

    /**
     * Method to update order if customer used PayPal Express
     * as payment method not a separate checkout from shopping cart
     *
     */
    public function updateOrderAction()
    {
        $error_message = '';
        $payPalSession = Mage::getSingleton('paypal/session');
        if ($orderId = Mage::getSingleton('checkout/session')->getLastOrderId()) {
            try{
                $this->getExpress()->updateOrder($orderId);
            } catch (Mage_Core_Exception $e) {
                $payPalSession->addError($e->getMessage());
                $this->_redirect('paypaluk/express/review');
                return;
            } catch (Exception $e) {
                Mage::helper('checkout')->sendPaymentFailedEmail($this->getReview()->getQuote(), $e->getMessage());
                $payPalSession->addError(Mage::helper('paypal')->__('There was an error processing your order. Please contact us or try again later.'));
                $this->_redirect('paypaluk/express/review');
                return;
            }
            $this->getReview()->getQuote()->setIsActive(false);
            $this->getReview()->getQuote()->save();
        }
        $payPalSession->unsExpressCheckoutMethod();
        $this->_redirect('checkout/onepage/success');
    }

}
