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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Reports
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customers Report collection
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Reports_Model_Mysql4_Customer_Collection extends Mage_Customer_Model_Entity_Customer_Collection
{

    protected $_customerIdTableName;
    protected $_customerIdFieldName;
    protected $_orderEntityTableName;
    protected $_orderEntityFieldName;

    public function addCartInfo()
    {
        foreach ($this->getItems() as $item)
        {
            $quote = Mage::getModel('sales/quote')->loadByCustomer($item->getId());

            if (is_object($quote))
            {
                $totals = $quote->getTotals();
                $item->setTotal($totals['subtotal']->getValue());
                $quote_items = Mage::getResourceModel('sales/quote_item_collection')->setQuoteFilter($quote->getId());
                $quote_items->load();
                $item->setItems($quote_items->count());
            } else {
                $item->remove();
            }

        }
        return $this;
    }

    public function addCustomerName()
    {
        $this->addNameToSelect();
        return $this;
    }

    /**
     * Order for each customer
     *
     * @param string $from
     * @param string $to
     * @return Mage_Reports_Model_Mysql4_Customer_Collection
     */
    public function joinOrders($from = '', $to = '')
    {
        $order = Mage::getResourceSingleton('sales/order');
        /* @var $order Mage_Sales_Model_Entity_Order */
        $attr = $order->getAttribute('customer_id');
        /* @var $attr Mage_Eav_Model_Entity_Attribute_Abstract */
        $attrId = $attr->getAttributeId();
        $this->_customerIdTableName = $attr->getBackend()->getTable();
        $this->_customerIdFieldName = $attr->getBackend()->isStatic() ? 'customer_id' : 'value';

        if ($from != '' && $to != '') {
            $dateFilter = " and {$this->_customerIdTableName}.created_at BETWEEN '{$from}' AND '{$to}'";
        } else {
            $dateFilter = '';
        }

        $this->getSelect()
            ->joinLeft($this->_customerIdTableName,
                "{$this->_customerIdTableName}.{$this->_customerIdFieldName}=e.entity_id".$dateFilter,
            array());

        return $this;
    }

    /**
     * Add orders count
     *
     * @return Mage_Reports_Model_Mysql4_Customer_Collection
     */
    public function addOrdersCount()
    {
        $joinCondition = "{$this->_customerIdTableName}.entity_id=order_state.entity_id";
        $joinCondition .= " AND order_state.state <> '" . Mage_Sales_Model_Order::STATE_CANCELED . "'";

        $this->getSelect()
            ->columns(array("orders_count" => "COUNT(order_state.entity_id)"))
            ->joinLeft(
                array('order_state' => $this->getTable('sales/order')),
                $joinCondition,
                array())
            ->group("e.entity_id");

        return $this;
    }

    /**
     * Order summary info for each customer
     * such as orders_count, orders_avg_amount, orders_total_amount
     *
     * @param int $storeId
     * @return Mage_Reports_Model_Mysql4_Customer_Collection
     */
    public function addSumAvgTotals($storeId = 0)
    {
        /**
         * calculate average and total amount
         */
        $expr = ($storeId == 0)
            ? "({$this->_customerIdTableName}.base_subtotal-IFNULL({$this->_customerIdTableName}.base_subtotal_canceled,0)-IFNULL({$this->_customerIdTableName}.base_subtotal_refunded,0))*{$this->_customerIdTableName}.base_to_global_rate"
            : "{$this->_customerIdTableName}.base_subtotal-IFNULL({$this->_customerIdTableName}.base_subtotal_canceled,0)-IFNULL({$this->_customerIdTableName}.base_subtotal_refunded,0)";

        $this->getSelect()
            ->columns(array("orders_avg_amount" => "AVG({$expr})"))
            ->columns(array("orders_sum_amount" => "SUM({$expr})"));

        return $this;
    }

    public function orderByTotalAmount($dir = 'desc')
    {
        $this->getSelect()
            ->order("orders_sum_amount {$dir}");
        return $this;
    }

    public function orderByCustomerRegistration($dir = 'desc')
    {
        $this->addAttributeToSort('entity_id', $dir);
        return $this;
    }

    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::HAVING);
        $countSelect->columns("count(DISTINCT e.entity_id)");
        $sql = $countSelect->__toString();
        return $sql;
    }
}
