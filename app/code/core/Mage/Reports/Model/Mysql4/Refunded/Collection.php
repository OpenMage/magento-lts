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
 * Reports refunded collection
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Mysql4_Refunded_Collection extends Mage_Sales_Model_Entity_Order_Collection
{

    public function setDateRange($from, $to)
    {
        $this->_reset()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to))
            ->addExpressionAttributeToSelect('orders', 'COUNT({{total_refunded}})', array('total_refunded'));

        $this->getSelect()
            ->where('base_total_refunded>0')
            ->group('("*")')
            ->having('orders > 0');

        return $this;
    }

    public function setStoreIds($storeIds)
    {
        $vals = array_values($storeIds);
        if (count($storeIds) >= 1 && $vals[0] != '') {
            $this->addAttributeToFilter('store_id', array('in' => (array)$storeIds))
                ->addExpressionAttributeToSelect(
                    'refunded',
                    'SUM({{base_total_refunded}})',
                    array('base_total_refunded'))
                ->addExpressionAttributeToSelect(
                    'online_refunded',
                    'SUM({{base_total_online_refunded}})',
                    array('base_total_online_refunded'))
                ->addExpressionAttributeToSelect(
                    'offline_refunded',
                    'SUM({{base_total_offline_refunded}})',
                    array('base_total_offline_refunded'));
        } else {
            $this->addExpressionAttributeToSelect(
                    'refunded',
                    'SUM({{base_total_refunded}}*{{base_to_global_rate}})',
                    array('base_total_refunded', 'base_to_global_rate'))
                ->addExpressionAttributeToSelect(
                    'online_refunded',
                    'SUM({{base_total_online_refunded}}*{{base_to_global_rate}})',
                    array('base_total_online_refunded', 'base_to_global_rate'))
                ->addExpressionAttributeToSelect(
                    'offline_refunded',
                    'SUM({{base_total_offline_refunded}}*{{base_to_global_rate}})',
                    array('base_total_offline_refunded', 'base_to_global_rate'));
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
