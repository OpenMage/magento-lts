<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2017-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Core
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
     * @param Mage_Core_Model_Email_Queue $object
     * @inheritDoc
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $object->setRecipients($this->getRecipients($object->getId()));
        $object->setMessageParameters(unserialize($object->getMessageParameters(), ['allowed_classes' => false]));
        return $this;
    }

    /**
     * Prepare object data for saving
     *
     * @param Mage_Core_Model_Email_Queue $object
     * @inheritDoc
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
     *
     * @return bool
     */
    public function wasEmailQueued(Mage_Core_Model_Email_Queue $queue)
    {
        $readAdapter = $this->_getReadAdapter();
        $select = $readAdapter->select()
            ->from(
                ['recips' => $this->getTable('core/email_recipients')],
                ['recipient_email', 'recipient_name', 'email_type'],
            )
            ->join(['queue' => $this->getMainTable()], 'queue.message_id = recips.message_id', [])
            ->where('queue.entity_id =? ', $queue->getEntityId())
            ->where('queue.entity_type =? ', $queue->getEntityType())
            ->where('queue.event_type =? ', $queue->getEventType())
            ->where('queue.message_body_hash =? ', md5($queue->getMessageBody()));

        // phpcs:ignore Ecg.Performance.FetchAll.Found
        $existingRecipients = $readAdapter->fetchAll($select);
        if ($existingRecipients) {
            $newRecipients = $queue->getRecipients();
            $oldEmails = $newEmails = [];
            foreach ($existingRecipients as $recipient) {
                $oldEmails[$recipient['recipient_email']] = [
                    $recipient['recipient_email'], $recipient['recipient_name'], $recipient['email_type'],
                ];
            }
            unset($recipient);
            foreach ($newRecipients as $recipient) {
                list($email, $name, $type) = $recipient;
                $newEmails[$email] = [$email, $name, $type];
            }
            $diff = array_diff_key($newEmails, $oldEmails);
            if (count($diff)) {
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
            ->from($this->getTable('core/email_recipients'), ['recipient_email', 'recipient_name', 'email_type'])
            ->where('message_id =? ', $messageId);
        // phpcs:ignore Ecg.Performance.FetchAll.Found
        $recipients = $readAdapter->fetchAll($select);
        $existingRecipients = [];
        if ($recipients) {
            foreach ($recipients as $recipient) {
                $existingRecipients[] = [
                    $recipient['recipient_email'],
                    $recipient['recipient_name'],
                    $recipient['email_type'],
                ];
            }
        }

        return $existingRecipients;
    }

    /**
     * Save message recipients
     *
     * @param int $messageId
     *
     * @throws Exception
     * @return $this
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
                    [
                        'message_id'      => $messageId,
                        'recipient_email' => $email,
                        'recipient_name'  => $name,
                        'email_type'      => $type,
                    ],
                    ['recipient_name'],
                );
            }
            $writeAdapter->commit();
        } catch (Exception $e) {
            $writeAdapter->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Remove already sent messages
     *
     * @return $this
     */
    public function removeSentMessages()
    {
        $this->_getWriteAdapter()->delete($this->getMainTable(), 'processed_at IS NOT NULL');
        return $this;
    }
}
