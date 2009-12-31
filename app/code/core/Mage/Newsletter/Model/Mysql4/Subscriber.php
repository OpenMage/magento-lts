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
 * @package     Mage_Newsletter
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Newsletter subscriber model for MySQL4
 *
 * @category   Mage
 * @package    Mage_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Newsletter_Model_Mysql4_Subscriber extends Mage_Core_Model_Mysql4_Abstract
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
     * Initialize resource model
     *
     * Get tablename from config
     */
    protected function _construct()
    {
        $this->_init('newsletter/subscriber', 'subscriber_id');
        $this->_subscriberTable = Mage::getSingleton('core/resource')->getTableName("newsletter/subscriber");
        $this->_subscriberLinkTable = Mage::getSingleton('core/resource')->getTableName("newsletter/queue_link");
        $this->_read = $this->_getReadAdapter();
        $this->_write = $this->_getWriteAdapter();
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
     * Generates random code for subscription confirmation
     *
     * @return string
     */
    protected function _generateRandomCode()
    {
        return Mage::helper('core')->uniqHash();
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
