<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Shipping
 */

/**
 * @package    Mage_Shipping
 */
class Mage_Shipping_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Shipping';

    /**
     * Allowed hash keys
     *
     * @var array
     */
    protected $_allowedHashKeys = ['ship_id', 'order_id', 'track_id'];

    /**
     * Decode url hash
     *
     * @param  string $hash
     * @return array
     */
    public function decodeTrackingHash($hash)
    {
        $hash = explode(':', Mage::helper('core')->urlDecode($hash));
        if (count($hash) === 3 && in_array($hash[0], $this->_allowedHashKeys)) {
            return ['key' => $hash[0], 'id' => (int) $hash[1], 'hash' => $hash[2]];
        }

        return [];
    }

    /**
     * Retrieve tracking url with params
     *
     * @deprecated the non-model usage
     *
     * @param  string $key
     * @param  int|Mage_Sales_Model_Order|Mage_Sales_Model_Order_Shipment|Mage_Sales_Model_Order_Shipment_Track $model
     * @param  string $method - option
     * @return string
     */
    protected function _getTrackingUrl($key, $model, $method = 'getId')
    {
        if (empty($model)) {
            $param = [$key => '']; // @deprecated after 1.4.0.0-alpha3
        } elseif (!is_object($model)) {
            $param = [$key => $model]; // @deprecated after 1.4.0.0-alpha3
        } else {
            $param = [
                'hash' => Mage::helper('core')->urlEncode("{$key}:{$model->$method()}:{$model->getProtectCode()}"),
            ];
        }

        $storeId = is_object($model) ? $model->getStoreId() : null;
        $storeModel = Mage::app()->getStore($storeId);
        return $storeModel->getUrl('shipping/tracking/popup', $param);
    }

    /**
     * @param string $order
     * @return string
     * @deprecated after 1.4.0.0-alpha3
     * Retrieve tracking pop up url by order id or object
     *
     */
    public function getTrackingPopUpUrlByOrderId($order = '')
    {
        if ($order && !is_object($order)) {
            $order = Mage::getModel('sales/order')->load($order);
        }

        return $this->_getTrackingUrl('order_id', $order);
    }

    /**
     * @param string $track
     * @return string
     * @deprecated after 1.4.0.0-alpha3
     * Retrieve tracking pop up url by track id or object
     *
     */
    public function getTrackingPopUpUrlByTrackId($track = '')
    {
        if ($track && !is_object($track)) {
            $track = Mage::getModel('sales/order_shipment_track')->load($track);
        }

        return $this->_getTrackingUrl('track_id', $track, 'getEntityId');
    }

    /**
     * @param string $ship
     * @return string
     * @deprecated after 1.4.0.0-alpha3
     * Retrieve tracking pop up url by ship id or object
     *
     */
    public function getTrackingPopUpUrlByShipId($ship = '')
    {
        if ($ship && !is_object($ship)) {
            $ship = Mage::getModel('sales/order_shipment')->load($ship);
        }

        return $this->_getTrackingUrl('ship_id', $ship);
    }

    /**
     * Shipping tracking popup URL getter
     *
     * @param Mage_Sales_Model_Abstract $model
     * @return string
     */
    public function getTrackingPopupUrlBySalesModel($model)
    {
        if ($model instanceof Mage_Sales_Model_Order) {
            return $this->_getTrackingUrl('order_id', $model);
        } elseif ($model instanceof Mage_Sales_Model_Order_Shipment) {
            return $this->_getTrackingUrl('ship_id', $model);
        } elseif ($model instanceof Mage_Sales_Model_Order_Shipment_Track) {
            return $this->_getTrackingUrl('track_id', $model, 'getEntityId');
        }

        return '';
    }

    /**
     * Retrieve tracking ajax url
     *
     * @return string
     */
    public function getTrackingAjaxUrl()
    {
        return $this->_getUrl('shipping/tracking/ajax');
    }

    /**
     * @param string $method
     * @param int $storeId
     * @return bool
     */
    public function isFreeMethod($method, $storeId = null)
    {
        $arr = explode('_', $method, 2);
        if (!isset($arr[1])) {
            return false;
        }

        $freeMethod = Mage::getStoreConfig('carriers/' . $arr[0] . '/free_method', $storeId);
        return $freeMethod == $arr[1];
    }
}
