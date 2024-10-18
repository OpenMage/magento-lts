<?php

class Mage_Oauth2_Model_Resource_Access_Token_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize the collection
     */
    protected function _construct()
    {
        $this->_init('oauth2/access_token');
    }
}
