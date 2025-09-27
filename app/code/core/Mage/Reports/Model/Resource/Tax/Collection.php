<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * Reports tax collection
 *
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Tax_Collection extends Mage_Sales_Model_Entity_Order_Collection
{
    /**
     * Set row identifier field name
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->setRowIdFieldName('tax_id');
    }

    /**
     * Set date range
     *
     * @param string $from
     * @param string $to
     * @return $this
     */
    public function setDateRange($from, $to)
    {
        $this->_reset();

        $this->addAttributeToFilter('created_at', ['from' => $from, 'to' => $to])
            ->addExpressionAttributeToSelect('orders', 'COUNT(DISTINCT({{entity_id}}))', ['entity_id'])
            ->getSelect()
            ->join(
                ['tax_table' => $this->getTable('sales/order_tax')],
                'e.entity_id = tax_table.order_id',
            )
            ->group('tax_table.code')
            ->order(['process', 'priority']);
        /*
         * Allow Analytic Functions Usage
         */
        $this->_useAnalyticFunction = true;

        return $this;
    }

    /**
     * Set store filter to collection
     *
     * @param array $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds)
    {
        if ($storeIds) {
            $this->getSelect()
                ->where('e.store_id IN(?)', (array) $storeIds)
                ->columns(['tax' => 'SUM(tax_table.base_real_amount)']);
        } else {
            $this->addExpressionAttributeToSelect(
                'tax',
                'SUM(tax_table.base_real_amount*{{base_to_global_rate}})',
                ['base_to_global_rate'],
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
        $countSelect->columns('COUNT(DISTINCT e.entity_id)');
        return $countSelect;
    }
}
