<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */

/**
 * API exception
 *
 * @package    Mage_Api2
 */
class Mage_Api2_Exception extends Exception
{
    /**
     * Log the exception in the log file?
     */
    protected bool $shouldLog = true;

    /**
     * Exception constructor
     *
     * @param string $message
     * @param int    $code
     * @param bool   $shouldLog
     */
    public function __construct($message, $code, $shouldLog = true)
    {
        if ($code <= 100 || $code >= 599) {
            throw new Exception(sprintf('Invalid Exception code "%d"', $code));
        }

        $this->shouldLog = $shouldLog;
        parent::__construct($message, $code);
    }

    /**
     * Check if exception should be logged
     */
    public function shouldLog(): bool
    {
        return $this->shouldLog;
    }
}
