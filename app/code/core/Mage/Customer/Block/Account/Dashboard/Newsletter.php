<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Dashboard neswletter info
 *
 * @category   Mage
 * @package    Mage_Customer
 */
class Mage_Customer_Block_Account_Dashboard_Newsletter extends Mage_Core_Block_Template
{
    /**
     * @var Mage_Newsletter_Model_Subscriber|null
     */
    protected $_subscription = null;

    /**
     * @return Mage_Newsletter_Model_Subscriber
     */
    public function getSubscriptionObject()
    {
        if (is_null($this->_subscription)) {
            $this->_subscription = Mage::getModel('newsletter/subscriber')
                ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer());
        }
        return $this->_subscription;
    }
}
