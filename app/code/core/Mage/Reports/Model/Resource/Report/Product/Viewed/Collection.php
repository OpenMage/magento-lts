<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Report most viewed collection
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Resource_Report_Product_Viewed_Collection extends Mage_Reports_Model_Resource_Report_Collection_Abstract
{
    /**
     * Rating limit
     *
     * @var int
     */
    protected $_ratingLimit        = 5;

    /**
     * Selected columns
     *
     * @var array
     */
    protected $_selectedColumns    = [];

    /**
     * Initialize custom resource model
     *
     */
    public function __construct()
    {
        parent::_construct();
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('sales/report')
            ->init(Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_DAILY);
        $this->setConnection($this->getResource()->getReadConnection());
        // overwrite default behaviour
        $this->_applyFilters = false;
    }

    /**
     * Retrieve selected columns
     *
     * @return array
     */
    protected function _getSelectedColumns()
    {
        $adapter = $this->getConnection();

        if (!$this->_selectedColumns) {
            if ($this->isTotals()) {
                $this->_selectedColumns = $this->getAggregatedColumns();
            } else {
                $this->_selectedColumns = [
                    'period'         =>  sprintf('MAX(%s)', $adapter->getDateFormatSql('period', '%Y-%m-%d')),
                    'views_num'      => 'SUM(views_num)',
                    'product_id'     => 'product_id',
                    'product_name'   => 'MAX(product_name)',
                    'product_price'  => 'MAX(product_price)',
                ];
                if ($this->_period == 'year') {
                    $this->_selectedColumns['period'] = $adapter->getDateFormatSql('period', '%Y');
                } elseif ($this->_period == 'month') {
                    $this->_selectedColumns['period'] = $adapter->getDateFormatSql('period', '%Y-%m');
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
        $adapter = $this->getConnection();
        $cols    = $this->_getSelectedColumns();
        $cols['views_num'] = 'SUM(views_num)';
        $select  = $adapter->select()
            ->from($this->getResource()->getMainTable(), $cols)
            ->where('period >= ?', $from)
            ->where('period <= ?', $to)
            ->group('product_id')
            ->order('views_num DESC')
            ->limit($this->_ratingLimit);

        $this->_applyStoresFilterToSelect($select);

        return $select;
    }

    /**
     * Init collection select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        $select = $this->getSelect();

        // if grouping by product, not by period
        if (!$this->_period) {
            $cols = $this->_getSelectedColumns();
            $cols['views_num'] = 'SUM(views_num)';
            if ($this->_from || $this->_to) {
                $mainTable = $this->getTable(Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_DAILY);
                $select->from($mainTable, $cols);
            } else {
                $mainTable = $this->getTable(Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_YEARLY);
                $select->from($mainTable, $cols);
            }

            //exclude removed products
            $subSelect = $this->getConnection()->select();
            $subSelect->from(['existed_products' => $this->getTable('catalog/product')], new Zend_Db_Expr('1)'));

            $select->exists($subSelect, $mainTable . '.product_id = existed_products.entity_id')
                ->group('product_id')
                ->order('views_num ' . Varien_Db_Select::SQL_DESC)
                ->limit($this->_ratingLimit);

            return $this;
        }

        if ($this->_period == 'year') {
            $mainTable = $this->getTable(Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_YEARLY);
            $select->from($mainTable, $this->_getSelectedColumns());
        } elseif ($this->_period == 'month') {
            $mainTable = $this->getTable(Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_MONTHLY);
            $select->from($mainTable, $this->_getSelectedColumns());
        } else {
            $mainTable = $this->getTable(Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_DAILY);
            $select->from($mainTable, $this->_getSelectedColumns());
        }
        if (!$this->isTotals()) {
            $select->group(['period', 'product_id']);
        }
        $select->where('rating_pos <= ?', $this->_ratingLimit);

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
        $select = clone $this->getSelect();
        $select->reset(Zend_Db_Select::ORDER);
        return $this->getConnection()->select()->from($select, 'COUNT(*)');
    }

    /**
     * Set ids for store restrictions
     *
     * @param  array $storeIds
     * @return $this
     */
    public function addStoreRestrictions($storeIds)
    {
        if (!is_array($storeIds)) {
            $storeIds = [$storeIds];
        }
        $currentStoreIds = $this->_storesIds;
        if (isset($currentStoreIds) && $currentStoreIds != Mage_Core_Model_App::ADMIN_STORE_ID
            && $currentStoreIds != [Mage_Core_Model_App::ADMIN_STORE_ID]) {
            if (!is_array($currentStoreIds)) {
                $currentStoreIds = [$currentStoreIds];
            }
            $this->_storesIds = array_intersect($currentStoreIds, $storeIds);
        } else {
            $this->_storesIds = $storeIds;
        }

        return $this;
    }

    /**
     * Redeclare parent method for applying filters after parent method
     * but before adding unions and calculating totals
     *
     * @return $this
     */
    protected function _beforeLoad()
    {
        parent::_beforeLoad();

        $this->_applyStoresFilter();

        if ($this->_period) {
            $selectUnions = [];

            // apply date boundaries (before calling $this->_applyDateRangeFilter())
            $dtFormat   = Varien_Date::DATE_INTERNAL_FORMAT;
            $periodFrom = (!is_null($this->_from) ? new Zend_Date($this->_from, $dtFormat) : null);
            $periodTo   = (!is_null($this->_to) ? new Zend_Date($this->_to, $dtFormat) : null);
            if ($this->_period == 'year') {
                if ($periodFrom) {
                    // not the first day of the year
                    if ($periodFrom->toValue(Zend_Date::MONTH) != 1 || $periodFrom->toValue(Zend_Date::DAY) != 1) {
                        $dtFrom = $periodFrom->getDate();
                        // last day of the year
                        $dtTo = $periodFrom->getDate()->setMonth(12)->setDay(31);
                        if (!$periodTo || $dtTo->isEarlier($periodTo)) {
                            $selectUnions[] = $this->_makeBoundarySelect(
                                $dtFrom->toString($dtFormat),
                                $dtTo->toString($dtFormat)
                            );

                            // first day of the next year
                            $this->_from = $periodFrom->getDate()
                                ->addYear(1)
                                ->setMonth(1)
                                ->setDay(1)
                                ->toString($dtFormat);
                        }
                    }
                }

                if ($periodTo) {
                    // not the last day of the year
                    if ($periodTo->toValue(Zend_Date::MONTH) != 12 || $periodTo->toValue(Zend_Date::DAY) != 31) {
                        $dtFrom = $periodTo->getDate()->setMonth(1)->setDay(1);  // first day of the year
                        $dtTo = $periodTo->getDate();
                        if (!$periodFrom || $dtFrom->isLater($periodFrom)) {
                            $selectUnions[] = $this->_makeBoundarySelect(
                                $dtFrom->toString($dtFormat),
                                $dtTo->toString($dtFormat)
                            );

                            // last day of the previous year
                            $this->_to = $periodTo->getDate()
                                ->subYear(1)
                                ->setMonth(12)
                                ->setDay(31)
                                ->toString($dtFormat);
                        }
                    }
                }

                if ($periodFrom && $periodTo) {
                    // the same year
                    if ($periodFrom->toValue(Zend_Date::YEAR) == $periodTo->toValue(Zend_Date::YEAR)) {
                        $dtFrom = $periodFrom->getDate();
                        $dtTo = $periodTo->getDate();
                        $selectUnions[] = $this->_makeBoundarySelect(
                            $dtFrom->toString($dtFormat),
                            $dtTo->toString($dtFormat)
                        );

                        $this->getSelect()->where('1<>1');
                    }
                }
            } elseif ($this->_period == 'month') {
                if ($periodFrom) {
                    // not the first day of the month
                    if ($periodFrom->toValue(Zend_Date::DAY) != 1) {
                        $dtFrom = $periodFrom->getDate();
                        // last day of the month
                        $dtTo = $periodFrom->getDate()->addMonth(1)->setDay(1)->subDay(1);
                        if (!$periodTo || $dtTo->isEarlier($periodTo)) {
                            $selectUnions[] = $this->_makeBoundarySelect(
                                $dtFrom->toString($dtFormat),
                                $dtTo->toString($dtFormat)
                            );

                            // first day of the next month
                            $this->_from = $periodFrom->getDate()->addMonth(1)->setDay(1)->toString($dtFormat);
                        }
                    }
                }

                if ($periodTo) {
                    // not the last day of the month
                    if ($periodTo->toValue(Zend_Date::DAY) != $periodTo->toValue(Zend_Date::MONTH_DAYS)) {
                        $dtFrom = $periodTo->getDate()->setDay(1);  // first day of the month
                        $dtTo = $periodTo->getDate();
                        if (!$periodFrom || $dtFrom->isLater($periodFrom)) {
                            $selectUnions[] = $this->_makeBoundarySelect(
                                $dtFrom->toString($dtFormat),
                                $dtTo->toString($dtFormat)
                            );

                            // last day of the previous month
                            $this->_to = $periodTo->getDate()->setDay(1)->subDay(1)->toString($dtFormat);
                        }
                    }
                }

                if ($periodFrom && $periodTo) {
                    // the same month
                    if ($periodFrom->toValue(Zend_Date::YEAR) == $periodTo->toValue(Zend_Date::YEAR)
                        && $periodFrom->toValue(Zend_Date::MONTH) == $periodTo->toValue(Zend_Date::MONTH)
                    ) {
                        $dtFrom = $periodFrom->getDate();
                        $dtTo = $periodTo->getDate();
                        $selectUnions[] = $this->_makeBoundarySelect(
                            $dtFrom->toString($dtFormat),
                            $dtTo->toString($dtFormat)
                        );

                        $this->getSelect()->where('1<>1');
                    }
                }
            }

            $this->_applyDateRangeFilter();

            // add unions to select
            if ($selectUnions) {
                $unionParts = [];
                $cloneSelect = clone $this->getSelect();
                /** @var Mage_Core_Model_Resource_Helper_Mysql4 $helper */
                $helper = Mage::getResourceHelper('core');
                $unionParts[] = '(' . $cloneSelect . ')';
                foreach ($selectUnions as $union) {
                    $query = $helper->getQueryUsingAnalyticFunction($union);
                    $unionParts[] = '(' . $query . ')';
                }
                $this->getSelect()->reset()->union($unionParts, Zend_Db_Select::SQL_UNION_ALL);
            }

            if ($this->isTotals()) {
                // calculate total
                $cloneSelect = clone $this->getSelect();
                $this->getSelect()->reset()->from($cloneSelect, $this->getAggregatedColumns());
            } else {
                // add sorting
                $this->getSelect()->order(['period ASC', 'views_num DESC']);
            }
        }

        return $this;
    }
}
