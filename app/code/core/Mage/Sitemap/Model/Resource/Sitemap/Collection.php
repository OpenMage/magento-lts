<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Sitemap
 */

/**
 * Sitemap resource model collection
 *
 * @category   Mage
 * @package    Mage_Sitemap
 */
class Mage_Sitemap_Model_Resource_Sitemap_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Init collection
     *
     */
    public function _construct()
    {
        $this->_init('sitemap/sitemap');
    }

    /**
     * Filter collection by specified store ids
     *
     * @param array|int $storeIds
     * @return $this
     */
    public function addStoreFilter($storeIds)
    {
        $this->getSelect()->where('main_table.store_id IN (?)', $storeIds);
        return $this;
    }
}
