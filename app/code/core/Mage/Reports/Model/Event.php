<?php

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
 * @method int                                          getEventTypeId()
 * @method string                                       getLoggedAt()
 * @method int                                          getObjectId()
 * @method Mage_Reports_Model_Resource_Event            getResource()
 * @method Mage_Reports_Model_Resource_Event_Collection getResourceCollection()
 * @method int                                          getStoreId()
 * @method int                                          getSubjectId()
 * @method int                                          getSubtype()
 * @method $this                                        setEventTypeId(int $value)
 * @method $this                                        setLoggedAt(string $value)
 * @method $this                                        setObjectId(int $value)
 * @method $this                                        setStoreId(int $value)
 * @method $this                                        setSubjectId(int $value)
 * @method $this                                        setSubtype(int $value)
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

    /**
     * Before Event save process
     *
     * @inheritDoc
     */
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
