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

/**
 * Email Template Mailer Model
 *
 * @method Mage_Core_Model_Email_Queue setEntityId(int $value)
 * @method Mage_Core_Model_Email_Queue setEntityType(string $value)
 * @method Mage_Core_Model_Email_Queue setEventType(string $value)
 * @method Mage_Core_Model_Email_Queue setIsForceCheck(int $value)
 * @method int getIsForceCheck()
 * @method int getEntityId()
 * @method string getEntityType()
 * @method string getEventType()
 * @method string getMessageBodyHash()
 * @method string getMessageBody()
 * @method Mage_Core_Model_Email_Queue setMessageBody(string $value)
 * @method Mage_Core_Model_Email_Queue setMessageParameters(array $value)
 * @method Mage_Core_Model_Email_Queue setProcessedAt(string $value)
 * @method array getMessageParameters()
 *
 * @category    Mage
 * @package     Mage_Core
 */
class Mage_Core_Model_Email_Queue extends Mage_Core_Model_Abstract
{
    /**
     * Email types
     */
    const EMAIL_TYPE_TO  = 0;
    const EMAIL_TYPE_CC  = 1;
    const EMAIL_TYPE_BCC = 2;

    /**
     * Maximum number of messages to be sent oer one cron run
     */
    const MESSAGES_LIMIT_PER_CRON_RUN = 100;

    /**
     * Store message recipients list
     *
     * @var array
     */
    protected $_recipients = array();

    /**
     * Initialize object
     */
    protected function _construct()
    {
        $this->_init('core/email_queue');
    }

    /**
     * Save bind recipients to message
     *
     * @return Mage_Core_Model_Email_Queue
     */
    protected function _afterSave()
    {
        $this->_getResource()->saveRecipients($this->getId(), $this->getRecipients());
        return parent::_afterSave();
    }

    /**
     * Validate recipients before saving
     *
     * @return Mage_Core_Model_Email_Queue
     */
    protected function _beforeSave()
    {
        if (empty($this->_recipients) || !is_array($this->_recipients)) {
            Mage::throwException(Mage::helper('core')->__('Message recipients data must be set.'));
        }
        return parent::_beforeSave();
    }

    /**
     * Add message to queue
     *
     * @return Mage_Core_Model_Email_Queue
     */
    public function addMessageToQueue()
    {
        if ($this->getIsForceCheck() && $this->_getResource()->wasEmailQueued($this)) {
            return $this;
        }
        try {
            $this->save();
            $this->setId(null);
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }

    /**
     * Add message recipients by email type
     *
     * @param array|string $emails
     * @param array|string|null $names
     * @param int $type
     *
     * @return Mage_Core_Model_Email_Queue
     */
    public function addRecipients($emails, $names = null, $type = self::EMAIL_TYPE_TO)
    {
        $_supportedEmailTypes = array(
            self::EMAIL_TYPE_TO,
            self::EMAIL_TYPE_CC,
            self::EMAIL_TYPE_BCC
        );
        $type = !in_array($type, $_supportedEmailTypes) ? self::EMAIL_TYPE_TO : $type;
        $emails = array_values((array)$emails);
        $names = is_array($names) ? $names : (array)$names;
        $names = array_values($names);
        foreach ($emails as $key => $email) {
            $this->_recipients[] = array($email, isset($names[$key]) ? $names[$key] : '', $type);
        }
        return $this;
    }

    /**
     * Clean recipients data from object
     *
     * @return Mage_Core_Model_Email_Queue
     */
    public function clearRecipients()
    {
        $this->_recipients = array();
        return $this;
    }

    /**
     * Set message recipients data
     *
     * @param array $recipients
     *
     * @return Mage_Core_Model_Email_Queue
     */
    public function setRecipients(array $recipients)
    {
        $this->_recipients = $recipients;
        return $this;
    }

    /**
     * Get message recipients list
     *
     * @return array
     */
    public function getRecipients()
    {
        return $this->_recipients;
    }

    /**
     * Send all messages in a queue
     *
     * @return Mage_Core_Model_Email_Queue
     */
    public function send()
    {
        /** @var $collection Mage_Core_Model_Resource_Email_Queue_Collection */
        $collection = Mage::getModel('core/email_queue')->getCollection()
            ->addOnlyForSendingFilter()
            ->setPageSize(self::MESSAGES_LIMIT_PER_CRON_RUN)
            ->setCurPage(1)
            ->load();


        ini_set('SMTP', Mage::getStoreConfig('system/smtp/host'));
        ini_set('smtp_port', Mage::getStoreConfig('system/smtp/port'));

        /** @var $message Mage_Core_Model_Email_Queue */
        foreach ($collection as $message) {
            if ($message->getId()) {
                $parameters = new Varien_Object($message->getMessageParameters());
                if ($parameters->getReturnPathEmail() !== null) {
                    $mailTransport = new Zend_Mail_Transport_Sendmail("-f" . $parameters->getReturnPathEmail());
                    Zend_Mail::setDefaultTransport($mailTransport);
                }

                $mailer = new Zend_Mail('utf-8');
                foreach ($message->getRecipients() as $recipient) {
                    list($email, $name, $type) = $recipient;
                    switch ($type) {
                        case self::EMAIL_TYPE_BCC:
                            $mailer->addBcc($email, '=?utf-8?B?' . base64_encode($name) . '?=');
                            break;
                        case self::EMAIL_TYPE_TO:
                        case self::EMAIL_TYPE_CC:
                        default:
                            $mailer->addTo($email, '=?utf-8?B?' . base64_encode($name) . '?=');
                            break;
                    }
                }

                if ($parameters->getIsPlain()) {
                    $mailer->setBodyText($message->getMessageBody());
                } else {
                    $mailer->setBodyHTML($message->getMessageBody());
                }

                $mailer->setSubject('=?utf-8?B?' . base64_encode($parameters->getSubject()) . '?=');
                $mailer->setFrom($parameters->getFromEmail(), $parameters->getFromName());
                if ($parameters->getReplyTo() !== null) {
                    $mailer->setReplyTo($parameters->getReplyTo());
                }
                if ($parameters->getReturnTo() !== null) {
                    $mailer->setReturnPath($parameters->getReturnTo());
                }

                try {
                    $mailer->send();
                } catch (Exception $e) {
                    Mage::logException($e);
                }

                unset($mailer);
                $message->setProcessedAt(Varien_Date::formatDate(true));
                $message->save();
            }
        }

        return $this;
    }

    /**
     * Clean queue from sent messages
     *
     * @return Mage_Core_Model_Email_Queue
     */
    public function cleanQueue()
    {
        $this->_getResource()->removeSentMessages();
        return $this;
    }
}
