<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

use PaypalServerSdkLib\Models\CheckoutPaymentIntent;

/**
 * PayPal Payment Method Model
 *
 * Main payment method interface that delegates to specialized classes
 */
class Mage_Paypal_Model_Paypal extends Mage_Payment_Model_Method_Abstract
{
    // Payment method configuration
    protected $_code = 'paypal';
    protected $_formBlockType = 'paypal/form';
    protected $_infoBlockType = 'paypal/adminhtml_info';
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid = true;
    protected $_canUseForMultishipping = false;
    protected $_canUseInternal = false;
    protected $_isGateway = true;
    protected $_canUseCheckout = true;

    /**
     * Create PayPal order via API
     *
     * @param Mage_Sales_Model_Quote $quote Customer quote
     * @return array{success: bool, id?: string, error?: string}
     * @throws Mage_Core_Exception
     */
    public function create(Mage_Sales_Model_Quote $quote): array
    {
        return $this->getOrderHandler()->createOrder($quote);
    }

    /**
     * Capture PayPal payment via API
     *
     * @param string $orderId PayPal order ID
     * @throws Mage_Core_Exception
     */
    public function captureOrder(string $orderId, Mage_Sales_Model_Quote|Mage_Sales_Model_Order $quote): void
    {
        $this->getPaymentProcessor()->captureOrder($orderId, $quote);
    }

    /**
     * Authorize PayPal payment via API
     *
     * @param string $orderId PayPal order ID
     * @throws Mage_Core_Exception
     */
    public function authorizePayment(string $orderId, Mage_Sales_Model_Quote $quote): void
    {
        $this->getPaymentProcessor()->authorizePayment($orderId, $quote);
    }

    /**
     * Reauthorize a payment after the initial authorization has expired
     *
     * @param string $orderId PayPal order ID
     * @param Mage_Sales_Model_Order $order Magento order
     * @return array{success: bool, authorization_id?: string, error?: string}
     */
    public function reauthorizePayment(string $orderId, Mage_Sales_Model_Order $order): array
    {
        return $this->getPaymentProcessor()->reauthorizePayment($orderId, $order);
    }

    /**
     * Validate payment method information object
     */
    public function validate(): static
    {
        parent::validate();
        return $this;
    }

    /**
     * Refund payment method
     *
     * @param Varien_Object $payment Payment object
     * @param float $amount Refund amount
     * @throws Mage_Core_Exception
     */
    public function refund(Varien_Object $payment, $amount): static
    {
        $this->getPaymentProcessor()->processRefund($payment, $amount);
        return $this;
    }

    /**
     * Capture payment method
     *
     * @param Varien_Object $payment Payment object
     * @param float $amount Capture amount
     * @throws Mage_Core_Exception
     */
    public function capture(Varien_Object $payment, $amount): static
    {
        $this->getPaymentProcessor()->processCapture($payment, $amount);
        return $this;
    }

    /**
     * Void payment method
     *
     * @param Varien_Object $payment Payment object
     * @throws Mage_Core_Exception
     */
    public function void(Varien_Object $payment): static
    {
        $this->getPaymentProcessor()->processVoid($payment);
        return $this;
    }

    /**
     * Cancel payment method
     *
     * @param Varien_Object $payment Payment object
     * @throws Mage_Core_Exception
     */
    public function cancel(Varien_Object $payment): static
    {
        $this->getPaymentProcessor()->processCancel($payment);
        return $this;
    }

    /**
     * Check if payment method is available
     *
     * @param Mage_Sales_Model_Quote|null $quote
     */
    public function isAvailable($quote = null): bool
    {
        if (!$this->getConfigData('client_id') || !$this->getConfigData('client_secret')) {
            return false;
        }

        $this->getHelper()->handleMultishippingNotification($quote);

        return parent::isAvailable($quote);
    }

    /**
     * Get PayPal Order Handler instance
     */
    private function getOrderHandler(): Mage_Paypal_Model_Order
    {
        return Mage::getSingleton('paypal/order');
    }

    /**
     * Get PayPal Payment Processor instance
     */
    private function getPaymentProcessor(): Mage_Paypal_Model_Payment
    {
        return Mage::getSingleton('paypal/payment');
    }

    /**
     * Get PayPal Helper instance
     */
    private function getHelper(): Mage_Paypal_Model_Helper
    {
        return Mage::getSingleton('paypal/helper');
    }
}
