<?php

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
