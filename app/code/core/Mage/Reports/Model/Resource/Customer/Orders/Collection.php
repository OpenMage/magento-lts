<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * Customers by orders Report collection
 *
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Customer_Orders_Collection extends Mage_Reports_Model_Resource_Order_Collection
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_useAnalyticFunction = true;
    }

    /**
     * Join fields
     *
     * @param  string $from
     * @param  string $to
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
     * @param  string $from
     * @param  string $to
     * @return $this
     */
    public function setDateRange($from, $to)
    {
        $this->_reset()
            ->_joinFields($from, $to);
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
            $this->addAttributeToFilter('store_id', ['in' => (array) $storeIds]);
            $this->addSumAvgTotals(1)
                ->orderByOrdersCount();
        } else {
            $this->addSumAvgTotals()
                ->orderByOrdersCount();
        }

        return $this;
    }
}
