<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Catalog
 */

/**
 * Catalog Product Relations Resource model
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Resource_Product_Relation extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('catalog/product_relation', 'parent_id');
    }

    /**
     * Save (rebuild) product relations
     *
     * @param int $parentId
     * @param array $childIds
     * @return $this
     */
    public function processRelations($parentId, $childIds)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), ['child_id'])
            ->where('parent_id = ?', $parentId);
        $old = $this->_getReadAdapter()->fetchCol($select);
        $new = $childIds;

        $insert = array_diff($new, $old);
        $delete = array_diff($old, $new);

        if (!empty($insert)) {
            $insertData = [];
            foreach ($insert as $childId) {
                $insertData[] = [
                    'parent_id' => $parentId,
                    'child_id'  => $childId,
                ];
            }
            $this->_getWriteAdapter()->insertMultiple($this->getMainTable(), $insertData);
        }
        if (!empty($delete)) {
            $where = implode(' AND ', [
                $this->_getWriteAdapter()->quoteInto('parent_id = ?', $parentId),
                $this->_getWriteAdapter()->quoteInto('child_id IN(?)', $delete),
            ]);
            $this->_getWriteAdapter()->delete($this->getMainTable(), $where);
        }

        return $this;
    }
}
