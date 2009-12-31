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
 * Reports orders collection
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Mysql4_Order_Collection extends Mage_Sales_Model_Entity_Order_Collection
{

    public function prepareSummary($range, $customStart, $customEnd, $isFilter=0)
    {

        if ($isFilter==0) {
            $this->addExpressionAttributeToSelect('revenue',
                'SUM({{base_grand_total}}*{{base_to_global_rate}})',
                array('base_grand_total', 'base_to_global_rate'));
        } else{
            $this->addExpressionAttributeToSelect('revenue',
                'SUM({{base_grand_total}})',
                array('base_grand_total'));
        }

        $this->addExpressionAttributeToSelect('quantity', 'COUNT({{attribute}})', 'entity_id')
            ->addExpressionAttributeToSelect('range', $this->_getRangeExpression($range), 'created_at')
            ->addAttributeToFilter('created_at', $this->getDateRange($range, $customStart, $customEnd))
            ->groupByAttribute('range')
            ->addAttributeToFilter('state', array('neq' => Mage_Sales_Model_Order::STATE_CANCELED))
            ->getSelect()->order('range', 'asc');

        return $this;
    }

    protected function _getRangeExpression($range)
    {
        // dont need of this offset bc we are format date in block
        //$timeZoneOffset = Mage::getModel('core/date')->getGmtOffset();

        switch ($range)
        {
            case '24h':
                $expression = 'DATE_FORMAT({{attribute}}, \'%Y-%m-%d %H:00\')';

                break;
            case '7d':
            case '1m':
               $expression = 'DATE_FORMAT({{attribute}}, \'%Y-%m-%d\')';
               break;
            case '1y':
            case '2y':
            case 'custom':
            default:
                $expression = 'DATE_FORMAT({{attribute}}, \'%Y-%m\')';
                break;
        }

        return $expression;
    }

    public function getDateRange($range, $customStart, $customEnd, $returnObjects = false)
    {
        $dateEnd = new Zend_Date(Mage::getModel('core/date')->gmtTimestamp());
        $dateStart = clone $dateEnd;

        // go to the end of a day
        $dateEnd->setHour(23);
        $dateEnd->setMinute(59);
        $dateEnd->setSecond(59);

        $dateStart->setHour(0);
        $dateStart->setMinute(0);
        $dateStart->setSecond(0);

        switch ($range)
        {
            case '24h':
                $dateEnd = new Zend_Date(Mage::getModel('core/date')->gmtTimestamp());
                $dateEnd->addHour(1);
                $dateStart = clone $dateEnd;
                $dateStart->subDay(1);
                break;

            case '7d':
                // substract 6 days we need to include
                // only today and not hte last one from range
                $dateStart->subDay(6);
                break;

            case '1m':
                $dateStart->setDay(Mage::getStoreConfig('reports/dashboard/mtd_start'));
                break;

            case 'custom':
                $dateStart = $customStart ? $customStart : $dateEnd;
                $dateEnd   = $customEnd ? $customEnd : $dateEnd;
                break;

            case '1y':
            case '2y':
                $startMonthDay = explode(',', Mage::getStoreConfig('reports/dashboard/ytd_start'));
                $startMonth = isset($startMonthDay[0]) ? (int)$startMonthDay[0] : 1;
                $startDay = isset($startMonthDay[1]) ? (int)$startMonthDay[1] : 1;
                $dateStart->setMonth($startMonth);
                $dateStart->setDay($startDay);
                if ($range == '2y') {
                    $dateStart->subYear(1);
                }
                break;
        }

        if ($returnObjects) {
            return array($dateStart, $dateEnd);
        } else {
            return array('from'=>$dateStart, 'to'=>$dateEnd, 'datetime'=>true);
        }
    }

    public function addItemCountExpr()
    {
//        $orderItemEntityTypeId = Mage::getResourceSingleton('sales/order_item')->getTypeId();
//        $this->getSelect()->join(
//                array('items'=>Mage::getResourceSingleton('sales/order_item')->getEntityTable()),
//                'items.parent_id=e.entity_id and items.entity_type_id='.$orderItemEntityTypeId,
//                array('items_count'=>new Zend_Db_Expr('COUNT(items.entity_id)'))
//            )
//            ->group('e.entity_id');
        $this->getSelect()->join(
                array('items'=>$this->getTable('sales/order_item')),
                'items.order_id=e.entity_id',
                array('items_count'=>new Zend_Db_Expr('COUNT(items.item_id)', 'parent_item'))
            )
            ->where('items.parent_item_id is NULL')
            ->group('e.entity_id');
        return $this;
    }

    public function calculateTotals($isFilter = 0)
    {
        if ($isFilter == 0) {
            $this->addExpressionAttributeToSelect(
                    'revenue',
                     'SUM(({{base_subtotal}}-IFNULL({{base_subtotal_refunded}},0)-IFNULL({{base_subtotal_canceled}},0)-IFNULL({{base_discount_amount}},0)+IFNULL({{base_discount_refunded}},0))*{{base_to_global_rate}})',
                     array('base_subtotal', 'base_to_global_rate', 'base_subtotal_refunded', 'base_subtotal_canceled','base_discount_amount','base_discount_refunded'))
                ->addExpressionAttributeToSelect(
                    'tax',
                    'SUM(({{base_tax_amount}}-IFNULL({{base_tax_refunded}},0)-IFNULL({{base_tax_canceled}},0))*{{base_to_global_rate}})',
                    array('base_tax_amount', 'base_tax_canceled', 'base_tax_refunded', 'base_to_global_rate'))
                ->addExpressionAttributeToSelect(
                    'shipping',
                    'SUM(({{base_shipping_amount}}-IFNULL({{base_shipping_refunded}},0)-IFNULL({{base_shipping_canceled}},0))*{{base_to_global_rate}})',
                    array('base_shipping_amount', 'base_shipping_refunded', 'base_shipping_canceled', 'base_to_global_rate'));
        } else {
            $this->addExpressionAttributeToSelect(
                    'revenue',
                     'SUM({{base_subtotal}}-IFNULL({{base_subtotal_refunded}},0)-IFNULL({{base_subtotal_canceled}},0)-IFNULL({{base_discount_amount}},0)+IFNULL({{base_discount_refunded}},0))',
                     array('base_subtotal', 'base_subtotal_refunded', 'base_subtotal_canceled','base_discount_amount','base_discount_refunded'))
                ->addExpressionAttributeToSelect(
                    'tax',
                    'SUM({{base_tax_amount}}-IFNULL({{base_tax_refunded}},0)-IFNULL({{base_tax_canceled}},0))',
                    array('base_tax_amount', 'base_tax_refunded', 'base_tax_canceled'))
                ->addExpressionAttributeToSelect(
                    'shipping',
                    'SUM({{base_shipping_amount}}-IFNULL({{base_shipping_refunded}},0)-IFNULL({{base_shipping_canceled}},0))',
                    array('base_shipping_amount', 'base_shipping_refunded', 'base_shipping_canceled'));
        }

        $this->addExpressionAttributeToSelect('quantity', 'COUNT({{entity_id}})', array('entity_id'))
            ->addAttributeToFilter('state', array('neq' => Mage_Sales_Model_Order::STATE_CANCELED))
            ->groupByAttribute('entity_type_id');

        return $this;
    }

    public function calculateSales($isFilter = 0)
    {
        if ($isFilter == 0) {
            $expr = "({{base_subtotal}}-IFNULL({{base_subtotal_refunded}},0)-IFNULL({{base_subtotal_canceled}},0)-IFNULL({{base_discount_amount}},0)+IFNULL({{base_discount_refunded}},0))*{{base_to_global_rate}}";
            $attrs = array('base_subtotal', 'base_to_global_rate', 'base_subtotal_refunded', 'base_subtotal_canceled','base_discount_amount','base_discount_refunded');
            $this->addExpressionAttributeToSelect('lifetime', "SUM({$expr})", $attrs)
                ->addExpressionAttributeToSelect('average', "AVG({$expr})", $attrs);
        } else {
            $expr = "({{base_subtotal}}-IFNULL({{base_subtotal_refunded}},0)-IFNULL({{base_subtotal_canceled}},0)-IFNULL({{base_discount_amount}},0)+IFNULL({{base_discount_amount}},0))";
            $attrs = array('base_subtotal', 'base_subtotal_refunded', 'base_subtotal_canceled','base_discount_amount','base_discount_refunded');
            $this->addExpressionAttributeToSelect('lifetime', "SUM($expr)", $attrs)
                ->addExpressionAttributeToSelect('average', "AVG($expr)", $attrs);
        }

        $this->addAttributeToFilter('state', array('neq' => Mage_Sales_Model_Order::STATE_CANCELED))
            ->groupByAttribute('entity_type_id');
        return $this;
    }

    public function setDateRange($from, $to)
    {
        $this->_reset()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to))
            ->addExpressionAttributeToSelect('orders', 'COUNT(DISTINCT({{entity_id}}))', array('entity_id'))
            ->addAttributeToFilter('state', array('neq' => Mage_Sales_Model_Order::STATE_CANCELED))
            ->getSelect()->group('("*")');

        /**
         * getting qty count for each order
         */

//        $orderItem = Mage::getResourceSingleton('sales/order_item');
//        /* @var $orderItem Mage_Sales_Model_Entity_Quote */
//        $attr = $orderItem->getAttribute('parent_id');
//        /* @var $attr Mage_Eav_Model_Entity_Attribute_Abstract */
//        $attrId = $attr->getAttributeId();
//        $tableName = $attr->getBackend()->getTable();
//
//        $this->getSelect()
//            ->joinLeft(array("order_items" => $tableName),
//                "order_items.parent_id = e.entity_id and order_items.entity_type_id=".$orderItem->getTypeId(), array());
//
//        $attr = $orderItem->getAttribute('qty_ordered');
//        /* @var $attr Mage_Eav_Model_Entity_Attribute_Abstract */
//        $attrId = $attr->getAttributeId();
//        $tableName = $attr->getBackend()->getTable();
//        $fieldName = $attr->getBackend()->isStatic() ? 'qty_ordered' : 'value';
//
//        $this->getSelect()
//            ->joinLeft(array("order_items2" => $tableName),
//                "order_items2.entity_id = `order_items`.entity_id and order_items2.attribute_id = {$attrId}", array())
//            ->columns(array("items" => "sum(order_items2.{$fieldName})"));

        $countSql = clone $this->getSelect();
        $countSql->reset();

        $countSql->from(array("order_items" => $this->getTable('sales/order_item')), array("sum(`order_items2`.`qty_ordered`)"))
            ->joinLeft(array("order_items2" => $this->getTable('sales/order_item')),
                "order_items2.item_id = `order_items`.item_id", array())
            ->where("`order_items`.`order_id` = `e`.`entity_id`")
            ->where("`order_items2`.`parent_item_id` is NULL");

        $this->getSelect()->columns(array("items" => "SUM((".$countSql."))"));

        return $this;
    }

    public function setStoreIds($storeIds)
    {
        $vals = array_values($storeIds);
        if (count($storeIds) >= 1 && $vals[0] != '') {
            $this->addAttributeToFilter('store_id', array('in' => (array)$storeIds))
                ->addExpressionAttributeToSelect(
                    'subtotal',
                    'SUM({{base_subtotal}})',
                    array('base_subtotal'))
                ->addExpressionAttributeToSelect(
                    'tax',
                    'SUM({{base_tax_amount}})',
                    array('base_tax_amount'))
                ->addExpressionAttributeToSelect(
                    'shipping',
                    'SUM({{base_shipping_amount}})',
                    array('base_shipping_amount'))
                ->addExpressionAttributeToSelect(
                    'discount',
                    'SUM({{base_discount_amount}})',
                    array('base_discount_amount'))
                ->addExpressionAttributeToSelect(
                    'total',
                    'SUM({{base_grand_total}})',
                    array('base_grand_total'))
                ->addExpressionAttributeToSelect(
                    'invoiced',
                    'SUM({{base_total_paid}})',
                    array('base_total_paid'))
                ->addExpressionAttributeToSelect(
                    'refunded',
                    'SUM({{base_total_refunded}})',
                    array('base_total_refunded'))
                ->addExpressionAttributeToSelect(
                    'profit',
                    'SUM(IFNULL({{base_subtotal_invoiced}}, 0)) + SUM(IFNULL({{base_discount_refunded}}, 0)) - SUM(IFNULL({{base_subtotal_refunded}}, 0)) - SUM(IFNULL({{base_discount_invoiced}}, 0)) - SUM(IFNULL({{base_total_invoiced_cost}}, 0))',
                    array('base_subtotal_invoiced', 'base_discount_refunded', 'base_subtotal_refunded', 'base_discount_invoiced', 'base_total_invoiced_cost'));
        } else {
            $this->addExpressionAttributeToSelect(
                    'subtotal',
                    'SUM({{base_subtotal}}*{{base_to_global_rate}})',
                    array('base_subtotal', 'base_to_global_rate'))
                ->addExpressionAttributeToSelect(
                    'tax',
                    'SUM({{base_tax_amount}}*{{base_to_global_rate}})',
                    array('base_tax_amount', 'base_to_global_rate'))
                ->addExpressionAttributeToSelect(
                    'shipping',
                    'SUM({{base_shipping_amount}}*{{base_to_global_rate}})',
                    array('base_shipping_amount', 'base_to_global_rate'))
                ->addExpressionAttributeToSelect(
                    'discount',
                    'SUM({{base_discount_amount}}*{{base_to_global_rate}})',
                    array('base_discount_amount', 'base_to_global_rate'))
                ->addExpressionAttributeToSelect(
                    'total',
                    'SUM({{base_grand_total}}*{{base_to_global_rate}})',
                    array('base_grand_total', 'base_to_global_rate'))
                ->addExpressionAttributeToSelect(
                    'invoiced',
                    'SUM({{base_total_paid}}*{{base_to_global_rate}})',
                    array('base_total_paid', 'base_to_global_rate'))
                ->addExpressionAttributeToSelect(
                    'refunded',
                    'SUM({{base_total_refunded}}*{{base_to_global_rate}})',
                    array('base_total_refunded', 'base_to_global_rate'))
                ->addExpressionAttributeToSelect(
                    'profit',
                    'SUM(IFNULL({{base_subtotal_invoiced}}, 0)*{{base_to_global_rate}}) + SUM(IFNULL({{base_discount_refunded}}, 0)*{{base_to_global_rate}}) - SUM(IFNULL({{base_subtotal_refunded}}, 0)*{{base_to_global_rate}}) - SUM(IFNULL({{base_discount_invoiced}}, 0)*{{base_to_global_rate}}) - SUM(IFNULL({{base_total_invoiced_cost}}, 0)*{{base_to_global_rate}})',
                    array('base_subtotal_invoiced', 'base_discount_refunded', 'base_subtotal_refunded', 'base_discount_invoiced', 'base_total_invoiced_cost', 'base_to_global_rate'));
        }

        return $this;
    }

    /**
     * Add group By customer attribute
     *
     * @return Mage_Reports_Model_Mysql4_Order_Collection
     */
    public function groupByCustomer()
    {
        $this->groupByAttribute('customer_id');

        return $this;
    }

    /**
     * Join Customer Name (concat)
     *
     * @return Mage_Reports_Model_Mysql4_Order_Collection
     */
    public function joinCustomerName()
    {
        //TODO: add full name logic
        $this->joinAttribute('firstname', 'customer/firstname', 'customer_id');
        $this->joinAttribute('lastname', 'customer/lastname', 'customer_id');
        $this->getSelect()->columns(array('name' => 'CONCAT(_table_firstname.value," ", _table_lastname.value)'));
        return $this;
    }

    /**
     * Add Order count field to select
     *
     * @return Mage_Reports_Model_Mysql4_Order_Collection
     */
    public function addOrdersCount()
    {
        $this->addAttributeToFilter('state', array('neq' => Mage_Sales_Model_Order::STATE_CANCELED));
        $this->getSelect()
            ->columns(array("orders_count" => "COUNT(e.entity_id)"));

        return $this;
    }

    /**
     * Add summary average totals
     *
     * @param int $storeId
     * @return Mage_Reports_Model_Mysql4_Order_Collection
     */
    public function addSumAvgTotals($storeId = 0)
    {
        /**
         * calculate average and total amount
         */
        $expr = ($storeId == 0)
            ? '(e.base_subtotal-IFNULL(e.base_subtotal_refunded,0)-IFNULL(e.base_subtotal_canceled,0))*e.base_to_global_rate'
            : 'e.base_subtotal-IFNULL(e.base_subtotal_canceled,0)-IFNULL(e.base_subtotal_refunded,0)';

        $this->getSelect()
            ->columns(array("orders_avg_amount" => "AVG({$expr})"))
            ->columns(array("orders_sum_amount" => "SUM({$expr})"));

        return $this;
    }

    /**
     * Sort order by total amount
     *
     * @param string $dir
     * @return Mage_Reports_Model_Mysql4_Order_Collection
     */
    public function orderByTotalAmount($dir = 'desc')
    {
        $this->getSelect()
            ->order("orders_sum_amount {$dir}");
        return $this;
    }

    public function orderByOrdersCount($dir = 'desc')
    {
        $this->getSelect()
            ->order("orders_count {$dir}");
        return $this;
    }

    public function orderByCustomerRegistration($dir = 'desc')
    {
        $this->addAttributeToSort('customer_id', $dir);
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

    /**
     * Add period filter by created_at attribute
     *
     * @param string $period
     * @return Mage_Reports_Model_Mysql4_Order_Collection
     */
    public function addCreateAtPeriodFilter($period)
    {
        list($from, $to) = $this->getDateRange($period, 0, 0, true);

        $this->addAttributeToFilter('created_at', array(
            'from'  => $from->toString(Varien_Date::DATETIME_INTERNAL_FORMAT),
            'to'    => $to->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)
        ));

        return $this;
    }
}
