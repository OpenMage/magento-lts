<?php

declare(strict_types=1);

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
 * @method Mage_Sales_Model_Resource_Order_Shipment_Track            _getResource()
 * @method Mage_Sales_Model_Resource_Order_Shipment_Track_Collection getCollection()
 * @method Mage_Sales_Model_Resource_Order_Shipment_Track            getResource()
 * @method Mage_Sales_Model_Resource_Order_Shipment_Track_Collection getResourceCollection()
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
    protected function _construct()
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
        return $this->getDataByKey('track_number');
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
        }

        $carrierInstance->setStore($this->getStore());

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
    #[Override]
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getParentId() && $this->getShipment()) {
            $this->setParentId($this->getShipment()->getId());
        }

        return $this;
    }

    public function getCarrierCode(): string
    {
        return (string) $this->_getData('carrier_code');
    }

    public function setCarrierCode(string $value): static
    {
        return $this->setData('carrier_code', $value);
    }

    public function getDescription(): ?string
    {
        $value = $this->_getData('description');
        return $value !== null ? (string) $value : null;
    }

    public function setDescription(string $value): static
    {
        return $this->setData('description', $value);
    }

    public function getOrderId(): int
    {
        return (int) $this->_getData('order_id');
    }

    public function setOrderId(int $value): static
    {
        return $this->setData('order_id', $value);
    }

    public function getParentId(): int
    {
        return (int) $this->_getData('parent_id');
    }

    public function setParentId(int $value): static
    {
        return $this->setData('parent_id', $value);
    }

    public function getQty(): float
    {
        return (float) $this->_getData('qty');
    }

    public function setQty(float $value): static
    {
        return $this->setData('qty', $value);
    }

    public function getTitle(): string
    {
        return (string) $this->_getData('title');
    }

    public function setTitle(string $value): static
    {
        return $this->setData('title', $value);
    }

    public function getTrackNumber(): ?string
    {
        $value = $this->_getData('track_number');
        return $value !== null ? (string) $value : null;
    }

    public function setNumber(string $value): static
    {
        return $this->setData('track_number', $value);
    }

    public function getWeight(): ?float
    {
        $value = $this->_getData('weight');
        return $value !== null ? (float) $value : null;
    }

    public function setWeight(float $value): static
    {
        return $this->setData('weight', $value);
    }
}
