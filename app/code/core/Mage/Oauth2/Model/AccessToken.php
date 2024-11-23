<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Oauth2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

class Mage_Oauth2_Model_AccessToken extends Mage_Core_Model_Abstract
{
    public const USER_TYPE_ADMIN = 'admin';
    public const USER_TYPE_CUSTOMER = 'customer';

    protected function _construct()
    {
        $this->_init('oauth2/accessToken');
    }

    /**
     * Get user type associated with the token
     *
     * @return string|null
     * @throws Mage_Core_Exception
     */
    public function getUserType()
    {
        if ($this->getAdminId()) {
            return self::USER_TYPE_ADMIN;
        } elseif ($this->getCustomerId()) {
            return self::USER_TYPE_CUSTOMER;
        } else {
            Mage::throwException(Mage::helper('oauth2')->__('User type is unknown'));
        }
    }
}
