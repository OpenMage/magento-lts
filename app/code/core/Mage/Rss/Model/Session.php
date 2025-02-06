<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Rss
 */

/**
 * Auth session model
 *
 * @category   Mage
 * @package    Mage_Rss
 *
 * @method Mage_Admin_Model_User getAdmin()
 * @method $this setAdmin(Mage_Admin_Model_User $value)
 * @method Mage_Customer_Model_Customer getCustomer()
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
