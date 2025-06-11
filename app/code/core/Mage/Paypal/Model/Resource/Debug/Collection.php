<?php
class Mage_Paypal_Model_Resource_Debug_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('paypal/debug');
    }

    /**
     * Add filter by quote ID
     *
     * @param int $quoteId
     * @return $this
     */
    public function addQuoteIdFilter($quoteId)
    {
        $this->addFieldToFilter('quote_id', $quoteId);
        return $this;
    }

    /**
     * Add filter by transaction ID
     *
     * @param string $transactionId
     * @return $this
     */
    public function addTransactionIdFilter($transactionId)
    {
        $this->addFieldToFilter('transaction_id', $transactionId);
        return $this;
    }

    /**
     * Add filter by action
     *
     * @param string $action
     * @return $this
     */
    public function addActionFilter($action)
    {
        $this->addFieldToFilter('action', $action);
        return $this;
    }

    /**
     * Add date range filter
     *
     * @param string $from
     * @param string $to
     * @return $this
     */
    public function addDateRangeFilter($from, $to)
    {
        if ($from) {
            $this->addFieldToFilter('created_at', ['gteq' => $from]);
        }
        if ($to) {
            $this->addFieldToFilter('created_at', ['lteq' => $to]);
        }
        return $this;
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->order('created_at DESC');
        return $this;
    }
}
