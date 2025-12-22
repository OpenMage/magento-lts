<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * Reports refunded collection
 *
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Refunded_Collection extends Mage_Sales_Model_Entity_Order_Collection
{
    /**
     * Set date range
     *
     * @param  string $from
     * @param  string $to
     * @return $this
     */
    public function setDateRange($from, $to)
    {
        $this->_reset()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('created_at', ['from' => $from, 'to' => $to])
            ->addExpressionAttributeToSelect('orders', 'COUNT({{total_refunded}})', ['total_refunded']);

        $this->getSelect()
            ->where('base_total_refunded > ?', 0)
            ->group('("*")')
            ->having('orders > ?', 0);

        return $this;
    }

    /**
     * Set store filter to collection
     *
     * @param  array $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds)
    {
        if ($storeIds) {
            $this->addAttributeToFilter('store_id', ['in' => (array) $storeIds])
                ->addExpressionAttributeToSelect(
                    'refunded',
                    'SUM({{base_total_refunded}})',
                    ['base_total_refunded'],
                )
                ->addExpressionAttributeToSelect(
                    'online_refunded',
                    'SUM({{base_total_online_refunded}})',
                    ['base_total_online_refunded'],
                )
                ->addExpressionAttributeToSelect(
                    'offline_refunded',
                    'SUM({{base_total_offline_refunded}})',
                    ['base_total_offline_refunded'],
                );
        } else {
            $this->addExpressionAttributeToSelect(
                'refunded',
                'SUM({{base_total_refunded}}*{{base_to_global_rate}})',
                ['base_total_refunded', 'base_to_global_rate'],
            )
            ->addExpressionAttributeToSelect(
                'online_refunded',
                'SUM({{base_total_online_refunded}}*{{base_to_global_rate}})',
                ['base_total_online_refunded', 'base_to_global_rate'],
            )
            ->addExpressionAttributeToSelect(
                'offline_refunded',
                'SUM({{base_total_offline_refunded}}*{{base_to_global_rate}})',
                ['base_total_offline_refunded', 'base_to_global_rate'],
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
