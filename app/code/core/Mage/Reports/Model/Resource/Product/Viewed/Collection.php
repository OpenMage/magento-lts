<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * Products Most Viewed Report collection
 *
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Product_Viewed_Collection extends Mage_Reports_Model_Resource_Product_Collection
{
    /**
     * List of store ids for the current collection will be filtered by
     *
     * @var array
     */
    protected $_storeIds = [];

    /**
     * Join fields
     *
     * @param string $from
     * @param string $to
     * @return $this
     */
    protected function _joinFields($from = '', $to = '')
    {
        $this->addAttributeToSelect('*')
            ->addViewsCount($from, $to);
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
     * Set store ids
     *
     * @param array $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds)
    {
        $storeId = array_pop($storeIds);
        $this->setStoreId($storeId);
        $this->addStoreFilter($storeId);
        $this->addStoreIds($storeId);
        return $this;
    }

    /**
     * Add store ids to filter 'report_event' data by store
     *
     * @param array|int $storeIds
     * @return $this
     */
    public function addStoreIds($storeIds)
    {
        if (is_array($storeIds)) {
            $this->_storeIds = array_merge($this->_storeIds, $storeIds);
        } else {
            $this->_storeIds[] = $storeIds;
        }
        return $this;
    }

    /**
     * Apply store filter
     *
     * @return $this
     */
    protected function _applyStoreIds()
    {
        $this->getSelect()->where('store_id IN(?)', $this->_storeIds);
        return $this;
    }

    /**
     * Apply filters
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _beforeLoad()
    {
        $this->_applyStoreIds();
        return parent::_beforeLoad();
    }
}
