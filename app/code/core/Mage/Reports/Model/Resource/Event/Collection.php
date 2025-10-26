<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * Report event collection
 *
 * @package    Mage_Reports
 *
 * @method Mage_Reports_Model_Resource_Event _getResource()
 * @method Mage_Reports_Model_Resource_Event getResource()
 */
class Mage_Reports_Model_Resource_Event_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Store Ids
     *
     * @var array
     */
    protected $_storeIds;

    /**
     * Use analytic function flag
     * If true - allows to prepare final select with analytic function
     *
     * @var bool
     */
    protected $_useAnalyticFunction         = true;

    protected function _construct()
    {
        $this->_init('reports/event');
    }

    /**
     * Add store ids filter
     *
     * @return $this
     */
    public function addStoreFilter(array $storeIds)
    {
        $this->_storeIds = $storeIds;
        return $this;
    }

    /**
     * Add recently filter
     *
     * @param int $typeId
     * @param int $subjectId
     * @param int $subtype
     * @param int|array $ignore
     * @param int $limit
     * @return $this
     */
    public function addRecentlyFiler($typeId, $subjectId, $subtype = 0, $ignore = null, $limit = 15)
    {
        $stores = $this->getResource()->getCurrentStoreIds($this->_storeIds);
        $select = $this->getSelect();
        $select->where('event_type_id = ?', $typeId)
            ->where('subject_id = ?', $subjectId)
            ->where('subtype = ?', $subtype)
            ->where('store_id IN(?)', $stores);
        if ($ignore) {
            if (is_array($ignore)) {
                $select->where('object_id NOT IN(?)', $ignore);
            } else {
                $select->where('object_id <> ?', $ignore);
            }
        }

        $select->group('object_id')
            ->limit($limit);
        return $this;
    }
}
