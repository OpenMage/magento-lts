<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales report shipping collection
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Report_Shipping_Collection_Order extends Mage_Sales_Model_Resource_Report_Collection_Abstract
{
    /**
     * Period format
     *
     * @var Zend_Db_Expr
     */
    protected $_periodFormat;

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
        $this->_resource = Mage::getResourceModel('sales/report')->init('sales/shipping_aggregated_order');
        $this->setConnection($this->getResource()->getReadConnection());
    }

    /**
     * Get selected columns
     *
     * @return array
     */
    protected function _getSelectedColumns()
    {
        $adapter = $this->getConnection();
        if ($this->_period == 'month') {
            $this->_periodFormat = $adapter->getDateFormatSql('period', '%Y-%m');
        } elseif ($this->_period == 'year') {
            $this->_periodFormat = $adapter->getDateExtractSql('period', Varien_Db_Adapter_Interface::INTERVAL_YEAR);
        } else {
            $this->_periodFormat = $adapter->getDateFormatSql('period', '%Y-%m-%d');
        }

        if (!$this->isTotals() && !$this->isSubTotals()) {
            $this->_selectedColumns = [
                'period'                => $this->_periodFormat,
                'shipping_description'  => 'shipping_description',
                'orders_count'          => 'SUM(orders_count)',
                'total_shipping'        => 'SUM(total_shipping)',
                'total_shipping_actual' => 'SUM(total_shipping_actual)',
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
        $this->getSelect()->from(
            $this->getResource()->getMainTable(),
            $this->_getSelectedColumns(),
        );

        if (!$this->isTotals() && !$this->isSubTotals()) {
            $this->getSelect()->group([
                $this->_periodFormat,
                'shipping_description',
            ]);
        }
        if ($this->isSubTotals()) {
            $this->getSelect()->group([
                $this->_periodFormat,
            ]);
        }
        return $this;
    }
}
