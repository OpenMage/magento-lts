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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales report coupons collection
 *
 * @category   Mage
 * @package    Mage_SalesRule
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_SalesRule_Model_Mysql4_Report_Updatedat_Collection extends Mage_Sales_Model_Mysql4_Report_Order_Updatedat_Collection
{
    protected $_selectedColumns = array(
        'store_id'                => 'e.store_id',
        'order_status'            => 'e.status',
        'coupon_code'             => 'e.coupon_code',
        'coupon_uses'             => 'COUNT(e.`entity_id`)',
        'subtotal_amount'         => 'SUM((e.`base_subtotal` - IFNULL(e.`base_subtotal_canceled`, 0)) * e.`base_to_global_rate`)',
        'discount_amount'         => 'SUM((ABS(e.`base_discount_amount`) - IFNULL(e.`base_discount_canceled`, 0)) * e.`base_to_global_rate`)',
        'total_amount'            => 'SUM((e.`base_subtotal` - IFNULL(e.`base_subtotal_canceled`, 0) + IFNULL(ABS(e.`base_discount_amount`) - IFNULL(e.`base_discount_canceled`, 0), 0)) * e.`base_to_global_rate`)',
        'subtotal_amount_actual'  => 'SUM((e.`base_subtotal_invoiced` - IFNULL(e.`base_subtotal_refunded`, 0)) * e.`base_to_global_rate`)',
        'discount_amount_actual'  => 'SUM((e.`base_discount_invoiced` - IFNULL(e.`base_discount_refunded`, 0)) * e.`base_to_global_rate`)',
        'total_amount_actual'     => 'SUM((e.`base_subtotal_invoiced` - IFNULL(e.`base_subtotal_refunded`, 0) - IFNULL(e.`base_discount_invoiced` - IFNULL(e.`base_discount_refunded`, 0), 0)) * e.`base_to_global_rate`)',
    );

    /**
     * Add selected data
     *
     * @return Mage_SalesRule_Model_Mysql4_Report_Updatedat_Collection
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
            ->where('e.coupon_code <> ?', '');

        $this->_applyStoresFilter();
        $this->_applyOrderStatusFilter();

        if ($this->_to !== null) {
            $this->getSelect()->where('DATE(e.updated_at) <= DATE(?)', $this->_to);
        }
        if ($this->_from !== null) {
            $this->getSelect()->where('DATE(e.updated_at) >= DATE(?)', $this->_from);
        }

        if ($this->isSubTotals()) {
            $this->getSelect()->group($this->_periodFormat);
        } else if (!$this->isTotals()) {
            $this->getSelect()->group(array(
                $this->_periodFormat,
                'coupon_code'
            ));
        }

        $this->getSelect()->having('coupon_uses > 0');

        $this->_inited = true;
        return $this;
    }
}
