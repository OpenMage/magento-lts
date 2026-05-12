<?php

declare(strict_types=1);

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
 * @method Mage_Sales_Model_Resource_Order_Shipment_Item_Collection getCollection()
 * @method Mage_Sales_Model_Resource_Order_Shipment_Item            getResource()
 * @method Mage_Sales_Model_Resource_Order_Shipment_Item_Collection getResourceCollection()
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
        $qty = $this->getOrderItem()->getIsQtyDecimal() ? (float) $qty : (int) $qty;
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
    #[Override]
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getParentId() && $this->getShipment()) {
            $this->setParentId($this->getShipment()->getId());
        }

        return $this;
    }

    public function getAdditionalData(): ?string
    {
        $value = $this->_getData('additional_data');
        return $value !== null ? (string) $value : null;
    }

    public function setAdditionalData(string $value): static
    {
        return $this->setData('additional_data', $value);
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

    public function getName(): ?string
    {
        $value = $this->_getData('name');
        return $value !== null ? (string) $value : null;
    }

    public function setName(string $value): static
    {
        return $this->setData('name', $value);
    }

    public function getOrderItemId(): int
    {
        return (int) $this->_getData('order_item_id');
    }

    public function setOrderItemId(int $value): static
    {
        return $this->setData('order_item_id', $value);
    }

    public function getParentId(): int
    {
        return (int) $this->_getData('parent_id');
    }

    public function setParentId(int $value): static
    {
        return $this->setData('parent_id', $value);
    }

    public function getPrice(): float
    {
        return (float) $this->_getData('price');
    }

    public function setPrice(float $value): static
    {
        return $this->setData('price', $value);
    }

    public function getProductId(): int
    {
        return (int) $this->_getData('product_id');
    }

    public function setProductId(int $value): static
    {
        return $this->setData('product_id', $value);
    }

    public function getQty(): float
    {
        return (float) $this->_getData('qty');
    }

    public function getRowTotal(): float
    {
        return (float) $this->_getData('row_total');
    }

    public function setRowTotal(float $value): static
    {
        return $this->setData('row_total', $value);
    }

    public function getSku(): ?string
    {
        $value = $this->_getData('sku');
        return $value !== null ? (string) $value : null;
    }

    public function setSku(string $value): static
    {
        return $this->setData('sku', $value);
    }

    public function getWeight(): float
    {
        return (float) $this->_getData('weight');
    }

    public function setWeight(float $value): static
    {
        return $this->setData('weight', $value);
    }

    public function setStoreId(int $value): static
    {
        return $this->setData('store_id', $value);
    }
}
