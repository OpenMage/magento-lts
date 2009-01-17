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
 * Newsletter subscriber model for MySQL4
 *
 * @category   Mage
 * @package    Mage_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Newsletter_Model_Mysql4_Subscriber
{
    /**
     * DB read connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_read;

    /**
     * DB write connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_write;

    /**
     * Name of subscriber DB table
     *
     * @var string
     */
    protected $_subscriberTable;

    /**
     * Name of subscriber link DB table
     *
     * @var string
     */
    protected $_subscriberLinkTable;

    /**
     * Name of scope for error messages
     *
     * @var string
     */
    protected $_messagesScope = 'newsletter/session';

    /**
     * Constructor
     *
     * Set read and write connection, get tablename from config
     */
    public function __construct()
    {
        $this->_subscriberTable = Mage::getSingleton('core/resource')->getTableName("newsletter/subscriber");
        $this->_subscriberLinkTable = Mage::getSingleton('core/resource')->getTableName("newsletter/queue_link");
        $this->_read = Mage::getSingleton('core/resource')->getConnection('newsletter_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('newsletter_write');
    }

    /**
     * Load subscriber from DB
     *
     * @param int $subscriberId
     * @return array
     */
    public function load($subscriberId)
    {
        $select = $this->_read->select()
            ->from($this->_subscriberTable)
            ->where('subscriber_id=?',$subscriberId);

        return $this->_read->fetchRow($select);
    }

    /**
     * Set error messages scope
     *
     * @param string $scope
     */
    public function setMessagesScope($scope)
    {
    	$this->_messagesScope = $scope;
    }

    /**
     * Load subscriber from DB by email
     *
     * @param string $subscriberEmail
     * @return array
     */
    public function loadByEmail($subscriberEmail)
    {
        $select = $this->_read->select()
            ->from($this->_subscriberTable)
            ->where('subscriber_email=?',$subscriberEmail);

        $result = $this->_read->fetchRow($select);

        if(!$result) {
            return array();
        }

        return $result;
    }



    /**
     * Load subscriber by customer
     *
     * @param 	Mage_Customer_Model_Customer $customer
     * @return 	array
     */
    public function loadByCustomer(Mage_Customer_Model_Customer $customer)
    {
        $select = $this->_read->select()
            ->from($this->_subscriberTable)
            ->where('customer_id=?',$customer->getId());

        $result = $this->_read->fetchRow($select);

        if ($result) {
            return $result;
        }

        $select = $this->_read->select()
            ->from($this->_subscriberTable)
            ->where('subscriber_email=?',$customer->getEmail());

        $result = $this->_read->fetchRow($select);

        if ($result) {
            return $result;
        }

        return array();
    }

    /**
     * Save subscriber info from it model.
     *
     * @param  Mage_Newsletter_Model_Subscriber $subscriber
     * @return Mage_Newsletter_Model_Subscriber
     */
    public function save(Mage_Newsletter_Model_Subscriber $subscriber)
    {
        $this->_write->beginTransaction();
        try {
            $data = $this->_prepareSave($subscriber);
            if ($subscriber->getId()) {
                $this->_write->update($this->_subscriberTable, $data,
                                      $this->_write->quoteInto('subscriber_id=?',$subscriber->getId()));
            } else {
                $this->_write->insert($this->_subscriberTable, $data);
                $subscriber->setId($this->_write->lastInsertId($this->_subscriberTable));
            }
            $this->_write->commit();
        }
        catch(Exception $e) {
            $this->_write->rollBack();
            Mage::throwException(Mage::helper('newsletter')->__('Cannot save your subscription: %s', $e->getMessage()));
        }

        return $subscriber;
    }

    /**
     * Generates random code for subscription confirmation
     *
     * @return string
     */
    protected function _generateRandomCode()
    {
        return md5(microtime() + rand());
    }

    /**
     * Preapares data for saving of subscriber
     *
     * @param  Mage_Newsletter_Model_Subscriber $subscriber
     * @return array
     */
    protected function _prepareSave(Mage_Newsletter_Model_Subscriber $subscriber)
    {
        $data = array();
        $data['customer_id'] = $subscriber->getCustomerId();
        $data['store_id'] 	 = $subscriber->getStoreId() ? $subscriber->getStoreId() : 0;
        $data['subscriber_status'] = $subscriber->getStatus();
        $data['subscriber_email']  = $subscriber->getEmail();
        $data['subscriber_confirm_code'] = $subscriber->getCode();

        if($subscriber->getIsStatusChanged()) {
        	$data['change_status_at'] = Mage::getSingleton('core/date')->gmtDate();
        }

        $validators = array('subscriber_email' => 'EmailAddress');
        $filters = array();
        $input = new Zend_Filter_Input($filters, $validators, $data);
        $session = Mage::getSingleton($this->_messagesScope);
        if ($input->hasInvalid() || $input->hasMissing()) {
            foreach ($input->getMessages() as $message) {
                if(is_array($message)) {
                    foreach( $message as $error ) {
                    	$session->addError($error);
                    }
                } else {
                	$session->addError($message);
                }
            }
            Mage::throwException(Mage::helper('newsletter')->__('Form was filled incorrectly'));
        }

        return $data;
    }

    /**
     * Delete subscriber from DB
     *
     * @param int $subscriberId
     */
    public function delete($subscriberId)
    {
        if(!(int)$subscriberId) {
            Mage::throwException(Mage::helper('newsletter')->__('Invalid subscriber ID'));
        }

        $this->_write->beginTransaction();
        try {
            $this->_write->delete($this->_subscriberTable,
                                  $this->_write->quoteInto('subscriber_id=?', $subscriberId));
            $this->_write->commit();
        }
        catch (Exception $e) {
            $this->_write->rollBack();
            Mage::throwException(Mage::helper('newsletter')->__('Cannot delete subscriber'));
        }
    }

    public function received(Mage_Newsletter_Model_Subscriber $subscriber, Mage_Newsletter_Model_Queue $queue)
    {
    	$this->_write->beginTransaction();
    	 try {
    	 	$data['letter_sent_at'] = now();
            $this->_write->update($this->_subscriberLinkTable,
            					  $data,
                                  array($this->_write->quoteInto('subscriber_id=?', $subscriber->getId()),
                                  		$this->_write->quoteInto('queue_id=?', $queue->getId())));
            $this->_write->commit();
        }
        catch (Exception $e) {
            $this->_write->rollBack();
            Mage::throwException(Mage::helper('newsletter')->__('Cannot mark as received subscriber'));
        }
    	return $this;
    }
}
