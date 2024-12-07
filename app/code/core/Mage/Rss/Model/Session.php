<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Rss
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2021-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
