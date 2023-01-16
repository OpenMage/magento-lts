<?php
declare(strict_types=1);

abstract class Mage_Log_Model_Console_Abstract extends Mage_Console_Model_Command
{
    /**
     * Log instance
     *
     * @var Mage_Log_Model_Log|null
     */
    protected $_log;

    /**
     * Retrieve Log instance
     *
     * @return Mage_Log_Model_Log
     */
    protected function _getLog()
    {
        if (is_null($this->_log)) {
            $this->_log = Mage::getModel('log/log');
        }
        return $this->_log;
    }
}
