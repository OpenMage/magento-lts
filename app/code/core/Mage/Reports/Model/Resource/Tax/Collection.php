<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Reports tax collection
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
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
                'e.entity_id = tax_table.order_id'
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
                ->where('e.store_id IN(?)', (array)$storeIds)
                ->columns(['tax' => 'SUM(tax_table.base_real_amount)']);
        } else {
            $this->addExpressionAttributeToSelect(
                'tax',
                'SUM(tax_table.base_real_amount*{{base_to_global_rate}})',
                ['base_to_global_rate']
            );
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
        $countSelect->columns("COUNT(DISTINCT e.entity_id)");
        return $countSelect;
    }
}
