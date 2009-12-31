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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
    /**
     * Use getFlag('store_filter') & setFlag('store_filter', true) instead.
     *
     * @deprecated after 1.3.2.3
     */
    protected $_isStoreFilter = false;

    /**
     * @deprecated after 1.3.2.3
     */
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
        if ($this->getFlag('add_stores_after')) {
            $this->_addStoresVisibility();
        }
        return $this;
    }

    /**
     * Set flag about joined table.
     * setFlag method must be used in future.
     *
     * @deprecated after 1.3.2.3
     * @param string $table
     * @return Mage_Tag_Model_Mysql4_Tag_Collection
     */
    public function setJoinFlag($table)
    {
        $this->setFlag($table, true);
        return $this;
    }

    /**
     * Get flag's status about joined table.
     * getFlag method must be used in future.
     *
     * @deprecated after 1.3.2.3
     * @param $table
     * @return bool
     */
    public function getJoinFlag($table)
    {
        return $this->getFlag($table);
    }

    /**
     * Unset value of join flag.
     * Set false (bool) value to flag instead in future.
     *
     * @deprecated after 1.3.2.3
     * @param $table
     * @return Mage_Tag_Model_Mysql4_Tag_Collection
     */
    public function unsetJoinFlag($table=null)
    {
        $this->setFlag($table, false);
        return $this;
    }

    public function limit($limit)
    {
        $this->getSelect()->limit($limit);
        return $this;
    }

    /**
     * Replacing popularity by sum of popularity and base_popularity
     *
     * @param int $limit
     * @return Mage_Tag_Model_Mysql4_Tag_Collection
     */
    public function addPopularity($limit = null)
    {
        if (!$this->getFlag('popularity')) {
            $this->getSelect()
                ->joinLeft(array('relation'=>$this->getTable('tag/relation')), 'main_table.tag_id=relation.tag_id')
                ->joinLeft(array('summary'=>$this->getTable('tag/summary')), 'main_table.tag_id=summary.tag_id',
                    array('popularity' => '(summary.popularity + summary.base_popularity)')
                )
                ->group('main_table.tag_id');

            if (!is_null($limit)) {
                $this->getSelect()->limit($limit);
            }

            $this->setFlag('popularity');
        }
        return $this;
    }

    public function addSummary($storeId)
    {
        if (!$this->getFlag('summary')) {
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
                    array('store_id','popularity', 'base_popularity', 'customers', 'products', 'uses', 'historical_uses'
                ));

            $this->setFlag('summary', true);
        }
        return $this;
    }

    public function addStoresVisibility()
    {
        $this->setFlag('add_stores_after', true);

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
        if ($this->getFlag('relation') && 'popularity' == $field) {
            // TOFIX
            $this->getSelect()->having($this->_getConditionSql('count(relation.tag_relation_id)', $condition));
        } elseif ($this->getFlag('summary') && in_array($field, array('customers', 'products', 'uses', 'historical_uses', 'popularity'))) {
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

    /**
     * Add filter by store
     *
     * @param array | int $storeId
     * @param bool $allFilter
     * @return Mage_Tag_Model_Mysql4_Tag_Collection
     */
    public function addStoreFilter($storeId, $allFilter = true)
    {
        if (!$this->getFlag('store_filter')) {

            $this->getSelect()->joinLeft(
                array('summary_store'=>$this->getTable('summary')),
                'main_table.tag_id = summary_store.tag_id'
            );

            $this->getSelect()->where('summary_store.store_id IN (?)', $storeId);

            $this->getSelect()->group('summary_store.tag_id');

            if($this->getFlag('relation') && $allFilter) {
                $this->getSelect()->where('relation.store_id IN (?)', $storeId);
            }
            if($this->getFlag('prelation') && $allFilter) {
                $this->getSelect()->where('prelation.store_id IN (?)', $storeId);
            }

            $this->setFlag('store_filter', true);
        }

        return $this;
    }

    public function setActiveFilter()
    {
        $this->getSelect()->where('relation.active = 1');
        if($this->getFlag('prelation')) {
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
        if($this->getFlag('prelation')) {
            $this->addFieldToFilter('prelation.product_id', $productId);
        }
        return $this;
    }

    public function addCustomerFilter($customerId)
    {
        $this->getSelect()
            ->where('relation.customer_id = ?', $customerId);
        if($this->getFlag('prelation')) {
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
        $this->setFlag('relation', true);
        $this->getSelect()->joinLeft(array('relation'=>$this->getTable('tag/relation')), 'main_table.tag_id=relation.tag_id');
        return $this;
    }
}
