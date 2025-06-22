<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

/**
 * PayPal Transaction Controller
 */
class Mage_Paypal_Adminhtml_Paypal_TransactionController extends Mage_Adminhtml_Controller_Action
{
    private const PAYMENT_METHOD_CODE = 'paypal';

    /**
     * Reauthorize a PayPal payment for a specific order.
     */
    public function reauthorizeAction(): void
    {
        $orderId = (int) $this->getRequest()->getParam('order_id');
        if (!$orderId) {
            $this->_getSession()->addError($this->__('Order ID is required.'));
            $this->_redirect('*/*/sales_order');
            return;
        }

        try {
            $order = $this->_loadOrder($orderId);
            $payment = $order->getPayment();

            $this->_validatePaymentMethod($payment);

            $authorizationId = $payment->getAdditionalInformation(Mage_Paypal_Model_Paypal::PAYPAL_PAYMENT_AUTHORIZATION_ID);
            if (!$authorizationId) {
                Mage::throwException($this->__('This order does not have a PayPal authorization.'));
            }

            $result = $this->_getPaypalModel()->reauthorizePayment($authorizationId, $order);

            if (!empty($result['success'])) {
                $this->_getSession()->addSuccess(
                    $this->__('Payment successfully reauthorized. New authorization ID: %s', $result['authorization_id']),
                );
            } else {
                Mage::throwException($result['error'] ?? $this->__('Failed to reauthorize payment.'));
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->__('An unexpected error occurred.'));
        }

        $this->_redirect('*/sales_order/view', ['order_id' => $orderId]);
    }

    /**
     * Load order by ID or throw exception.
     *
     * @throws Mage_Core_Exception
     */
    protected function _loadOrder(int $orderId): Mage_Sales_Model_Order
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($orderId);
        if (!$order->getId()) {
            Mage::throwException($this->__('Order not found.'));
        }
        return $order;
    }

    /**
     * Validate if the payment method is PayPal.
     *
     * @throws Mage_Core_Exception
     */
    protected function _validatePaymentMethod(Mage_Sales_Model_Order_Payment $payment): void
    {
        if ($payment->getMethod() !== self::PAYMENT_METHOD_CODE) {
            Mage::throwException($this->__('This order was not placed using PayPal.'));
        }
    }

    /**
     * Get PayPal model instance.
     */
    protected function _getPaypalModel(): Mage_Paypal_Model_Paypal
    {
        return Mage::getModel('paypal/paypal');
    }

    /**
     * Check controller permissions.
     */
    protected function _isAllowed(): bool
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/order');
    }
}
