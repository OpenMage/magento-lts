<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Shipping
 */

use Carbon\Carbon;

/**
 * Class Mage_Shipping_Block_Tracking_Popup
 *
 * @package    Mage_Shipping
 *
 * @method string getProtectCode()
 *
 * @SuppressWarnings("PHPMD.CamelCasePropertyName")
 */
class Mage_Shipping_Block_Tracking_Popup extends Mage_Core_Block_Template
{
    /**
     * @deprecated after 1.3.2.3
     */
    protected $_track_id;

    /**
     * @deprecated after 1.3.2.3
     */
    protected $_order_id;

    /**
     * @deprecated after 1.3.2.3
     */
    protected $_ship_id;

    /**
     * @param  int   $oid
     * @return $this
     */
    #[Deprecated(since: '1.3.2.3')]
    public function setOrderId($oid)
    {
        return $this->setData('order_id', $oid);
    }

    #[Deprecated(since: '1.3.2.3')]
    public function getOrderId()
    {
        return $this->_getData('order_id');
    }

    /**
     * @param  int   $oid
     * @return $this
     */
    #[Deprecated(since: '1.3.2.3')]
    public function setShipId($oid)
    {
        return $this->setData('ship_id', $oid);
    }

    #[Deprecated(since: '1.3.2.3')]
    public function getShipId()
    {
        return $this->_getData('ship_id');
    }

    /**
     * @param  string $tid
     * @return $this
     */
    #[Deprecated(since: '1.3.2.3')]
    public function setTrackId($tid = '')
    {
        return $this->setData('track_id', $tid);
    }

    #[Deprecated(since: '1.3.2.3')]
    public function getTrackId()
    {
        return $this->_getData('track_id');
    }

    /**
     * @return false|Mage_Sales_Model_Order
     */
    #[Deprecated(message: <<<'TXT'
    after 1.4.0.0-alpha3
     Initialize order model instance
    TXT)]
    protected function _initOrder()
    {
        $order = Mage::getModel('sales/order')->load($this->getOrderId());

        if (!$order->getId() || $this->getProtectCode() != $order->getProtectCode()) {
            return false;
        }

        return $order;
    }

    /**
     * @return false|Mage_Sales_Model_Order_Shipment
     */
    #[Deprecated(message: <<<'TXT'
    after 1.4.0.0-alpha3
     Initialize ship model instance
    TXT)]
    protected function _initShipment()
    {
        $ship = Mage::getModel('sales/order_shipment')->load($this->getShipId());

        if (!$ship->getEntityId() || $this->getProtectCode() != $ship->getProtectCode()) {
            return false;
        }

        return $ship;
    }

    /**
     * Retrieve array of tracking info
     *
     * @return array
     */
    public function getTrackingInfo()
    {
        /** @var Mage_Shipping_Model_Info $info */
        $info = Mage::registry('current_shipping_info');

        return $info->getTrackingInfo();
    }

    /**
     * @return array
     */
    #[Deprecated(message: <<<'TXT'
    after 1.4.0.0-alpha3
     Retrieve all tracking by orders id
    TXT)]
    public function getTrackingInfoByOrder()
    {
        $shipTrack = [];
        if ($order = $this->_initOrder()) {
            $shipments = $order->getShipmentsCollection();
            /** @var Mage_Sales_Model_Order_Shipment $shipment */
            foreach ($shipments as $shipment) {
                $incrementId = $shipment->getIncrementId();
                $tracks = $shipment->getTracksCollection();

                $trackingInfos = [];
                foreach ($tracks as $track) {
                    $trackingInfos[] = $track->getNumberDetail();
                }

                $shipTrack[$incrementId] = $trackingInfos;
            }
        }

        return $shipTrack;
    }

    /**
     * @return array
     */
    #[Deprecated(message: <<<'TXT'
    after 1.4.0.0-alpha3
     Retrieve all tracking by ship id
    TXT)]
    public function getTrackingInfoByShip()
    {
        $shipTrack = [];
        if ($shipment = $this->_initShipment()) {
            $incrementId = $shipment->getIncrementId();
            $tracks = $shipment->getTracksCollection();

            $trackingInfos = [];
            foreach ($tracks as $track) {
                $trackingInfos[] = $track->getNumberDetail();
            }

            $shipTrack[$incrementId] = $trackingInfos;
        }

        return $shipTrack;
    }

    /**
     * @return array
     */
    #[Deprecated(message: <<<'TXT'
    after 1.4.0.0-alpha3
     Retrieve tracking by tracking entity id
    TXT)]
    public function getTrackingInfoByTrackId()
    {
        $track = Mage::getModel('sales/order_shipment_track')->load($this->getTrackId());
        if ($this->getProtectCode() == $track->getProtectCode()) {
            return [[$track->getNumberDetail()]];
        }

        return [[]];
    }

    /**
     * Format given date and time in current locale without changing timezone
     *
     * @param  string $date
     * @param  string $time
     * @return string
     */
    public function formatDeliveryDateTime($date, $time)
    {
        return $this->formatDeliveryDate($date) . ' ' . $this->formatDeliveryTime($time);
    }

    /**
     * Format given date in current locale without changing timezone
     *
     * @param  string $date
     * @return string
     */
    public function formatDeliveryDate($date)
    {
        $locale = Mage::app()->getLocale();
        $format = $locale->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
        return $locale->date(Carbon::parse($date)->getTimestamp(), Zend_Date::TIMESTAMP, null, false)
            ->toString($format);
    }

    /**
     * Format given time [+ date] in current locale without changing timezone
     *
     * @param  string $time
     * @param  string $date
     * @return string
     */
    public function formatDeliveryTime($time, $date = null)
    {
        if (!empty($date)) {
            $time = $date . ' ' . $time;
        }

        $locale = Mage::app()->getLocale();

        $format = $locale->getTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        return $locale->date(Carbon::parse($time)->getTimestamp(), Zend_Date::TIMESTAMP, null, false)
            ->toString($format);
    }

    /**
     * Is 'contact us' option enabled?
     *
     * @return bool
     */
    public function getContactUsEnabled()
    {
        return Mage::getStoreConfigFlag('contacts/contacts/enabled');
    }

    /**
     * @return string
     */
    public function getStoreSupportEmail()
    {
        return Mage::getStoreConfig('trans_email/ident_support/email');
    }

    /**
     * @return string
     */
    public function getContactUs()
    {
        return $this->getUrl('contacts');
    }
}
