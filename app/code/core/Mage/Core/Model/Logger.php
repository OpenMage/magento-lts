<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

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
     * @param string $message
     * @param int $level
     * @param string $file
     * @param bool $forceLog
     */
    public function log($message, $level = null, $file = '', $forceLog = false)
    {
        Mage::log($message, $level, $file, $forceLog);
    }

    /**
     * Log exception wrapper
     */
    public function logException(Exception $e)
    {
        Mage::logException($e);
    }
}
