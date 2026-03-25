<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * New Accounts Report collection
 *
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Accounts_Collection extends Mage_Reports_Model_Resource_Customer_Collection
{
    /**
     * Join created_at and accounts fields
     *
     * @param  string $from
     * @param  string $to
     * @return $this
     */
    protected function _joinFields($from = '', $to = '')
    {
        $this->getSelect()->reset(Zend_Db_Select::COLUMNS);
        $this->addAttributeToFilter('created_at', ['from' => $from, 'to' => $to, 'datetime' => true])
             ->addExpressionAttributeToSelect('accounts', 'COUNT({{entity_id}})', ['entity_id']);

        $this->getSelect()->having("{$this->_joinFields['accounts']['field']} > ?", 0);

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
     * Set store ids to final result
     *
     * @param  array $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds)
    {
        if ($storeIds) {
            $this->addAttributeToFilter('store_id', ['in' => (array) $storeIds]);
        }

        return $this;
    }
}
