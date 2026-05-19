<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * Report Products Tags collection
 *
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Tag_Collection extends Mage_Tag_Model_Resource_Popular_Collection
{
    /**
     * Add group by tag
     *
     * @return $this
     * @deprecated after 1.4.0.1
     */
    public function addGroupByTag()
    {
        return $this;
    }

    /**
     * Add tag popularity to select by specified store ids
     *
     * @param  array|int $storeIds
     * @return $this
     */
    public function addPopularity($storeIds)
    {
        $select = $this->getSelect()
            ->joinLeft(
                ['tr' => $this->getTable('tag/relation')],
                'main_table.tag_id = tr.tag_id AND tr.active = 1',
                ['popularity' => 'COUNT(tr.tag_id)'],
            );
        if (!empty($storeIds)) {
            $select->where('tr.store_id IN(?)', $storeIds);
        }

        $select->group('main_table.tag_id');

        /**
         * Allow to use analytic function
         */
        $this->_useAnalyticFunction = true;

        return $this;
    }
}
