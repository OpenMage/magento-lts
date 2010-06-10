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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Report bestsellers collection
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Mysql4_Report_Bestsellers_Collection extends Mage_Sales_Model_Mysql4_Report_Collection_Abstract
{
    /**
     * @var int
     */
    protected $_ratingLimit = 5;

    protected $_selectedColumns = array();

    /**
     * Initialize custom resource model
     */
    public function __construct()
    {
        parent::_construct();
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('sales/report')->init('sales/bestsellers_aggregated_daily');
        $this->setConnection($this->getResource()->getReadConnection());
        // overwrite default behaviour
        $this->_applyFilters = false;
    }

    protected function _getSelectedColumns()
    {
        if (!$this->_selectedColumns) {
            if ($this->isTotals()) {
                $this->_selectedColumns = $this->getAggregatedColumns();
            } else {
                $this->_selectedColumns = array(
                    'period'         => 'period',
                    'qty_ordered'    => 'qty_ordered',
                    'product_id'     => 'product_id',
                    'product_name'   => 'product_name',
                    'product_price'  => 'product_price',
                );
                if ('year' == $this->_period) {
                    $this->_selectedColumns['period'] = 'YEAR(period)';
                } else if ('month' == $this->_period) {
                    $this->_selectedColumns['period'] = "DATE_FORMAT(period, '%Y-%m')";
                }
            }
        }
        return $this->_selectedColumns;
    }

    /**
     * Make select object for date boundary
     *
     * @param mixed $from
     * @param mixed $to
     * @return Zend_Db_Select
     */
    protected function _makeBoundarySelect($from, $to)
    {
        $cols = $this->_getSelectedColumns();
        $cols['qty_ordered'] = 'SUM(qty_ordered)';
        $sel = $this->getConnection()->select()
            ->from($this->getResource()->getMainTable(), $cols)
            ->where('period >= ?', $from)
            ->where('period <= ?', $to)
            ->group('product_id')
            ->order('qty_ordered DESC')
            ->limit($this->_ratingLimit);
        $this->_applyStoresFilterToSelect($sel);
        return $sel;
    }

    /**
     * Add selected data
     *
     * @return Mage_Sales_Model_Mysql4_Report_Order_Collection
     */
    protected function _initSelect()
    {
        // if grouping by product, not by period
        if (!$this->_period) {
            $cols = $this->_getSelectedColumns();
            $cols['qty_ordered'] = 'SUM(qty_ordered)';
            if ($this->_from || $this->_to) {
                $this->getSelect()->from($this->getTable('sales/bestsellers_aggregated_daily'), $cols);
            } else {
                $this->getSelect()->from($this->getTable('sales/bestsellers_aggregated_yearly'), $cols);
            }
            $this->_applyStoresFilter();
            $this->_applyDateRangeFilter();
            $this->getSelect()
                ->group('product_id')
                ->order('qty_ordered DESC')
                ->limit($this->_ratingLimit);
            return $this;
        }


        if ('year' == $this->_period) {
            $this->getSelect()->from($this->getTable('sales/bestsellers_aggregated_yearly'), $this->_getSelectedColumns());
        } else if ('month' == $this->_period) {
            $this->getSelect()->from($this->getTable('sales/bestsellers_aggregated_monthly'), $this->_getSelectedColumns());
        } else {
            $this->getSelect()->from($this->getTable('sales/bestsellers_aggregated_daily'), $this->_getSelectedColumns());
        }
        if (!$this->isTotals()) {
            $this->getSelect()->group(array('period', 'product_id'));
        }
        $this->getSelect()->where('rating_pos <= ?', $this->_ratingLimit);

        //
        $selectUnions = array();

        // apply date boundaries (before calling $this->_applyDateRangeFilter())
        $dtFormat   = Varien_Date::DATE_INTERNAL_FORMAT;
        $periodFrom = (!is_null($this->_from) ? new Zend_Date($this->_from, $dtFormat) : null);
        $periodTo   = (!is_null($this->_to)   ? new Zend_Date($this->_to,   $dtFormat) : null);
        if ('year' == $this->_period) {

            if ($periodFrom) {
                if ($periodFrom->toValue(Zend_Date::MONTH) != 1 || $periodFrom->toValue(Zend_Date::DAY) != 1) {  // not the first day of the year
                    $dtFrom = $periodFrom->getDate();
                    $dtTo = $periodFrom->getDate()->setMonth(12)->setDay(31);  // last day of the year
                    if (!$periodTo || $dtTo->isEarlier($periodTo)) {
                        $selectUnions[] = $this->_makeBoundarySelect($dtFrom->toString($dtFormat), $dtTo->toString($dtFormat));

                        $this->_from = $periodFrom->getDate()->addYear(1)->setMonth(1)->setDay(1)->toString($dtFormat);  // first day of the next year
                    }
                }
            }

            if ($periodTo) {
                if ($periodTo->toValue(Zend_Date::MONTH) != 12 || $periodTo->toValue(Zend_Date::DAY) != 31) {  // not the last day of the year
                    $dtFrom = $periodTo->getDate()->setMonth(1)->setDay(1);  // first day of the year
                    $dtTo = $periodTo->getDate();
                    if (!$periodFrom || $dtFrom->isLater($periodFrom)) {
                        $selectUnions[] = $this->_makeBoundarySelect($dtFrom->toString($dtFormat), $dtTo->toString($dtFormat));

                        $this->_to = $periodTo->getDate()->subYear(1)->setMonth(12)->setDay(31)->toString($dtFormat);  // last day of the previous year
                    }
                }
            }

            if ($periodFrom && $periodTo) {
                if ($periodFrom->toValue(Zend_Date::YEAR) == $periodTo->toValue(Zend_Date::YEAR)) {  // the same year
                    $dtFrom = $periodFrom->getDate();
                    $dtTo = $periodTo->getDate();
                    $selectUnions[] = $this->_makeBoundarySelect($dtFrom->toString($dtFormat), $dtTo->toString($dtFormat));

                    $this->getSelect()->where('1<>1');
                }
            }

        }
        else if ('month' == $this->_period) {

            if ($periodFrom) {
                if ($periodFrom->toValue(Zend_Date::DAY) != 1) {  // not the first day of the month
                    $dtFrom = $periodFrom->getDate();
                    $dtTo = $periodFrom->getDate()->addMonth(1)->setDay(1)->subDay(1);  // last day of the month
                    if (!$periodTo || $dtTo->isEarlier($periodTo)) {
                        $selectUnions[] = $this->_makeBoundarySelect($dtFrom->toString($dtFormat), $dtTo->toString($dtFormat));

                        $this->_from = $periodFrom->getDate()->addMonth(1)->setDay(1)->toString($dtFormat);  // first day of the next month
                    }
                }
            }

            if ($periodTo) {
                if ($periodTo->toValue(Zend_Date::DAY) != $periodTo->toValue(Zend_Date::MONTH_DAYS)) {  // not the last day of the month
                    $dtFrom = $periodTo->getDate()->setDay(1);  // first day of the month
                    $dtTo = $periodTo->getDate();
                    if (!$periodFrom || $dtFrom->isLater($periodFrom)) {
                        $selectUnions[] = $this->_makeBoundarySelect($dtFrom->toString($dtFormat), $dtTo->toString($dtFormat));

                        $this->_to = $periodTo->getDate()->setDay(1)->subDay(1)->toString($dtFormat);  // last day of the previous month
                    }
                }
            }

            if ($periodFrom && $periodTo) {
                if ($periodFrom->toValue(Zend_Date::YEAR) == $periodTo->toValue(Zend_Date::YEAR)
                    && $periodFrom->toValue(Zend_Date::MONTH) == $periodTo->toValue(Zend_Date::MONTH)) {  // the same month
                    $dtFrom = $periodFrom->getDate();
                    $dtTo = $periodTo->getDate();
                    $selectUnions[] = $this->_makeBoundarySelect($dtFrom->toString($dtFormat), $dtTo->toString($dtFormat));

                    $this->getSelect()->where('1<>1');
                }
            }

        }

        $this->_applyStoresFilter();
        $this->_applyDateRangeFilter();

        // add unions to select
        if ($selectUnions) {
            $unionParts = array();
            $cloneSelect = clone $this->getSelect();
            $unionParts[] = '(' . $cloneSelect . ')';
            foreach ($selectUnions as $union) {
                $unionParts[] = '(' . $union . ')';
            }
            $this->getSelect()->reset()->union($unionParts, Zend_Db_Select::SQL_UNION_ALL);
        }

        if ($this->isTotals()) {
            // calculate total
            $cloneSelect = clone $this->getSelect();
            $this->getSelect()->reset()->from($cloneSelect, $this->getAggregatedColumns());
        } else {
            // add sorting
            $this->getSelect()->order(array('period ASC', 'qty_ordered DESC'));
        }

        return $this;
    }

    /**
     * Get SQL for get record count
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();
        return $this->getConnection()->select()->from($this->getSelect(), 'COUNT(*)');
    }

}
