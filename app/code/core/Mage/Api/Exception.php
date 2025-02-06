<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Api
 */

/**
 * Api exception
 *
 * @category   Mage
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
