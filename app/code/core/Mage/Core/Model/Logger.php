<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

use Monolog\Level;

/**
 * Logger model
 *
 * @package    Mage_Core
 */
class Mage_Core_Model_Logger
{
    /**
     * Log wrapper
     *
     * @param string   $message
     * @param Level::* $level
     * @param string   $file
     * @param bool     $forceLog
     * @param array    $context  additional context for the log entry
     */
    public function log($message, $level = null, $file = '', $forceLog = false, array $context = [])
    {
        Mage::log($message, $level, $file, $forceLog, $context);
    }

    /**
     * Log exception wrapper
     */
    public function logException(Exception $e)
    {
        Mage::logException($e);
    }
}
