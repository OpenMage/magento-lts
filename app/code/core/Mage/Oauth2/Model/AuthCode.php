<?php

class Mage_Oauth2_Model_AuthCode extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('oauth2/authCode');
    }
}
