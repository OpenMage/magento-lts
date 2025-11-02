<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Rss
 */

/**
 * Auth session model
 *
 * @package    Mage_Rss
 *
 * @method Mage_Admin_Model_User getAdmin()
 * @method Mage_Customer_Model_Customer getCustomer()
 * @method $this setAdmin(Mage_Admin_Model_User $value)
 */
class Mage_Rss_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $this->init('rss');
    }

    /**
     * @return bool
     */
    public function isAdminLoggedIn()
    {
        return $this->getAdmin() && $this->getAdmin()->getId();
    }

    /**
     * @return bool
     */
    public function isCustomerLoggedIn()
    {
        return $this->getCustomer() && $this->getCustomer()->getId();
    }
}
