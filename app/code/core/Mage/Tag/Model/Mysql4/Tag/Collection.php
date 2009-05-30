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
 * @category   Mage
 * @package    Mage_Tag
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tag collection model
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tag_Model_Mysql4_Tag_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_isStoreFilter = false;
    protected $_joinFlags = array();

    var $_map = array(
        'fields' => array(
            'tag_id' => 'main_table.tag_id'
        ),
    );

    protected function _construct()
    {
        $this->_init('tag/tag');
    }

    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        parent::load($printQuery, $logQuery);
        if ($this->getJoinFlag('add_stores_after')) {
            $this->_addStoresVisibility();
        }
        return $this;
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


    public function limit($limit)
    {
        $this->getSelect()->limit($limit);
        return $this;
    }


    public function addPopularity($limit=null)
    {
        $this->getSelect()
            ->joinLeft(
                array('prelation'=>$this->getTable('tag/relation')),
                'main_table.tag_id=prelation.tag_id',
                array('popularity' => 'COUNT(DISTINCT relation.tag_relation_id)'
            ))
            ->group('main_table.tag_id');
            $this->joinRel();
        if (! is_null($limit)) {
            $this->getSelect()->limit($limit);
        }
        $this->setJoinFlag('prelation');
        return $this;
    }

    public function addSummary($storeId)
    {
        $joinCondition = '';
        if (is_array($storeId)) {
            $joinCondition = ' AND summary.store_id IN (' . implode(',', $storeId) . ')';
        } else {
            $joinCondition = $this->getConnection()->quoteInto(' AND summary.store_id = ?', (int)$storeId);
        }

        $this->getSelect()
            ->joinLeft(
                array('summary'=>$this->getTable('tag/summary')),
                'main_table.tag_id=summary.tag_id' . $joinCondition,
                array('store_id','popularity', 'customers', 'products', 'uses', 'historical_uses'
            ));

        $this->setJoinFlag('summary');
        return $this;
    }

    public function addStoresVisibility()
    {
        $this->setJoinFlag('add_stores_after');
        return $this;
    }

    protected function _addStoresVisibility()
    {
        $tagIds = $this->getColumnValues('tag_id');

        $tagsStores = array();
        if (sizeof($tagIds)>0) {
            $select = $this->getConnection()->select()
                ->from($this->getTable('summary'), array('store_id', 'tag_id'))
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
            if(isset($tagsStores[$item->getId()])) {
                $item->setStores($tagsStores[$item->getId()]);
            } else {
                $item->setStores(array());
            }
        }

        return $this;
    }

    public function addFieldToFilter($field, $condition=null)
    {
        if ($this->getJoinFlag('relation') && 'popularity' == $field) {
            // TOFIX
            $this->getSelect()->having($this->_getConditionSql('count(relation.tag_relation_id)', $condition));
        } elseif ($this->getJoinFlag('summary') && in_array($field, array('customers', 'products', 'uses', 'historical_uses', 'popularity'))) {
            $this->getSelect()->where($this->_getConditionSql('summary.'.$field, $condition));
        } else {
           parent::addFieldToFilter($field, $condition);
        }
        return $this;
    }

    /**
     * Get sql for get record count
     *
     * @return  string
     */

    public function getSelectCountSql()
    {
        $this->_renderFilters();
        $countSelect = clone $this->_select;
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);

        $sql = $countSelect->__toString();
        // TOFIX
        $sql = preg_replace('/^select\s+.+?\s+from\s+/is', 'select COUNT(DISTINCT main_table.tag_id) from ', $sql);
        return $sql;
    }

    public function addStoreFilter($storeId, $allFilter = true)
    {
        if ($this->_isStoreFilter) {
            return $this;
        }
        if (!is_array($storeId)) {
            $storeId = array($storeId);
        }
        $this->getSelect()->join(array(
            'summary_store'=>$this->getTable('summary')),
            'main_table.tag_id = summary_store.tag_id
            AND summary_store.store_id IN (' . implode(',', $storeId) . ')',
            array());

        $this->getSelect()->group('summary_store.tag_id');

        if($this->getJoinFlag('relation') && $allFilter) {
            $this->getSelect()->where('relation.store_id IN (' . implode(',', $storeId) . ')');
        }

        if($this->getJoinFlag('prelation') && $allFilter) {
            $this->getSelect()->where('prelation.store_id IN (' . implode(',', $storeId) . ')');
        }
        $this->_isStoreFilter = true;

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

    public function addStatusFilter($status)
    {
        $this->addFieldToFilter('main_table.status', $status);
        return $this;
    }

    public function addProductFilter($productId)
    {
        $this->addFieldToFilter('relation.product_id', $productId);
        if($this->getJoinFlag('prelation')) {
            $this->addFieldToFilter('prelation.product_id', $productId);
        }
        return $this;
    }

    public function addCustomerFilter($customerId)
    {
        $this->getSelect()
            ->where('relation.customer_id = ?', $customerId);
        if($this->getJoinFlag('prelation')) {
            $this->getSelect()
                ->where('prelation.customer_id = ?', $customerId);
        }
        return $this;
    }


    public function addTagGroup()
    {
        $this->getSelect()->group('main_table.tag_id');
        return $this;
    }


    public function joinRel()
    {
        $this->setJoinFlag('relation');
        $this->getSelect()->joinLeft(array('relation'=>$this->getTable('tag/relation')), 'main_table.tag_id=relation.tag_id');
        return $this;
    }
}