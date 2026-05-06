<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Newsletter
 */

/**
 * Newsletter problem model
 *
 * @package    Mage_Newsletter
 *
 * @method Mage_Newsletter_Model_Resource_Problem            _getResource()
 * @method Mage_Newsletter_Model_Resource_Problem_Collection getCollection()
 * @method Mage_Newsletter_Model_Resource_Problem            getResource()
 * @method Mage_Newsletter_Model_Resource_Problem_Collection getResourceCollection()
 * @method $this                                             setCustomerFirstName(string $value)
 * @method $this                                             setCustomerLastName(string $value)
 * @method $this                                             setCustomerName(string $value)
 */
class Mage_Newsletter_Model_Problem extends Mage_Core_Model_Abstract
{
    /**
     * Current Subscriber
     *
     * @var null|Mage_Newsletter_Model_Subscriber
     */
    protected $_subscriber = null;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('newsletter/problem');
    }

    public function getProblemErrorCode(): int
    {
        return (int) $this->_getData('problem_error_code');
    }

    public function getProblemErrorText(): string
    {
        return (string) $this->_getData('problem_error_text');
    }

    public function getQueueId(): int
    {
        return (int) $this->_getData('queue_id');
    }

    public function getSubscriberId(): ?int
    {
        return $this->_getData('subscriber_id') !== null ? (int) $this->_getData('subscriber_id') : null;
    }

    public function setProblemErrorCode(int $value): static
    {
        return $this->setData('problem_error_code', $value);
    }

    public function setProblemErrorText(string $value): static
    {
        return $this->setData('problem_error_text', $value);
    }

    public function setQueueId(int $value): static
    {
        return $this->setData('queue_id', $value);
    }

    public function setSubscriberId(?int $value): static
    {
        return $this->setData('subscriber_id', $value);
    }

    public function getCustomerId(): ?int
    {
        $value = $this->_getData('customer_id');
        return $value !== null ? (int) $value : null;
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
    public function addErrorData(Exception $exception)
    {
        $this->setProblemErrorCode($exception->getCode());
        $this->setProblemErrorText($exception->getMessage());
        return $this;
    }

    /**
     * Retrieve Subscriber
     *
     * @return null|Mage_Newsletter_Model_Subscriber
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
