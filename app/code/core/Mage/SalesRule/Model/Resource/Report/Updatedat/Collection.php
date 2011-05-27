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
 * @package     Mage_SalesRule
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Sales report coupons collection
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_SalesRule_Model_Resource_Report_Updatedat_Collection
    extends Mage_Sales_Model_Resource_Report_Order_Updatedat_Collection
{
    /**
     * Selected columns array
     *
     * @var array
     */
    protected $_selectedColumns    = array();

    /**
     * Retrieve array of columns to select
     *
     * @return array
     */

    protected function _getSelectedColumns()
    {
        $adapter = $this->getConnection();

        $this->_selectedColumns = array(
            'store_id'                => 'MIN(e.store_id)',
            'order_status'            => 'MIN(e.status)',
            'coupon_code'             => 'MIN(e.coupon_code)',
            'coupon_uses'             => 'COUNT(e.entity_id)',

            'subtotal_amount'         =>
                'SUM((e.base_subtotal - '.
                $adapter->getIfNullSql('e.base_subtotal_canceled', 0).
                    ') * e.base_to_global_rate)',

            'discount_amount'         =>
                'SUM((ABS(e.base_discount_amount) - '.
                $adapter->getIfNullSql('e.base_discount_canceled', 0).
                    ') * e.base_to_global_rate)',

            'total_amount'            =>
                'SUM((e.base_subtotal - '.
                $adapter->getIfNullSql('e.base_subtotal_canceled', 0) . ' - ' .
                $adapter->getIfNullSql('ABS(e.base_discount_amount) - ' .
                $adapter->getIfNullSql('e.base_discount_canceled', 0), 0) .
                    ') * e.base_to_global_rate)',

            'subtotal_amount_actual'  =>
                'SUM((e.base_subtotal_invoiced - '.
                $adapter->getIfNullSql('e.base_subtotal_refunded', 0).
                    ') * e.base_to_global_rate)',

            'discount_amount_actual'  =>
                'SUM((e.base_discount_invoiced - '.
                $adapter->getIfNullSql('e.base_discount_refunded', 0).
                    ') * e.base_to_global_rate)',

            'total_amount_actual'     =>
                'SUM((e.base_subtotal_invoiced - '.
                $adapter->getIfNullSql('e.base_subtotal_refunded', 0) . ' - '.
                $adapter->getIfNullSql('e.base_discount_invoiced - '.
                $adapter->getIfNullSql('e.base_discount_refunded', 0), 0).
                    ') * e.base_to_global_rate)',
        );

        if (!$this->isTotals()) {


            if ('month' == $this->_period) {
                $this->_periodFormat = $adapter->getDateFormatSql(
                    'e.updated_at',
                    '%Y-%m'
                );
            } elseif ('year' == $this->_period) {
                $this->_periodFormat = $adapter->getDateExtractSql(
                    'e.updated_at',
                    Varien_Db_Adapter_Interface::INTERVAL_YEAR
                );
            } else {
                $this->_periodFormat = $adapter->getDateFormatSql(
                    'e.updated_at',
                    '%Y-%m-%d'
                );
            }
            $this->_selectedColumns['period'] = $this->_periodFormat;
        }

        return $this->_selectedColumns;
    }

    /**
     * Add selected data
     *
     * @return Mage_SalesRule_Model_Resource_Report_Updatedat_Collection
     */
    protected function _initSelect()
    {
        if ($this->_inited) {
            return $this;
        }

        $columns = $this->_getSelectedColumns();
        if ($this->isTotals() || $this->isSubTotals()) {
            unset($columns['coupon_code']);
        }

        $this->getSelect()
            ->from(array('e' => $this->getResource()->getMainTable()), $columns)
            ->where('e.coupon_code IS NOT NULL');

        $this->_applyStoresFilter();
        $this->_applyOrderStatusFilter();

        if ($this->_to !== null) {
            $this->getSelect()->where('e.updated_at <= ?', $this->_to);
        }
        if ($this->_from !== null) {
            $this->getSelect()->where('e.updated_at >= ?', $this->_from);
        }

        if ($this->isSubTotals()) {
            $this->getSelect()->group($this->_periodFormat);
        } else if (!$this->isTotals()) {
            $this->getSelect()->group(array(
                $this->_periodFormat,
                'coupon_code'
            ));
        }

        $this->getSelect()->having('COUNT(e.entity_id) > 0');

        $this->_inited = true;
        return $this;
    }
}
