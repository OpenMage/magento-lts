<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Core Design resource collection
 *
 * @package    Mage_Core
 */
class Mage_Core_Model_Resource_Design_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('core/design');
    }

    /**
     * Join store data to collection
     *
     * @return $this
     */
    public function joinStore()
    {
        return $this->join(
            ['cs' => 'core/store'],
            'cs.store_id = main_table.store_id',
            ['cs.name'],
        );
    }

    /**
     * Add date filter to collection
     *
     * @param  null|int|string|Zend_Date $date
     * @return $this
     */
    public function addDateFilter($date = null)
    {
        if (is_null($date)) {
            $date = $this->formatDate(true);
        } else {
            $date = $this->formatDate($date);
        }

        $this->addFieldToFilter('date_from', ['lteq' => $date]);
        $this->addFieldToFilter('date_to', ['gteq' => $date]);
        return $this;
    }

    /**
     * Add store filter to collection
     *
     * @param  array|int $storeId
     * @return $this
     */
    public function addStoreFilter($storeId)
    {
        return $this->addFieldToFilter('store_id', ['in' => $storeId]);
    }
}
