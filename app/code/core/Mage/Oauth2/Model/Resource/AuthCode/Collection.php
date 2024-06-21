<?php

class Mage_Oauth2_Model_Resource_Auth_Code_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('oauth2/auth_code');
    }
}
