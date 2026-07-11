<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Payment
 */

/**
 * Payment exception
 *
 * @package    Mage_Payment
 */
class Mage_Payment_Exception extends Exception
{
    protected $_code = null;

    /**
     * Mage_Payment_Exception constructor.
     * @param null|string $message
     * @param int         $code
     */
    public function __construct($message = null, $code = 0)
    {
        $this->_code = $code;
        parent::__construct($message, 0);
    }

    /**
     * @return null|int
     */
    public function getFields()
    {
        return $this->_code;
    }
}
