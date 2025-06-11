<?php
class Mage_Paypal_Model_Resource_Debug extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('paypal/debug', 'entity_id');
    }
    
}
