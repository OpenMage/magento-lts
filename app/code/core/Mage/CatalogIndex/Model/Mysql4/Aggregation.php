<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogIndex
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_CatalogIndex_Model_Mysql4_Aggregation extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_tagTable;
    protected $_toTagTable;

    /**
     * Initialize resource tables
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
     * @param   string $key
     * @param   int $store
     * @return  array
     */
    public function getCacheData($key, $storeId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('a'=>$this->getMainTable()), 'data')
            ->where('a.store_id=?', $storeId)
            ->where('a.key=?', $key);
        $data = $this->_getReadAdapter()->fetchOne($select);
        if ($data) {
            $data = unserialize($data);
        } else {
            $data = array();
        }
        return $data;
    }

    /**
     * Save data to aggreagation table with tags relations
     *
     * @param   array $data
     * @param   string $key
     * @param   array|string $tags
     * @param   int $storeId
     * @return  Mage_CatalogIndex_Model_Mysql4_Aggregation
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

        $this->_getWriteAdapter()->insertOnDuplicate($this->getMainTable(), array(
            'store_id'  => $storeId,
            'created_at'=> $this->formatDate(time()),
            'key'       => $key,
            'data'      => $data
        ), array('created_at', 'data'));

        $id = $this->_getWriteAdapter()->lastInsertId();

        $this->_saveTagRelations($id, $tags);
        return $this;
    }

    public function clearCacheData($tags, $storeId)
    {
        $conditions = array();
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
     * @param   int $aggregationId
     * @param   array $tags
     * @return  Mage_CatalogIndex_Model_Mysql4_Aggregation
     */
    protected function _saveTagRelations($aggregationId, $tags)
    {
        $query = "REPLACE INTO `{$this->_toTagTable}` (aggregation_id, tag_id) VALUES ";
        $data = array();
        foreach ($tags as $tagId) {
            $data[] = $aggregationId.','.$tagId;
        }
        $query.= '(' . implode('),(', $data) . ')';
        $this->_getWriteAdapter()->query($query);
        return $this;
    }

    /**
     * Get identifiers of tags
     * if some tags not exist they will be added
     *
     * @param   array $tags
     * @return  array
     */
    protected function _getTagIds($tags)
    {
        if (!is_array($tags)) {
            $tags = array($tags);
        }

        $select = $this->_getReadAdapter()->select()
            ->from(array('tags'=>$this->_tagTable), array('tag_code', 'tag_id'))
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
     * @param   string | array $tags
     * @return  Mage_CatalogIndex_Model_Mysql4_Aggregation
     */
    protected function _addTags($tags)
    {
        if (is_array($tags)) {
            $tags = array_unique($tags);
            foreach ($tags as $index => $tag) {
                $tags[$index] = $this->_getWriteAdapter()->quote($tag);
            }
            $query = "INSERT INTO `{$this->_tagTable}` (tag_code) VALUES (".implode('),(', $tags).")";
            $this->_getWriteAdapter()->query($query);
        }
        else {
            $this->_getWriteAdapter()->insert($this->_tagTable, array(
                'tag_code' => $tags
            ));
        }
        return $this;
    }

    public function getProductCategoryPaths($productIds)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('cat'=>$this->getTable('catalog/category')), 'path')
            ->joinInner(
                array('cat_prod'=>$this->getTable('catalog/category_product')),
                $this->_getReadAdapter()->quoteInto(
                    'cat.entity_id=cat_prod.category_id AND cat_prod.product_id IN (?)',
                    $productIds
                ),
                array()
            );
        return $this->_getReadAdapter()->fetchCol($select);
    }
}
