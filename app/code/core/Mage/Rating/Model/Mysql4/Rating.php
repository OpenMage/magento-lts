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
 * @package    Mage_Rating
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Rating model
 *
 * @category   Mage
 * @package    Mage_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Rating_Model_Mysql4_Rating extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('rating/rating', 'rating_id');
    }

    /**
     * Initialize unique fields
     *
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(array(
            'field' => 'rating_code',
            'title' => /* Mage::helper('rating')->__('Rating with the same title')*/ ''
        ));
        return $this;
    }

    protected function _getLoadSelect($field, $value, $object)
    {
        $read = $this->_getReadAdapter();

        $select = $read->select()
            ->from(array('main'=>$this->getMainTable()), array('rating_id', 'entity_id', 'position'))
            ->joinLeft(array('title'=>$this->getTable('rating_title')),
                          'main.rating_id=title.rating_id AND title.store_id = '. (int) Mage::getSingleton('core/store')->getId(),
                          array('IF(title.value IS NULL, main.rating_code, title.value) AS rating_code'))
            ->where('main.'.$field.'=?', $value);
        return $select;
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object) {
        parent::_afterLoad($object);
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('rating_title'))
            ->where('rating_id=?', $object->getId());

        $data = $this->_getReadAdapter()->fetchAll($select);
        $storeCodes = array();
        foreach ($data as $row) {
            $storeCodes[$row['store_id']] = $row['value'];
        }
        if(sizeof($storeCodes)>0) {
            $object->setRatingCodes($storeCodes);
        }

        $storesSelect = $this->_getReadAdapter()->select()
            ->from($this->getTable('rating_store'))
            ->where('rating_id=?', $object->getId());

        $stores = $this->_getReadAdapter()->fetchAll($storesSelect);

        $putStores = array();
        foreach ($stores as $store) {
            $putStores[] = $store['store_id'];
        }

        $object->setStores($putStores);

        return $this;
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object) {
        parent::_afterSave($object);

        if($object->hasRatingCodes()) {
            try {
                $this->_getWriteAdapter()->beginTransaction();
                $condition = $this->_getWriteAdapter()->quoteInto('rating_id = ?', $object->getId());
                $this->_getWriteAdapter()->delete($this->getTable('rating_title'), $condition);
                if ($ratingCodes = $object->getRatingCodes()) {
                    foreach ($ratingCodes as $storeId=>$value) {
                        if(trim($value)=='') {
                            continue;
                        }
                        $data = new Varien_Object();
                        $data->setRatingId($object->getId())
                            ->setStoreId($storeId)
                            ->setValue($value);
                        $this->_getWriteAdapter()->insert($this->getTable('rating_title'), $data->getData());
                    }
                }
                $this->_getWriteAdapter()->commit();
            }
            catch (Exception $e) {
                $this->_getWriteAdapter()->rollBack();
            }
        }

        if($object->hasStores()) {
            try {
                $condition = $this->_getWriteAdapter()->quoteInto('rating_id = ?', $object->getId());
                $this->_getWriteAdapter()->delete($this->getTable('rating_store'), $condition);
                foreach ($object->getStores() as $storeId) {
                    $storeInsert = new Varien_Object();
                    $storeInsert->setStoreId($storeId);
                    $storeInsert->setRatingId($object->getId());
                    $this->_getWriteAdapter()->insert($this->getTable('rating_store'), $storeInsert->getData());
                }
            }
            catch (Exception  $e) {
                $this->_getWriteAdapter()->rollBack();
            }
        }

        return $this;
    }

    /**
     * Perform actions after object delete
     * Prepare rating data for reaggregate all data for reviews
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Rating_Model_Mysql4_Rating
     */
    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
        parent::_afterDelete($object);
        $data = $this->_getEntitySummaryData($object);
        $summary = array();
        foreach ($data as $row) {
            $clone = clone $object;
            $clone->addData( $row );
            $summary[$clone->getStoreId()][$clone->getEntityPkValue()] = $clone;
        }
        Mage::getResourceModel('review/review_summary')->reAggregate($summary);
        return $this;
    }

    /**
     * Return array of rating summary
     *
     * @param Mage_Rating_Model_Rating $object
     * @param boolean $onlyForCurrentStore
     * @return array
     */
    public function getEntitySummary($object, $onlyForCurrentStore = true)
    {
        $data = $this->_getEntitySummaryData($object);

        if($onlyForCurrentStore) {
            foreach ($data as $row) {
                if($row['store_id']==Mage::app()->getStore()->getId()) {
                    $object->addData( $row );
                }
            }
            return $object;
        }

        $result = array();

        //$stores = Mage::app()->getStore()->getResourceCollection()->load();
        $stores = Mage::getModel('core/store')->getResourceCollection()->load();

        foreach ($data as $row) {
            $clone = clone $object;
            $clone->addData( $row );
            $result[$clone->getStoreId()] = $clone;
        }

        $usedStoresId = array_keys($result);

        foreach ($stores as $store) {
               if (!in_array($store->getId(), $usedStoresId)) {
                    $clone = clone $object;
                    $clone->setCount(0);
                    $clone->setSum(0);
                    $clone->setStoreId($store->getId());
                    $result[$store->getId()] = $clone;
               }
        }

        return array_values($result);
    }

    /**
     * Return data of rating summary
     *
     * @param Mage_Rating_Model_Rating $object
     * @return array
     */
    protected function _getEntitySummaryData($object)
    {
        $read = $this->_getReadAdapter();
        $sql = "SELECT
                    {$this->getTable('rating_vote')}.entity_pk_value as entity_pk_value,
                    SUM({$this->getTable('rating_vote')}.percent) as sum,
                    COUNT(*) as count,
                    {$this->getTable('review/review_store')}.store_id
                FROM
                    {$this->getTable('rating_vote')}
                INNER JOIN
                    {$this->getTable('review/review')}
                    ON {$this->getTable('rating_vote')}.review_id={$this->getTable('review/review')}.review_id
                LEFT JOIN
                    {$this->getTable('review/review_store')}
                    ON {$this->getTable('rating_vote')}.review_id={$this->getTable('review/review_store')}.review_id
                INNER JOIN
                    {$this->getTable('rating/rating_store')} AS rst
                    ON rst.rating_id = {$this->getTable('rating_vote')}.rating_id AND rst.store_id = {$this->getTable('review/review_store')}.store_id
                INNER JOIN
                    {$this->getTable('review/review_status')} AS review_status
                    ON {$this->getTable('review/review')}.status_id = review_status.status_id
                WHERE ";
        if ($object->getEntityPkValue()) {
            $sql .= "{$read->quoteInto($this->getTable('rating_vote').'.entity_pk_value=?', $object->getEntityPkValue())} AND ";
        }
        $sql .= "review_status.status_code = 'approved'
                GROUP BY
                    {$this->getTable('rating_vote')}.entity_pk_value, {$this->getTable('review/review_store')}.store_id";

        return $read->fetchAll($sql);
    }

    public function getReviewSummary($object, $onlyForCurrentStore = true)
    {
        $read = $this->_getReadAdapter();
        $sql = "SELECT
                    SUM({$this->getTable('rating_vote')}.percent) as sum,
                    COUNT(*) as count,
                    {$this->getTable('review/review_store')}.store_id
                FROM
                    {$this->getTable('rating_vote')}
                LEFT JOIN
                    {$this->getTable('review/review_store')}
                    ON {$this->getTable('rating_vote')}.review_id={$this->getTable('review/review_store')}.review_id
                INNER JOIN
                    {$this->getTable('rating/rating_store')} AS rst
                    ON rst.rating_id = {$this->getTable('rating_vote')}.rating_id AND rst.store_id = {$this->getTable('review/review_store')}.store_id
                WHERE
                    {$read->quoteInto($this->getTable('rating_vote').'.review_id=?', $object->getReviewId())}
                GROUP BY
                    {$this->getTable('rating_vote')}.review_id, {$this->getTable('review/review_store')}.store_id";

        $data = $read->fetchAll($sql);

        if($onlyForCurrentStore) {
            foreach ($data as $row) {
                if($row['store_id']==Mage::app()->getStore()->getId()) {
                    $object->addData( $row );
                }
            }
            return $object;
        }

        $result = array();

        $stores = Mage::app()->getStore()->getResourceCollection()->load();

        foreach ($data as $row) {
            $clone = clone $object;
            $clone->addData( $row );
            $result[$clone->getStoreId()] = $clone;
        }

        $usedStoresId = array_keys($result);

        foreach ($stores as $store) {
               if (!in_array($store->getId(), $usedStoresId)) {
                   $clone = clone $object;
                $clone->setCount(0);
                $clone->setSum(0);
                $clone->setStoreId($store->getId());
                $result[$store->getId()] = $clone;

               }
        }

        return array_values($result);
    }
}