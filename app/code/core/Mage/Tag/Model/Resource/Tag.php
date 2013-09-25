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
 * @package     Mage_Tag
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Tag resourse model
 *
 * @category    Mage
 * @package     Mage_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tag_Model_Resource_Tag extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table and primary index
     *
     */
    protected function _construct()
    {
        $this->_init('tag/tag', 'tag_id');
    }

    /**
     * Initialize unique fields
     *
     * @return Mage_Tag_Model_Resource_Tag
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(array(
            'field' => 'name',
            'title' => Mage::helper('tag')->__('Tag')
        ));
        return $this;
    }

    /**
     * Loading tag by name
     *
     * @param Mage_Tag_Model_Tag $model
     * @param string $name
     * @return array|false
     */
    public function loadByName($model, $name)
    {
        if ( $name ) {
            $read = $this->_getReadAdapter();
            $select = $read->select();
            if (Mage::helper('core/string')->strlen($name) > 255) {
                $name = Mage::helper('core/string')->substr($name, 0, 255);
            }

            $select->from($this->getMainTable())
                ->where('name = :name');
            $data = $read->fetchRow($select, array('name' => $name));

            $model->setData(( is_array($data) ) ? $data : array());
        } else {
            return false;
        }
    }

    /**
     * Before saving actions
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Tag_Model_Resource_Tag
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getId() && $object->getStatus() == $object->getApprovedStatus()) {
            $searchTag = new Varien_Object();
            $this->loadByName($searchTag, $object->getName());
            if ($searchTag->getData($this->getIdFieldName())
                    && $searchTag->getStatus() == $object->getPendingStatus()) {
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
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getStore() || !Mage::app()->getStore()->isAdmin()) {
            return parent::_afterSave($object);
        }

        $tagId = ($object->isObjectNew()) ? $object->getTagId() : $object->getId();

        $writeAdapter = $this->_getWriteAdapter();
        $writeAdapter->insertOnDuplicate($this->getTable('tag/properties'), array(
            'tag_id'            => $tagId,
            'store_id'          => $object->getStore(),
            'base_popularity'   => (!$object->getBasePopularity()) ? 0 : $object->getBasePopularity()
        ));

        return parent::_afterSave($object);
    }

    /**
     * Getting base popularity per store view for specified tag
     *
     * @deprecated after 1.4.0.0
     *
     * @param int $tagId
     * @return array
     */
    protected function _getExistingBasePopularity($tagId)
    {
        $read = $this->_getReadAdapter();
        $selectSummary = $read->select()
            ->from(
                array('main' => $this->getTable('tag/summary')),
                array('store_id', 'base_popularity')
            )
            ->where('main.tag_id = :tag_id')
            ->where('main.store_id != 0');

        return $read->fetchAssoc($selectSummary, array('tag_id' => $tagId));
    }

    /**
     * Get aggregation data per store view
     *
     * @deprecated after 1.4.0.0
     *
     * @param int $tagId
     * @return array
     */
    protected function _getAggregationPerStoreView($tagId)
    {
        $readAdapter = $this->_getReadAdapter();
        $selectLocal = $readAdapter->select()
            ->from(
                array('main'  => $this->getTable('tag/relation')),
                array(
                    'customers' => 'COUNT(DISTINCT main.customer_id)',
                    'products'  => 'COUNT(DISTINCT main.product_id)',
                    'store_id',
                    'uses'      => 'COUNT(main.tag_relation_id)'
                )
            )
            ->join(array('store' => $this->getTable('core/store')),
                'store.store_id = main.store_id AND store.store_id > 0',
                array()
            )
            ->join(array('product_website' => $this->getTable('catalog/product_website')),
                'product_website.website_id = store.website_id AND product_website.product_id = main.product_id',
                array()
            )
            ->where('main.tag_id = :tag_id')
            ->where('main.active = 1')
            ->group('main.store_id');

        $selectLocalResult = $readAdapter->fetchAll($selectLocal, array('tag_id' => $tagId));

        $selectHistorical = $readAdapter->select()
            ->from(
                array('main' => $this->getTable('tag/relation')),
                array('historical_uses' => 'COUNT(main.tag_relation_id)',
                'store_id')
            )
            ->join(array('store' => $this->getTable('core/store')),
                'store.store_id = main.store_id AND store.store_id > 0',
                array()
            )
            ->join(array('product_website' => $this->getTable('catalog/product_website')),
                'product_website.website_id = store.website_id AND product_website.product_id = main.product_id',
                array()
            )
            ->group('main.store_id')
            ->where('main.tag_id = :tag_id')
            ->where('main.active = 1');

        $selectHistoricalResult = $readAdapter->fetchAll($selectHistorical, array('tag_id' => $tagId));

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
     * @deprecated after 1.4.0.0
     *
     * @param int $tagId
     * @return array
     */
    protected function _getGlobalAggregation($tagId)
    {
        $readAdapter = $this->_getReadAdapter();
        // customers and products stats
        $selectGlobal = $readAdapter->select()
            ->from(
                array('main' => $this->getTable('tag/relation')),
                array(
                    'customers' => 'COUNT(DISTINCT main.customer_id)',
                    'products'  => 'COUNT(DISTINCT main.product_id)',
                    'store_id'  => new Zend_Db_Expr(0),
                    'uses'      => 'COUNT(main.tag_relation_id)'
                )
            )
            ->join(array('store' => $this->getTable('core/store')),
                'store.store_id=main.store_id AND store.store_id>0',
                array()
            )
            ->join(array('product_website' => $this->getTable('catalog/product_website')),
                'product_website.website_id = store.website_id AND product_website.product_id = main.product_id',
                array()
            )
            ->where('main.tag_id = :tag_id')
            ->where('main.active = 1');
        $result = $readAdapter->fetchRow($selectGlobal, array('tag_id' => $tagId));
        if (!$result) {
            return array();
        }

        // historical uses stats
        $selectHistoricalGlobal = $readAdapter->select()
            ->from(
                array('main' => $this->getTable('tag/relation')),
                array('historical_uses' => 'COUNT(main.tag_relation_id)')
            )
            ->join(array('store' => $this->getTable('core/store')),
                'store.store_id = main.store_id AND store.store_id > 0',
                array()
            )
            ->join(array('product_website' => $this->getTable('catalog/product_website')),
                'product_website.website_id = store.website_id AND product_website.product_id = main.product_id',
                array()
            )
            ->where('main.tag_id = :tag_id')
            ->where('main.active = 1');
        $result['historical_uses'] = (int) $readAdapter->fetchOne($selectHistoricalGlobal, array('tag_id' => $tagId));

        return $result;
    }

    /**
     * Getting statistics data into buffer.
     * Replacing our buffer array with new statistics and incoming data.
     *
     * @deprecated after 1.4.0.0
     *
     * @param Mage_Tag_Model_Tag $object
     * @return Mage_Tag_Model_Tag
     */
    public function aggregate($object)
    {
        $tagId   = (int)$object->getId();
        $storeId = (int)$object->getStore();

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
            $storeId = (int)$row['store_id'];
            foreach ($row as $key => $value) {
                $finalSummary[$storeId][$key] = $value;
            }
        }

        // prepare static parameters to final summary for insertion
        foreach ($finalSummary as $key => $row) {
            $finalSummary[$key]['tag_id'] = $tagId;
            foreach (array('base_popularity', 'popularity', 'historical_uses', 'uses', 'products', 'customers') as $k) {
                if (!isset($row[$k])) {
                    $finalSummary[$key][$k] = 0;
                }
            }
            $finalSummary[$key]['popularity'] = $finalSummary[$key]['historical_uses'];
        }

        // remove old and insert new data
        $write = $this->_getWriteAdapter();
        $write->delete(
            $this->getTable('tag/summary'), array('tag_id = ?' => $tagId)
        );
        $write->insertMultiple($this->getTable('tag/summary'), $finalSummary);

        return $object;
    }

    /**
     * Decrementing tag products quantity as action for product delete
     *
     * @param array $tagsId
     * @return int The number of affected rows
     */
    public function decrementProducts(array $tagsId)
    {
        $writeAdapter = $this->_getWriteAdapter();
        if (empty($tagsId)) {
            return 0;
        }

        return $writeAdapter->update(
            $this->getTable('tag/summary'),
            array('products' => new Zend_Db_Expr('products - 1')),
            array('tag_id IN (?)' => $tagsId)
        );
    }

    /**
     * Add summary data to specified object
     *
     * @deprecated after 1.4.0.0
     *
     * @param Mage_Tag_Model_Tag $object
     * @return Mage_Tag_Model_Tag
     */
    public function addSummary($object)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from(array('relation' => $this->getTable('tag/relation')), array())
            ->joinLeft(
                array('summary' => $this->getTable('tag/summary')),
                'relation.tag_id = summary.tag_id AND relation.store_id = summary.store_id',
                array(
                    'customers',
                    'products',
                    'popularity'
                )
            )
            ->where('relation.tag_id = :tag_id')
            ->where('relation.store_id = :store_id')
            ->limit(1);
        $bind = array(
            'tag_id' => (int)$object->getId(),
            'store_id' => (int)$object->getStoreId()
        );
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
     * @param string $field
     * @param mixed $value
     * @param Mage_Core_Model_Abstract $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($object->getAddBasePopularity() && $object->hasStoreId()) {
            $select->joinLeft(
                array('properties' => $this->getTable('tag/properties')),
                "properties.tag_id = {$this->getMainTable()}.tag_id AND properties.store_id = {$object->getStoreId()}",
                'base_popularity'
            );
        }
        return $select;
    }

    /**
     * Fetch store ids in which tag visible
     *
     * @param Mage_Tag_Model_Resource_Tag $object
     * @return Mage_Tag_Model_Resource_Tag
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from($this->getTable('tag/summary'), array('store_id'))
            ->where('tag_id = :tag_id');
        $storeIds = $read->fetchCol($select, array('tag_id' => $object->getId()));

        $object->setVisibleInStoreIds($storeIds);

        return $this;
    }
}
