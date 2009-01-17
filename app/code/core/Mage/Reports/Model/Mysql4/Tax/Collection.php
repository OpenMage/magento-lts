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
 * @category   Mage
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Reports tax collection
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Mysql4_Tax_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('sales/order_tax');
    }

    public function setDateRange($from, $to)
    {
        $this->_reset();

        $this->getSelect()
            ->join(array('e'=>$this->getTable('sales/order')), 'e.entity_id = main_table.order_id', array())
            ->where('e.created_at >= ?', $from)
            ->where('e.created_at <= ?', $to);

        $this->getSelect()->from('', array('tax'=>'SUM(base_real_amount)', 'orders'=>'COUNT(DISTINCT order_id)', 'tax_rate'=>'main_table.percent', 'tax_title'=>'main_table.code'))
            ->group('main_table.code')
            ->order(array('process', 'priority'));
        /*
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to))
            ->addExpressionAttributeToSelect('orders', 'COUNT(DISTINCT({{entity_id}}))', array('entity_id'));

        /**
         * getting qty count for each order
         */

//        $orderItem = Mage::getResourceSingleton('sales/order_item');
//        /* @var $orderItem Mage_Sales_Model_Entity_Quote */
//
//        $this->getSelect()
//            ->joinLeft(array("order_items" => $orderItem->getEntityTable()),
//                "order_items.parent_id = e.entity_id and order_items.entity_type_id=".$orderItem->getTypeId(), array());
//
//        $attr = $orderItem->getAttribute('tax_percent');
//        /* @var $attr Mage_Eav_Model_Entity_Attribute_Abstract */
//        $attrId = $attr->getAttributeId();
//        $tableName = $attr->getBackend()->getTable();
//
//        $this->getSelect()
//            ->joinLeft(array("order_items2" => $tableName),
//                "order_items2.entity_id = order_items.entity_id and order_items2.attribute_id = {$attrId}", array());
//
//        $this->getSelect()->from("", array("tax_rate" => "IFNULL(order_items2.value, 0)"))
//            ->group('order_items2.value')
//            ->order('orders desc')
//            ->having('orders > 0');
/*
        $this->getSelect()
            ->joinLeft(array("order_items" => $this->getTable('sales/order_item')),
                "order_items.order_id = e.entity_id", array())
            ->joinLeft(array("order_items2" => $this->getTable('sales/order_item')),
                "order_items2.item_id = order_items.item_id", array());

        $this->getSelect()->from("", array("tax_rate" => "IFNULL(order_items2.tax_percent, 0)"))
            ->group('order_items2.tax_percent')
            ->order('orders desc')
            ->having('orders > 0');
*/
        return $this;
    }

    public function setStoreIds($storeIds)
    {
        $vals = array_values($storeIds);
        if (count($storeIds) >= 1 && $vals[0] != '') {
            $this->getSelect()->where('e.store_id in (?)', (array)$storeIds);
        }

        /*
        if (count($storeIds) >= 1 && $vals[0] != '') {
            $this->addAttributeToFilter('store_id', array('in' => (array)$storeIds))
                ->addExpressionAttributeToSelect(
                    'tax',
                    'SUM({{base_tax_amount}})',
                    array('base_tax_amount'));
        } else {
            $this->addExpressionAttributeToSelect(
                    'tax',
                    'SUM({{base_tax_amount}}/{{store_to_base_rate}})',
                    array('base_tax_amount', 'store_to_base_rate'));
        }
        */

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
        $countSelect->from("", "count(DISTINCT e.entity_id)");
        $sql = $countSelect->__toString();
        return $sql;
    }
}
