<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * Events model
 *
 * @package    Mage_Reports
 *
 * @method Mage_Reports_Model_Resource_Event            _getResource()
 * @method Mage_Reports_Model_Resource_Event_Collection getCollection()
 * @method Mage_Reports_Model_Resource_Event            getResource()
 * @method Mage_Reports_Model_Resource_Event_Collection getResourceCollection()
 */
class Mage_Reports_Model_Event extends Mage_Core_Model_Abstract
{
    public const EVENT_PRODUCT_VIEW    = 1;

    public const EVENT_PRODUCT_SEND    = 2;

    public const EVENT_PRODUCT_COMPARE = 3;

    public const EVENT_PRODUCT_TO_CART = 4;

    public const EVENT_PRODUCT_TO_WISHLIST = 5;

    public const EVENT_WISHLIST_SHARE  = 6;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('reports/event');
    }

    public function getEventTypeId(): int
    {
        return (int) $this->_getData('event_type_id');
    }

    public function getLoggedAt(): string
    {
        return (string) $this->_getData('logged_at');
    }

    public function getObjectId(): int
    {
        return (int) $this->_getData('object_id');
    }

    public function getStoreId(): int
    {
        return (int) $this->_getData('store_id');
    }

    public function getSubjectId(): int
    {
        return (int) $this->_getData('subject_id');
    }

    public function getSubtype(): int
    {
        return (int) $this->_getData('subtype');
    }

    public function setEventTypeId(int $value): static
    {
        return $this->setData('event_type_id', $value);
    }

    public function setLoggedAt(string $value): static
    {
        return $this->setData('logged_at', $value);
    }

    public function setObjectId(int $value): static
    {
        return $this->setData('object_id', $value);
    }

    public function setStoreId(int $value): static
    {
        return $this->setData('store_id', $value);
    }

    public function setSubjectId(int $value): static
    {
        return $this->setData('subject_id', $value);
    }

    public function setSubtype(int $value): static
    {
        return $this->setData('subtype', $value);
    }

    /**
     * Before Event save process
     *
     * @inheritDoc
     */
    #[Override]
    protected function _beforeSave()
    {
        $this->setLoggedAt(Mage::getModel('core/date')->gmtDate());
        return parent::_beforeSave();
    }

    /**
     * Update customer type after customer login
     *
     * @param  int   $visitorId
     * @param  int   $customerId
     * @param  array $types
     * @return $this
     */
    public function updateCustomerType($visitorId, $customerId, $types = null)
    {
        if (is_null($types)) {
            $types = [];
            foreach (Mage::getModel('reports/event_type')->getCollection() as $eventType) {
                if ($eventType->getCustomerLogin()) {
                    $types[$eventType->getId()] = $eventType->getId();
                }
            }
        }

        $this->getResource()->updateCustomerType($this, $visitorId, $customerId, $types);
        return $this;
    }

    /**
     * Clean events (visitors)
     *
     * @return $this
     */
    public function clean()
    {
        $this->getResource()->clean($this);
        return $this;
    }
}
