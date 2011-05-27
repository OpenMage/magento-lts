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
 * @package     Mage_Tax
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Tax report collection
 *
 * @category    Mage
 * @package     Mage_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tax_Model_Resource_Report_Updatedat_Collection extends Mage_Sales_Model_Resource_Report_Collection_Abstract
{
    /**
     * Period format
     *
     * @var unknown
     */
    protected $_periodFormat;

    /**
     * Flag if collection was initiated
     *
     * @var unknown
     */
    protected $_inited             = false;

    /**
     * Default selected columns
     *
     * @var unknown
     */
    protected $_selectedColumns    = array(
        'orders_count'          => 'COUNT(DISTINCT(e.entity_id))',
        'tax_base_amount_sum'   => 'SUM(tax.base_real_amount * e.base_to_global_rate)'
    );

    /**
     * Initialize custom resource model
     *
     */
    public function __construct()
    {
        parent::_construct();
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('sales/report')->init('sales/order', 'entity_id');
        $this->setConnection($this->getResource()->getReadConnection());
    }

    /**
     * Apply stores filter
     *
     * @return Mage_Tax_Model_Resource_Report_Updatedat_Collection
     */
    protected function _applyStoresFilter()
    {
        $nullCheck = false;
        $storeIds = $this->_storesIds;

        if (!is_array($storeIds)) {
            $storeIds = array($storeIds);
        }

        $storeIds = array_unique($storeIds);

        if ($index = array_search(null, $storeIds)) {
            unset($storeIds[$index]);
            $nullCheck = true;
        }

        if ($nullCheck) {
            $this->getSelect()->where('store_id IN(?) OR store_id IS NULL', $storeIds);
        } elseif ($storeIds[0] != '') {
            $this->getSelect()->where('store_id IN(?)', $storeIds);
        }

        return $this;
    }

    /**
     * Apply order status filter
     *
     * @return Mage_Tax_Model_Resource_Report_Updatedat_Collection
     */
    protected function _applyOrderStatusFilter()
    {
        if (is_null($this->_orderStatus)) {
            return $this;
        }
        $orderStatus = $this->_orderStatus;
        if (!is_array($orderStatus)) {
            $orderStatus = array($orderStatus);
        }
        $this->getSelect()->where('status IN(?)', $orderStatus);
        return $this;
    }

    /**
     * Retrieve array of columns to select
     *
     * @return array
     */
    protected function _getSelectedColumns()
    {
        $adapter = $this->getConnection();

        if ('month' == $this->_period) {
            $this->_periodFormat = $adapter->getDateFormatSql('e.updated_at', '%Y-%m');
        } elseif ('year' == $this->_period) {
            $this->_periodFormat = $adapter->getDateExtractSql('e.updated_at', Varien_Db_Adapter_Interface::INTERVAL_YEAR);
        } else {
            $this->_periodFormat = $adapter->getDateFormatSql('e.updated_at', '%Y-%m-%d');
        }

        // To make this query valid in MSSQL and Oralce we have to add
        // MIN() for store_id, status, percent.
        // You should aggregate additional columns if override

        if (!$this->isTotals() && !$this->isSubTotals()) {
            $this->_selectedColumns = array(
                'period'                => $this->_periodFormat,
                'store_id'              => 'MIN(store_id)',
                'code'                  => 'tax.code',
                'order_status'          => 'MIN(e.status)',
                'percent'               => 'MIN(' . $this->getConnection()->quoteIdentifier('tax.percent') . ')',
                'orders_count'          => 'COUNT(DISTINCT(e.entity_id))',
                'tax_base_amount_sum'   => 'SUM(tax.base_real_amount * e.base_to_global_rate)'
            );
        }

        if ($this->isSubTotals()) {
            $this->_selectedColumns += array('period' => $this->_periodFormat);
        }

        return $this->_selectedColumns;
    }

    /**
     * Add selected data
     *
     * @return Mage_Tax_Model_Resource_Report_Updatedat_Collection
     */
    protected function _initSelect()
    {
        if ($this->_inited) {
            return $this;
        }

        $columns = $this->_getSelectedColumns();
        $mainTable = $this->getResource()->getMainTable();

        $select = $this->getSelect()
            ->from(array('e' => $mainTable), $columns)
            ->joinInner(array('tax'=> $this->getTable('tax/sales_order_tax')), 'e.entity_id = tax.order_id', array());

        $this->_applyStoresFilter();
        $this->_applyOrderStatusFilter();

        $adapter = $this->getConnection();
        $dateUpdatedAt = $adapter->getDatePartSql('e.updated_at');

        if ($this->_to !== null) {
            $dateTo = $adapter->formatDate($this->_to, false);
            $select->where("{$dateUpdatedAt} <= {$dateTo}");
        }

        if ($this->_from !== null) {
            $dateFrom = $adapter->formatDate($this->_from, false);
            $select->where("{$dateUpdatedAt} >= {$dateFrom}");
        }

        if (!$this->isTotals() && !$this->isSubTotals()) {
            $select->group(array($this->_periodFormat, 'code', 'percent'));
        }

        if ($this->isSubTotals()) {
            $select->group(array(
                $this->_periodFormat
            ));
        }

        $this->_inited = true;
        return $this;
    }

    /**
     * Load
     *
     * @param boolean $printQuery
     * @param boolean $logQuery
     * @return Mage_Tax_Model_Resource_Report_Updatedat_Collection
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        $this->_initSelect();
        $this->setApplyFilters(false);
        return parent::load($printQuery, $logQuery);
    }
}
