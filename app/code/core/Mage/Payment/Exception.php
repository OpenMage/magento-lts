<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Payment
 */

/**
 * Payment exception
 *
 * @category   Mage
 * @package    Mage_Payment
 */
class Mage_Payment_Exception extends Exception
{
    protected $_code = null;

    /**
     * Mage_Payment_Exception constructor.
     * @param string|null $message
     * @param int $code
     */
    public function __construct($message = null, $code = 0)
    {
        $this->_code = $code;
        parent::__construct($message, 0);
    }

    /**
     * @return int|null
     */
    public function getFields()
    {
        return $this->_code;
    }
}
