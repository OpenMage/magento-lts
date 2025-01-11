<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Events model
 *
 * @category   Mage
 * @package    Mage_Reports
 *
 * @method Mage_Reports_Model_Resource_Event _getResource()
 * @method Mage_Reports_Model_Resource_Event getResource()
 * @method Mage_Reports_Model_Resource_Event_Collection getCollection()
 * @method string getLoggedAt()
 * @method $this setLoggedAt(string $value)
 * @method int getEventTypeId()
 * @method $this setEventTypeId(int $value)
 * @method int getObjectId()
 * @method $this setObjectId(int $value)
 * @method int getSubjectId()
 * @method $this setSubjectId(int $value)
 * @method int getSubtype()
 * @method $this setSubtype(int $value)
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
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
     * Initialize resource
     *
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
     * @param int $visitorId
     * @param int $customerId
     * @param array $types
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
