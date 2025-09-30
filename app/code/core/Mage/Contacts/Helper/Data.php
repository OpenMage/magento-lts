<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Contacts
 */

/**
 * Contacts base helper
 *
 * @package    Mage_Contacts
 */
class Mage_Contacts_Helper_Data extends Mage_Core_Helper_Abstract
{
    public const XML_PATH_ENABLED   = 'contacts/contacts/enabled';

    protected $_moduleName = 'Mage_Contacts';

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED);
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return '';
        }
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return trim($customer->getName());
    }

    /**
     * @return string
     */
    public function getUserEmail()
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return '';
        }
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return $customer->getEmail();
    }
}
