<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Class Mage_Sales_Model_Order_Shipment_Comment
 *
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Resource_Order_Shipment_Comment            _getResource()
 * @method Mage_Sales_Model_Resource_Order_Shipment_Comment_Collection getCollection()
 * @method string                                                      getComment()
 * @method int                                                         getIsCustomerNotified()
 * @method int                                                         getIsVisibleOnFront()
 * @method int                                                         getParentId()
 * @method Mage_Sales_Model_Resource_Order_Shipment_Comment            getResource()
 * @method Mage_Sales_Model_Resource_Order_Shipment_Comment_Collection getResourceCollection()
 * @method $this                                                       setComment(string $value)
 * @method $this                                                       setIsCustomerNotified(int $value)
 * @method $this                                                       setIsVisibleOnFront(int $value)
 * @method $this                                                       setParentId(int $value)
 * @method $this                                                       setStoreId(int $value)
 */
class Mage_Sales_Model_Order_Shipment_Comment extends Mage_Sales_Model_Abstract
{
    /**
     * Shipment instance
     *
     * @var Mage_Sales_Model_Order_Shipment
     */
    protected $_shipment;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sales/order_shipment_comment');
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
