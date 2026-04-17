<?php

use Laminas\Db\Sql\Select;

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
     * @param  null|string $dateFrom
     * @param  null|string $dateTo
     * @return $this
     */
    protected function _joinFields($dateFrom = '', $dateTo = '')
    {
        $this->getSelect()->reset(Select::COLUMNS);
        $this->addAttributeToFilter('created_at', ['from' => $dateFrom, 'to' => $dateTo, 'datetime' => true])
             ->addExpressionAttributeToSelect('accounts', 'COUNT({{entity_id}})', ['entity_id']);

        $this->getSelect()->having("{$this->_joinFields['accounts']['field']} > ?", 0);

        return $this;
    }

    /**
     * Set date range
     *
     * @param  null|string $dateFrom
     * @param  null|string $dateTo
     * @return $this
     */
    public function setDateRange($dateFrom, $dateTo)
    {
        $this->_reset()
             ->_joinFields($dateFrom, $dateTo);
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
