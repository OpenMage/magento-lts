<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Grid_Abstract extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_resourceCollectionName  = '';
    protected $_currentCurrencyCode     = null;
    protected $_storeIds                = [];
    protected $_aggregatedColumns       = null;

    /**
     * Column for grid to be grouped by
     *
     * @var string
     */
    protected $_columnGroupBy;

    /**
     * Mage_Adminhtml_Block_Report_Grid_Abstract constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setUseAjax(false);
        if (isset($this->_columnGroupBy)) {
            $this->isColumnGrouped($this->_columnGroupBy, true);
        }
        $this->setEmptyCellLabel(Mage::helper('adminhtml')->__('No records found for this period.'));
    }

    /**
     * @return string
     */
    public function getResourceCollectionName()
    {
        return $this->_resourceCollectionName;
    }

    /**
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract|Mage_Reports_Model_Grouped_Collection
     */
    public function getCollection()
    {
        if (is_null($this->_collection)) {
            $collection = Mage::getModel('reports/grouped_collection');
            $this->setCollection($collection);
        }
        return $this->_collection;
    }

    /**
     * @return array
     */
    protected function _getAggregatedColumns()
    {
        if (is_null($this->_aggregatedColumns)) {
            foreach ($this->getColumns() as $column) {
                if (!is_array($this->_aggregatedColumns)) {
                    $this->_aggregatedColumns = [];
                }
                if ($column->hasTotal()) {
                    $this->_aggregatedColumns[$column->getId()] = "{$column->getTotal()}({$column->getIndex()})";
                }
            }
        }
        return $this->_aggregatedColumns;
    }

    /**
     * Add column to grid
     * Overridden to add support for visibility_filter column option
     * It stands for conditional visibility of the column depending on filter field values
     * Value of visibility_filter supports (filter_field_name => filter_field_value) pairs
     *
     * @param   string $columnId
     * @param   array $column
     * @return  Mage_Adminhtml_Block_Report_Grid_Abstract
     */
    public function addColumn($columnId, $column)
    {
        if (is_array($column) && array_key_exists('visibility_filter', $column)) {
            $filterData = $this->getFilterData();
            $visibilityFilter = $column['visibility_filter'];
            if (!is_array($visibilityFilter)) {
                $visibilityFilter = [$visibilityFilter];
            }
            foreach ($visibilityFilter as $k => $v) {
                if (is_int($k)) {
                    $filterFieldId = $v;
                    $filterFieldValue = true;
                } else {
                    $filterFieldId = $k;
                    $filterFieldValue = $v;
                }
                if (!$filterData->hasData($filterFieldId) ||
                    $filterData->getData($filterFieldId) != $filterFieldValue
                ) {
                    return $this;  // don't add column
                }
            }
        }
        return parent::addColumn($columnId, $column);
    }

    /**
     * Get allowed store ids array intersected with selected scope in store switcher
     *
     * @return array
     */
    protected function _getStoreIds()
    {
        $filterData = $this->getFilterData();
        if ($filterData) {
            $storeIds = explode(',', (string) $filterData->getData('store_ids'));
        } else {
            $storeIds = [];
        }
        // By default storeIds array contains only allowed stores
        $allowedStoreIds = array_keys(Mage::app()->getStores());
        // And then array_intersect with post data for prevent unauthorized stores reports
        $storeIds = array_intersect($allowedStoreIds, $storeIds);
        // If selected all websites or unauthorized stores use only allowed
        if (empty($storeIds)) {
            $storeIds = $allowedStoreIds;
        }
        // reset array keys
        $storeIds = array_values($storeIds);

        return $storeIds;
    }

    protected function _prepareCollection()
    {
        $filterData = $this->getFilterData();

        if ($filterData->getData('from') == null || $filterData->getData('to') == null) {
            $this->setCountTotals(false);
            $this->setCountSubTotals(false);
            return parent::_prepareCollection();
        }

        $storeIds = $this->_getStoreIds();

        $orderStatuses = $filterData->getData('order_statuses');
        if (is_array($orderStatuses)) {
            if (count($orderStatuses) == 1 && str_contains($orderStatuses[0], ',')) {
                $filterData->setData('order_statuses', explode(',', $orderStatuses[0]));
            }
        }

        /** @var Mage_Sales_Model_Resource_Report_Collection_Abstract $resourceCollection */
        $resourceCollection = Mage::getResourceModel($this->getResourceCollectionName());
        $resourceCollection
            ->setPeriod($filterData->getData('period_type'))
            ->setDateRange($filterData->getData('from', null), $filterData->getData('to', null))
            ->addStoreFilter($storeIds)
            ->setAggregatedColumns($this->_getAggregatedColumns());

        $this->_addOrderStatusFilter($resourceCollection, $filterData);
        $this->_addCustomFilter($resourceCollection, $filterData);

        if ($this->_isExport) {
            $this->setCollection($resourceCollection);
            return $this;
        }

        if ($filterData->getData('show_empty_rows', false)) {
            Mage::helper('reports')->prepareIntervalsCollection(
                $this->getCollection(),
                $filterData->getData('from', null),
                $filterData->getData('to', null),
                $filterData->getData('period_type'),
            );
        }

        if ($this->getCountSubTotals()) {
            $this->getSubTotals();
        }

        if ($this->getCountTotals()) {
            /** @var Mage_Sales_Model_Resource_Report_Collection_Abstract $totalsCollection */
            $totalsCollection = Mage::getResourceModel($this->getResourceCollectionName());
            $totalsCollection
                ->setPeriod($filterData->getData('period_type'))
                ->setDateRange($filterData->getData('from', null), $filterData->getData('to', null))
                ->addStoreFilter($storeIds)
                ->setAggregatedColumns($this->_getAggregatedColumns())
                ->isTotals(true);

            $this->_addOrderStatusFilter($totalsCollection, $filterData);
            $this->_addCustomFilter($totalsCollection, $filterData);

            foreach ($totalsCollection as $item) {
                $this->setTotals($item);
                break;
            }
        }

        $this->getCollection()->setColumnGroupBy($this->_columnGroupBy);
        $this->getCollection()->setResourceCollection($resourceCollection);

        return parent::_prepareCollection();
    }

    public function getCountTotals()
    {
        if (!$this->getTotals()) {
            $filterData = $this->getFilterData();
            /** @var Mage_Sales_Model_Resource_Report_Collection_Abstract $totalsCollection */
            $totalsCollection = Mage::getResourceModel($this->getResourceCollectionName());
            $totalsCollection
                ->setPeriod($filterData->getData('period_type'))
                ->setDateRange($filterData->getData('from', null), $filterData->getData('to', null))
                ->addStoreFilter($this->_getStoreIds())
                ->setAggregatedColumns($this->_getAggregatedColumns())
                ->isTotals(true);

            $this->_addOrderStatusFilter($totalsCollection, $filterData);
            $this->_addCustomFilter($totalsCollection, $filterData);

            if (count($totalsCollection->getItems()) < 1 || !$filterData->getData('from')) {
                $this->setTotals(new Varien_Object());
            } else {
                foreach ($totalsCollection->getItems() as $item) {
                    $this->setTotals($item);
                    break;
                }
            }
        }
        return parent::getCountTotals();
    }

    public function getSubTotals()
    {
        $filterData = $this->getFilterData();
        /** @var Mage_Sales_Model_Resource_Report_Collection_Abstract $subTotalsCollection */
        $subTotalsCollection = Mage::getResourceModel($this->getResourceCollectionName());
        $subTotalsCollection
            ->setPeriod($filterData->getData('period_type'))
            ->setDateRange($filterData->getData('from', null), $filterData->getData('to', null))
            ->addStoreFilter($this->_getStoreIds())
            ->setAggregatedColumns($this->_getAggregatedColumns())
            ->isSubTotals(true);

        $this->_addOrderStatusFilter($subTotalsCollection, $filterData);
        $this->_addCustomFilter($subTotalsCollection, $filterData);

        $this->setSubTotals($subTotalsCollection->getItems());
        return parent::getSubTotals();
    }

    public function setStoreIds($storeIds)
    {
        $this->_storeIds = $storeIds;
        return $this;
    }

    public function getCurrentCurrencyCode()
    {
        if (is_null($this->_currentCurrencyCode)) {
            $this->_currentCurrencyCode = (count($this->_storeIds) > 0)
                ? Mage::app()->getStore(array_shift($this->_storeIds))->getBaseCurrencyCode()
                : Mage::app()->getStore()->getBaseCurrencyCode();
        }
        return $this->_currentCurrencyCode;
    }

    /**
     * Get currency rate (base to given currency)
     *
     * @param string|Mage_Directory_Model_Currency $toCurrency
     * @return double
     */
    public function getRate($toCurrency)
    {
        return Mage::app()->getStore()->getBaseCurrency()->getRate($toCurrency);
    }

    /**
     * Add order status filter
     *
     * @param Mage_Sales_Model_Resource_Report_Collection_Abstract $collection
     * @param Varien_Object $filterData
     * @return $this
     */
    protected function _addOrderStatusFilter($collection, $filterData)
    {
        $collection->addOrderStatusFilter($filterData->getData('order_statuses'));
        return $this;
    }

    /**
     * Adds custom filter to resource collection
     * Can be overridden in child classes if custom filter needed
     *
     * @param Mage_Sales_Model_Resource_Report_Collection_Abstract $collection
     * @param Varien_Object $filterData
     * @return $this
     */
    protected function _addCustomFilter($collection, $filterData)
    {
        return $this;
    }
}
