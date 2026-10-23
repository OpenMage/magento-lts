<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Checkout workflow helper
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Helper_Checkout extends Mage_Core_Helper_Abstract
{
    /**
     * Checkout session key holding the nonce that authorises a gateway cancel/decline return
     */
    public const SESSION_KEY_CANCEL_NONCE = 'paypal_cancel_nonce';

    /**
     * Route parameter carrying the cancel nonce on gateway return URLs
     */
    public const CANCEL_NONCE_PARAM = 'nonce';

    protected $_moduleName = 'Mage_Paypal';

    /**
     * Return the cancel nonce for an order, minting one if the session holds none for it
     *
     * Gateway cancel/decline return URLs are entered as cross-site GET (or POST) requests, so they
     * cannot carry a form key. The nonce is minted when the return URLs are handed to the gateway
     * and is echoed back in the URL path, which proves that the request is the gateway return for
     * this very order and not a forged request riding the customer's session.
     *
     * @param  string $orderIncrementId
     * @return string
     */
    public function getCancelNonce($orderIncrementId)
    {
        $session = $this->_getCheckoutSession();
        $data = $session->getData(self::SESSION_KEY_CANCEL_NONCE);
        if (is_array($data)
            && isset($data['order'], $data['nonce'])
            && (string) $data['order'] === (string) $orderIncrementId
        ) {
            return (string) $data['nonce'];
        }

        $nonce = bin2hex(random_bytes(16));
        $session->setData(self::SESSION_KEY_CANCEL_NONCE, [
            'order' => (string) $orderIncrementId,
            'nonce' => $nonce,
        ]);

        return $nonce;
    }

    /**
     * Add the cancel nonce of an order to an already built gateway return URL
     *
     * @param  string $url
     * @param  string $orderIncrementId
     * @return string
     */
    public function addCancelNonceToUrl($url, $orderIncrementId)
    {
        return $this->appendNonceToUrl((string) $url, $this->getCancelNonce($orderIncrementId));
    }

    /**
     * Append the nonce as a path parameter, before any query string or fragment
     *
     * A path parameter is used rather than a query parameter because gateways that post back to
     * the return URL, or that append their own query parameters, keep the path intact.
     *
     * @param  string $url
     * @param  string $nonce
     * @return string
     */
    public function appendNonceToUrl($url, $nonce)
    {
        $suffix = '';
        $position = strcspn($url, '?#');
        if ($position < strlen($url)) {
            $suffix = substr($url, $position);
            $url = substr($url, 0, $position);
        }

        return rtrim($url, '/') . '/' . self::CANCEL_NONCE_PARAM . '/' . rawurlencode((string) $nonce) . '/' . $suffix;
    }

    /**
     * Whether a gateway cancel/decline return may cancel the last placed order and restore its quote
     *
     * The last placed order must have been paid with the given method, and the request must carry
     * the nonce minted for that order. On success the nonce is consumed.
     *
     * @param  null|string     $nonce       nonce from the request
     * @param  string|string[] $methodCodes payment method(s) handled by the calling controller
     * @return bool
     */
    public function canCancelLastRealOrder($nonce, $methodCodes)
    {
        $session = $this->_getCheckoutSession();
        $orderIncrementId = (string) $session->getLastRealOrderId();
        if ($orderIncrementId === '') {
            return false;
        }

        $order = $session->getLastRealOrder();
        if (!$order || !$order->getId() || !$order->getPayment()
            || !in_array($order->getPayment()->getMethod(), (array) $methodCodes, true)
        ) {
            return false;
        }

        return $this->validateCancelNonce($nonce, $orderIncrementId);
    }

    /**
     * Check the request nonce against the one minted for the given order and consume it on success
     *
     * @param  null|string $nonce
     * @param  string      $orderIncrementId
     * @return bool
     */
    public function validateCancelNonce($nonce, $orderIncrementId)
    {
        if (!is_string($nonce) || $nonce === '') {
            return false;
        }

        $session = $this->_getCheckoutSession();
        $data = $session->getData(self::SESSION_KEY_CANCEL_NONCE);
        if (!is_array($data) || !isset($data['order'], $data['nonce'])
            || (string) $data['order'] !== (string) $orderIncrementId
            || !hash_equals((string) $data['nonce'], $nonce)
        ) {
            return false;
        }

        $session->unsetData(self::SESSION_KEY_CANCEL_NONCE);

        return true;
    }

    /**
     * Restore last active quote based on checkout session
     *
     * @return bool True if quote restored successfully, false otherwise
     */
    public function restoreQuote()
    {
        $order = $this->_getCheckoutSession()->getLastRealOrder();
        if ($order->getId()) {
            $quote = $this->_getQuote($order->getQuoteId());
            if ($quote->getId()) {
                $quote->setIsActive(1)
                    ->setReservedOrderId(null)
                    ->save();
                $this->_getCheckoutSession()
                    ->replaceQuote($quote)
                    ->unsLastRealOrderId();
                return true;
            }
        }

        return false;
    }

    /**
     * Cancel last placed order with specified comment message
     *
     * @param  string $comment Comment appended to order history
     * @return bool   True if order cancelled, false otherwise
     */
    public function cancelCurrentOrder($comment)
    {
        $order = $this->_getCheckoutSession()->getLastRealOrder();
        if ($order->getId() && $order->getState() != Mage_Sales_Model_Order::STATE_CANCELED) {
            $order->registerCancellation($comment)->save();
            return true;
        }

        return false;
    }

    /**
     * Return checkout session instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Return sales quote instance for specified ID
     *
     * @param  int                    $quoteId Quote identifier
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote($quoteId)
    {
        return Mage::getModel('sales/quote')->load($quoteId);
    }
}
