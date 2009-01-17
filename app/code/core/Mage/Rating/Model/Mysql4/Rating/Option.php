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
 * @package    Mage_Rating
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
                $data['remote_ip'] = $action->getRequest()->getServer('REMOTE_ADDR');
                $data['remote_ip_long'] = ip2long($action->getRequest()->getServer('REMOTE_ADDR'));
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
        $select = $this->_read->select();

        $optionData = $this->load($option->getId());

        $vote = Mage::getModel('rating/rating_option_vote')->load($option->getVoteId());

        $select->from($this->_aggregateTable)
            ->where('rating_id = ?', $vote->getRatingId())
            ->where('entity_pk_value = ?', $vote->getEntityPkValue());

        $data = $this->_read->fetchAll($select);

        $oldData = array();
        foreach($data as $row) {
            $oldData[$row['store_id']] = $row['primary_id'];
        }

        $select = $this->_read->select()
            ->from(array('vote'=>$this->_ratingVoteTable),
                      array('COUNT(vote.vote_id) AS vote_count',
                              'SUM(vote.value) AS vote_value_sum'))
            ->join(array('review'=>$this->_reviewTable), 'vote.review_id=review.review_id', array())
            ->joinLeft(array('store'=>$this->_reviewStoreTable), 'vote.review_id=store.review_id', 'store_id')
            ->join(array('rstore'=>$this->_ratingStoreTable), 'vote.rating_id=rstore.rating_id AND rstore.store_id=store.store_id', array())
            ->where('vote.rating_id = ?', $vote->getRatingId())
            ->where('vote.entity_pk_value = ?', $vote->getEntityPkValue())
            ->group('vote.rating_id')
            ->group('vote.entity_pk_value')
            ->group('store.store_id');

         $perStoreInfo = $this->_read->fetchAll($select);

         $usedStores = array();
         foreach($perStoreInfo as $row) {
             $saveData = new Varien_Object();
             $saveData->setRatingId($vote->getRatingId())
                ->setEntityPkValue($vote->getEntityPkValue())
                ->setVoteCount($row['vote_count'])
                ->setVoteValueSum($row['vote_value_sum'])
                ->setPercent( (($row['vote_value_sum']/$row['vote_count'])/5) * 100 )
                ->setStoreId($row['store_id']);

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
        /* Wrong
        if( $oldData['primary_id'] > 0 ) {
            $option->setVoteCount(new Zend_Db_Expr('vote_count + 1'))
                ->setVoteValueSum( new Zend_Db_Expr('vote_value_sum + ' . $optionData['value']) )
                ->setPercent( ((($oldData['vote_value_sum'] / 100) / (($oldData['vote_count'] * 5) / 100)) * 100) )
                ->unsetData('option_id')
                ->unsetData('review_id');

            $condition = $this->_write->quoteInto("{$this->_aggregateTable}.primary_id = ?", $oldData['primary_id']);
            $this->_write->update($this->_aggregateTable, $option->getData(), $condition);
        } else {
            $option->setVoteCount('1')
                ->setVoteValueSum( $optionData['value'] )
                ->setPercent( (($optionData['value'] / 5) * 100) )
                ->unsetData('option_id')
                ->unsetData('review_id');

            $this->_write->insert($this->_aggregateTable, $option->getData());
        }*/
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
