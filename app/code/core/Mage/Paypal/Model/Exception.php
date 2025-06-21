<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

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
