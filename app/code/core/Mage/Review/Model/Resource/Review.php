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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Review
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Review resource model
 *
 * @category    Mage
 * @package     Mage_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Review_Model_Resource_Review extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Review table
     *
     * @var string
     */
    protected $_reviewTable;

    /**
     * Review Detail table
     *
     * @var string
     */
    protected $_reviewDetailTable;

    /**
     * Review status table
     *
     * @var string
     */
    protected $_reviewStatusTable;

    /**
     * Review entity table
     *
     * @var string
     */
    protected $_reviewEntityTable;

    /**
     * Review store table
     *
     * @var string
     */
    protected $_reviewStoreTable;

    /**
     * Review aggregate table
     *
     * @var string
     */
    protected $_aggregateTable;

    /**
     * Cache of deleted rating data
     *
     * @var array
     */
    private $_deleteCache   = array();

    /**
     * Define main table. Define other tables name
     *
     */
    protected function _construct()
    {
        $this->_init('review/review', 'review_id');
        $this->_reviewTable         = $this->getTable('review/review');
        $this->_reviewDetailTable   = $this->getTable('review/review_detail');
        $this->_reviewStatusTable   = $this->getTable('review/review_status');
        $this->_reviewEntityTable   = $this->getTable('review/review_entity');
        $this->_reviewStoreTable    = $this->getTable('review/review_store');
        $this->_aggregateTable      = $this->getTable('review/review_aggregate');
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Core_Model_Abstract $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $select->join(
            $this->_reviewDetailTable,
            $this->getMainTable().".review_id = {$this->_reviewDetailTable}.review_id"
        );
        return $select;
    }

    /**
     * Perform actions before object save
     *
     * @param Mage_Core_Model_Abstract|Mage_Review_Model_Review $object
     * @return $this
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getId()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }
        if ($object->hasData('stores') && is_array($object->getStores())) {
            $stores = $object->getStores();
            $stores[] = 0;
            $object->setStores($stores);
        } elseif ($object->hasData('stores')) {
            $object->setStores(array($object->getStores(), 0));
        }
        return $this;
    }

    /**
     * Perform actions after object save
     *
     * @param Mage_Core_Model_Abstract|Mage_Review_Model_Review $object
     * @return $this
     * @throws Zend_Db_Adapter_Exception
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $adapter = $this->_getWriteAdapter();
        /**
         * save detail
         */
        $detail = array(
            'title'     => $object->getTitle(),
            'detail'    => $object->getDetail(),
            'nickname'  => $object->getNickname(),
        );
        $select = $adapter->select()
            ->from($this->_reviewDetailTable, 'detail_id')
            ->where('review_id = :review_id');
        $detailId = $adapter->fetchOne($select, array(':review_id' => $object->getId()));

        if ($detailId) {
            $condition = array("detail_id = ?" => $detailId);
            $adapter->update($this->_reviewDetailTable, $detail, $condition);
        } else {
            $detail['store_id']   = $object->getStoreId();
            $detail['customer_id']= $object->getCustomerId();
            $detail['review_id']  = $object->getId();
            $adapter->insert($this->_reviewDetailTable, $detail);
        }


        /**
         * save stores
         */
        $stores = $object->getStores();
        if (!empty($stores)) {
            $condition = array('review_id = ?' => $object->getId());
            $adapter->delete($this->_reviewStoreTable, $condition);

            $insertedStoreIds = array();
            foreach ($stores as $storeId) {
                if (in_array($storeId, $insertedStoreIds)) {
                    continue;
                }

                $insertedStoreIds[] = $storeId;
                $storeInsert = array(
                    'store_id' => $storeId,
                    'review_id'=> $object->getId()
                );
                $adapter->insert($this->_reviewStoreTable, $storeInsert);
            }
        }

        // reaggregate ratings, that depend on this review
        $this->_aggregateRatings(
            $this->_loadVotedRatingIds($object->getId()),
            $object->getEntityPkValue()
        );

        return $this;
    }

    /**
     * Perform actions after object load
     *
     * @param Mage_Core_Model_Abstract|Mage_Review_Model_Review $object
     * @return $this
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->_reviewStoreTable, array('store_id'))
            ->where('review_id = :review_id');
        $stores = $adapter->fetchCol($select, array(':review_id' => $object->getId()));
        if (empty($stores) && Mage::app()->isSingleStoreMode()) {
            $object->setStores(array(Mage::app()->getStore(true)->getId()));
        } else {
            $object->setStores($stores);
        }
        return $this;
    }

    /**
     * Action before delete
     *
     * @param Mage_Core_Model_Abstract|Mage_Review_Model_Review $object
     * @return $this
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        // prepare rating ids, that depend on review
        $this->_deleteCache = array(
            'ratingIds'     => $this->_loadVotedRatingIds($object->getId()),
            'entityPkValue' => $object->getEntityPkValue()
        );
        return $this;
    }

    /**
     * Perform actions after object delete
     *
     * @param Mage_Core_Model_Abstract $object
     * @return $this
     */
    public function afterDeleteCommit(Mage_Core_Model_Abstract $object)
    {
        $this->aggregate($object);

        // reaggregate ratings, that depended on this review
        $this->_aggregateRatings(
            $this->_deleteCache['ratingIds'],
            $this->_deleteCache['entityPkValue']
        );
        $this->_deleteCache = array();

        return $this;
    }

    /**
     * Retrieves total reviews
     *
     * @param int $entityPkValue
     * @param bool $approvedOnly
     * @param int $storeId
     * @return int
     */
    public function getTotalReviews($entityPkValue, $approvedOnly = false, $storeId = 0)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from(
                $this->_reviewTable,
                array(
                    'review_count' => new Zend_Db_Expr('COUNT(*)')
                )
            )
            ->where("{$this->_reviewTable}.entity_pk_value = :pk_value");
        $bind = array(':pk_value' => $entityPkValue);
        if ($storeId > 0) {
            $select->join(
                array('store'=>$this->_reviewStoreTable),
                $this->_reviewTable.'.review_id=store.review_id AND store.store_id = :store_id',
                array()
            );
            $bind[':store_id'] = (int)$storeId;
        }
        if ($approvedOnly) {
            $select->where("{$this->_reviewTable}.status_id = :status_id");
            $bind[':status_id'] = Mage_Review_Model_Review::STATUS_APPROVED;
        }
        return $adapter->fetchOne($select, $bind);
    }

    /**
     * Aggregate
     *
     * @param Mage_Core_Model_Abstract|Mage_Review_Model_Review $object
     */
    public function aggregate($object)
    {
        $readAdapter    = $this->_getReadAdapter();
        $writeAdapter   = $this->_getWriteAdapter();
        if (!$object->getEntityPkValue() && $object->getId()) {
            $object->load($object->getReviewId());
        }

        $ratingModel    = Mage::getModel('rating/rating');
        $ratingSummaries= $ratingModel->getEntitySummary($object->getEntityPkValue(), false);

        foreach ($ratingSummaries as $ratingSummaryObject) {
            if ($ratingSummaryObject->getCount()) {
                $ratingSummary = round($ratingSummaryObject->getSum() / $ratingSummaryObject->getCount());
            } else {
                $ratingSummary = $ratingSummaryObject->getSum();
            }

            $reviewsCount = $this->getTotalReviews(
                $object->getEntityPkValue(),
                true,
                $ratingSummaryObject->getStoreId()
            );
            $select = $readAdapter->select()
                ->from($this->_aggregateTable)
                ->where('entity_pk_value = :pk_value')
                ->where('entity_type = :entity_type')
                ->where('store_id = :store_id');
            $bind = array(
                ':pk_value'    => $object->getEntityPkValue(),
                ':entity_type' => $object->getEntityId(),
                ':store_id'    =>$ratingSummaryObject->getStoreId()
            );
            $oldData = $readAdapter->fetchRow($select, $bind);

            $data = new Varien_Object();

            $data->setReviewsCount($reviewsCount)
                ->setEntityPkValue($object->getEntityPkValue())
                ->setEntityType($object->getEntityId())
                ->setRatingSummary(($ratingSummary > 0) ? $ratingSummary : 0)
                ->setStoreId($ratingSummaryObject->getStoreId());

            $writeAdapter->beginTransaction();
            try {
                if ($oldData['primary_id'] > 0) {
                    $condition = array("{$this->_aggregateTable}.primary_id = ?" => $oldData['primary_id']);
                    $writeAdapter->update($this->_aggregateTable, $data->getData(), $condition);
                } else {
                    $writeAdapter->insert($this->_aggregateTable, $data->getData());
                }
                $writeAdapter->commit();
            } catch (Exception $e) {
                $writeAdapter->rollBack();
            }
        }
    }

    /**
     * Get rating IDs from review votes
     *
     * @param int $reviewId
     * @return array
     */
    protected function _loadVotedRatingIds($reviewId)
    {
        $adapter = $this->_getReadAdapter();
        if (empty($reviewId)) {
            return array();
        }
        $select = $adapter->select()
            ->from(array('v' => $this->getTable('rating/rating_option_vote')), 'r.rating_id')
            ->joinInner(array('r' => $this->getTable('rating/rating')), 'v.rating_id=r.rating_id')
            ->where('v.review_id = :revire_id');
        return $adapter->fetchCol($select, array(':revire_id' => $reviewId));
    }

    /**
     * Aggregate this review's ratings.
     * Useful, when changing the review.
     *
     * @param array $ratingIds
     * @param int $entityPkValue
     * @return $this
     */
    protected function _aggregateRatings($ratingIds, $entityPkValue)
    {
        if ($ratingIds && !is_array($ratingIds)) {
            $ratingIds = array((int)$ratingIds);
        }
        if ($ratingIds && $entityPkValue
            && ($resource = Mage::getResourceSingleton('rating/rating_option'))
            ) {
            foreach ($ratingIds as $ratingId) {
                $resource->aggregateEntityByRatingId(
                    $ratingId,
                    $entityPkValue
                );
            }
        }
        return $this;
    }

    /**
     * Reaggregate this review's ratings.
     *
     * @param int $reviewId
     * @param int $entityPkValue
     */
    public function reAggregateReview($reviewId, $entityPkValue)
    {
        $this->_aggregateRatings($this->_loadVotedRatingIds($reviewId), $entityPkValue);
    }

    /**
     * Get review entity type id by code
     *
     * @param string $entityCode
     * @return int|bool
     */
    public function getEntityIdByCode($entityCode)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->_reviewEntityTable, array('entity_id'))
            ->where('entity_code = :entity_code');
        return $adapter->fetchOne($select, array(':entity_code' => $entityCode));
    }

    /**
     * Delete reviews by product id.
     * Better to call this method in transaction, because operation performed on two separated tables
     *
     * @param int $productId
     * @return $this
     */
    public function deleteReviewsByProductId($productId)
    {
        $this->_getWriteAdapter()->delete($this->_reviewTable, array(
            'entity_pk_value=?' => $productId,
            'entity_id=?' => $this->getEntityIdByCode(Mage_Review_Model_Review::ENTITY_PRODUCT_CODE)
        ));
        $this->_getWriteAdapter()->delete($this->getTable('review/review_aggregate'), array(
            'entity_pk_value=?' => $productId,
            'entity_type=?' => $this->getEntityIdByCode(Mage_Review_Model_Review::ENTITY_PRODUCT_CODE)
        ));
        return $this;
    }
}
