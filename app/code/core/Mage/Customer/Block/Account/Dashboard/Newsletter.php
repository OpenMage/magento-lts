<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Customer
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
