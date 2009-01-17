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
 * @category   Mage
 * @package    Mage_Tag
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tagged products Collection
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tag_Model_Mysql4_Product_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{
    protected $_entitiesAlias = array();
    protected $_customerFilterId;
    protected $_tagIdFilter;


    protected $_joinFlags = array();

    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_joinFields();
        $this->getSelect()->group('e.entity_id');
    }

    public function setJoinFlag($table)
    {
        $this->_joinFlags[$table] = true;
        return $this;
    }

    public function getJoinFlag($table)
    {
        return isset($this->_joinFlags[$table]);
    }

    public function unsetJoinFlag($table=null)
    {
        if (is_null($table)) {
            $this->_joinFlags = array();
        } elseif ($this->getJoinFlag($table)) {
            unset($this->_joinFlags[$table]);
        }

        return $this;
    }

    public function addStoresVisibility()
    {
        $this->setJoinFlag('add_stores_after');
        return $this;
    }

    protected function _addStoresVisibility()
    {
        $tagIds =array();

        foreach ($this as $item) {
            $tagIds[] = $item->getTagId();
        }

        $tagsStores = array();
        if (sizeof($tagIds)>0) {
            $select = $this->getConnection()->select()
                ->from($this->getTable('tag/summary'), array('store_id','tag_id'))
                ->where('tag_id IN(?)', $tagIds);
            $tagsRaw = $this->getConnection()->fetchAll($select);
            foreach ($tagsRaw as $tag) {
                if (!isset($tagsStores[$tag['tag_id']])) {
                    $tagsStores[$tag['tag_id']] = array();
                }

                $tagsStores[$tag['tag_id']][] = $tag['store_id'];
            }
        }

        foreach ($this as $item) {
            if(isset($tagsStores[$item->getTagId()])) {
                $item->setStores($tagsStores[$item->getTagId()]);
            } else {
                $item->setStores(array());
            }
        }


        return $this;
    }

    public function addGroupByTag()
    {
        $this->getSelect()->group('relation.tag_relation_id');
        return $this;
    }

    public function addCustomerFilter($customerId)
    {
        $this->getSelect()->where('relation.customer_id = ?', $customerId);
        $this->_customerFilterId = $customerId;
        return $this;
    }

    public function addTagFilter($tagId)
    {
        $this->getSelect()->where('relation.tag_id = ?', $tagId);
        $this->setJoinFlag('distinct');
        return $this;
    }

    public function addStatusFilter($status)
    {
        $this->getSelect()->where('t.status = ?', $status);
        return $this;
    }

    public function setDescOrder($dir='DESC')
    {
        $this->setOrder('relation.tag_relation_id', $dir);
        return $this;
    }

    public function addPopularity($tagId, $storeId=null)
    {
        $tagRelationTable = $this->getTable('tag/relation');

        $condition = '';
        if(!is_null($storeId)) {
          $condition = 'AND ' . $this->getConnection()->quoteInto('prelation.store_id = ?', $storeId);
        }

        $this->getSelect()->joinLeft(
                array('prelation' => $tagRelationTable),
                'prelation.product_id=e.entity_id '.$condition ,
                array('COUNT(DISTINCT prelation.tag_relation_id) AS popularity')
            )
            ->where('prelation.tag_id = ?', $tagId);

        $this->_tagIdFilter = $tagId;
        $this->setJoinFlag('prelation');
        return $this;
    }

    public function addPopularityFilter($condition) {
        $tagRelationTable = Mage::getSingleton('core/resource')->getTableName('tag/relation');

        $select = $this->getConnection()->select()
            ->from($tagRelationTable, array('product_id', 'COUNT(DISTINCT tag_relation_id) as popularity'))
            ->where('tag_id = ?', $this->_tagIdFilter)
            ->group('product_id')
            ->having($this->_getConditionSql('popularity', $condition));

        $prodIds = array();
        foreach($this->getConnection()->fetchAll($select) as $item) {
            $prodIds[] = $item['product_id'];
        }

        if(sizeof($prodIds)>0) {
            $this->getSelect()->where('e.entity_id IN(?)', $prodIds);
        } else {
            $this->getSelect()->where('e.entity_id IN(0)');
        }

        return $this;
    }

    public function setActiveFilter()
    {
        $this->getSelect()->where('relation.active = 1');
        if($this->getJoinFlag('prelation')) {
            $this->getSelect()->where('prelation.active = 1');
        }
        return $this;
    }

    public function addProductTags($storeId=null)
    {
        foreach( $this->getItems() as $item ) {
            $tagsCollection = Mage::getModel('tag/tag')->getResourceCollection();

            if (!is_null($storeId)) {
                $tagsCollection->addStoreFilter($storeId);
            }

            $tagsCollection->addPopularity()
                ->addProductFilter($item->getEntityId())
                ->addCustomerFilter($this->_customerFilterId)
                ->setActiveFilter();



            $tagsCollection->load();
            $item->setProductTags( $tagsCollection );
        }

        return $this;
    }

    protected function _joinFields()
    {
        $tagTable           = $this->getTable('tag/tag');
        $tagRelationTable   = $this->getTable('tag/relation');

        $this->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('small_image');

        $this->getSelect()
            ->join(array('relation' => $tagRelationTable), 'relation.product_id = e.entity_id')
            ->join(array('t' => $tagTable),
                't.tag_id = relation.tag_id',
                array('tag_id', 'name', 'tag_status' => 'status', 'tag_name' => 'name')
            );
        return $this;
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();

        if($this->getJoinFlag('add_stores_after')) {
            $this->_addStoresVisibility();
        }

        return $this;
    }

    /**
     * Render SQL for retrieve product count
     *
     * @return string
     */
    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();

        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::GROUP);

        if($this->getJoinFlag('group_tag')) {
            $field = 'relation.tag_id';
        } else {
            $field = 'e.entity_id';
        }
        $sql = $countSelect->__toString();
        $sql = preg_replace('/^select\s+.+?\s+from\s+/is', 'select count(' . ( $this->getJoinFlag('distinct') ? 'DISTINCT ' : '' ) . $field . ') from ', $sql);
        return $sql;
    }

    public function setOrder($attribute, $dir='desc')
    {
        if ($attribute == 'popularity') {
            $this->getSelect()->order($attribute . ' ' . $dir);
        }
        else {
            parent::setOrder($attribute, $dir);
        }
        return $this;
    }

    public function setRelationId()
    {
        $this->_setIdFieldName('tag_relation_id');
        return $this;
    }
}