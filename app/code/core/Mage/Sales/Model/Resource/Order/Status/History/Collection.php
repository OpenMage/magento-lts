<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Flat sales order status history collection
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Status_History_Collection extends Mage_Sales_Model_Resource_Order_Collection_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_status_history_collection';

    /**
     * @var string
     */
    protected $_eventObject    = 'order_status_history_collection';

    protected function _construct()
    {
        $this->_init('sales/order_status_history');
    }

    /**
     * Get history object collection for specified instance (order, shipment, invoice or credit memo)
     * Parameter instance may be one of the following types: Mage_Sales_Model_Order,
     * Mage_Sales_Model_Order_Creditmemo, Mage_Sales_Model_Order_Invoice, Mage_Sales_Model_Order_Shipment
     *
     * @param mixed $instance
     * @param string $historyEntityName
     *
     * @return Mage_Sales_Model_Order_Status_History|null
     */
    public function getUnnotifiedForInstance($instance, $historyEntityName = Mage_Sales_Model_Order::HISTORY_ENTITY_NAME)
    {
        if (!$instance instanceof Mage_Sales_Model_Order) {
            $instance = $instance->getOrder();
        }
        $this->setOrderFilter($instance)->setOrder('created_at', 'desc')
            ->addFieldToFilter('entity_name', $historyEntityName)
            ->addFieldToFilter('is_customer_notified', 0)->setPageSize(1);
        foreach ($this as $historyItem) {
            return $historyItem;
        }
        return null;
    }
}
