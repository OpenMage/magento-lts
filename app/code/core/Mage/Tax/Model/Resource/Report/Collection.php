<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax report collection
 *
 * @category   Mage
 * @package    Mage_Tax
 */
class Mage_Tax_Model_Resource_Report_Collection extends Mage_Sales_Model_Resource_Report_Collection_Abstract
{
    /**
     * @var Zend_Db_Expr
     */
    protected $_periodFormat;

    /**
     * Aggregated Data Table
     *
     * @var string
     */
    protected $_aggregationTable = 'tax/tax_order_aggregated_created';

    /**
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
        $this->_resource = Mage::getResourceModel('sales/report')->init($this->_aggregationTable);
        $this->setConnection($this->getResource()->getReadConnection());
    }

    /**
     * @return array
     */
    protected function _getSelectedColumns()
    {
        if ($this->_period == 'month') {
            $this->_periodFormat = $this->getConnection()->getDateFormatSql('period', '%Y-%m');
        } elseif ($this->_period == 'year') {
            $this->_periodFormat = $this->getConnection()->getDateFormatSql('period', '%Y');
        } else {
            $this->_periodFormat = $this->getConnection()->getDateFormatSql('period', '%Y-%m-%d');
        }

        if (!$this->isTotals() && !$this->isSubTotals()) {
            $this->_selectedColumns = [
                'period'                => $this->_periodFormat,
                'code'                  => 'code',
                'percent'               => 'percent',
                'orders_count'          => 'SUM(orders_count)',
                'tax_base_amount_sum'   => 'SUM(tax_base_amount_sum)'
            ];
        }

        if ($this->isTotals()) {
            $this->_selectedColumns = $this->getAggregatedColumns();
        }

        if ($this->isSubTotals()) {
            $this->_selectedColumns = $this->getAggregatedColumns() + ['period' => $this->_periodFormat];
        }

        return $this->_selectedColumns;
    }

    /**
     * Add selected data
     *
     * @return $this
     */
    protected function _initSelect()
    {
        $this->getSelect()->from($this->getResource()->getMainTable(), $this->_getSelectedColumns());
        if (!$this->isTotals() && !$this->isSubTotals()) {
            $this->getSelect()->group([$this->_periodFormat, 'code', 'percent']);
        }

        if ($this->isSubTotals()) {
            $this->getSelect()->group([
                $this->_periodFormat
            ]);
        }

        /**
         * Allow to use analytic function
         */
        $this->_useAnalyticFunction = true;

        return $this;
    }
}
