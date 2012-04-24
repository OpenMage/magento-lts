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
 * @package     Mage_Shipping
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shipping data helper
 */
class Mage_Shipping_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Allowed hash keys
     *
     * @var array
     */
    protected $_allowedHashKeys = array('ship_id', 'order_id', 'track_id');

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
            return array('key' => $hash[0], 'id' => (int)$hash[1], 'hash' => $hash[2]);
        }
        return array();
    }

    /**
     * Retrieve tracking url with params
     *
     * @deprecated the non-model usage
     *
     * @param  string $key
     * @param  integer|Mage_Sales_Model_Order|Mage_Sales_Model_Order_Shipment|Mage_Sales_Model_Order_Shipment_Track $model
     * @param  string $method - option
     * @return string
     */
    protected function _getTrackingUrl($key, $model, $method = 'getId')
    {
         if (empty($model)) {
             $param = array($key => ''); // @deprecated after 1.4.0.0-alpha3
         } else if (!is_object($model)) {
             $param = array($key => $model); // @deprecated after 1.4.0.0-alpha3
         } else {
             $param = array(
                 'hash' => Mage::helper('core')->urlEncode("{$key}:{$model->$method()}:{$model->getProtectCode()}")
             );
         }
         $storeId = is_object($model) ? $model->getStoreId() : null;
         $storeModel = Mage::app()->getStore($storeId);
         return $storeModel->getUrl('shipping/tracking/popup', $param);
    }

    /**
     * @deprecated after 1.4.0.0-alpha3
     * Retrieve tracking pop up url by order id or object
     *
     * @param  int|Mage_Sales_Model_Order $order
     * @return string
     */
    public function getTrackingPopUpUrlByOrderId($order = '')
    {
        if ($order && !is_object($order)) {
            $order = Mage::getModel('sales/order')->load($order);
        }
        return $this->_getTrackingUrl('order_id', $order);
    }

    /**
     * @deprecated after 1.4.0.0-alpha3
     * Retrieve tracking pop up url by track id or object
     *
     * @param  int|Mage_Sales_Model_Order_Shipment_Track $track
     * @return string
     */
    public function getTrackingPopUpUrlByTrackId($track = '')
    {
        if ($track && !is_object($track)) {
            $track = Mage::getModel('sales/order_shipment_track')->load($track);
        }
        return $this->_getTrackingUrl('track_id', $track, 'getEntityId');
    }

    /**
     * @deprecated after 1.4.0.0-alpha3
     * Retrieve tracking pop up url by ship id or object
     *
     * @param  int|Mage_Sales_Model_Order_Shipment $track
     * @return string
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
