<?php

declare(strict_types=1);

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_SalesRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * SalesRule Model Observer
 *
 * @category   Mage
 * @package    Mage_SalesRule
 * @deprecated seems not to be used anymore
 */
class Mage_SalesRule_Model_Observer_SalesQuoteAddressDiscountItem implements Mage_Core_Observer_Interface
{
    /**
     * Sales Rule Validator
     *
     * @var Mage_SalesRule_Model_Validator
     */
    protected $_validator;


    /**
     * Process quote item (apply discount to item)
     */
    public function execute(Varien_Event_Observer $observer): void
    {
        $this->getValidator($observer->getEvent())
            ->process($observer->getEvent()->getItem());
    }

    /**
     * Get quote item validator/processor object
     *
     * @deprecated
     * @param   Varien_Event $event
     * @return  Mage_SalesRule_Model_Validator
     */
    public function getValidator($event)
    {
        if (!$this->_validator) {
            $this->_validator = Mage::getModel('salesrule/validator')
                ->init($event->getWebsiteId(), $event->getCustomerGroupId(), $event->getCouponCode());
        }
        return $this->_validator;
    }
}
