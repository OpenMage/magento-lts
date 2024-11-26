<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_CatalogIndex
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Resource Model CatalogIndex Aggregation
 *
 * @category   Mage
 * @package    Mage_CatalogIndex
 */
class Mage_CatalogIndex_Model_Resource_Aggregation extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Table name of catalogindex/aggregation_tag table
     *
     * @var string
     */
    protected $_tagTable;

    /**
     * Table name of catalogindex/aggregation_to_tag table
     *
     * @var string
     */
    protected $_toTagTable;

    /**
     * Initialize resource tables
     *
     */
    protected function _construct()
    {
        $this->_init('catalogindex/aggregation', 'aggregation_id');
        $this->_tagTable    = $this->getTable('catalogindex/aggregation_tag');
        $this->_toTagTable  = $this->getTable('catalogindex/aggregation_to_tag');
    }

    /**
     * Get aggregated cache data by data key and store
     *
     * @param string $key
     * @param int $storeId
     * @return array
     */
    public function getCacheData($key, $storeId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(['a' => $this->getMainTable()], 'data')
            ->where('a.store_id=?', $storeId)
            ->where('a.key=?', $key);
        $data = $this->_getReadAdapter()->fetchOne($select);
        if ($data) {
            $data = unserialize($data, ['allowed_classes' => false]);
        } else {
            $data = [];
        }
        return $data;
    }

    /**
     * Save data to aggreagation table with tags relations
     *
     * @param array $data
     * @param string $key
     * @param array|string $tags
     * @param int $storeId
     * @return $this
     */
    public function saveCacheData($data, $key, $tags, $storeId)
    {
        $data = serialize($data);
        $tags = $this->_getTagIds($tags);

        /*
        $select = $this->_getWriteAdapter()->select()
            ->from(array('a'=>$this->getMainTable()), $this->getIdFieldName())
            ->where('a.store_id=?', $storeId)
            ->where('a.key=?', $key);

        $id = $this->_getWriteAdapter()->fetchOne($select);
        if ($id) {
            $this->_getWriteAdapter()->update(
                $this->getMainTable(),
                array('data'=>$data),
                $this->_getWriteAdapter()->quoteInto($this->getIdFieldName().'=?', $id)
            );
        } else {
            $this->_getWriteAdapter()->insert($this->getMainTable(), array(
                'store_id'  => $storeId,
                'created_at'=> $this->formatDate(time()),
                'key'       => $key,
                'data'      => $data
            ));
            $id = $this->_getWriteAdapter()->lastInsertId();
        }
        */

        $this->_getWriteAdapter()->insertOnDuplicate($this->getMainTable(), [
            'store_id'  => $storeId,
            'created_at' => $this->formatDate(time()),
            'key'       => $key,
            'data'      => $data
        ], ['created_at', 'data']);

        $id = $this->_getWriteAdapter()->lastInsertId($this->getMainTable());

        $this->_saveTagRelations($id, $tags);
        return $this;
    }

    /**
     * Clear data in cache
     *
     * @param   array $tags
     * @param   int|null|string $storeId
     * @return $this
     */
    public function clearCacheData($tags, $storeId)
    {
        $conditions = [];
        if (!$write = $this->_getWriteAdapter()) {
            return $this;
        }
        if (!empty($tags)) {
            $tagIds = $this->_getTagIds($tags);
            $select = $write->select()
                ->from($this->_toTagTable, 'aggregation_id')
                ->where('tag_id IN (?)', $tagIds);
            $conditions[] = $write->quoteInto('aggregation_id IN ?', $select);
        }

        if ($storeId !== null) {
            $conditions[] = $write->quoteInto('store_id=?', $storeId);
        }

        $write->delete($this->getMainTable(), implode(' AND ', $conditions));
        return $this;
    }

    /**
     * Save related tags for aggreagation data
     *
     * @param int $aggregationId
     * @param array $tags
     * @return $this
     */
    protected function _saveTagRelations($aggregationId, $tags)
    {
        $query = "REPLACE INTO `{$this->_toTagTable}` (aggregation_id, tag_id) VALUES ";
        $data = [];
        foreach ($tags as $tagId) {
            $data[] = $aggregationId . ',' . $tagId;
        }
        $query .= '(' . implode('),(', $data) . ')';
        $this->_getWriteAdapter()->query($query);
        return $this;
    }

    /**
     * Get identifiers of tags
     * if some tags not exist they will be added
     *
     * @param array $tags
     * @return array
     */
    protected function _getTagIds($tags)
    {
        if (!is_array($tags)) {
            $tags = [$tags];
        }

        $select = $this->_getReadAdapter()->select()
            ->from(['tags' => $this->_tagTable], ['tag_code', 'tag_id'])
            ->where('tags.tag_code IN (?)', $tags);

        $tagIds = $this->_getReadAdapter()->fetchPairs($select);

        /**
         * Detect new tags
         */
        $newTags = array_diff($tags, array_keys($tagIds));
        if (!empty($newTags)) {
            $this->_addTags($newTags);
            $select->reset(Zend_Db_Select::WHERE)
                ->where('tags.tag_code IN (?)', $newTags);
            $newTags = $this->_getReadAdapter()->fetchPairs($select);
            $tagIds = array_merge($tagIds, $newTags);
        }
        return $tagIds;
    }

    /**
     * Insert tags to tag table
     *
     * @param string | array $tags
     * @return $this
     */
    protected function _addTags($tags)
    {
        if (is_array($tags)) {
            $tags = array_unique($tags);
            foreach ($tags as $index => $tag) {
                $tags[$index] = $this->_getWriteAdapter()->quote($tag);
            }
            $query = "INSERT INTO `{$this->_tagTable}` (tag_code) VALUES (" . implode('),(', $tags) . ')';
            $this->_getWriteAdapter()->query($query);
        } else {
            $this->_getWriteAdapter()->insert($this->_tagTable, [
                'tag_code' => $tags
            ]);
        }
        return $this;
    }

    /**
     * ProductCategoryPaths getter
     *
     * @param array $productIds
     * @return array
     */
    public function getProductCategoryPaths($productIds)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(['cat' => $this->getTable('catalog/category')], 'path')
            ->joinInner(
                ['cat_prod' => $this->getTable('catalog/category_product')],
                $this->_getReadAdapter()->quoteInto(
                    'cat.entity_id=cat_prod.category_id AND cat_prod.product_id IN (?)',
                    $productIds
                ),
                []
            );
        return $this->_getReadAdapter()->fetchCol($select);
    }
}
