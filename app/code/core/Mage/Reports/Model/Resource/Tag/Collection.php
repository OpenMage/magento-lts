<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Report Products Tags collection
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Resource_Tag_Collection extends Mage_Tag_Model_Resource_Popular_Collection
{
    /**
     * Add group by tag
     *
     * @deprecated after 1.4.0.1
     *
     * @return $this
     */
    public function addGroupByTag()
    {
        return $this;
    }

    /**
     * Add tag popularity to select by specified store ids
     *
     * @param int|array $storeIds
     * @return $this
     */
    public function addPopularity($storeIds)
    {
        $select = $this->getSelect()
            ->joinLeft(
                ['tr' => $this->getTable('tag/relation')],
                'main_table.tag_id = tr.tag_id AND tr.active = 1',
                ['popularity' => 'COUNT(tr.tag_id)']
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
