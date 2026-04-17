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
     * @param  null|string $dateFrom
     * @param  null|string $dateTo
     * @return $this
     */
    protected function _joinFields($dateFrom = '', $dateTo = '')
    {
        $this->joinCustomerName()
            ->groupByCustomer()
            ->addOrdersCount()
            ->addAttributeToFilter('created_at', ['from' => $dateFrom, 'to' => $dateTo, 'datetime' => true]);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDateRange($dateFrom, $dateTo)
    {
        $this->_reset()
            ->_joinFields($dateFrom, $dateTo);
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
