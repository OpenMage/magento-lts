<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tag
 */

/**
 * Popular tags collection model
 *
 * @package    Mage_Tag
 */
class Mage_Tag_Model_Resource_Popular_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Defines resource model and model
     *
     */
    protected function _construct()
    {
        $this->_init('tag/tag');
    }

    /**
     * Replacing popularity by sum of popularity and base_popularity
     *
     * @param int $storeId
     * @return $this
     */
    public function joinFields($storeId = 0)
    {
        $this->getSelect()
            ->reset()
            ->from(
                ['tag_summary' => $this->getTable('tag/summary')],
                ['popularity' => 'tag_summary.popularity'],
            )
            ->joinInner(
                ['tag' => $this->getTable('tag/tag')],
                'tag.tag_id = tag_summary.tag_id AND tag.status = ' . Mage_Tag_Model_Tag::STATUS_APPROVED,
            )
            ->where('tag_summary.store_id = ?', $storeId)
            ->where('tag_summary.products > ?', 0)
            ->order('popularity ' . Varien_Db_Select::SQL_DESC);

        return $this;
    }

    /**
     * Add filter by specified tag status
     *
     * @param string $statusCode
     * @return $this
     */
    public function addStatusFilter($statusCode)
    {
        $this->getSelect()->where('main_table.status = ?', $statusCode);
        return $this;
    }

    /**
     * Loads collection
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        parent::load($printQuery, $logQuery);
        return $this;
    }

    /**
     * Sets limit
     *
     * @param int $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->getSelect()->limit($limit);
        return $this;
    }

    /**
     * Get SQL for get record count
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();
        $select = clone $this->getSelect();
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);

        $countSelect = $this->getConnection()->select();
        $countSelect->from(['a' => $select], 'COUNT(popularity)');
        return $countSelect;
    }
}
