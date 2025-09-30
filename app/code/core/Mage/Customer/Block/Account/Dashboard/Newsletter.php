<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Dashboard neswletter info
 *
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
