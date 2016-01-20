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
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Core_Model_Resource_Email_Queue extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize email queue resource model
     *
     */
    protected function _construct()
    {
        $this->_init('core/email_queue', 'message_id');
    }

    /**
     * Load recipients, unserialize message parameters
     *
     * @param Mage_Core_Model_Abstract $object
     *
     * @return Mage_Core_Model_Resource_Email_Queue
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $object->setRecipients($this->getRecipients($object->getId()));
        $object->setMessageParameters(unserialize($object->getMessageParameters()));
        return $this;
    }

    /**
     * Prepare object data for saving
     *
     * @param Mage_Core_Model_Email_Queue|Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Resource_Email_Queue
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->isObjectNew()) {
            $object->setCreatedAt($this->formatDate(true));
        }
        $object->setMessageBodyHash(md5($object->getMessageBody()));
        $object->setMessageParameters(serialize($object->getMessageParameters()));

        return parent::_beforeSave($object);
    }

    /**
     * Check if email was added to queue for requested recipients
     *
     * @param Mage_Core_Model_Email_Queue $queue
     *
     * @return bool
     */
    public function wasEmailQueued(Mage_Core_Model_Email_Queue $queue)
    {
        $readAdapter = $this->_getReadAdapter();
        $select = $readAdapter->select()
            ->from(
                array('recips' => $this->getTable('core/email_recipients')),
                array('recipient_email', 'recipient_name', 'email_type')
            )
            ->join(array('queue' => $this->getMainTable()), 'queue.message_id = recips.message_id', array())
            ->where('queue.entity_id =? ', $queue->getEntityId())
            ->where('queue.entity_type =? ', $queue->getEntityType())
            ->where('queue.event_type =? ', $queue->getEventType())
            ->where('queue.message_body_hash =? ', md5($queue->getMessageBody()));

        $existingRecipients = $readAdapter->fetchAll($select);
        if ($existingRecipients) {
            $newRecipients = $queue->getRecipients();
            $oldEmails = $newEmails = array();
            foreach ($existingRecipients as $recipient) {
                $oldEmails[$recipient['recipient_email']] = array(
                    $recipient['recipient_email'], $recipient['recipient_name'], $recipient['email_type']
                );
            }
            unset($recipient);
            foreach ($newRecipients as $recipient) {
                list($email, $name, $type) = $recipient;
                $newEmails[$email] = array($email, $name, $type);
            }
            $diff = array_diff_key($newEmails, $oldEmails);
            if (sizeof($diff) > 0) {
                $queue->clearRecipients();
                foreach ($diff as $recipient) {
                    list($email, $name, $type) = $recipient;
                    $queue->addRecipients($email, $name, $type);
                }
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * Retrieve recipients data for specified message
     *
     * @param int $messageId
     *
     * @return array
     */
    public function getRecipients($messageId)
    {
        $readAdapter = $this->_getReadAdapter();
        $select = $readAdapter->select()
            ->from($this->getTable('core/email_recipients'), array('recipient_email', 'recipient_name', 'email_type'))
            ->where('message_id =? ', $messageId);
        $recipients = $readAdapter->fetchAll($select);
        $existingRecipients = array();
        if ($recipients) {
            foreach ($recipients as $recipient) {
                $existingRecipients[] = array(
                    $recipient['recipient_email'],
                    $recipient['recipient_name'],
                    $recipient['email_type']
                );
            }
        }

        return $existingRecipients;
    }

    /**
     * Save message recipients
     *
     * @param int $messageId
     * @param array $recipients
     *
     * @throws Exception
     *
     * @return Mage_Core_Model_Resource_Email_Queue
     */
    public function saveRecipients($messageId, array $recipients)
    {
        $writeAdapter = $this->_getWriteAdapter();
        $recipientsTable = $this->getTable('core/email_recipients');
        $writeAdapter->beginTransaction();

        try {
            foreach ($recipients as $recipient) {
                list($email, $name, $type) = $recipient;
                $writeAdapter->insertOnDuplicate(
                    $recipientsTable,
                    array(
                         'message_id'      => $messageId,
                         'recipient_email' => $email,
                         'recipient_name'  => $name,
                         'email_type'      => $type
                    ),
                    array('recipient_name')
                );
            }
            $writeAdapter->commit();
        } catch (Exception $e) {
            $writeAdapter->rollback();
            throw $e;
        }

        return $this;
    }

    /**
     * Remove already sent messages
     *
     * @return Mage_Core_Model_Resource_Email_Queue
     */
    public function removeSentMessages()
    {
        $this->_getWriteAdapter()->delete($this->getMainTable(), 'processed_at IS NOT NULL');
        return $this;
    }
}
