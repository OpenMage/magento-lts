<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Order status history comments
 *
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Resource_Order_Status_History _getResource()
 * @method Mage_Sales_Model_Resource_Order_Status_History getResource()
 * @method string getComment()
 * @method $this setComment(string $value)
 * @method string getCreatedAt()
 * @method $this setCreatedAt(string $value)
 * @method int getIsCustomerNotified()
 * @method $this setEntityName(string $value)
 * @method int getParentId()
 * @method $this setParentId(int $value)
 * @method string getStatus()
 * @method $this setStatus(string $value)
 * @method int getIsVisibleOnFront()
 * @method $this setIsVisibleOnFront(int $value)
 */
class Mage_Sales_Model_Order_Status_History extends Mage_Sales_Model_Abstract
{
    public const CUSTOMER_NOTIFICATION_NOT_APPLICABLE = 2;

    /**
     * Order instance
     *
     * @var Mage_Sales_Model_Order
     */
    protected $_order;

    protected $_eventPrefix = 'sales_order_status_history';

    protected $_eventObject = 'status_history';

    protected function _construct()
    {
        $this->_init('sales/order_status_history');
    }

    /**
     * Set order object
     *
     * @return  $this
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        return $this;
    }

    /**
     * Get store id
     *
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getStoreId(): int
    {
        return $this->getStore()->getStoreId();
    }

    /**
     * Notification flag
     *
     * @param  mixed $flag OPTIONAL (notification is not applicable by default)
     * @return $this
     */
    public function setIsCustomerNotified($flag = null)
    {
        if (is_null($flag)) {
            $flag = self::CUSTOMER_NOTIFICATION_NOT_APPLICABLE;
        }

        return $this->setData('is_customer_notified', $flag);
    }

    /**
     * Customer Notification Applicable check method
     *
     * @return bool
     */
    public function isCustomerNotificationNotApplicable()
    {
        return $this->getIsCustomerNotified() == self::CUSTOMER_NOTIFICATION_NOT_APPLICABLE;
    }

    /**
     * Retrieve order instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * Retrieve status label
     *
     * @return string
     */
    public function getStatusLabel()
    {
        if ($this->getOrder()) {
            return $this->getOrder()->getConfig()->getStatusLabel($this->getStatus());
        }

        return '';
    }

    /**
     * Get store object
     *
     * @return Mage_Core_Model_Store
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getStore()
    {
        if ($this->getOrder()) {
            return $this->getOrder()->getStore();
        }

        return Mage::app()->getStore();
    }

    /**
     * Set order again if required
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getParentId() && $this->getOrder()) {
            $this->setParentId($this->getOrder()->getId());
        }

        return $this;
    }
}
