<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Class Mage_Sales_Model_Order_Shipment_Item
 *
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Resource_Order_Shipment_Item            _getResource()
 * @method string                                                   getAdditionalData()
 * @method Mage_Sales_Model_Resource_Order_Shipment_Item_Collection getCollection()
 * @method string                                                   getDescription()
 * @method string                                                   getName()
 * @method int                                                      getOrderItemId()
 * @method int                                                      getParentId()
 * @method float                                                    getPrice()
 * @method int                                                      getProductId()
 * @method float                                                    getQty()
 * @method Mage_Sales_Model_Resource_Order_Shipment_Item            getResource()
 * @method Mage_Sales_Model_Resource_Order_Shipment_Item_Collection getResourceCollection()
 * @method float                                                    getRowTotal()
 * @method string                                                   getSku()
 * @method float                                                    getWeight()
 * @method $this                                                    setAdditionalData(string $value)
 * @method $this                                                    setDescription(string $value)
 * @method $this                                                    setName(string $value)
 * @method $this                                                    setOrderItemId(int $value)
 * @method $this                                                    setParentId(int $value)
 * @method $this                                                    setPrice(float $value)
 * @method $this                                                    setProductId(int $value)
 * @method $this                                                    setRowTotal(float $value)
 * @method $this                                                    setSku(string $value)
 * @method $this                                                    setStoreId(int $value)
 * @method $this                                                    setWeight(float $value)
 */
class Mage_Sales_Model_Order_Shipment_Item extends Mage_Core_Model_Abstract
{
    protected $_eventPrefix = 'sales_shipment_item';

    protected $_eventObject = 'shipment_item';

    protected $_shipment = null;

    protected $_orderItem = null;

    protected function _construct()
    {
        $this->_init('sales/order_shipment_item');
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
        return $this->_shipment;
    }

    /**
     * Declare order item instance
     *
     * @return $this
     */
    public function setOrderItem(Mage_Sales_Model_Order_Item $item)
    {
        $this->_orderItem = $item;
        if ($this->getOrderItemId() != $item->getId()) {
            $this->setOrderItemId($item->getId());
        }

        return $this;
    }

    /**
     * Retrieve order item instance
     *
     * @return Mage_Sales_Model_Order_Item
     */
    public function getOrderItem()
    {
        if (is_null($this->_orderItem)) {
            if ($this->getShipment()) {
                $this->_orderItem = $this->getShipment()->getOrder()->getItemById($this->getOrderItemId());
            } else {
                $this->_orderItem = Mage::getModel('sales/order_item')
                    ->load($this->getOrderItemId());
            }
        }

        return $this->_orderItem;
    }

    /**
     * Declare qty
     *
     * @param  float               $qty
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function setQty($qty)
    {
        if ($this->getOrderItem()->getIsQtyDecimal()) {
            $qty = (float) $qty;
        } else {
            $qty = (int) $qty;
        }

        $qty = $qty > 0 ? $qty : 0;
        /**
         * Check qty availability
         */
        if ($qty <= $this->getOrderItem()->getQtyToShip() || $this->getOrderItem()->isDummy(true)) {
            $this->setData('qty', $qty);
        } else {
            Mage::throwException(
                Mage::helper('sales')->__('Invalid qty to ship for item "%s"', $this->getName()),
            );
        }

        return $this;
    }

    /**
     * Applying qty to order item
     *
     * @return $this
     */
    public function register()
    {
        $this->getOrderItem()->setQtyShipped(
            $this->getOrderItem()->getQtyShipped() + $this->getQty(),
        );
        return $this;
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
