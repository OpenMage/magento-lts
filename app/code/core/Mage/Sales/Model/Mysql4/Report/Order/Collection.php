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
 * Report order collection
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Mysql4_Report_Order_Collection extends Mage_Sales_Model_Mysql4_Report_Collection_Abstract
{
    protected $_periodFormat;
    protected $_selectedColumns = array();

    /**
     * Initialize custom resource model
     */
    public function __construct()
    {
        parent::_construct();
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('sales/report')->init('sales/order_aggregated_created');
        $this->setConnection($this->getResource()->getReadConnection());
    }

    protected function _getSelectedColumns()
    {
        if ('month' == $this->_period) {
            $this->_periodFormat = 'DATE_FORMAT(period, \'%Y-%m\')';
        } elseif ('year' == $this->_period) {
            $this->_periodFormat = 'EXTRACT(YEAR FROM period)';
        } else {
            $this->_periodFormat = 'period';
        }

        if (!$this->isTotals()) {
            $this->_selectedColumns = array(
                'period'                         => $this->_periodFormat,
                'orders_count'                   => 'SUM(orders_count)',
                'total_qty_ordered'              => 'SUM(total_qty_ordered)',
                'total_qty_invoiced'             => 'SUM(total_qty_invoiced)',
                'total_income_amount'            => 'SUM(total_income_amount)',
                'total_revenue_amount'           => 'SUM(total_revenue_amount)',
                'total_profit_amount'            => 'SUM(total_profit_amount)',
                'total_invoiced_amount'          => 'SUM(total_invoiced_amount)',
                'total_canceled_amount'          => 'SUM(total_canceled_amount)',
                'total_paid_amount'              => 'SUM(total_paid_amount)',
                'total_refunded_amount'          => 'SUM(total_refunded_amount)',
                'total_tax_amount'               => 'SUM(total_tax_amount)',
                'total_tax_amount_actual'        => 'SUM(total_tax_amount_actual)',
                'total_shipping_amount'          => 'SUM(total_shipping_amount)',
                'total_shipping_amount_actual'   => 'SUM(total_shipping_amount_actual)',
                'total_discount_amount'          => 'SUM(total_discount_amount)',
                'total_discount_amount_actual'   => 'SUM(total_discount_amount_actual)',
            );
        }

        if ($this->isTotals()) {
            $this->_selectedColumns = $this->getAggregatedColumns();
        }

        return $this->_selectedColumns;
    }

    /**
     * Add selected data
     *
     * @return Mage_Sales_Model_Mysql4_Report_Order_Collection
     */
    protected function _initSelect()
    {
        $this->getSelect()->from($this->getResource()->getMainTable(), $this->_getSelectedColumns());
        if (!$this->isTotals()) {
            $this->getSelect()->group($this->_periodFormat);
        }
        return $this;
    }
}
