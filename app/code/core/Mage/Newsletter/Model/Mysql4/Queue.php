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
 * @package    Mage_Newsletter
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Newsletter queue saver
 * 
 * @category   Mage
 * @package    Mage_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */ 
class Mage_Newsletter_Model_Mysql4_Queue extends Mage_Core_Model_Mysql4_Abstract
{
    
    protected function _construct() 
    {
        $this->_init('newsletter/queue', 'queue_id');
    }
       
    /**
     * Add subscribers to queue
     *
     * @param Mage_Newsletter_Model_Queue $queue
     * @param array $subscriberIds
     */
    public function addSubscribersToQueue(Mage_Newsletter_Model_Queue $queue, array $subscriberIds) 
    {
        if (count($subscriberIds)==0) {
            Mage::throwException(Mage::helper('newsletter')->__('No subscribers selected'));
        }
        
        if (!$queue->getId() && $queue->getQueueStatus()!=Mage_Newsletter_Model_Queue::STATUS_NEVER) {
            Mage::throwException(Mage::helper('newsletter')->__('Invalid queue selected'));
        }
        
        $select = $this->_getWriteAdapter()->select();
        $select->from($this->getTable('queue_link'),'subscriber_id')
            ->where('queue_id = ?', $queue->getId())
            ->where('subscriber_id in (?)', $subscriberIds);
        
        $usedIds = $this->_getWriteAdapter()->fetchCol($select);
        $this->_getWriteAdapter()->beginTransaction();
        try {
            foreach($subscriberIds as $subscriberId) {
                if(in_array($subscriberId, $usedIds)) {
                    continue;
                }
                $data = array();
                $data['queue_id'] = $queue->getId();
                $data['subscriber_id'] = $subscriberId;
                $this->_getWriteAdapter()->insert($this->getTable('queue_link'), $data);
            }
            $this->_getWriteAdapter()->commit();
        } 
        catch (Exception $e) {
            $this->_getWriteAdapter()->rollBack();
        }
        
    }
    
    public function removeSubscribersFromQueue(Mage_Newsletter_Model_Queue $queue)
    {
        try {
            $this->_getWriteAdapter()->delete(
                $this->getTable('queue_link'), 
                array(
                    $this->_getWriteAdapter()->quoteInto('queue_id = ?', $queue->getId()),
                    'letter_sent_at IS NULL'
                )
            );
            
            $this->_getWriteAdapter()->commit();
        } 
        catch (Exception $e) {
            $this->_getWriteAdapter()->rollBack();
        }
        
    }
    
    public function setStores(Mage_Newsletter_Model_Queue $queue) 
    {
        $this->_getWriteAdapter()
            ->delete(
                $this->getTable('queue_store_link'), 
                $this->_getWriteAdapter()->quoteInto('queue_id = ?', $queue->getId())
            );
        
        if (!is_array($queue->getStores())) { 
            $stores = array(); 
        } else {
            $stores = $queue->getStores();
        }
        
        foreach ($stores as $storeId) {
            $data = array();
            $data['store_id'] = $storeId;
            $data['queue_id'] = $queue->getId();
            $this->_getWriteAdapter()->insert($this->getTable('queue_store_link'), $data);
        }
         
        $this->removeSubscribersFromQueue($queue);

        if(count($stores)==0) {
            return $this;
        }
        $subscribers = Mage::getResourceSingleton('newsletter/subscriber_collection')
            ->addFieldToFilter('store_id', array('in'=>$stores))
            ->useOnlySubscribed()
            ->load();
         
        $subscriberIds = array();
        
        foreach ($subscribers as $subscriber) {
            $subscriberIds[] = $subscriber->getId();
        }
        
        if (count($subscriberIds) > 0) {
            $this->addSubscribersToQueue($queue, $subscriberIds);
        }
        
        return $this;
    }
    
    public function getStores(Mage_Newsletter_Model_Queue $queue) 
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('queue_store_link'), 'store_id')
            ->where('queue_id = ?', $queue->getId());
        
        if(!($result = $this->_getReadAdapter()->fetchCol($select))) {
            $result = array();
        }
        
        return $result;
    }
    
    /**
     * Saving template after saving queue action
     *
     * @param Mage_Core_Model_Abstract $queue
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    protected function _afterSave(Mage_Core_Model_Abstract $queue) 
    {
        if($queue->getSaveTemplateFlag()) {
            $queue->getTemplate()->save();
        }
        
        if($queue->getSaveStoresFlag()) {
            $this->setStores($queue);           
        }
        
        return $this;
    }
    
}
