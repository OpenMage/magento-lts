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
 * @package    Mage_Poll
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Poll Mysql4 resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Poll_Model_Mysql4_Poll extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('poll/poll', 'poll_id');
        $this->_uniqueFields = array(
            array(
                'field' => 'poll_title',
                'title' => Mage::helper('poll')->__('Poll with the same question')
            )
        );
    }

    /**
     * Get random identifier not closed poll
     *
     * @param   Mage_Poll_Model_Poll $object
     * @return  int
     */
    public function getRandomId($object)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select();

        if ($object->getExcludeFilter()) {
            $select->where('main_table.poll_id NOT IN(?)', $object->getExcludeFilter());
        }

        $select->from(array('main_table'=>$this->getMainTable()), $this->getIdFieldName())
            ->where('closed = ?', 0)
            ->order(new Zend_Db_Expr('RAND()'))
            ->limit(1);

        if (($storeId = $object->getStoreFilter())) {
            $select->join(
                array('store' => $this->getTable('poll/poll_store')),
                $read->quoteInto('main_table.poll_id=store.poll_id AND store.store_id = ?', $storeId),
                array()
            );
        }

        return $read->fetchOne($select);
    }

    /**
     * Check answer id existing for poll
     *
     * @param   Mage_Poll_Model_Poll $poll
     * @param   int $answerId
     * @return  bool
     */
    public function checkAnswerId($poll, $answerId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('poll_answer'), 'answer_id')
            ->where('poll_id=?', $poll->getId())
            ->where('answer_id=?', $answerId);
        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Get voted poll ids by specified IP-address
     *
     * Will return non-empty only if appropriate option in config is enabled
     * If poll id is not empty, it will look only for records with specified value
     *
     * @param string $ipAddress
     * @param int $pollId
     * @return array
     */
    public function getVotedPollIdsByIp($ipAddress, $pollId = false)
    {
        // check if validation by ip is enabled
        if (!Mage::getModel('poll/poll')->isValidationByIp()) {
            return array();
        }

        // look for ids in database
        $select = $this->_getReadAdapter()->select()
            ->distinct()
            ->from($this->getTable('poll_vote'), 'poll_id')
            ->where('ip_address=?', ip2long($ipAddress));
        if (!empty($pollId)) {
            $select->where('poll_id=?', $pollId);
        }
        $result = $this->_getReadAdapter()->fetchCol($select);
        if (empty($result)) {
            $result = array();
        }
        return $result;
    }

    public function resetVotesCount($object)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select();
        $select->from($this->getTable('poll_answer'), new Zend_Db_Expr("SUM(votes_count)"))
            ->where("poll_id = ?", $object->getPollId());

        $count = $read->fetchOne($select);

        $write = $this->_getWriteAdapter();
        $condition = $write->quoteInto("{$this->getIdFieldName()} = ?", $object->getPollId());
        $write->update($this->getMainTable(), array('votes_count' => $count), $condition);
        return $object;
    }


    public function loadStoreIds(Mage_Poll_Model_Poll $object)
    {
        $pollId   = $object->getId();
        $storeIds = array();
        if ($pollId) {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getTable('poll/poll_store'), 'store_id')
                ->where('poll_id = ?', $pollId);
            $storeIds = $this->_getReadAdapter()->fetchCol($select);
        }
        $object->setStoreIds($storeIds);
    }

    public function _afterSave(Mage_Core_Model_Abstract $object)
    {
        /** stores */
        $deleteWhere = $this->_getWriteAdapter()->quoteInto('poll_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('poll/poll_store'), $deleteWhere);

        foreach ($object->getStoreIds() as $storeId) {
            $pollStoreData = array(
            'poll_id'   => $object->getId(),
            'store_id'  => $storeId
            );
            $this->_getWriteAdapter()->insert($this->getTable('poll/poll_store'), $pollStoreData);
        }

        /** answers */
        foreach ($object->getAnswers() as $answer) {
            $answer->setPollId($object->getId());
            $answer->save();
        }
    }
}