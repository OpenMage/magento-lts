<?php
class Mage_Paypal_Model_Exception extends Mage_Core_Exception
{
    protected $_debugData = [];

    public function __construct($message = '', $debugData = [])
    {
        $this->_debugData = $debugData;
        parent::__construct($message);
    }

    public function getDebugData()
    {
        return $this->_debugData;
    }
}
