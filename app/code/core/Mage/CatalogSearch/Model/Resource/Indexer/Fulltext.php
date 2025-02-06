<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 */

/**
 * CatalogSearch fulltext indexer resource model
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 */
class Mage_CatalogSearch_Model_Resource_Indexer_Fulltext extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('catalogsearch/fulltext', 'product_id');
    }

    /**
     * Retrieve product relations by children
     *
     * @param int|array $childIds
     * @return array
     */
    public function getRelationsByChild($childIds)
    {
        $write = $this->_getWriteAdapter();
        $select = $write->select()
            ->from($this->getTable('catalog/product_relation'), 'parent_id')
            ->where('child_id IN(?)', $childIds);

        return $write->fetchCol($select);
    }
}
