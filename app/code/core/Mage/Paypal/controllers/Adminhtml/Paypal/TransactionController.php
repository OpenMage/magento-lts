<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * PayPal Transaction Controller
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @author     Magento Core Team <core@magento.com>
 */
class Mage_Paypal_Adminhtml_Paypal_TransactionController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Reauthorize a payment
     */
    public function reauthorizeAction()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if (!$orderId) {
            $this->_getSession()->addError($this->__('Order ID is required'));
            $this->_redirect('*/sales_order/');
            return;
        }

        try {
            $order = Mage::getModel('sales/order')->load($orderId);
            if (!$order->getId()) {
                throw new Exception($this->__('Order not found'));
            }

            $payment = $order->getPayment();
            if ($payment->getMethod() !== 'paypal') {
                throw new Exception($this->__('This order was not placed using PayPal'));
            }

            $authorizationId = $payment->getAdditionalInformation(Mage_Paypal_Model_Paypal::PAYPAL_PAYMENT_AUTHORIZATION_ID);
            if (!$authorizationId) {
                throw new Exception($this->__('This order does not have a PayPal authorization'));
            }

            $paypalModel = Mage::getModel('paypal/paypal');
            $result = $paypalModel->reauthorizePayment($authorizationId, $order);

            if ($result['success']) {
                $this->_getSession()->addSuccess($this->__('Payment has been successfully reauthorized. New authorization ID: %s', $result['authorization_id']));
            } else {
                throw new Exception($result['error'] ?? $this->__('Failed to reauthorize payment'));
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirect('*/sales_order/view', ['order_id' => $orderId]);
    }

    /**
     * Check if the current user is allowed to access this controller
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
