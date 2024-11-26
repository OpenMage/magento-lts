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
 * Reports invoiced collection
 *
 * @category   Mage
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Invoiced_Collection extends Mage_Sales_Model_Entity_Order_Collection
{
    /**
     * Set date range
     *
     * @param string $from
     * @param string $to
     * @return $this
     */
    public function setDateRange($from, $to)
    {
        $orderInvoicedExpr = $this->getConnection()->getCheckSql('{{base_total_invoiced}} > 0', '1', '0');
        $this->_reset()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('created_at', ['from' => $from, 'to' => $to])
            ->addExpressionAttributeToSelect(
                'orders',
                'COUNT({{base_total_invoiced}})',
                ['base_total_invoiced']
            )
            ->addExpressionAttributeToSelect(
                'orders_invoiced',
                "SUM({$orderInvoicedExpr})",
                ['base_total_invoiced']
            )
            ->addAttributeToFilter('state', ['neq' => Mage_Sales_Model_Order::STATE_CANCELED])
            ->getSelect()
            ->group('entity_id')
            ->having('orders > ?', 0);
        /*
         * Allow Analytic Functions Usage
         */
        $this->_useAnalyticFunction = true;

        return $this;
    }

    /**
     * Set store filter collection
     *
     * @param array $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds)
    {
        if ($storeIds) {
            $this->addAttributeToFilter('store_id', ['in' => (array)$storeIds])
            ->addExpressionAttributeToSelect(
                'invoiced',
                'SUM({{base_total_invoiced}})',
                ['base_total_invoiced']
            )
            ->addExpressionAttributeToSelect(
                'invoiced_captured',
                'SUM({{base_total_paid}})',
                ['base_total_paid']
            )
            ->addExpressionAttributeToSelect(
                'invoiced_not_captured',
                'SUM({{base_total_invoiced}}-{{base_total_paid}})',
                ['base_total_invoiced', 'base_total_paid']
            );
        } else {
            $this->addExpressionAttributeToSelect(
                'invoiced',
                'SUM({{base_total_invoiced}}*{{base_to_global_rate}})',
                ['base_total_invoiced', 'base_to_global_rate']
            )
            ->addExpressionAttributeToSelect(
                'invoiced_captured',
                'SUM({{base_total_paid}}*{{base_to_global_rate}})',
                ['base_total_paid', 'base_to_global_rate']
            )
            ->addExpressionAttributeToSelect(
                'invoiced_not_captured',
                'SUM(({{base_total_invoiced}}-{{base_total_paid}})*{{base_to_global_rate}})',
                ['base_total_invoiced', 'base_to_global_rate', 'base_total_paid']
            );
        }

        return $this;
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
        $countSelect->columns('COUNT(*)');

        return $countSelect;
    }
}
