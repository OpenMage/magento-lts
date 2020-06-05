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
 * @package     Mage_Rating
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Rating option resource model
 *
 * @category    Mage
 * @package     Mage_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rating_Model_Resource_Rating_Option extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Review table
     *
     * @var string
     */
    protected $_reviewTable;

    /**
     * Rating option table
     *
     * @var string
     */
    protected $_ratingOptionTable;

    /**
     * Rating vote table
     *
     * @var string
     */
    protected $_ratingVoteTable;

    /**
     * Aggregate table
     *
     * @var string
     */
    protected $_aggregateTable;

    /**
     * Review store table
     *
     * @var string
     */
    protected $_reviewStoreTable;

    /**
     * Rating store table
     *
     * @var string
     */
    protected $_ratingStoreTable;

    /**
    * Option data
    *
    * @var array
    */
    protected $_optionData;

    /**
    * Option id
    *
    * @var int
    */
    protected $_optionId;

    /**
     * Resource initialization. Define other tables name
     *
     */
    protected function _construct()
    {
        $this->_init('rating/rating_option', 'option_id');

        $this->_reviewTable         = $this->getTable('review/review');
        $this->_ratingOptionTable   = $this->getTable('rating/rating_option');
        $this->_ratingVoteTable     = $this->getTable('rating/rating_option_vote');
        $this->_aggregateTable      = $this->getTable('rating/rating_vote_aggregated');
        $this->_reviewStoreTable    = $this->getTable('review/review_store');
        $this->_ratingStoreTable    = $this->getTable('rating/rating_store');
    }

    /**
     * Add vote
     *
     * @param Mage_Rating_Model_Rating_Option $option
     * @return $this
     */
    public function addVote($option)
    {
        $adapter = $this->_getWriteAdapter();
        $optionData = $this->loadDataById($option->getId());
        $data = array(
            'option_id'     => $option->getId(),
            'review_id'     => $option->getReviewId(),
            'percent'       => (($optionData['value'] / 5) * 100),
            'value'         => $optionData['value']
        );

        if (!$option->getDoUpdate()) {
            $data['remote_ip']       = Mage::helper('core/http')->getRemoteAddr();
            $data['remote_ip_long']  = Mage::helper('core/http')->getRemoteAddr(true);
            $data['customer_id']     = Mage::getSingleton('customer/session')->getCustomerId();
            $data['entity_pk_value'] = $option->getEntityPkValue();
            $data['rating_id']       = $option->getRatingId();
        }

        $adapter->beginTransaction();
        try {
            if ($option->getDoUpdate()) {
                $condition = array(
                    'vote_id = ?'   => $option->getVoteId(),
                    'review_id = ?' => $option->getReviewId()
                );
                $adapter->update($this->_ratingVoteTable, $data, $condition);
                $this->aggregate($option);
            } else {
                $adapter->insert($this->_ratingVoteTable, $data);
                $option->setVoteId($adapter->lastInsertId($this->_ratingVoteTable));
                $this->aggregate($option);
            }
            $adapter->commit();
        } catch (Exception $e) {
            $adapter->rollBack();
            throw new Exception($e->getMessage());
        }
        return $this;
    }

    /**
     * Aggregate options
     *
     * @param Mage_Rating_Model_Rating_Option $option
     */
    public function aggregate($option)
    {
        $vote = Mage::getModel('rating/rating_option_vote')->load($option->getVoteId());
        $this->aggregateEntityByRatingId($vote->getRatingId(), $vote->getEntityPkValue());
    }

    /**
     * Aggregate entity by rating id
     *
     * @param int $ratingId
     * @param int $entityPkValue
     */
    public function aggregateEntityByRatingId($ratingId, $entityPkValue)
    {
        $readAdapter  = $this->_getReadAdapter();
        $writeAdapter = $this->_getWriteAdapter();

        $select = $readAdapter->select()
            ->from($this->_aggregateTable, array('store_id', 'primary_id'))
            ->where('rating_id = :rating_id')
            ->where('entity_pk_value = :pk_value');
        $bind = array(':rating_id' => $ratingId, ':pk_value' => $entityPkValue);
        $oldData = $readAdapter->fetchPairs($select, $bind);

        $appVoteCountCond    = $readAdapter->getCheckSql('review.status_id=1', 'vote.vote_id', 'NULL');
        $appVoteValueSumCond = $readAdapter->getCheckSql('review.status_id=1', 'vote.value', '0');

        $select = $readAdapter->select()
            ->from(array('vote'=>$this->_ratingVoteTable),
                array(
                    'vote_count'         => new Zend_Db_Expr('COUNT(vote.vote_id)'),
                    'vote_value_sum'     => new Zend_Db_Expr('SUM(vote.value)'),
                    'app_vote_count'     => new Zend_Db_Expr("COUNT({$appVoteCountCond})"),
                    'app_vote_value_sum' => new Zend_Db_Expr("SUM({$appVoteValueSumCond})") ))
            ->join(array('review'   =>$this->_reviewTable),
                'vote.review_id=review.review_id',
                array())
            ->joinLeft(array('store'=>$this->_reviewStoreTable),
                'vote.review_id=store.review_id', 'store_id')
            ->join(array('rstore'   =>$this->_ratingStoreTable),
                'vote.rating_id=rstore.rating_id AND rstore.store_id=store.store_id',
                array())
            ->where('vote.rating_id = :rating_id')
            ->where('vote.entity_pk_value = :pk_value')
            ->group(array(
                'vote.rating_id',
                'vote.entity_pk_value',
                'store.store_id'
            ));

        $perStoreInfo = $readAdapter->fetchAll($select, $bind);

        $usedStores = array();
        foreach ($perStoreInfo as $row) {
            $saveData = array(
                'rating_id'        => $ratingId,
                'entity_pk_value'  => $entityPkValue,
                'vote_count'       => $row['vote_count'],
                'vote_value_sum'   => $row['vote_value_sum'],
                'percent'          => (($row['vote_value_sum']/$row['vote_count'])/5) * 100,
                'percent_approved' => ($row['app_vote_count'] ? ((($row['app_vote_value_sum']/$row['app_vote_count'])/5) * 100) : 0),
                'store_id'         => $row['store_id'],
            );

            if (isset($oldData[$row['store_id']])) {
                $condition = array('primary_id = ?' => $oldData[$row['store_id']]);
                $writeAdapter->update($this->_aggregateTable, $saveData, $condition);
            } else {
                $writeAdapter->insert($this->_aggregateTable, $saveData);
            }

            $usedStores[] = $row['store_id'];
        }

        $toDelete = array_diff(array_keys($oldData), $usedStores);

        foreach ($toDelete as $storeId) {
            $condition = array('primary_id = ?' => $oldData[$storeId]);
            $writeAdapter->delete($this->_aggregateTable, $condition);
        }
    }

    /**
     * Load object data by optionId
     * Method renamed from 'load'.
     *
     * @param int $optionId
     * @return array
     */
    public function loadDataById($optionId)
    {
        if (!$this->_optionData || $this->_optionId != $optionId) {
            $adapter = $this->_getReadAdapter();
            $select = $adapter->select();
            $select->from($this->_ratingOptionTable)
                ->where('option_id = :option_id');

            $data = $adapter->fetchRow($select, array(':option_id' => $optionId));

            $this->_optionData = $data;
            $this->_optionId = $optionId;
            return $data;
        }

        return $this->_optionData;
    }

}
