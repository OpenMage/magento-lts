<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Email Template Mailer Model
 *
 * @package    Mage_Core
 *
 * @method Mage_Core_Model_Resource_Email_Queue            _getResource()
 * @method Mage_Core_Model_Resource_Email_Queue_Collection getCollection()
 * @method int                                             getEntityId()
 * @method string                                          getEntityType()
 * @method string                                          getEventType()
 * @method int                                             getIsForceCheck()
 * @method string                                          getMessageBody()
 * @method string                                          getMessageBodyHash()
 * @method array                                           getMessageParameters()
 * @method Mage_Core_Model_Resource_Email_Queue            getResource()
 * @method Mage_Core_Model_Resource_Email_Queue_Collection getResourceCollection()
 * @method $this                                           setEntityId(int $value)
 * @method $this                                           setEntityType(string $value)
 * @method $this                                           setEventType(string $value)
 * @method $this                                           setIsForceCheck(int $value)
 * @method $this                                           setMessageBody(string $value)
 * @method $this                                           setMessageBodyHash(string $value)
 * @method $this                                           setMessageParameters(array $value)
 * @method $this                                           setProcessedAt(string $value)
 */
class Mage_Core_Model_Email_Queue extends Mage_Core_Model_Abstract
{
    /**
     * Email types
     */
    public const EMAIL_TYPE_TO  = 0;

    public const EMAIL_TYPE_CC  = 1;

    public const EMAIL_TYPE_BCC = 2;

    /**
     * Maximum number of messages to be sent oer one cron run
     */
    public const MESSAGES_LIMIT_PER_CRON_RUN = 100;

    /**
     * Store message recipients list
     *
     * @var array
     */
    protected $_recipients = [];

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('core/email_queue');
    }

    /**
     * Save bind recipients to message
     *
     * @inheritDoc
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    protected function _afterSave()
    {
        $this->_getResource()->saveRecipients($this->getId(), $this->getRecipients());
        return parent::_afterSave();
    }

    /**
     * Validate recipients before saving
     *
     * @inheritDoc
     */
    protected function _beforeSave()
    {
        if (empty($this->_recipients) || !is_array($this->_recipients) || empty($this->_recipients[0])) { // additional check of recipients information (email address)
            $error = Mage::helper('core')->__('Message recipients data must be set.');
            Mage::throwException("{$error} - ID: " . $this->getId());
        }

        return parent::_beforeSave();
    }

    /**
     * Add message to queue
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function addMessageToQueue()
    {
        if ($this->getIsForceCheck() && $this->_getResource()->wasEmailQueued($this)) {
            return $this;
        }

        try {
            $this->save();
            $this->setId(null);
        } catch (Throwable $throwable) {
            Mage::logException($throwable);
        }

        return $this;
    }

    /**
     * Add message recipients by email type
     *
     * @param  array|string      $emails
     * @param  null|array|string $names
     * @param  int               $type
     * @return $this
     * @SuppressWarnings("PHPMD.CamelCaseVariableName")
     */
    public function addRecipients($emails, $names = null, $type = self::EMAIL_TYPE_TO)
    {
        $_supportedEmailTypes = [
            self::EMAIL_TYPE_TO,
            self::EMAIL_TYPE_CC,
            self::EMAIL_TYPE_BCC,
        ];
        $type = in_array($type, $_supportedEmailTypes) ? $type : self::EMAIL_TYPE_TO;
        $emails = array_values((array) $emails);
        $names = is_array($names) ? $names : (array) $names;
        $names = array_values($names);
        foreach ($emails as $key => $email) {
            $this->_recipients[] = [$email, $names[$key] ?? '', $type];
        }

        return $this;
    }

    /**
     * Clean recipients data from object
     *
     * @return $this
     */
    public function clearRecipients()
    {
        $this->_recipients = [];
        return $this;
    }

    /**
     * Set message recipients data
     *
     * @return $this
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
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Zend_Cache_Exception
     * @throws Zend_Mail_Exception
     */
    public function send()
    {
        $collection = Mage::getModel('core/email_queue')->getCollection()
            ->addOnlyForSendingFilter()
            ->setPageSize(self::MESSAGES_LIMIT_PER_CRON_RUN)
            ->setCurPage(1)
            ->load();

        ini_set('SMTP', Mage::getStoreConfig('system/smtp/host'));
        ini_set('smtp_port', Mage::getStoreConfig('system/smtp/port'));

        /** @var Mage_Core_Model_Email_Queue $message */
        foreach ($collection as $message) {
            if ($message->getId()) {
                $parameters = new Varien_Object($message->getMessageParameters());
                if ($parameters->getReturnPathEmail() !== null) {
                    $mailTransport = new Zend_Mail_Transport_Sendmail('-f' . $parameters->getReturnPathEmail());
                    Zend_Mail::setDefaultTransport($mailTransport);
                }

                $mailer = new Zend_Mail('utf-8');
                foreach ($message->getRecipients() as $recipient) {
                    [$email, $name, $type] = $recipient;
                    match ((int) $type) {
                        self::EMAIL_TYPE_BCC => $mailer->addBcc($email),
                        default => $mailer->addTo($email, $this->getBase64EncodedString($name)),
                    };
                }

                if ($parameters->getIsPlain()) {
                    $mailer->setBodyText($message->getMessageBody());
                } else {
                    $mailer->setBodyHtml($message->getMessageBody());
                }

                $mailer->setSubject($this->getBase64EncodedString($parameters->getSubject()));
                $mailer->setFrom($parameters->getFromEmail(), $parameters->getFromName());
                if ($parameters->getReplyTo() !== null) {
                    $mailer->setReplyTo($parameters->getReplyTo());
                }

                if ($parameters->getReturnTo() !== null) {
                    $mailer->setReturnPath($parameters->getReturnTo());
                }

                try {
                    $transport = new Varien_Object();
                    Mage::dispatchEvent('email_queue_send_before', [
                        'mail'      => $mailer,
                        'message'   => $message,
                        'transport' => $transport,
                    ]);

                    if ($transport->getTransport()) {
                        $mailer->send($transport->getTransport());
                    } else {
                        $mailer->send();
                    }

                    unset($mailer);
                    $message->setProcessedAt(Varien_Date::formatDate(true));
                    // save() is throwing exception when recipient is not set
                    // phpcs:ignore Ecg.Performance.Loop.ModelLSD
                    $message->save();

                    foreach ($message->getRecipients() as $recipient) {
                        [$email, $name, $type] = $recipient;
                        Mage::dispatchEvent('email_queue_send_after', [
                            'to'         => $email,
                            'html'       => !$parameters->getIsPlain(),
                            'subject'    => $parameters->getSubject(),
                            'email_body' => $message->getMessageBody(),
                        ]);
                    }
                } catch (Throwable $throwable) {
                    Mage::logException($throwable);
                }
            }
        }

        return $this;
    }

    /**
     * Clean queue from sent messages
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function cleanQueue()
    {
        $this->_getResource()->removeSentMessages();
        return $this;
    }

    protected function getBase64EncodedString(string $string): string
    {
        return '=?utf-8?B?' . base64_encode($string) . '?=';
    }
}
