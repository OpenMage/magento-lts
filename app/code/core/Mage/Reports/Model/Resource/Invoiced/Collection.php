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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Reports
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Reports invoiced collection
 *
 * @category    Mage
 * @package     Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Resource_Invoiced_Collection extends Mage_Sales_Model_Entity_Order_Collection
{
    /**
     * Set date range
     *
     * @param string $from
     * @param string $to
     * @return Mage_Reports_Model_Resource_Invoiced_Collection
     */
    public function setDateRange($from, $to)
    {
        $orderInvoicedExpr = $this->getConnection()->getCheckSql('{{base_total_invoiced}} > 0', 1, 0);
        $this->_reset()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to))
            ->addExpressionAttributeToSelect('orders',
                'COUNT({{base_total_invoiced}})',
                array('base_total_invoiced'))
            ->addExpressionAttributeToSelect('orders_invoiced',
                "SUM({$orderInvoicedExpr})",
                array('base_total_invoiced'))
            ->addAttributeToFilter('state', array('neq' => Mage_Sales_Model_Order::STATE_CANCELED))
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
     * @return Mage_Reports_Model_Resource_Invoiced_Collection
     */
    public function setStoreIds($storeIds)
    {
        if ($storeIds) {
            $this->addAttributeToFilter('store_id', array('in' => (array)$storeIds))
            ->addExpressionAttributeToSelect(
                'invoiced',
                'SUM({{base_total_invoiced}})',
                array('base_total_invoiced'))
            ->addExpressionAttributeToSelect(
                'invoiced_captured',
                'SUM({{base_total_paid}})',
                array('base_total_paid'))
            ->addExpressionAttributeToSelect(
                'invoiced_not_captured',
                'SUM({{base_total_invoiced}}-{{base_total_paid}})',
                array('base_total_invoiced', 'base_total_paid'));
        } else {
            $this->addExpressionAttributeToSelect(
                'invoiced',
                'SUM({{base_total_invoiced}}*{{base_to_global_rate}})',
                array('base_total_invoiced', 'base_to_global_rate'))
            ->addExpressionAttributeToSelect(
                'invoiced_captured',
                'SUM({{base_total_paid}}*{{base_to_global_rate}})',
                array('base_total_paid', 'base_to_global_rate'))
            ->addExpressionAttributeToSelect(
                'invoiced_not_captured',
                'SUM(({{base_total_invoiced}}-{{base_total_paid}})*{{base_to_global_rate}})',
                array('base_total_invoiced', 'base_to_global_rate', 'base_total_paid'));
        }

        return $this;
    }

    /**
     * Get select count sql
     *
     * @return string
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
        $countSelect->columns("COUNT(*)");

        return $countSelect;
    }
}
