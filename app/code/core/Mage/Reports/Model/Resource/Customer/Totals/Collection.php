<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Reports
 */

/**
 * Customers by totals Report collection
 *
 * @category   Mage
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Customer_Totals_Collection extends Mage_Reports_Model_Resource_Order_Collection
{
    protected function _construct()
    {
        parent::_construct();
        $this->_useAnalyticFunction = true;
    }
    /**
     * Join fields
     *
     * @param string $from
     * @param string $to
     * @return $this
     */
    protected function _joinFields($from = '', $to = '')
    {
        $this->joinCustomerName()
            ->groupByCustomer()
            ->addOrdersCount()
            ->addAttributeToFilter('created_at', ['from' => $from, 'to' => $to, 'datetime' => true]);
        return $this;
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
        $this->_reset()
            ->_joinFields($from, $to);
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
            $this->addAttributeToFilter('store_id', ['in' => (array) $storeIds]);
            $this->addSumAvgTotals(1)
                ->orderByTotalAmount();
        } else {
            $this->addSumAvgTotals()
                ->orderByTotalAmount();
        }

        return $this;
    }
}
