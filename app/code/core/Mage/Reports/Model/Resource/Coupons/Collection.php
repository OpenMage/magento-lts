<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Coupons Report collection
 *
 * @category   Mage
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Coupons_Collection extends Mage_Sales_Model_Entity_Order_Collection
{
    /**
     * From value
     *
     * @var string
     */
    protected $_from     = '';

    /**
     * To value
     *
     * @var string
     */
    protected $_to       = '';

    /**
     * Set date range
     *
     * @param string $from
     * @param string $to
     * @return $this
     */
    public function setDateRange($from, $to)
    {
        $this->_from = $from;
        $this->_to   = $to;
        $this->_reset();
        return $this;
    }

    /**
     * Set store ids
     *
     * @param array $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds)
    {
        $this->joinFields($this->_from, $this->_to, $storeIds);
        return $this;
    }

    /**
     * Join fields
     *
     * @param string $from
     * @param string $to
     * @param array $storeIds
     */
    public function joinFields($from, $to, $storeIds = [])
    {
        $this->groupByAttribute('coupon_code')
            ->addAttributeToFilter('created_at', ['from' => $from, 'to' => $to, 'datetime' => true])
            ->addAttributeToFilter('coupon_code', ['neq' => ''])
            ->getSelect()->columns(['uses' => 'COUNT(e.entity_id)'])
            ->having('uses > ?', 0)
            ->order('uses ' . self::SORT_ORDER_DESC);

        $storeIds = array_values($storeIds);
        if (count($storeIds) >= 1 && $storeIds[0] != '') {
            $this->addAttributeToFilter('store_id', ['in' => $storeIds]);
            $this->addExpressionAttributeToSelect(
                'subtotal',
                'SUM({{base_subtotal}})',
                ['base_subtotal']
            )
            ->addExpressionAttributeToSelect(
                'discount',
                'SUM({{base_discount_amount}})',
                ['base_discount_amount']
            )
            ->addExpressionAttributeToSelect(
                'total',
                'SUM({{base_subtotal}}-{{base_discount_amount}})',
                ['base_subtotal', 'base_discount_amount']
            );
        } else {
            $this->addExpressionAttributeToSelect(
                'subtotal',
                'SUM({{base_subtotal}}*{{base_to_global_rate}})',
                ['base_subtotal', 'base_to_global_rate']
            )
            ->addExpressionAttributeToSelect(
                'discount',
                'SUM({{base_discount_amount}}*{{base_to_global_rate}})',
                ['base_discount_amount', 'base_to_global_rate']
            )
            ->addExpressionAttributeToSelect(
                'total',
                'SUM(({{base_subtotal}}-{{base_discount_amount}})*{{base_to_global_rate}})',
                ['base_subtotal', 'base_discount_amount', 'base_to_global_rate']
            );
        }
    }

    /**
     * Get select count sql
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::HAVING);
        $countSelect->columns('COUNT(DISTINCT main_table.rule_id)');

        return $countSelect;
    }
}
