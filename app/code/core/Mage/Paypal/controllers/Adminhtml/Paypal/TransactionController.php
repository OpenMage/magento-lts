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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
