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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tag resourse model
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tag_Model_Mysql4_Tag extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('tag/tag', 'tag_id');
    }

    /**
     * Initialize unique fields
     *
     * @return Mage_Core_Model_Mysql4_Abstract
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
     * @return unknown
     */
    public function loadByName($model, $name)
    {
        if( $name ) {
            $read = $this->_getReadAdapter();
            $select = $read->select();
            if (Mage::helper('core/string')->strlen($name) > 255) {
                $name = Mage::helper('core/string')->substr($name, 0, 255);
            }

            $select->from($this->getMainTable())
                ->where('name = ?', $name);
            $data = $read->fetchRow($select);

            $model->setData( ( is_array($data) ) ? $data : array() );
        } else {
            return false;
        }
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getId() && $object->getStatus()==$object->getApprovedStatus()) {
            $searchTag = new Varien_Object();
            $this->loadByName($searchTag, $object->getName());
            if($searchTag->getData($this->getIdFieldName()) && $searchTag->getStatus()==$object->getPendingStatus()) {
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
     * @return Mage_Core_Model_Mysql4_Abstract
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
            'base_popularity'   => (is_null($object->getBasePopularity())) ? 0 : $object->getBasePopularity()
        ));

        return parent::_afterSave($object);
    }

    /**
     * Getting base popularity per store view for specified tag
     *
     * @deprecated after 1.4.0.0
     * @param int $tagId
     * @return array
     */
    protected function _getExistingBasePopularity($tagId)
    {
        $selectSummary = $this->_getReadAdapter()->select()
            ->from(
                array('main' => $this->getTable('summary')),
                array('store_id', 'base_popularity')
            )
            ->where('main.tag_id = ?', $tagId)
            ->where('main.store_id != 0');

        return $this->_getReadAdapter()->fetchAssoc($selectSummary);
    }


    /**
     * Get aggregation data per store view
     *
     * @deprecated after 1.4.0.0
     * @param int $tagId
     * @return array
     */
    protected function _getAggregationPerStoreView($tagId)
    {
        $readAdapter = $this->_getReadAdapter();
        $selectLocal = $readAdapter->select()
            ->from(
                array('main'  => $this->getTable('relation')),
                array(
                    'customers'=>'COUNT(DISTINCT main.customer_id)',
                    'products'=>'COUNT(DISTINCT main.product_id)',
                    'store_id',
                    'uses'=>'COUNT(main.tag_relation_id)'
                )
            )
            ->join(array('store' => $this->getTable('core/store')),
                'store.store_id=main.store_id AND store.store_id>0',
                array()
            )
            ->join(array('product_website' => $this->getTable('catalog/product_website')),
                'product_website.website_id=store.website_id AND product_website.product_id=main.product_id',
                array()
            )
            ->where('main.tag_id = ?', $tagId)
            ->where('main.active')
            ->group('main.store_id');

        $selectLocalResult = $readAdapter->fetchAll($selectLocal);

        $selectHistorical = $readAdapter->select()
            ->from(
                array('main'=>$this->getTable('relation')),
                array('historical_uses'=>'COUNT(main.tag_relation_id)',
                'store_id')
            )
            ->join(array('store' => $this->getTable('core/store')),
                'store.store_id=main.store_id AND store.store_id>0',
                array()
            )
            ->join(array('product_website' => $this->getTable('catalog/product_website')),
                'product_website.website_id=store.website_id AND product_website.product_id=main.product_id',
                array()
            )
            ->group('main.store_id')
            ->where('main.tag_id = ?', $tagId);

        $selectHistoricalResult = $readAdapter->fetchAll($selectHistorical);

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
     * @param int $tagId
     * @return array
     */
    protected function _getGlobalAggregation($tagId)
    {
        $readAdapter = $this->_getReadAdapter();
        // customers and products stats
        $selectGlobal = $readAdapter->select()
            ->from(
                array('main'=>$this->getTable('relation')),
                array(
                    'customers'=>'COUNT(DISTINCT main.customer_id)',
                    'products'=>'COUNT(DISTINCT main.product_id)',
                    'store_id'=>'( 0 )' /* Workaround*/,
                    'uses'=>'COUNT(main.tag_relation_id)'
                )
            )
            ->join(array('store' => $this->getTable('core/store')),
                'store.store_id=main.store_id AND store.store_id>0',
                array()
            )
            ->join(array('product_website' => $this->getTable('catalog/product_website')),
                'product_website.website_id=store.website_id AND product_website.product_id=main.product_id',
                array()
            )
            ->where('main.tag_id = ?', $tagId)
            ->where('main.active');
        $result = $readAdapter->fetchRow($selectGlobal);
        if (!$result) {
            return array();
        }

        // historical uses stats
        $selectHistoricalGlobal = $readAdapter->select()
            ->from(
                array('main'=>$this->getTable('relation')),
                array('historical_uses'=>'COUNT(main.tag_relation_id)')
            )
            ->join(array('store' => $this->getTable('core/store')),
                'store.store_id=main.store_id AND store.store_id>0',
                array()
            )
            ->join(array('product_website' => $this->getTable('catalog/product_website')),
                'product_website.website_id=store.website_id AND product_website.product_id=main.product_id',
                array()
            )
            ->where('main.tag_id = ?', $tagId);
        $result['historical_uses'] = (int) $readAdapter->fetchOne($selectHistoricalGlobal);

        return $result;
    }

    /**
     * Getting statistics data into buffer.
     * Replacing our buffer array with new statistics and incoming data.
     *
     * @deprecated after 1.4.0.0
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
        $this->_getWriteAdapter()->delete(
            $this->getTable('summary'), $this->_getWriteAdapter()->quoteInto('tag_id = ?', $tagId)
        );
        $this->_getWriteAdapter()->insertMultiple($this->getTable('summary'), $finalSummary);

        return $object;
    }

    /**
     * Decrementing tag products quantity as action for product delete
     *
     * @param  array $tagsId
     * @return int The number of affected rows
     */
    public function decrementProducts(array $tagsId)
    {
        $writeAdapter = $this->_getWriteAdapter();
        $whereCond    = $writeAdapter->quoteInto('`tag_id` IN (?)', $tagsId, Zend_Db::INT_TYPE);

        return $writeAdapter->update($this->getTable('summary'), array('products' => new Zend_Db_Expr('products - 1')), $whereCond);
    }

    /**
     * Add summary data to specified object
     *
     * @deprecated after 1.4.0.0
     * @param Mage_Tag_Model_Tag $object
     * @return Mage_Tag_Model_Tag
     */
    public function addSummary($object)
    {
        $select = $this->_getReadAdapter()->select()
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
            ->where('relation.tag_id = ?', (int)$object->getId())
            ->where('relation.store_id = ?', (int)$object->getStoreId())
            ->limit(1);

        $row = $this->_getReadAdapter()->fetchRow($select);
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
     * @param Mage_Tag_Model_Mysql4_Tag $object
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('tag/summary'), array('store_id'))
            ->where('tag_id = ?', $object->getId());
        $storeIds = $this->_getReadAdapter()->fetchCol($select);

        $object->setVisibleInStoreIds($storeIds);

        return $this;
    }
}
