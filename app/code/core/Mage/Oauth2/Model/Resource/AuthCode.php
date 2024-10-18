<?php

class Mage_Oauth2_Model_Resource_AuthCode extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('oauth2/auth_code', 'authorization_code');
        $this->_isPkAutoIncrement = false;
    }
}
