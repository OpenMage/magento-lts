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
 * @category   Mage
 * @package    Mage_Shipping
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Shipping_Block_Tracking_Popup extends Mage_Core_Block_Template
{

    protected $_track_id;
    protected $_order_id;
    protected $_ship_id;

    public function setOrderId($oid)
    {
        $this->_order_id=$oid;
    }

    public function getOrderId()
    {
        return $this->_order_id;
    }

    public function setShipId($oid)
    {
        $this->_ship_id=$oid;
    }

    public function getShipId()
    {
        return $this->_ship_id;
    }

    public function setTrackId($tid='')
    {
        $this->_track_id=$tid;
    }

    public function getTrackId()
    {
        return $this->_track_id;
    }

     /**
     * Initialize order model instance
     *
     * @return Mage_Sales_Model_Order || false
     */
    protected function _initOrder()
    {
        $order = Mage::getModel('sales/order')->load($this->_order_id);

        if (!$order->getId()) {
            return false;
        }

        return $order;
    }

    /**
     * Initialize ship model instance
     *
     * @return Mage_Sales_Model_Order_Shipment || false
     */
    protected function _initShipment()
    {
        $ship = Mage::getModel('sales/order_shipment')->load($this->_ship_id);

        if (!$ship->getEntityId()) {
            return false;
        }

        return $ship;
    }


    public function getTrackingInfo()
    {
        $this->setOrderId($this->getRequest()->getParam('order_id'));
        $this->setTrackId($this->getRequest()->getParam('track_id'));
        $this->setShipId($this->getRequest()->getParam('ship_id'));

        if ($this->getOrderId()>0) {
            return $this->getTrackingInfoByOrder();
        } elseif($this->getShipId()>0) {
            return $this->getTrackingInfoByShip();
        } else {
            return $this->getTrackingInfoByTrackId();
        }
    }

    /*
    * retrieve all tracking by orders id
    */
    public function getTrackingInfoByOrder()
    {
        $shipTrack = array();
        if ($order = $this->_initOrder()) {
            $shipments = $order->getShipmentsCollection();
            foreach ($shipments as $shipment){
                $increment_id = $shipment->getIncrementId();
                $tracks = $shipment->getTracksCollection();

                $trackingInfos=array();
                foreach ($tracks as $track){
                    $trackingInfos[] = $track->getNumberDetail();
                }
                $shipTrack[$increment_id] = $trackingInfos;
            }
        }
        return $shipTrack;
    }

    /*
    * retrieve all tracking by ship id
    */
    public function getTrackingInfoByShip()
    {
        $shipTrack = array();
        if ($shipment = $this->_initShipment()) {
            $increment_id = $shipment->getIncrementId();
            $tracks = $shipment->getTracksCollection();

            $trackingInfos=array();
            foreach ($tracks as $track){
                $trackingInfos[] = $track->getNumberDetail();
            }
            $shipTrack[$increment_id] = $trackingInfos;

        }
        return $shipTrack;
    }

    /*
    * retrieve tracking by tracking entity id
    */
    public function getTrackingInfoByTrackId()
    {
        $shipTrack[] = array(Mage::getModel('sales/order_shipment_track')->load($this->getTrackId())
                       ->getNumberDetail());
        return $shipTrack;
    }

    /*
    * change date format to mm/dd/Y hh:mm AM/PM
    */
    public function formatDeliveryDateTime($date,$time)
    {
        return Mage::app()->getLocale()->date(strtotime($date.' '.$time),Zend_Date::TIMESTAMP)->toString('MM/dd/YYYY hh:mm a');
    }

     /*
    * change date format to mm/dd/Y
    */
    public function formatDeliveryDate($date)
    {
        return Mage::app()->getLocale()->date(strtotime($date),Zend_Date::TIMESTAMP)->toString('MM/dd/YYYY');
    }

       /*
    * change date format to mm/dd/Y
    */
    public function formatDeliveryTime($time)
    {
        return Mage::app()->getLocale()->date(strtotime($time),Zend_Date::TIMESTAMP)->toString('hh:mm a');
    }

    public function getStoreSupportEmail()
    {
        return Mage::getStoreConfig('trans_email/ident_support/email');
    }

    public function getContactUs()
    {
        return $this->getUrl('contacts');
    }

}
