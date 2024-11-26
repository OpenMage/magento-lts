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
 * Newsletter problem model
 *
 * @category   Mage
 * @package    Mage_Newsletter
 *
 * @method Mage_Newsletter_Model_Resource_Problem _getResource()
 * @method Mage_Newsletter_Model_Resource_Problem getResource()
 * @method int getCustomerId()
 * @method $this setCustomerName(string $value)
 * @method $this setCustomerFirstName(string $value)
 * @method $this setCustomerLastName(string $value)
 * @method int getQueueId()
 * @method $this setQueueId(int $value)
 * @method int getProblemErrorCode()
 * @method $this setProblemErrorCode(int $value)
 * @method string getProblemErrorText()
 * @method $this setProblemErrorText(string $value)
 * @method int getSubscriberId()
 * @method $this setSubscriberId(int $value)
 */
class Mage_Newsletter_Model_Problem extends Mage_Core_Model_Abstract
{
    /**
     * Current Subscriber
     *
     * @var Mage_Newsletter_Model_Subscriber|null
     */
    protected $_subscriber = null;

    /**
     * Initialize Newsletter Problem Model
     */
    protected function _construct()
    {
        $this->_init('newsletter/problem');
    }

    /**
     * Add Subscriber Data
     *
     * @return $this
     */
    public function addSubscriberData(Mage_Newsletter_Model_Subscriber $subscriber)
    {
        $this->setSubscriberId($subscriber->getId());
        return $this;
    }

    /**
     * Add Queue Data
     *
     * @return $this
     */
    public function addQueueData(Mage_Newsletter_Model_Queue $queue)
    {
        $this->setQueueId($queue->getId());
        return $this;
    }

    /**
     * Add Error Data
     *
     * @return $this
     */
    public function addErrorData(Exception $e)
    {
        $this->setProblemErrorCode($e->getCode());
        $this->setProblemErrorText($e->getMessage());
        return $this;
    }

    /**
     * Retrieve Subscriber
     *
     * @return Mage_Newsletter_Model_Subscriber|null
     */
    public function getSubscriber()
    {
        if (!$this->getSubscriberId()) {
            return null;
        }

        if (is_null($this->_subscriber)) {
            $this->_subscriber = Mage::getModel('newsletter/subscriber')
                ->load($this->getSubscriberId());
        }

        return $this->_subscriber;
    }

    /**
     * Unsubscribe Subscriber
     *
     * @return $this
     */
    public function unsubscribe()
    {
        if ($this->getSubscriber()) {
            $this->getSubscriber()->setSubscriberStatus(Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED)
                ->setIsStatusChanged(true)
                ->save();
        }
        return $this;
    }
}
