<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Newsletter
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Newsletter queue model.
 *
 * @category   Mage
 * @package    Mage_Newsletter
 *
 * @method Mage_Newsletter_Model_Resource_Queue _getResource()
 * @method Mage_Newsletter_Model_Resource_Queue getResource()
 * @method Mage_Newsletter_Model_Resource_Queue_Collection getCollection()
 *
 * @method int getTemplateId()
 * @method $this setTemplateId(int $value)
 * @method int getNewsletterType()
 * @method $this setNewsletterType(int $value)
 * @method string getNewsletterText()
 * @method $this setNewsletterText(string $value)
 * @method string getNewsletterStyles()
 * @method $this setNewsletterStyles(string $value)
 * @method string getNewsletterSubject()
 * @method $this setNewsletterSubject(string $value)
 * @method string getNewsletterSenderName()
 * @method $this setNewsletterSenderName(string $value)
 * @method string getNewsletterSenderEmail()
 * @method $this setNewsletterSenderEmail(string $value)
 * @method int getQueueStatus()
 * @method $this setQueueStatus(int $value)
 * @method string getQueueStartAt()
 * @method $this setQueueStartAt(string $value)
 * @method string getQueueFinishAt()
 * @method $this setQueueFinishAt(string $value)
 */
class Mage_Newsletter_Model_Queue extends Mage_Core_Model_Template
{
    /**
     * Newsletter Template object
     *
     * @var Mage_Newsletter_Model_Template|null
     */
    protected $_template;

    /**
     * Subscribers collection
     * @var Mage_Newsletter_Model_Resource_Subscriber_Collection|null
     */
    protected $_subscribersCollection = null;

    /**
     * save template flag
     *
     * @var bool
     * @deprecated since 1.4.0.1
     */
    protected $_saveTemplateFlag = false;

    /**
     * Save stores flag.
     *
     * @var bool
     */
    protected $_saveStoresFlag = false;

    /**
     * Stores assigned to queue.
     *
     * @var array
     */
    protected $_stores = [];

    public const STATUS_NEVER = 0;
    public const STATUS_SENDING = 1;
    public const STATUS_CANCEL = 2;
    public const STATUS_SENT = 3;
    public const STATUS_PAUSE = 4;

    protected function _construct()
    {
        $this->_init('newsletter/queue');
    }

    /**
     * Return: is this queue newly created or not.
     *
     * @return bool
     */
    public function isNew()
    {
        return (is_null($this->getQueueStatus()));
    }

    /**
     * Returns subscribers collection for this queue
     *
     * @return Mage_Newsletter_Model_Resource_Subscriber_Collection
     */
    public function getSubscribersCollection()
    {
        if (is_null($this->_subscribersCollection)) {
            $this->_subscribersCollection = Mage::getResourceModel('newsletter/subscriber_collection')
                ->useQueue($this);
        }

        return $this->_subscribersCollection;
    }

    /**
     * Add template data to queue.
     *
     * @param Varien_Object $data
     * @return $this
     * @deprecated since 1.4.0.1
     */
    public function addTemplateData($data)
    {
        $template = $this->getTemplate();
        if ($data->getTemplateId() && $data->getTemplateId() != $template->getId()) {
            $template->load($data->getTemplateId());
        }

        return $this;
    }

    /**
     * Set $_data['queue_start'] based on string from backend, which based on locale.
     *
     * @param string|null $startAt start date of the mailing queue
     * @return $this
     */
    public function setQueueStartAtByString($startAt)
    {
        if (is_null($startAt) || $startAt == '') {
            $this->setQueueStartAt(null);
        } else {
            $locale = Mage::app()->getLocale();
            $format = $locale->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
            $time = $locale->date($startAt, $format)->getTimestamp();
            $this->setQueueStartAt(Mage::getModel('core/date')->gmtDate(null, $time));
        }
        return $this;
    }

    /**
     * Send messages to subscribers for this queue
     *
     * @param   int     $count
     * @return $this
     */
    public function sendPerSubscriber($count = 20, array $additionalVariables = [])
    {
        if ($this->getQueueStatus() != self::STATUS_SENDING
            && ($this->getQueueStatus() != self::STATUS_NEVER && $this->getQueueStartAt())
        ) {
            return $this;
        }

        if ($this->getSubscribersCollection()->getSize() == 0) {
            $this->_finishQueue();
            return $this;
        }

        $collection = $this->getSubscribersCollection()
            ->useOnlyUnsent()
            ->showCustomerInfo()
            ->setPageSize($count)
            ->setCurPage(1)
            ->load();

        $sender = Mage::getModel('core/email_template');
        $sender->setSenderName($this->getNewsletterSenderName())
            ->setSenderEmail($this->getNewsletterSenderEmail())
            ->setTemplateType(self::TYPE_HTML)
            ->setTemplateSubject($this->getNewsletterSubject())
            ->setTemplateText($this->getNewsletterText())
            ->setTemplateStyles($this->getNewsletterStyles())
            ->setTemplateFilter(Mage::helper('newsletter')->getTemplateProcessor());

        /** @var Mage_Newsletter_Model_Subscriber $item */
        foreach ($collection->getItems() as $item) {
            $email = $item->getSubscriberEmail();
            $name = $item->getSubscriberFullName();

            $sender->emulateDesign($item->getStoreId());
            $successSend = $sender->send($email, $name, ['subscriber' => $item]);
            $sender->revertDesign();

            if ($successSend) {
                $item->received($this);
            } else {
                $problem = Mage::getModel('newsletter/problem');
                $notification = Mage::helper('newsletter')->__('Please refer to exeption.log');
                $problem->addSubscriberData($item)
                    ->addQueueData($this)
                    ->addErrorData(new Exception($notification))
                    ->save();
                $item->received($this);
            }
        }

        if (count($collection->getItems()) < $count - 1 || count($collection->getItems()) == 0) {
            $this->_finishQueue();
        }
        return $this;
    }

    /**
     * Finish queue: set status SENT and update finish date
     *
     * @return $this
     */
    protected function _finishQueue()
    {
        $this->setQueueFinishAt(Mage::getSingleton('core/date')->gmtDate());
        $this->setQueueStatus(self::STATUS_SENT);
        $this->save();

        return $this;
    }

    /**
     * Getter data for saving
     *
     * @return array
     */
    public function getDataForSave()
    {
        $data = [];
        $data['template_id'] = $this->getTemplateId();
        $data['queue_status'] = $this->getQueueStatus();
        $data['queue_start_at'] = $this->getQueueStartAt();
        $data['queue_finish_at'] = $this->getQueueFinishAt();
        return $data;
    }

    /**
     * Add subscribers to queue.
     *
     * @return $this
     */
    public function addSubscribersToQueue(array $subscriberIds)
    {
        $this->_getResource()->addSubscribersToQueue($this, $subscriberIds);
        return $this;
    }

    /**
     * Setter for save template flag.
     *
     * @param bool|int|string $value
     * @return $this
     * @deprecated since 1.4.0.1
     */
    public function setSaveTemplateFlag($value)
    {
        $this->_saveTemplateFlag = (bool)$value;
        return $this;
    }

    /**
     * Getter for save template flag.
     *
     * @return bool
     * @deprecated since 1.4.0.1
     */
    public function getSaveTemplateFlag()
    {
        return $this->_saveTemplateFlag;
    }

    /**
     * Setter for save stores flag.
     *
     * @param bool|int|string $value
     * @return $this
     */
    public function setSaveStoresFlag($value)
    {
        $this->_saveStoresFlag = (bool)$value;
        return $this;
    }

    /**
     * Getter for save stores flag.
     *
     * @return bool
     */
    public function getSaveStoresFlag()
    {
        return $this->_saveStoresFlag;
    }

    /**
     * Setter for stores of queue.
     *
     * @return $this
     */
    public function setStores(array $storesIds)
    {
        $this->setSaveStoresFlag(true);
        $this->_stores = $storesIds;
        return $this;
    }

    /**
     * Getter for stores of queue.
     *
     * @return array
     */
    public function getStores()
    {
        if (!$this->_stores) {
            $this->_stores = $this->_getResource()->getStores($this);
        }

        return $this->_stores;
    }

    /**
     * Retrieve Newsletter Template object
     *
     * @return Mage_Newsletter_Model_Template
     */
    public function getTemplate()
    {
        if (is_null($this->_template)) {
            $this->_template = Mage::getModel('newsletter/template')
                ->load($this->getTemplateId());
        }
        return $this->_template;
    }

    /**
     * Getter for template type
     *
     * @return int|string
     */
    public function getType()
    {
        return $this->getNewsletterType();
    }
}
