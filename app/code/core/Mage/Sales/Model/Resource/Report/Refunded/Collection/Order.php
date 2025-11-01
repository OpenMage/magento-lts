<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Sales report refunded collection
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Report_Refunded_Collection_Order extends Mage_Sales_Model_Resource_Report_Collection_Abstract
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
     */
    public function __construct()
    {
        parent::_construct();
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('sales/report')->init('sales/refunded_aggregated_order');
        $this->setConnection($this->getResource()->getReadConnection());
    }

    /**
     * Retrieve selected columns
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

        if (!$this->isTotals()) {
            $this->_selectedColumns = [
                'period'            => $this->_periodFormat,
                'orders_count'      => 'SUM(orders_count)',
                'refunded'          => 'SUM(refunded)',
                'online_refunded'   => 'SUM(online_refunded)',
                'offline_refunded'  => 'SUM(offline_refunded)',
            ];
        }

        if ($this->isTotals()) {
            $this->_selectedColumns = $this->getAggregatedColumns();
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
        if (!$this->isTotals()) {
            $this->getSelect()->group($this->_periodFormat);
        }

        return $this;
    }
}
