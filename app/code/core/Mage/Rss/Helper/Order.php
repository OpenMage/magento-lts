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
        if (Mage::getStoreConfig('rss/order/status_notified')) {
            return true;
        }
        return false;
    }

    /**
     * Retrieve order status history url
     *
     * @param Mage_Sales_Model_Order $order
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
     * Retrieve order status url key
     *
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    public function getStatusUrlKey($order)
    {
        $data = [
            'order_id' => $order->getId(),
            'increment_id' => $order->getIncrementId(),
            'customer_id' => $order->getCustomerId(),
        ];
        return base64_encode(json_encode($data));
    }

    /**
     * Retrieve order instance by specified status url key
     *
     * @param string $key
     * @return Mage_Sales_Model_Order|null
     */
    public function getOrderByStatusUrlKey($key)
    {
        $data = json_decode(base64_decode($key), true);
        if (!is_array($data) || !isset($data['order_id']) || !isset($data['increment_id'])
            || !isset($data['customer_id'])
        ) {
            return null;
        }

        $orderId = (int) $data['order_id'];
        $incrementId = (int) $data['increment_id'];
        $customerId = (int) $data['customer_id'];

        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($orderId);

        if (!is_null($order->getId())
            && (int) $order->getIncrementId() === $incrementId
            && (int) $order->getCustomerId() === $customerId
        ) {
            return $order;
        }

        return null;
    }
}
