<?php
/**
 * Convert profile collection
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Dataflow
 */
class Mage_Dataflow_Model_Resource_Profile_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Define resource model and model
     *
     */
    protected function _construct()
    {
        $this->_init('dataflow/profile');
    }

    /**
     * Filter collection by specified store ids
     *
     * @param array|int $storeIds
     * @return $this
     */
    public function addStoreFilter($storeIds)
    {
        $this->getSelect()
            ->where('main_table.store_id IN (?)', [0, $storeIds]);
        return $this;
    }
}
