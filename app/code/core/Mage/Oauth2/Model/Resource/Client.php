<?php

class Mage_Oauth2_Model_Resource_Client extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('oauth2/client', 'entity_id');
    }
}
