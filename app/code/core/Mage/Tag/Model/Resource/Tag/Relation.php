<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tag
 */

/**
 * Tag Relation resource model
 *
 * @package    Mage_Tag
 */
class Mage_Tag_Model_Resource_Tag_Relation extends Mage_Core_Model_Resource_Db_Abstract
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
     * @return $this
     */
    public function loadByTagCustomer($model)
    {
        if ($model->getTagId() && $model->getCustomerId()) {
            $read = $this->_getReadAdapter();
            $bind = [
                'tag_id'      => $model->getTagId(),
                'customer_id' => $model->getCustomerId(),
            ];

            $select = $read->select()
                ->from($this->getMainTable())
                ->join(
                    $this->getTable('tag/tag'),
                    $this->getTable('tag/tag') . '.tag_id = ' . $this->getMainTable() . '.tag_id',
                )
                ->where($this->getMainTable() . '.tag_id = :tag_id')
                ->where('customer_id = :customer_id');

            if ($model->getProductId()) {
                $select->where($this->getMainTable() . '.product_id = :product_id');
                $bind['product_id'] = $model->getProductId();
            }

            if ($model->hasStoreId()) {
                $select->where($this->getMainTable() . '.store_id = :sore_id');
                $bind['sore_id'] = $model->getStoreId();
            }

            $data = $read->fetchRow($select, $bind);
            $model->setData((is_array($data)) ? $data : []);
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
        $bind = [
            'tag_id' => $model->getTagId(),
        ];
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'product_id')
            ->where($this->getMainTable() . '.tag_id=:tag_id');

        if (!is_null($model->getCustomerId())) {
            $select->where($this->getMainTable() . '.customer_id= :customer_id');
            $bind['customer_id'] = $model->getCustomerId();
        }

        if ($model->hasStoreId()) {
            $select->where($this->getMainTable() . '.store_id = :store_id');
            $bind['store_id'] = $model->getStoreId();
        }

        if (!is_null($model->getStatusFilter())) {
            $select->join(
                $this->getTable('tag/tag'),
                $this->getTable('tag/tag') . '.tag_id = ' . $this->getMainTable() . '.tag_id',
            )
            ->where($this->getTable('tag/tag') . '.status = :t_status');
            $bind['t_status'] = $model->getStatusFilter();
        }

        return $this->_getReadAdapter()->fetchCol($select, $bind);
    }

    /**
     * Retrieve related to product tag ids
     *
     * @param Mage_Tag_Model_Tag_Relation $model
     * @return array
     */
    public function getRelatedTagIds($model)
    {
        $productIds = (is_array($model->getProductId())) ? $model->getProductId() : [$model->getProductId()];
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'tag_id')
            ->where('product_id IN(?)', $productIds)
            ->order('tag_id');
        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Deactivate tag relations by tag and customer
     *
     * @param int $tagId
     * @param int $customerId
     * @return $this
     */
    public function deactivate($tagId, $customerId)
    {
        $condition = [
            'tag_id = ?'      => $tagId,
            'customer_id = ?' => $customerId,
        ];

        $data = ['active' => Mage_Tag_Model_Tag_Relation::STATUS_NOT_ACTIVE];
        $this->_getWriteAdapter()->update($this->getMainTable(), $data, $condition);
        return $this;
    }

    /**
     * Add TAG to PRODUCT relations
     *
     * @param Mage_Tag_Model_Tag_Relation $model
     * @return $this
     */
    public function addRelations($model)
    {
        $addedIds = $model->getAddedProductIds();

        $bind = [
            'tag_id'   => $model->getTagId(),
            'store_id' => $model->getStoreId(),
        ];
        $write = $this->_getWriteAdapter();

        $select = $write->select()
            ->from($this->getMainTable(), 'product_id')
            ->where('tag_id = :tag_id')
            ->where('store_id = :store_id');
        $oldRelationIds = $write->fetchCol($select, $bind);

        $insert = array_diff($addedIds, $oldRelationIds);
        $delete = array_diff($oldRelationIds, $addedIds);

        if (!empty($insert)) {
            $insertData = [];
            foreach ($insert as $value) {
                $insertData[] = [
                    'tag_id'        => $model->getTagId(),
                    'store_id'      => $model->getStoreId(),
                    'product_id'    => $value,
                    'customer_id'   => $model->getCustomerId(),
                    'created_at'    => $this->formatDate(time()),
                ];
            }

            $write->insertMultiple($this->getMainTable(), $insertData);
        }

        if (!empty($delete)) {
            $write->delete($this->getMainTable(), [
                'product_id IN (?)' => $delete,
                'store_id = ?'      => $model->getStoreId(),
            ]);
        }

        return $this;
    }
}
