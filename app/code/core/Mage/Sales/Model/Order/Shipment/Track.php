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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Enter description here ...
 *
 * @method Mage_Sales_Model_Resource_Order_Shipment_Track _getResource()
 * @method Mage_Sales_Model_Resource_Order_Shipment_Track getResource()
 * @method int getParentId()
 * @method Mage_Sales_Model_Order_Shipment_Track setParentId(int $value)
 * @method float getWeight()
 * @method Mage_Sales_Model_Order_Shipment_Track setWeight(float $value)
 * @method float getQty()
 * @method Mage_Sales_Model_Order_Shipment_Track setQty(float $value)
 * @method int getOrderId()
 * @method Mage_Sales_Model_Order_Shipment_Track setOrderId(int $value)
 * @method Mage_Sales_Model_Order_Shipment_Track setNumber(string $value)
 * @method string getDescription()
 * @method Mage_Sales_Model_Order_Shipment_Track setDescription(string $value)
 * @method string getTitle()
 * @method Mage_Sales_Model_Order_Shipment_Track setTitle(string $value)
 * @method string getCarrierCode()
 * @method Mage_Sales_Model_Order_Shipment_Track setCarrierCode(string $value)
 * @method string getCreatedAt()
 * @method Mage_Sales_Model_Order_Shipment_Track setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Mage_Sales_Model_Order_Shipment_Track setUpdatedAt(string $value)
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Shipment_Track extends Mage_Sales_Model_Abstract
{
    const CUSTOM_CARRIER_CODE   = 'custom';
    protected $_shipment = null;

    protected $_eventPrefix = 'sales_order_shipment_track';
    protected $_eventObject = 'track';

    /**
     * Initialize resource model
     */
    function _construct()
    {
        $this->_init('sales/order_shipment_track');
    }

    /**
     * Init mapping array of short fields to
     * its full names
     *
     * @resturn Varien_Object
     */
    protected function _initOldFieldsMap()
    {
        $this->_oldFieldsMap = array(
            'number' => 'track_number'
        );
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
     * @param   Mage_Sales_Model_Order_Shipment $shipment
     * @return  Mage_Sales_Model_Order_Shipment_Item
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
        return (string)$this->getShipment()->getProtectCode();
    }

    /**
     * Retrieve detail for shipment track
     *
     * @return string
     */
    public function getNumberDetail()
    {
        $carrierInstance = Mage::getSingleton('shipping/config')->getCarrierInstance($this->getCarrierCode());
        if (!$carrierInstance) {
            $custom = array();
            $custom['title'] = $this->getTitle();
            $custom['number'] = $this->getTrackNumber();
            return $custom;
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
     * @return Mage_Sales_Model_Order_Shipment_Track
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
