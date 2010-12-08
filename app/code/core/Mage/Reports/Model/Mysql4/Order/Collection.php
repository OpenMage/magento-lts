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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Reports orders collection
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Mysql4_Order_Collection extends Mage_Sales_Model_Mysql4_Order_Collection
{
    protected $_isLive = false;

    /**
     * Check range for live mode
     *
     * @param $range
     * @return boolean
     */
    public function checkIsLive($range)
    {
        $this->_isLive = !Mage::getStoreConfig('sales/dashboard/use_aggregated_data');
        return $this;
    }

    /**
     * Retrieve is live flag for rep
     *
     * @return boolean
     */
    public function isLive()
    {
        return $this->_isLive;
    }

    /**
     * Prepare report summary
     *
     * @param string $range
     * @param mixed $customStart
     * @param mixed $customEnd
     * @param int $isFilter
     * @return Mage_Reports_Model_Mysql4_Order_Collection
     */
    public function prepareSummary($range, $customStart, $customEnd, $isFilter=0)
    {
        $this->checkIsLive($range);
        if ($this->_isLive) {
            $this->_prepareSummaryLive($range, $customStart, $customEnd, $isFilter);
        } else {
            $this->_prepareSummaryAggregated($range, $customStart, $customEnd, $isFilter);
        }

        return $this;
    }

    /**
     * Prepare report summary from live data
     *
     * @param string $range
     * @param mixed $customStart
     * @param mixed $customEnd
     * @param int $isFilter
     * @return Mage_Reports_Model_Mysql4_Order_Collection
     */
    protected function _prepareSummaryLive($range, $customStart, $customEnd, $isFilter=0)
    {
        $this->setMainTable('sales/order');
        if ($isFilter==0) {
            $this->getSelect()->columns(array(
                'revenue' => 'SUM(main_table.base_grand_total*main_table.base_to_global_rate)'
            ));
        } else{
            $this->getSelect()->columns(array(
                'revenue' => 'SUM(main_table.base_grand_total)'
            ));
        }

        $this->getSelect()->columns(array(
            'quantity' => 'COUNT(main_table.entity_id)',
            'range' => $this->_getRangeExpressionForAttribute($range, 'created_at'),
        ))->order('range', 'asc')
            ->group('range');

        $this->addFieldToFilter('created_at', $this->getDateRange($range, $customStart, $customEnd))
            ->addFieldToFilter('state', array('neq' => Mage_Sales_Model_Order::STATE_CANCELED));
        return $this;
    }

    /**
     * Prepare report summary from aggregated data
     *
     * @param string $range
     * @param mixed $customStart
     * @param mixed $customEnd
     * @param int $isFilter
     * @return Mage_Reports_Model_Mysql4_Order_Collection
     */
    protected function _prepareSummaryAggregated($range, $customStart, $customEnd)
    {
        $this->setMainTable('sales/order_aggregated_created');
        $this->getSelect()->columns(array(
            'revenue' => 'SUM(main_table.total_revenue_amount)',
            'quantity' => 'SUM(main_table.orders_count)',
            'range' => $this->_getRangeExpressionForAttribute($range, 'main_table.period'),
        ))->order('range', 'asc')
        ->group('range');

        $this->getSelect()->where(
            $this->_getConditionSql('main_table.period', $this->getDateRange($range, $customStart, $customEnd))
        );

        $statuses = Mage::getSingleton('sales/config')
            ->getOrderStatusesForState(Mage_Sales_Model_Order::STATE_CANCELED);

        if (empty($statuses)) {
            $statuses = array(0);
        }

        $this->getSelect()->where('main_table.order_status NOT IN(?)', $statuses);
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

    /**
     * Retriev range exression adapteted for attribute
     *
     * @param string $range
     * @param unknown_type $attribute
     */
    protected function _getRangeExpressionForAttribute($range, $attribute)
    {
        $expression = $this->_getRangeExpression($range);
        return str_replace('{{attribute}}', $this->getConnection()->quoteIdentifier($attribute), $expression);
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
        $this->getSelect()->columns(array('items_count'=>'total_item_count'), 'main_table');
        return $this;
    }

    /**
     * Calculate totals report
     *
     * @param int $isFilter
     * @return Mage_Reports_Model_Mysql4_Order_Collection
     */
    public function calculateTotals($isFilter = 0)
    {
        if ($this->isLive()) {
            $this->_calculateTotalsLive($isFilter);
        } else {
            $this->_calculateTotalsAggregated($isFilter);
        }

        return $this;
    }

    /**
     * Calculate totals live report
     *
     * @param int $isFilter
     * @return Mage_Reports_Model_Mysql4_Order_Collection
     */
    protected function _calculateTotalsLive($isFilter = 0)
    {
        $this->setMainTable('sales/order');
        $this->removeAllFieldsFromSelect();

        if ($isFilter == 0) {
            $this->getSelect()->columns(array(
                'revenue' => 'SUM((main_table.base_subtotal-IFNULL(main_table.base_subtotal_refunded,0)-IFNULL(main_table.base_subtotal_canceled,0)-ABS(IFNULL(main_table.base_discount_amount,0))+IFNULL(main_table.base_discount_refunded,0))*main_table.base_to_global_rate)',
                'tax' => 'SUM((main_table.base_tax_amount-IFNULL(main_table.base_tax_refunded,0)-IFNULL(main_table.base_tax_canceled,0))*main_table.base_to_global_rate)',
                'shipping' => 'SUM((main_table.base_shipping_amount-IFNULL(main_table.base_shipping_refunded,0)-IFNULL(main_table.base_shipping_canceled,0))*main_table.base_to_global_rate)',
            ));
        } else {
            $this->getSelect()->columns(array(
                'revenue' => 'SUM((main_table.base_subtotal-IFNULL(main_table.base_subtotal_refunded,0)-IFNULL(main_table.base_subtotal_canceled,0)-ABS(IFNULL(main_table.base_discount_amount,0))+IFNULL(main_table.base_discount_refunded,0)))',
                'tax' => 'SUM((main_table.base_tax_amount-IFNULL(main_table.base_tax_refunded,0)-IFNULL(main_table.base_tax_canceled,0)))',
                'shipping' => 'SUM((main_table.base_shipping_amount-IFNULL(main_table.base_shipping_refunded,0)-IFNULL(main_table.base_shipping_canceled,0)))',
            ));
        }

        $this->getSelect()->columns(array(
                'quantity' => 'COUNT(main_table.entity_id)',
        ));

        $this->addFieldToFilter('state', array('neq' => Mage_Sales_Model_Order::STATE_CANCELED));

        return $this;
    }

    /**
     * Calculate totals agregated report
     *
     * @param int $isFilter
     * @return Mage_Reports_Model_Mysql4_Order_Collection
     */
    protected function _calculateTotalsAggregated($isFilter = 0)
    {
        $this->setMainTable('sales/order_aggregated_created');
        $this->removeAllFieldsFromSelect();

        $this->getSelect()->columns(array(
            'revenue' => 'SUM(main_table.total_revenue_amount)',
            'tax' => 'SUM(main_table.total_tax_amount_actual)',
            'shipping' => 'SUM(main_table.total_shipping_amount_actual)',
            'quantity' => 'SUM(orders_count)',
        ));

        $statuses = Mage::getSingleton('sales/config')
            ->getOrderStatusesForState(Mage_Sales_Model_Order::STATE_CANCELED);

        if (empty($statuses)) {
            $statuses = array(0);
        }

        $this->getSelect()->where('main_table.order_status NOT IN(?)', $statuses);

        return $this;
    }

    /**
     * Calculate lifitime sales
     *
     * @param int $isFilter
     * @return Mage_Reports_Model_Mysql4_Order_Collection
     */
    public function calculateSales($isFilter = 0)
    {
        $statuses = Mage::getSingleton('sales/config')
            ->getOrderStatusesForState(Mage_Sales_Model_Order::STATE_CANCELED);

        if (empty($statuses)) {
            $statuses = array(0);
        }

        if (Mage::getStoreConfig('sales/dashboard/use_aggregated_data')) {
            $this->setMainTable('sales/order_aggregated_created');
            $this->removeAllFieldsFromSelect();

            $this->getSelect()->columns(array(
                'lifetime' => 'SUM(main_table.total_revenue_amount)',
                'average'  => "IF(SUM(main_table.orders_count) > 0, SUM(main_table.total_revenue_amount)/SUM(main_table.orders_count), 0)"
            ));

            if (!$isFilter) {
                $this->addFieldToFilter('store_id',
                    array('eq' => Mage::app()->getStore(Mage_Core_Model_Store::ADMIN_CODE)->getId())
                );
            }
            $this->getSelect()->where('main_table.order_status NOT IN(?)', $statuses);
        } else {
            $this->setMainTable('sales/order');
            $this->removeAllFieldsFromSelect();
            $expr = 'IFNULL(main_table.base_subtotal, 0) - IFNULL(main_table.base_subtotal_refunded, 0)'
                . ' - IFNULL(main_table.base_subtotal_canceled, 0) - ABS(IFNULL(main_table.base_discount_amount, 0))'
                . ' + IFNULL(main_table.base_discount_refunded, 0)';

            $this->getSelect()->columns(array(
                'lifetime' => "SUM({$expr})",
                'average'  => "AVG({$expr})"
            ));
            $this->getSelect()->where('main_table.status NOT IN(?)', $statuses)
                ->where('main_table.state NOT IN(?)', array(Mage_Sales_Model_Order::STATE_NEW, Mage_Sales_Model_Order::STATE_PENDING_PAYMENT));
        }
        return $this;
    }

    public function setDateRange($from, $to)
    {
        $this->_reset()
            ->addFieldToFilter('created_at', array('from' => $from, 'to' => $to))
            ->addFieldToFilter('state', array('neq' => Mage_Sales_Model_Order::STATE_CANCELED))
            ->getSelect()
                ->columns(array('orders'=>'COUNT(DISTINCT(main_table.entity_id))'))
                ->group('("*")');

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

        $this->getSelect()->columns(array("items" => 'SUM(' . $this->getConnection()->quoteIdentifier('main_table.total_qty_ordered') . ')'));

        return $this;
    }

    public function setStoreIds($storeIds)
    {
        $vals = array_values($storeIds);
        if (count($storeIds) >= 1 && $vals[0] != '') {
            $this->getSelect()->columns(array(
                'subtotal' => 'SUM(main_table.base_subtotal)',
                'tax' => 'SUM(main_table.base_tax_amount)',
                'shipping' => 'SUM(main_table.base_shipping_amount)',
                'discount' => 'SUM(main_table.base_discount_amount)',
                'total' => 'SUM(main_table.base_grand_total)',
                'invoiced' => 'SUM(main_table.base_total_paid)',
                'refunded' => 'SUM(main_table.base_total_refunded)',
                'profit' => 'SUM(IFNULL(main_table.base_subtotal_invoiced, 0)) + SUM(IFNULL(main_table.base_discount_refunded, 0)) - SUM(IFNULL(main_table.base_subtotal_refunded, 0)) - SUM(IFNULL(main_table.base_discount_invoiced, 0)) - SUM(IFNULL(main_table.base_total_invoiced_cost, 0))',
            ));
        } else {
            $this->getSelect()->columns(array(
                'subtotal' => 'SUM(main_table.base_subtotal * main_table.base_to_global_rate)',
                'tax' => 'SUM(main_table.base_tax_amount * main_table.base_to_global_rate)',
                'shipping' => 'SUM(main_table.base_shipping_amount * main_table.base_to_global_rate)',
                'discount' => 'SUM(main_table.base_discount_amount * main_table.base_to_global_rate)',
                'total' => 'SUM(main_table.base_grand_total * main_table.base_to_global_rate)',
                'invoiced' => 'SUM(main_table.base_total_paid * main_table.base_to_global_rate)',
                'refunded' => 'SUM(main_table.base_total_refunded * main_table.base_to_global_rate)',
                'profit' => 'SUM(IFNULL(main_table.base_subtotal_invoiced, 0)* main_table.base_to_global_rate) + SUM(IFNULL(main_table.base_discount_refunded, 0)* main_table.base_to_global_rate) - SUM(IFNULL(main_table.base_subtotal_refunded, 0)* main_table.base_to_global_rate) - SUM(IFNULL(main_table.base_discount_invoiced, 0)* main_table.base_to_global_rate) - SUM(IFNULL(main_table.base_total_invoiced_cost, 0)* main_table.base_to_global_rate)',
            ));
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
        $this->getSelect()->group('main_table.customer_id');

        return $this;
    }

    /**
     * Join Customer Name (concat)
     *
     * @return Mage_Reports_Model_Mysql4_Order_Collection
     */
    public function joinCustomerName($alias = 'name')
    {
        $this->getSelect()->columns(array($alias => 'CONCAT(main_table.customer_firstname," ", main_table.customer_lastname)'));
        return $this;
    }

    /**
     * Add Order count field to select
     *
     * @return Mage_Reports_Model_Mysql4_Order_Collection
     */
    public function addOrdersCount()
    {
        $this->addFieldToFilter('state', array('neq' => Mage_Sales_Model_Order::STATE_CANCELED));
        $this->getSelect()
            ->columns(array("orders_count" => "COUNT(main_table.entity_id)"));

        return $this;
    }

    /**
     * Add revenue
     *
     * @param boolean $convertCurrency
     * @return Mage_Reports_Model_Mysql4_Order_Collection
     */
    public function addRevenueToSelect($convertCurrency = false)
    {
        if ($convertCurrency) {
            $this->getSelect()->columns(array(
                'revenue' => '(main_table.base_grand_total * main_table.base_to_global_rate)'
            ));
        } else {
            $this->getSelect()->columns(array(
                'revenue' => 'base_grand_total'
            ));
        }

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
            ? '(main_table.base_subtotal-IFNULL(main_table.base_subtotal_refunded,0)-IFNULL(main_table.base_subtotal_canceled,0))*main_table.base_to_global_rate'
            : 'main_table.base_subtotal-IFNULL(main_table.base_subtotal_canceled,0)-IFNULL(main_table.base_subtotal_refunded,0)';

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
        $this->setOrder('customer_id', $dir);
        return $this;
    }

    /**
     * Sort order by order created_at date
     * @param string $dir
     */
    public function orderByCreatedAt($dir = 'desc')
    {
        $this->setOrder('created_at', $dir);
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
        $countSelect->columns("count(DISTINCT main_table.entity_id)");

        $sql = $countSelect->__toString();

        return $sql;
    }

    /**
     * Initialize initial fields to select
     *
     * @return Mage_Reports_Model_Mysql4_Order_Collection
     */
    protected function _initInitialFieldsToSelect()
    {
        // No fields should be initialized
        return $this;
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

        $this->checkIsLive($period);

        if ($this->isLive()) {
            $fieldToFilter = 'created_at';
        } else {
            $fieldToFilter = 'period';
        }

        $this->addFieldToFilter($fieldToFilter, array(
            'from'  => $from->toString(Varien_Date::DATETIME_INTERNAL_FORMAT),
            'to'    => $to->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)
        ));

        return $this;
    }
}
