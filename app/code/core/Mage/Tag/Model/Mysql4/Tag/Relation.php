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
 * Tag Relation resource model
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tag_Model_Mysql4_Tag_Relation extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Initialize resource connection and define table resource
     *
     */
    protected function _construct()
    {
        $this->_init('tag/relation', 'tag_relation_id');
    }

    /**
     * Load by Tag and Customer
     *
     * @param Mage_Tag_Model_Tag_Relation $model
     * @return Mage_Tag_Model_Mysql4_Tag_Relation
     */
    public function loadByTagCustomer($model)
    {
        if ($model->getTagId() && $model->getCustomerId()) {
            $read = $this->_getReadAdapter();
            $select = $read->select()
                ->from($this->getMainTable())
                ->join($this->getTable('tag/tag'), "{$this->getTable('tag/tag')}.tag_id = {$this->getMainTable()}.tag_id")
                ->where("{$this->getMainTable()}.tag_id = ?", $model->getTagId())
                ->where('customer_id = ?', $model->getCustomerId());

            if ($model->getProductId()) {
                $select->where("{$this->getMainTable()}.product_id = ?", $model->getProductId());
            }

            if ($model->hasStoreId()) {
                $select->where("{$this->getMainTable()}.store_id = ?", $model->getStoreId());
            }

            $data = $read->fetchRow($select);
            $model->setData( ( is_array($data) ) ? $data : array() );
        }

        return $this;
    }

    /**
     * Retrieve Tagged Products
     *
     * @param Mage_Tag_Model_Tag_Relation $model
     * @return array
     */
    public function getProductIds($model)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'product_id')
            ->where("tag_id=?", $model->getTagId());

        if (is_null($model->getCustomerId())) {
            $select->where('customer_id IS NULL');
        } else {
            $select->where('customer_id=?', $model->getCustomerId());
        }

        if ($model->hasStoreId()) {
            $select->where('store_id = ?', $model->getStoreId());
        }

        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Deactivate tag relations by tag and customer
     *
     * @param int $tagId
     * @param int $customerId
     * @return Mage_Tag_Model_Mysql4_Tag_Relation
     */
    public function deactivate($tagId, $customerId)
    {
        $condition = $this->_getWriteAdapter()->quoteInto('tag_id = ?', $tagId) . ' AND ';
        $condition.= $this->_getWriteAdapter()->quoteInto('customer_id = ?', $customerId);
        $data = array('active'=>0);
        $this->_getWriteAdapter()->update($this->getMainTable(), $data, $condition);
        return $this;
    }

    /**
     * Add TAG to PRODUCT relations
     *
     * @param Mage_Tag_Model_Tag_Relation $model
     * @return Mage_Tag_Model_Mysql4_Tag_Relation
     */
    public function addRelations($model)
    {
        $addedIds = $model->getAddedProductIds();

        $select = $this->_getWriteAdapter()->select()
            ->from($this->getMainTable(), 'product_id')
            ->where("tag_id = ?", $model->getTagId())
            ->where("store_id = ?", $model->getStoreId())
            ->where("customer_id IS NULL");
        $oldRelationIds = $this->_getWriteAdapter()->fetchCol($select);

        $insert = array_diff($addedIds, $oldRelationIds);
        $delete = array_diff($oldRelationIds, $addedIds);

        if (!empty($insert)) {
            $insertData = array();
            foreach ($insert as $value) {
                $insertData[] = array(
                    'tag_id'        => $model->getTagId(),
                    'store_id'      => $model->getStoreId(),
                    'product_id'    => $value,
                    'customer_id'   => $model->getCustomerId(),
                    'created_at'    => $this->formatDate(time())
                );
            }
            $this->_getWriteAdapter()->insertMultiple($this->getMainTable(), $insertData);
        }

        if (!empty($delete)) {
            $this->_getWriteAdapter()->delete($this->getMainTable(), array(
                $this->_getWriteAdapter()->quoteInto('product_id IN (?)', $delete),
                $this->_getWriteAdapter()->quoteInto('store_id = ?', $model->getStoreId()),
                'customer_id IS NULL'
            ));
        }

        return $this;
    }
}
