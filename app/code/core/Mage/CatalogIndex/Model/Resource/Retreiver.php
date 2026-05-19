<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogIndex
 */

/**
 * Index type retriever resource model
 *
 * @package    Mage_CatalogIndex
 */
class Mage_CatalogIndex_Model_Resource_Retreiver extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('catalog/product', 'entity_id');
    }

    /**
     * Return id-type pairs
     *
     * @param  array $ids
     * @return array
     */
    public function getProductTypes($ids)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(['main_table' => $this->getTable('catalog/product')], ['id' => 'main_table.entity_id', 'type' => 'main_table.type_id'])
            ->where('main_table.entity_id in (?)', $ids);
        return $this->_getReadAdapter()->fetchAll($select);
    }
}
