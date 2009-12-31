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
 * @package     Mage_Rating
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Rating option resource model
 *
 * @category   Mage
 * @package    Mage_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rating_Model_Mysql4_Rating_Option
{
    protected $_reviewTable;
    protected $_ratingOptionTable;
    protected $_ratingVoteTable;
    protected $_aggregateTable;
    protected $_reviewStoreTable;
    protected $_ratingStoreTable;

    protected $_read;
    protected $_write;

    protected $_optionData;
    protected $_optionId;

    public function __construct()
    {
        $this->_reviewTable     = Mage::getSingleton('core/resource')->getTableName('review/review');
        $this->_ratingOptionTable  = Mage::getSingleton('core/resource')->getTableName('rating/rating_option');
        $this->_ratingVoteTable    = Mage::getSingleton('core/resource')->getTableName('rating/rating_vote');
        $this->_aggregateTable    = Mage::getSingleton('core/resource')->getTableName('rating/rating_vote_aggregated');
        $this->_reviewStoreTable  = Mage::getSingleton('core/resource')->getTableName('review/review_store');
        $this->_ratingStoreTable  = Mage::getSingleton('core/resource')->getTableName('rating/rating_store');

        $this->_read  = Mage::getSingleton('core/resource')->getConnection('rating_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('rating_write');
    }

    public function save($object)
    {
        if( $object->getId() ) {
            $condition = $this->_write->quoteInto('option_id = ?', $object->getId());
            $object->unsetData('option_id');
            $this->_write->update($this->_ratingOptionTable, $object->getData(), $condition);
        } else {
            $this->_write->insert($this->_ratingOptionTable, $object->getData());
        }
        return $object;
    }

    public function delete($object)
    {
        $condition = $this->_write->quoteInto('option_id = ?', $object->getId());
        $this->_write->delete($this->_ratingOptionTable, $condition);
    }

    public function addVote($option)
    {
        $action = Mage::app()->getFrontController()->getAction();

        if ($action instanceof Mage_Core_Controller_Front_Action || $action instanceof Mage_Adminhtml_Controller_Action) {
            $optionData = $this->load($option->getId());
            $data = array(
                'option_id'     => $option->getId(),
                'review_id'     => $option->getReviewId(),
                'percent'       => (($optionData['value'] / 5) * 100),
                'value'          => $optionData['value']
            );

            if( !$option->getDoUpdate() ) {
                $data['remote_ip'] = Mage::helper('core/http')->getRemoteAddr();
                $data['remote_ip_long'] = Mage::helper('core/http')->getRemoteAddr(true);
                $data['customer_id'] = Mage::getSingleton('customer/session')->getCustomerId();
                $data['entity_pk_value'] = $option->getEntityPkValue();
                $data['rating_id'] = $option->getRatingId();
            }

            $this->_write->beginTransaction();
            try {
                if( $option->getDoUpdate() ) {
                    $condition = "vote_id = '{$option->getVoteId()}' AND review_id = '{$option->getReviewId()}'";
                    $this->_write->update($this->_ratingVoteTable, $data, $condition);
                    $this->aggregate($option);
                } else {
                    $this->_write->insert($this->_ratingVoteTable, $data);
                    $option->setVoteId($this->_write->lastInsertId());
                    $this->aggregate($option);
                }
                $this->_write->commit();
            }
            catch (Exception $e){
                $this->_write->rollback();
                throw new Exception($e->getMessage());
            }
        }
        return $this;
    }

    public function aggregate($option)
    {
        $optionData = $this->load($option->getId());
        $vote = Mage::getModel('rating/rating_option_vote')->load($option->getVoteId());
        $this->aggregateEntityByRatingId($vote->getRatingId(), $vote->getEntityPkValue());
    }

    public function aggregateEntityByRatingId($ratingId, $entityPkValue)
    {
        $select = $this->_read->select()
            ->from($this->_aggregateTable)
            ->where('rating_id = ?', $ratingId)
            ->where('entity_pk_value = ?', $entityPkValue);

        $data = $this->_read->fetchAll($select);

        $oldData = array();
        foreach($data as $row) {
            $oldData[$row['store_id']] = $row['primary_id'];
        }

        $select = $this->_read->select()
            ->from(array('vote'=>$this->_ratingVoteTable),
                array('COUNT(vote.vote_id) AS vote_count',
                    'SUM(vote.value) AS vote_value_sum',
                    'COUNT(CASE WHEN review.status_id=1 THEN vote.vote_id ELSE NULL END) AS app_vote_count',
                    'SUM(CASE WHEN review.status_id=1 THEN vote.value ELSE 0 END) AS app_vote_value_sum',
            ))
            ->join(array('review'=>$this->_reviewTable), 'vote.review_id=review.review_id', array())
            ->joinLeft(array('store'=>$this->_reviewStoreTable), 'vote.review_id=store.review_id', 'store_id')
            ->join(array('rstore'=>$this->_ratingStoreTable), 'vote.rating_id=rstore.rating_id AND rstore.store_id=store.store_id', array())
            ->where('vote.rating_id = ?', $ratingId)
            ->where('vote.entity_pk_value = ?', $entityPkValue)
            ->group('vote.rating_id')
            ->group('vote.entity_pk_value')
            ->group('store.store_id');

         $perStoreInfo = $this->_read->fetchAll($select);

         $usedStores = array();
         foreach($perStoreInfo as $row) {
             $saveData = new Varien_Object(array(
                'rating_id'        => $ratingId,
                'entity_pk_value'  => $entityPkValue,
                'vote_count'       => $row['vote_count'],
                'vote_value_sum'   => $row['vote_value_sum'],
                'percent'          => (($row['vote_value_sum']/$row['vote_count'])/5) * 100,
                'percent_approved' => ($row['app_vote_count'] ? ((($row['app_vote_value_sum']/$row['app_vote_count'])/5) * 100) : 0),
                'store_id'         => $row['store_id'],
             ));

             if(isset($oldData[$row['store_id']])) {
                 $condition = $this->_write->quoteInto("primary_id = ?", $oldData[$row['store_id']]);
                 $this->_write->update($this->_aggregateTable, $saveData->getData(), $condition);
             } else {
                 $this->_write->insert($this->_aggregateTable, $saveData->getData());
             }

             $usedStores[] = $row['store_id'];
         }

         $toDelete = array_diff(array_keys($oldData), $usedStores);

         foreach ($toDelete as $storeId) {
             $condition = $this->_write->quoteInto("primary_id = ?", $oldData[$storeId]);
             $this->_write->delete($this->_aggregateTable, $condition);
         }
    }

    public function load($optionId)
    {
        if( !$this->_optionData || $this->_optionId != $optionId ) {
            $select = $this->_read->select();
            $select->from($this->_ratingOptionTable)
                ->where('option_id = ?', $optionId);

            $data = $this->_read->fetchRow($select);

            $this->_optionData = $data;
            $this->_optionId = $optionId;
            return $data;
        }

        return $this->_optionData;
    }
}
