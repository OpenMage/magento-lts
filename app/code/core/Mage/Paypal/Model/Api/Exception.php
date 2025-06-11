<?php
class Mage_Paypal_Model_Api_Exception extends Mage_Core_Exception
{
    protected $_errorCode;
    protected $_debugData;

    public function __construct($message = '', $errorCode = '', $debugData = [])
    {
        $this->_errorCode = $errorCode;
        $this->_debugData = $debugData;
        parent::__construct($message);
    }

    public function getErrorCode()
    {
        return $this->_errorCode;
    }

    public function getDebugData()
    {
        return $this->_debugData;
    }
} 