<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Class Mage_Sales_Model_Order_Shipment_Track
 *
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Resource_Order_Shipment_Track _getResource()
 * @method string getCarrierCode()
 * @method Mage_Sales_Model_Resource_Order_Shipment_Track_Collection getCollection()
 * @method string getCreatedAt()
 * @method string getDescription()
 * @method int getOrderId()
 * @method int getParentId()
 * @method float getQty()
 * @method Mage_Sales_Model_Resource_Order_Shipment_Track getResource()
 * @method Mage_Sales_Model_Resource_Order_Shipment_Track_Collection getResourceCollection()
 * @method string getTitle()
 * @method string getTrackNumber()
 * @method string getUpdatedAt()
 * @method float getWeight()
 * @method $this setCarrierCode(string $value)
 * @method $this setCreatedAt(string $value)
 * @method $this setDescription(string $value)
 * @method $this setNumber(string $value)
 * @method $this setOrderId(int $value)
 * @method $this setParentId(int $value)
 * @method $this setQty(float $value)
 * @method $this setStoreId(int $value)
 * @method $this setTitle(string $value)
 * @method $this setUpdatedAt(string $value)
 * @method $this setWeight(float $value)
 */
class Mage_Sales_Model_Order_Shipment_Track extends Mage_Sales_Model_Abstract
{
    public const CUSTOM_CARRIER_CODE   = 'custom';

    protected $_shipment = null;

    protected $_eventPrefix = 'sales_order_shipment_track';

    protected $_eventObject = 'track';

    /**
     * Initialize resource model
     */
    public function _construct()
    {
        $this->_init('sales/order_shipment_track');
    }

    /**
     * Init mapping array of short fields to
     * its full names
     *
     * @return void
     */
    protected function _initOldFieldsMap()
    {
        $this->_oldFieldsMap = [
            'number' => 'track_number',
        ];
    }

    /**
     * Back compatibility with old versions.
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->getData('track_number');
    }

    /**
     * Declare Shipment instance
     *
     * @return $this
     */
    public function setShipment(Mage_Sales_Model_Order_Shipment $shipment)
    {
        $this->_shipment = $shipment;
        return $this;
    }

    /**
     * Retrieve Shipment instance
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function getShipment()
    {
        if (!($this->_shipment instanceof Mage_Sales_Model_Order_Shipment)) {
            $this->_shipment = Mage::getModel('sales/order_shipment')->load($this->getParentId());
        }

        return $this->_shipment;
    }

    /**
     * @return bool
     */
    public function isCustom()
    {
        return $this->getCarrierCode() == self::CUSTOM_CARRIER_CODE;
    }

    /**
     * Retrieve hash code of current order
     *
     * @return string
     */
    public function getProtectCode()
    {
        return (string) $this->getShipment()->getProtectCode();
    }

    /**
     * Retrieve detail for shipment track
     *
     * @return array|string
     */
    public function getNumberDetail()
    {
        $carrierInstance = Mage::getSingleton('shipping/config')->getCarrierInstance($this->getCarrierCode());
        if (!$carrierInstance) {
            return [
                'title' => $this->getTitle(),
                'number' => $this->getTrackNumber(),
            ];
        } else {
            $carrierInstance->setStore($this->getStore());
        }

        if (!$trackingInfo = $carrierInstance->getTrackingInfo($this->getNumber())) {
            return Mage::helper('sales')->__('No detail for number "%s"', $this->getNumber());
        }

        return $trackingInfo;
    }

    /**
     * Get store object
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if ($this->getShipment()) {
            return $this->getShipment()->getStore();
        }

        return Mage::app()->getStore();
    }

    /**
     * Get store id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->getStore()->getId();
    }

    /**
     * Before object save
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getParentId() && $this->getShipment()) {
            $this->setParentId($this->getShipment()->getId());
        }

        return $this;
    }
}
