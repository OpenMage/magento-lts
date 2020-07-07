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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order status history comments
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
 * @method $this setStoreId(int $value)
 * @method int getIsVisibleOnFront()
 * @method $this setIsVisibleOnFront(int $value)
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Status_History extends Mage_Sales_Model_Abstract
{
    const CUSTOMER_NOTIFICATION_NOT_APPLICABLE = 2;

    /**
     * Order instance
     *
     * @var Mage_Sales_Model_Order
     */
    protected $_order;

    /**
     * Whether setting order again is required (for example when setting non-saved yet order)
     * @deprecated after 1.4, wrong logic of setting order id
     * @var bool
     */
    private $_shouldSetOrderBeforeSave = false;

    protected $_eventPrefix = 'sales_order_status_history';
    protected $_eventObject = 'status_history';

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('sales/order_status_history');
    }

    /**
     * Set order object and grab some metadata from it
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  $this
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        $this->setStoreId($order->getStoreId());
        return $this;
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
     * @return boolean
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
