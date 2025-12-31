<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tag
 */

/**
 * Tag resource model
 *
 * @package    Mage_Tag
 */
class Mage_Tag_Model_Resource_Tag extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('tag/tag', 'tag_id');
    }

    /**
     * Initialize unique fields
     *
     * @return $this
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = [[
            'field' => 'name',
            'title' => Mage::helper('tag')->__('Tag'),
        ]];
        return $this;
    }

    /**
     * Loading tag by name
     *
     * @param  Mage_Tag_Model_Tag|Varien_Object $model
     * @param  string                           $name
     * @return false|void
     * @throws Mage_Core_Exception
     */
    public function loadByName($model, $name)
    {
        if ($name) {
            $read = $this->_getReadAdapter();
            $select = $read->select();
            if (Mage::helper('core/string')->strlen($name) > 255) {
                $name = Mage::helper('core/string')->substr($name, 0, 255);
            }

            $select->from($this->getMainTable())
                ->where('name = :name');
            $data = $read->fetchRow($select, ['name' => $name]);

            $model->setData(is_array($data) ? $data : []);
        } else {
            return false;
        }
    }

    /**
     * Before saving actions
     *
     * @param Mage_Tag_Model_Tag $object
     * @inheritDoc
     * @throws Mage_Core_Exception
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getId() && $object->getStatus() == $object->getApprovedStatus()) {
            $searchTag = new Varien_Object();
            $this->loadByName($searchTag, $object->getName());
            if ($searchTag->getData($this->getIdFieldName())
                && $searchTag->getStatus() == $object->getPendingStatus()
            ) {
                $object->setId($searchTag->getData($this->getIdFieldName()));
            }
        }

        if (Mage::helper('core/string')->strlen($object->getName()) > 255) {
            $object->setName(Mage::helper('core/string')->substr($object->getName(), 0, 255));
        }

        return parent::_beforeSave($object);
    }

    /**
     * Saving tag's base popularity
     *
     * @param Mage_Tag_Model_Tag $object
     * @inheritDoc
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     * @throws Zend_Db_Exception
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getStore() || !Mage::app()->getStore()->isAdmin()) {
            return parent::_afterSave($object);
        }

        $tagId = ($object->isObjectNew()) ? $object->getTagId() : $object->getId();

        $writeAdapter = $this->_getWriteAdapter();
        $writeAdapter->insertOnDuplicate($this->getTable('tag/properties'), [
            'tag_id'            => $tagId,
            'store_id'          => $object->getStore(),
            'base_popularity'   => ($object->getBasePopularity()) ? $object->getBasePopularity() : 0,
        ]);

        return parent::_afterSave($object);
    }

    /**
     * Getting base popularity per store view for specified tag
     *
     * @param  int   $tagId
     * @return array
     * @deprecated after 1.4.0.0
     */
    protected function _getExistingBasePopularity($tagId)
    {
        $read = $this->_getReadAdapter();
        $selectSummary = $read->select()
            ->from(
                ['main' => $this->getTable('tag/summary')],
                ['store_id', 'base_popularity'],
            )
            ->where('main.tag_id = :tag_id')
            ->where('main.store_id != 0');

        return $read->fetchAssoc($selectSummary, ['tag_id' => $tagId]);
    }

    /**
     * Get aggregation data per store view
     *
     * @param  int   $tagId
     * @return array
     * @deprecated after 1.4.0.0
     */
    protected function _getAggregationPerStoreView($tagId)
    {
        $readAdapter = $this->_getReadAdapter();
        $selectLocal = $readAdapter->select()
            ->from(
                ['main'  => $this->getTable('tag/relation')],
                [
                    'customers' => 'COUNT(DISTINCT main.customer_id)',
                    'products'  => 'COUNT(DISTINCT main.product_id)',
                    'store_id',
                    'uses'      => 'COUNT(main.tag_relation_id)',
                ],
            )
            ->join(
                ['store' => $this->getTable('core/store')],
                'store.store_id = main.store_id AND store.store_id > 0',
                [],
            )
            ->join(
                ['product_website' => $this->getTable('catalog/product_website')],
                'product_website.website_id = store.website_id AND product_website.product_id = main.product_id',
                [],
            )
            ->where('main.tag_id = :tag_id')
            ->where('main.active = 1')
            ->group('main.store_id');

        $selectLocalResult = $readAdapter->fetchAll($selectLocal, ['tag_id' => $tagId]);

        $selectHistorical = $readAdapter->select()
            ->from(
                ['main' => $this->getTable('tag/relation')],
                ['historical_uses' => 'COUNT(main.tag_relation_id)',
                    'store_id'],
            )
            ->join(
                ['store' => $this->getTable('core/store')],
                'store.store_id = main.store_id AND store.store_id > 0',
                [],
            )
            ->join(
                ['product_website' => $this->getTable('catalog/product_website')],
                'product_website.website_id = store.website_id AND product_website.product_id = main.product_id',
                [],
            )
            ->group('main.store_id')
            ->where('main.tag_id = :tag_id')
            ->where('main.active = 1');

        $selectHistoricalResult = $readAdapter->fetchAll($selectHistorical, ['tag_id' => $tagId]);

        foreach ($selectHistoricalResult as $historical) {
            foreach ($selectLocalResult as $key => $local) {
                if ($local['store_id'] == $historical['store_id']) {
                    $selectLocalResult[$key]['historical_uses'] = $historical['historical_uses'];
                    break;
                }
            }
        }

        return $selectLocalResult;
    }

    /**
     * Get global aggregation data for row with store_id = 0
     *
     * @param  int   $tagId
     * @return array
     * @deprecated after 1.4.0.0
     */
    protected function _getGlobalAggregation($tagId)
    {
        $readAdapter = $this->_getReadAdapter();
        // customers and products stats
        $selectGlobal = $readAdapter->select()
            ->from(
                ['main' => $this->getTable('tag/relation')],
                [
                    'customers' => 'COUNT(DISTINCT main.customer_id)',
                    'products'  => 'COUNT(DISTINCT main.product_id)',
                    'store_id'  => new Zend_Db_Expr('0'),
                    'uses'      => 'COUNT(main.tag_relation_id)',
                ],
            )
            ->join(
                ['store' => $this->getTable('core/store')],
                'store.store_id=main.store_id AND store.store_id>0',
                [],
            )
            ->join(
                ['product_website' => $this->getTable('catalog/product_website')],
                'product_website.website_id = store.website_id AND product_website.product_id = main.product_id',
                [],
            )
            ->where('main.tag_id = :tag_id')
            ->where('main.active = 1');
        $result = $readAdapter->fetchRow($selectGlobal, ['tag_id' => $tagId]);
        if (!$result) {
            return [];
        }

        // historical uses stats
        $selectHistoricalGlobal = $readAdapter->select()
            ->from(
                ['main' => $this->getTable('tag/relation')],
                ['historical_uses' => 'COUNT(main.tag_relation_id)'],
            )
            ->join(
                ['store' => $this->getTable('core/store')],
                'store.store_id = main.store_id AND store.store_id > 0',
                [],
            )
            ->join(
                ['product_website' => $this->getTable('catalog/product_website')],
                'product_website.website_id = store.website_id AND product_website.product_id = main.product_id',
                [],
            )
            ->where('main.tag_id = :tag_id')
            ->where('main.active = 1');
        $result['historical_uses'] = (int) $readAdapter->fetchOne($selectHistoricalGlobal, ['tag_id' => $tagId]);

        return $result;
    }

    /**
     * Getting statistics data into buffer.
     * Replacing our buffer array with new statistics and incoming data.
     *
     * @param  Mage_Tag_Model_Tag  $object
     * @return Mage_Tag_Model_Tag
     * @throws Mage_Core_Exception
     * @throws Zend_Db_Exception
     * @deprecated after 1.4.0.0
     */
    public function aggregate($object)
    {
        $tagId   = (int) $object->getId();
        $storeId = (int) $object->getStore();

        // create final summary from existing data and add specified base popularity
        $finalSummary = $this->_getExistingBasePopularity($tagId);
        if ($object->hasBasePopularity() && $storeId) {
            $finalSummary[$storeId]['store_id'] = $storeId;
            $finalSummary[$storeId]['base_popularity'] = $object->getBasePopularity();
        }

        // calculate aggregation data
        $summaries = $this->_getAggregationPerStoreView($tagId);
        $summariesGlobal = $this->_getGlobalAggregation($tagId);
        if ($summariesGlobal) {
            $summaries[] = $summariesGlobal;
        }

        // override final summary with aggregated data
        foreach ($summaries as $row) {
            $storeId = (int) $row['store_id'];
            foreach ($row as $key => $value) {
                $finalSummary[$storeId][$key] = $value;
            }
        }

        // prepare static parameters to final summary for insertion
        foreach ($finalSummary as $key => $row) {
            $finalSummary[$key]['tag_id'] = $tagId;
            foreach (['base_popularity', 'popularity', 'historical_uses', 'uses', 'products', 'customers'] as $str) {
                if (!isset($row[$str])) {
                    $finalSummary[$key][$str] = 0;
                }
            }

            $finalSummary[$key]['popularity'] = $finalSummary[$key]['historical_uses'];
        }

        // remove old and insert new data
        $write = $this->_getWriteAdapter();
        $write->delete(
            $this->getTable('tag/summary'),
            ['tag_id = ?' => $tagId],
        );
        $write->insertMultiple($this->getTable('tag/summary'), $finalSummary);

        return $object;
    }

    /**
     * Decrementing tag products quantity as action for product delete
     *
     * @return int                       The number of affected rows
     * @throws Zend_Db_Adapter_Exception
     */
    public function decrementProducts(array $tagsId)
    {
        $writeAdapter = $this->_getWriteAdapter();
        if (empty($tagsId)) {
            return 0;
        }

        return $writeAdapter->update(
            $this->getTable('tag/summary'),
            ['products' => new Zend_Db_Expr('products - 1')],
            ['tag_id IN (?)' => $tagsId],
        );
    }

    /**
     * Add summary data to specified object
     *
     * @param  Mage_Tag_Model_Tag  $object
     * @return Mage_Tag_Model_Tag
     * @throws Mage_Core_Exception
     * @deprecated after 1.4.0.0
     */
    public function addSummary($object)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from(['relation' => $this->getTable('tag/relation')], [])
            ->joinLeft(
                ['summary' => $this->getTable('tag/summary')],
                'relation.tag_id = summary.tag_id AND relation.store_id = summary.store_id',
                [
                    'customers',
                    'products',
                    'popularity',
                ],
            )
            ->where('relation.tag_id = :tag_id')
            ->where('relation.store_id = :store_id')
            ->limit(1);
        $bind = [
            'tag_id' => (int) $object->getId(),
            'store_id' => (int) $object->getStoreId(),
        ];
        $row = $read->fetchRow($select, $bind);
        if ($row) {
            $object->addData($row);
        }

        return $object;
    }

    /**
     * Retrieve select object for load object data
     * Redeclare parent method just for adding tag's base popularity if flag exists
     *
     * @param  string                                      $field
     * @param  mixed                                       $value
     * @param  Mage_Core_Model_Abstract|Mage_Tag_Model_Tag $object
     * @return Zend_Db_Select
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($object->getAddBasePopularity() && $object->hasStoreId()) {
            $select->joinLeft(
                ['properties' => $this->getTable('tag/properties')],
                "properties.tag_id = {$this->getMainTable()}.tag_id AND properties.store_id = {$object->getStoreId()}",
                'base_popularity',
            );
        }

        return $select;
    }

    /**
     * Fetch store ids in which tag visible
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from($this->getTable('tag/summary'), ['store_id'])
            ->where('tag_id = :tag_id');
        $storeIds = $read->fetchCol($select, ['tag_id' => $object->getId()]);

        $object->setVisibleInStoreIds($storeIds);

        return $this;
    }
}
