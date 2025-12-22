<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * Report collection abstract model
 *
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Report_Collection_Abstract extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * From date
     *
     * @var null|string
     */
    protected $_from               = null;

    /**
     * To date
     *
     * @var null|string
     */
    protected $_to                 = null;

    /**
     * Period
     *
     * @var string
     */
    protected $_period             = null;

    /**
     * Store ids
     *
     * @var array|int
     */
    protected $_storesIds          = 0;

    /**
     * Does filters should be applied
     *
     * @var bool
     */
    protected $_applyFilters       = true;

    /**
     * Is totals
     *
     * @var bool
     */
    protected $_isTotals           = false;

    /**
     * Is subtotals
     *
     * @var bool
     */
    protected $_isSubTotals        = false;

    /**
     * Aggregated columns
     *
     * @var array
     */
    protected $_aggregatedColumns  = [];

    /**
     * Set array of columns that should be aggregated
     *
     * @return $this
     */
    public function setAggregatedColumns(array $columns)
    {
        $this->_aggregatedColumns = $columns;
        return $this;
    }

    /**
     * Retrieve array of columns that should be aggregated
     *
     * @return array
     */
    public function getAggregatedColumns()
    {
        return $this->_aggregatedColumns;
    }

    /**
     * Set date range
     *
     * @param  mixed $from
     * @param  mixed $to
     * @return $this
     */
    public function setDateRange($from = null, $to = null)
    {
        $this->_from = $from;
        $this->_to   = $to;
        return $this;
    }

    /**
     * Set period
     *
     * @param  string $period
     * @return $this
     */
    public function setPeriod($period)
    {
        $this->_period = $period;
        return $this;
    }

    /**
     * Apply date range filter
     *
     * @return $this
     */
    protected function _applyDateRangeFilter()
    {
        // Remember that field PERIOD is a DATE(YYYY-MM-DD) in all databases including Oracle
        if ($this->_from !== null) {
            $this->getSelect()->where('period >= ?', $this->_from);
        }

        if ($this->_to !== null) {
            $this->getSelect()->where('period <= ?', $this->_to);
        }

        return $this;
    }

    /**
     * Set store ids
     *
     * @param  mixed $storeIds (null, int|string, array, array may contain null)
     * @return $this
     */
    public function addStoreFilter($storeIds)
    {
        $this->_storesIds = $storeIds;
        return $this;
    }

    /**
     * Apply stores filter to select object
     *
     * @return $this
     */
    protected function _applyStoresFilterToSelect(Zend_Db_Select $select)
    {
        $nullCheck = false;
        $storeIds  = $this->_storesIds;

        if (!is_array($storeIds)) {
            $storeIds = [$storeIds];
        }

        $storeIds = array_unique($storeIds);

        if ($index = array_search(null, $storeIds)) {
            unset($storeIds[$index]);
            $nullCheck = true;
        }

        $storeIds[0] = ($storeIds[0] == '') ? 0 : $storeIds[0];

        if ($nullCheck) {
            $select->where('store_id IN(?) OR store_id IS NULL', $storeIds);
        } else {
            $select->where('store_id IN(?)', $storeIds);
        }

        return $this;
    }

    /**
     * Apply stores filter
     *
     * @return $this
     */
    protected function _applyStoresFilter()
    {
        return $this->_applyStoresFilterToSelect($this->getSelect());
    }

    /**
     * Set apply filters flag
     *
     * @param  bool  $flag
     * @return $this
     */
    public function setApplyFilters($flag)
    {
        $this->_applyFilters = $flag;
        return $this;
    }

    /**
     * Getter/Setter for isTotals
     *
     * @param  null|bool  $flag
     * @return $this|bool
     */
    public function isTotals($flag = null)
    {
        if (is_null($flag)) {
            return $this->_isTotals;
        }

        $this->_isTotals = $flag;
        return $this;
    }

    /**
     * Getter/Setter for isSubTotals
     *
     * @param  null|bool  $flag
     * @return $this|bool
     */
    public function isSubTotals($flag = null)
    {
        if (is_null($flag)) {
            return $this->_isSubTotals;
        }

        $this->_isSubTotals = $flag;
        return $this;
    }

    /**
     * Custom filters application ability
     *
     * @return $this
     */
    protected function _applyCustomFilter()
    {
        return $this;
    }

    /**
     * Load data
     * Redeclare parent load method just for adding method _beforeLoad
     *
     * @inheritDoc
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        $this->_initSelect();
        if ($this->_applyFilters) {
            $this->_applyDateRangeFilter();
            $this->_applyStoresFilter();
            $this->_applyCustomFilter();
        }

        return parent::load($printQuery, $logQuery);
    }


    /**
     * Get SQL for get record count
     *
     * @return Varien_Db_Select
     * @see Mage_Reports_Model_Resource_Report_Product_Viewed_Collection
     * @see Mage_Sales_Model_Resource_Report_Bestsellers_Collection
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
     * @see Mage_Reports_Model_Resource_Report_Product_Viewed_Collection
     * @see Mage_Sales_Model_Resource_Report_Bestsellers_Collection
     */
    public function addStoreRestrictions($storeIds)
    {
        if (!is_array($storeIds)) {
            $storeIds = [$storeIds];
        }

        $currentStoreIds = $this->_storesIds;
        if (isset($currentStoreIds) && $currentStoreIds != Mage_Core_Model_App::ADMIN_STORE_ID
            && $currentStoreIds != [Mage_Core_Model_App::ADMIN_STORE_ID]
        ) {
            if (!is_array($currentStoreIds)) {
                $currentStoreIds = [$currentStoreIds];
            }

            $this->_storesIds = array_intersect($currentStoreIds, $storeIds);
        } else {
            $this->_storesIds = $storeIds;
        }

        return $this;
    }
}
