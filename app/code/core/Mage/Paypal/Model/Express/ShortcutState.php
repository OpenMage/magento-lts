<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

/**
 * Owns the per-attempt PayPal Express shortcut state: order id, reserved order id, request id, and the
 * locked currency. Each value is mirrored on the quote payment's additional_information AND on the checkout
 * session, so reads always check the payment first and fall back to the session — a Buy-Now quote that
 * hasn't been saved yet only has the session copy.
 */
class Mage_Paypal_Model_Express_ShortcutState extends Mage_Core_Model_Abstract
{
    public function getOrderId(Mage_Sales_Model_Quote $quote): string
    {
        return $this->_read($quote, Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_ORDER_ID);
    }

    public function getReservedOrderId(Mage_Sales_Model_Quote $quote): string
    {
        return $this->_read($quote, Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_RESERVED_ORDER_ID);
    }

    public function getLockedCurrency(Mage_Sales_Model_Quote $quote): string
    {
        return $this->_read($quote, Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_CURRENCY);
    }

    public function getRequestId(Mage_Sales_Model_Quote $quote): string
    {
        return $this->_read($quote, Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_REQUEST_ID);
    }

    /**
     * Persist the per-attempt state to both the quote payment and the checkout session. The request id,
     * reserved order id, and quote currency are derived from the quote so callers only pass the PayPal
     * order id they just received.
     */
    public function store(Mage_Sales_Model_Quote $quote, string $orderId): void
    {
        $payment = $quote->getPayment();
        $requestId = (string) $payment->getAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_REQUEST_ID);
        $reservedOrderId = (string) $quote->getReservedOrderId();
        $currency = $this->_resolveQuoteCurrency($quote);

        $payment->setAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_ORDER_ID, $orderId)
            ->setAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_REQUEST_ID, $requestId)
            ->setAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_RESERVED_ORDER_ID, $reservedOrderId)
            ->setAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_CURRENCY, $currency)
            ->save();

        $this->_getCheckoutSession()
            ->setData(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_ORDER_ID, $orderId)
            ->setData(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_REQUEST_ID, $requestId)
            ->setData(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_RESERVED_ORDER_ID, $reservedOrderId)
            ->setData(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_CURRENCY, $currency);
    }

    /**
     * Drop the per-attempt state from both the quote payment and the checkout session, and reset any
     * transient transaction state on the payment so a retry starts clean.
     */
    public function clear(Mage_Sales_Model_Quote $quote): void
    {
        $payment = $quote->getPayment();
        foreach ($this->_getKeys() as $key) {
            $payment->unsAdditionalInformation($key);
        }

        $payment->setPaypalCorrelationId(null)
            ->setTransactionId(null)
            ->setIsTransactionClosed(false);
        if ((int) $quote->getId() > 0) {
            $payment->save();
        }

        $session = $this->_getCheckoutSession();
        foreach ($this->_getKeys() as $key) {
            $session->unsetData($key);
        }
    }

    /**
     * @return string[]
     */
    private function _getKeys(): array
    {
        return [
            Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,
            Mage_Paypal_Model_Payment::PAYPAL_REQUEST_ID,
            Mage_Paypal_Model_Payment::PAYPAL_PAYMENT_SOURCE,
            Mage_Paypal_Model_Payment::PAYPAL_PAYMENT_AUTHORIZATION_ID,
            Mage_Paypal_Model_Payment::PAYPAL_PAYMENT_AUTHORIZATION_REAUTHORIZED,
            Mage_Paypal_Model_Payment::PAYPAL_CAPTURED_AMOUNT,
            Mage_Paypal_Model_Transaction::PAYPAL_PAYMENT_STATUS,
            Mage_Paypal_Model_Transaction::PAYPAL_PAYMENT_AUTHORIZATION_ID,
            Mage_Paypal_Model_Transaction::PAYPAL_PAYMENT_AUTHORIZATION_EXPIRATION_TIME,
            Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_ORDER_ID,
            Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_REQUEST_ID,
            Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_RESERVED_ORDER_ID,
            Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_CURRENCY,
        ];
    }

    private function _resolveQuoteCurrency(Mage_Sales_Model_Quote $quote): string
    {
        $currency = $quote->getOrderCurrencyCode();
        if ($currency === null || $currency === '') {
            $currency = $quote->getQuoteCurrencyCode();
        }
        return (string) $currency;
    }

    private function _read(Mage_Sales_Model_Quote $quote, string $key): string
    {
        $value = (string) $quote->getPayment()->getAdditionalInformation($key);
        if ($value === '') {
            $value = (string) $this->_getCheckoutSession()->getData($key);
        }
        return $value;
    }

    private function _getCheckoutSession(): Mage_Checkout_Model_Session
    {
        return Mage::getSingleton('checkout/session');
    }
}
