<?php
/**
 * API exception
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Api2
 */
class Mage_Api2_Exception extends Exception
{
    /**
     * Exception constructor
     *
     * @param string $message
     * @param int $code
     */
    public function __construct($message, $code)
    {
        if ($code <= 100 || $code >= 599) {
            throw new Exception(sprintf('Invalid Exception code "%d"', $code));
        }

        parent::__construct($message, $code);
    }
}
