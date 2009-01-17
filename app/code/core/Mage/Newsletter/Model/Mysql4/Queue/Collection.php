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
 * @package    Mage_Newsletter
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Newsletter queue collection.
 *
 * @category   Mage
 * @package    Mage_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Newsletter_Model_Mysql4_Queue_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	protected $_addSubscribersFlag = false;

	/**
	 * Initializes collection
	 */
    protected function _construct()
    {
        $this->_init('newsletter/queue');
    }


    /**
     * Joines templates information
     *
     * @return Mage_Newsletter_Model_Mysql4_Queue_Collection
     */
    public function addTemplateInfo() {
        $this->getSelect()->joinLeft(array('template'=>$this->getTable('template')),
            'template.template_id=main_table.template_id',
            array('template_subject','template_sender_name','template_sender_email'));
   	    $this->_joinedTables['template'] = true;
   	    return $this;
    }

    protected  function _addSubscriberInfoToSelect()
    {
        $this->_addSubscribersFlag = true;
    	$this->getSize(); // Executing of count query!
    	$this->getSelect()
    		->joinLeft(array('link_total'=>$this->getTable('queue_link')),
    								 'main_table.queue_id=link_total.queue_id',
    								 array(
    								 	new Zend_Db_Expr('COUNT(DISTINCT link_total.queue_link_id) AS subscribers_total')
    								 ))
 			->joinLeft(array('link_sent'=>$this->getTable('queue_link')),
    								 'main_table.queue_id=link_sent.queue_id and link_sent.letter_sent_at IS NOT NULL',
    								 array(
    								 	new Zend_Db_Expr('COUNT(DISTINCT link_sent.queue_link_id) AS subscribers_sent')
    								 ))
    		->group('main_table.queue_id');
        return $this;
    }

    public function load($printQuery=false, $logQuery=false) {
    	if($this->_addSubscribersFlag && !$this->isLoaded()) {
    		$this->_addSubscriberInfoToSelect();
    	}

    	return parent::load($printQuery, $logQuery);
    }

    /**
     * Joines subscribers information
     *
     * @return Mage_Newsletter_Model_Mysql4_Queue_Collection
     */
    public function addSubscribersInfo()
    {
    	$this->_addSubscribersFlag = true;
    	return $this;
    }

    public function addFieldToFilter($field, $condition=null)
    {
    	if(in_array($field, array('subscribers_total', 'subscribers_sent'))) {
    		$this->addFieldToFilter('main_table.queue_id', array('in'=>$this->_getIdsFromLink($field, $condition)));
    		return $this;
    	} else {
    		return parent::addFieldToFilter($field, $condition);
    	}
    }

    protected function _getIdsFromLink($field, $condition) {
    	$select = $this->getConnection()->select()
    		->from($this->getTable('queue_link'), array('queue_id', 'COUNT(queue_link_id) as total'))
    		->group('queue_id')
    		->having($this->_getConditionSql('total', $condition));

    	if($field == 'subscribers_sent') {
    		$select->where('letter_sent_at IS NOT NULL');
    	}

    	$idList = $this->getConnection()->fetchCol($select);

    	if(count($idList)) {
    		return $idList;
    	}

    	return array(0);
    }

    /**
     * Set filter for queue by subscriber.
     *
     * @param 	int		$subscriberId
     * @return 	Mage_Newsletter_Model_Mysql4_Queue_Collection
     */
    public function addSubscriberFilter($subscriberId)
    {
    	$this->getSelect()
    		->join(array('link'=>$this->getTable('queue_link')),
    								 'main_table.queue_id=link.queue_id',
    								 array('letter_sent_at')
    								 )
 			->where('link.subscriber_id = ?', $subscriberId);

    	return $this;
    }

    /**
     * Add filter by only ready fot sending item
     *
     * @return Mage_Newsletter_Model_Mysql4_Queue_Collection
     */
    public function addOnlyForSendingFilter()
    {
    	$this->getSelect()
    		->where('main_table.queue_status in (?)', array(Mage_Newsletter_Model_Queue::STATUS_SENDING,
    														Mage_Newsletter_Model_Queue::STATUS_NEVER))
    		->where('main_table.queue_start_at < ?', Mage::getSingleton('core/date')->gmtdate())
    		->where('main_table.queue_start_at IS NOT NULL');

    	return $this;
    }

    /**
     * Add filter by only not sent items
     *
     * @return Mage_Newsletter_Model_Mysql4_Queue_Collection
     */
    public function addOnlyUnsentFilter()
    {
    	$this->getSelect()
    		->where('main_table.queue_status = ?',	Mage_Newsletter_Model_Queue::STATUS_NEVER);

   		return $this;
    }

    public function toOptionArray()
    {
        return $this->_toOptionArray('queue_id', 'template_subject');
    }

}
