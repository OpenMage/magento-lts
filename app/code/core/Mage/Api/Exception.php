<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/**
 * Api exception
 *
 * @package    Mage_Api
 */
class Mage_Api_Exception extends Mage_Core_Exception
{
    protected $_customMessage = null;

    /**
     * Mage_Api_Exception constructor.
     * @param string $faultCode
     * @param string|null $customMessage
     */
    public function __construct($faultCode, $customMessage = null)
    {
        parent::__construct($faultCode);
        $this->_customMessage = $customMessage;
    }

    /**
     * Custom error message, if error is not in api.
     *
     * @return string
     */
    public function getCustomMessage()
    {
        return $this->_customMessage;
    }
}
