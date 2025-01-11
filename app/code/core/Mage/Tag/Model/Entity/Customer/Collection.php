<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Tag
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customers collection
 *
 * @category   Mage
 * @package    Mage_Tag
 */
class Mage_Tag_Model_Entity_Customer_Collection extends Mage_Customer_Model_Entity_Customer_Collection
{
    protected $_tagTable;

    protected $_tagRelTable;

    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        parent::__construct();
        $this->_tagTable = $resource->getTableName('tag/tag');
        $this->_tagRelTable = $resource->getTableName('tag/tag_relation');
    }

    /**
     * @param int $tagId
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function addTagFilter($tagId)
    {
        $this->joinField('tag_tag_id', $this->_tagRelTable, 'tag_id', 'customer_id=entity_id');
        $this->getSelect()->where($this->_getAttributeTableAlias('tag_tag_id') . '.tag_id=?', $tagId);
        return $this;
    }

    /**
     * @param int $productId
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function addProductFilter($productId)
    {
        $this->joinField('tag_product_id', $this->_tagRelTable, 'product_id', 'customer_id=entity_id');
        $this->getSelect()->where($this->_getAttributeTableAlias('tag_product_id') . '.product_id=?', $productId);
        return $this;
    }

    /**
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this|Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function load($printQuery = false, $logQuery = false)
    {
        parent::load($printQuery, $logQuery);
        $this->_loadTags($printQuery, $logQuery);
        return $this;
    }

    /**
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
     */
    protected function _loadTags($printQuery = false, $logQuery = false)
    {
        if (empty($this->_items)) {
            return $this;
        }
        $customerIds = [];
        foreach ($this->getItems() as $item) {
            $customerIds[] = $item->getId();
        }
        $this->getSelect()->reset()
            ->from(['tr' => $this->_tagRelTable], ['*','total_used' => 'count(tr.tag_relation_id)'])
            ->joinLeft(['t' => $this->_tagTable], 't.tag_id=tr.tag_id')
            ->group(['tr.customer_id', 't.tag_id'])
            ->where('tr.customer_id in (?)', $customerIds)
        ;
        $this->printLogQuery($printQuery, $logQuery);

        $tags = [];
        $data = $this->_read->fetchAll($this->getSelect());
        foreach ($data as $row) {
            if (!isset($tags[ $row['customer_id'] ])) {
                $tags[ $row['customer_id'] ] = [];
            }
            $tags[ $row['customer_id'] ][] = $row;
        }
        foreach ($this->getItems() as $item) {
            if (isset($tags[$item->getId()])) {
                $item->setData('tags', $tags[$item->getId()]);
            }
        }
        return $this;
    }
}
