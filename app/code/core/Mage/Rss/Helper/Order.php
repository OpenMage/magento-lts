<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Rss
 */

/**
 * Default rss helper
 *
 * @package    Mage_Rss
 */
class Mage_Rss_Helper_Order extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Rss';

    /**
     * Check whether status notification is allowed
     *
     * @return bool
     */
    public function isStatusNotificationAllow()
    {
        return Mage::getStoreConfigFlag('rss/order/status_notified');
    }

    /**
     * Retrieve order status history url
     *
     * @param  Mage_Sales_Model_Order $order
     * @return string
     */
    public function getStatusHistoryRssUrl($order)
    {
        return $this->_getUrl(
            'rss/order/status',
            ['_secure' => true, '_query' => ['data' => $this->getStatusUrlKey($order)]],
        );
    }

    /**
     * Derive the digest that authenticates an order status feed URL
     *
     * Keyed with the order's own protect_code -- the same per-order secret that
     * already authenticates the guest order view -- so no new secret has to be
     * introduced or managed. The secret is used as an HMAC key rather than
     * carried in the URL verbatim: a feed URL travels through Referer headers,
     * browser history, proxy logs and third-party feed readers, and must not
     * itself become a reusable guest-order credential.
     *
     * @param  Mage_Sales_Model_Order $order
     * @return string
     */
    protected function _getStatusUrlDigest($order)
    {
        $message = implode('|', [
            (string) $order->getId(),
            (string) $order->getIncrementId(),
            (string) $order->getCustomerId(),
        ]);

        return hash_hmac('sha256', $message, (string) $order->getProtectCode());
    }

    /**
     * Retrieve order status url key
     *
     * @param  Mage_Sales_Model_Order $order
     * @return string
     */
    public function getStatusUrlKey($order)
    {
        $data = [
            'order_id' => $order->getId(),
            'increment_id' => $order->getIncrementId(),
            'customer_id' => $order->getCustomerId(),
            'hash' => $this->_getStatusUrlDigest($order),
        ];
        return base64_encode(json_encode($data));
    }

    /**
     * Retrieve order instance by specified status url key
     *
     * @param  string                      $key
     * @return null|Mage_Sales_Model_Order
     */
    public function getOrderByStatusUrlKey($key)
    {
        $data = json_decode((string) base64_decode($key, true), true);
        if (!is_array($data)
            || !isset($data['order_id'], $data['increment_id'], $data['customer_id'], $data['hash'])
            || !is_string($data['hash'])
        ) {
            return null;
        }

        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load((int) $data['order_id']);

        // Fail closed on an order that carries no protect_code: an empty HMAC key
        // would otherwise let anyone who can guess the identifiers derive the digest.
        if (!$order->getId() || (string) $order->getProtectCode() === '') {
            return null;
        }

        // The digest is recomputed from the stored order, never from the values in
        // the URL, so a forged payload cannot authenticate itself.
        if (!hash_equals($this->_getStatusUrlDigest($order), $data['hash'])) {
            return null;
        }

        // increment_id is compared as a string: stores using a non-numeric increment
        // prefix cast to 0 under the previous (int) comparison and never matched.
        if ((string) $order->getIncrementId() !== (string) $data['increment_id']
            || (int) $order->getCustomerId() !== (int) $data['customer_id']
        ) {
            return null;
        }

        return $order;
    }
}
