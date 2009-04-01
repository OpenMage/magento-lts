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
 * @category   Mage
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Reports invoiced collection
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Mysql4_Invoiced_Collection extends Mage_Sales_Model_Entity_Order_Collection
{

    public function setDateRange($from, $to)
    {
        $this->_reset()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to))
            ->addExpressionAttributeToSelect('orders',
                'COUNT({{base_total_invoiced}})',
                 array('base_total_invoiced'))
            ->addExpressionAttributeToSelect('orders_invoiced',
                'SUM(IF({{base_total_invoiced}} > 0, 1, 0))',
                 array('base_total_invoiced'))
            ->addAttributeToFilter('state', array('neq' => Mage_Sales_Model_Order::STATE_CANCELED))
            ->getSelect()->group('("*")')->having('orders > 0');

        return $this;
    }

    public function setStoreIds($storeIds)
    {
        $vals = array_values($storeIds);
        if (count($storeIds) >= 1 && $vals[0] != '') {
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

    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::HAVING);
        $countSelect->from("", "count(*)");
        $sql = $countSelect->__toString();
        return $sql;
    }
}
