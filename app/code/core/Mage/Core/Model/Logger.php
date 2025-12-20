<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

use Monolog\Level;
use Monolog\Logger;

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
     * @param Level::* $level
     * @param string $file
     * @param bool $forceLog
     * @param array $context additional context for the log entry
     */
    public function log($message, $level = null, $file = '', $forceLog = false, array $context = [])
    {
        try {
            $logActive = Mage::getStoreConfigFlag(Mage_Core_Helper_Log::XML_PATH_DEV_LOG_ENABLED);
            if (empty($file)) {
                $file = Mage::getStoreConfig(Mage_Core_Helper_Log::XML_PATH_DEV_LOG_FILE);
            }
        } catch (Exception) {
            $logActive = true;
        }

        if (!Mage::getIsDeveloperMode() && !$logActive && !$forceLog) {
            return;
        }

        static $loggers = [];

        $maxLogLevel = Mage_Core_Helper_Log::getLogLevelMax();
        $levelValue = Mage_Core_Helper_Log::getLogLevel($level);

        if (!Mage::getIsDeveloperMode() && $levelValue > $maxLogLevel && !$forceLog) {
            return;
        }

        $file = empty($file) ? Mage_Core_Helper_Log::getLogFile() : basename($file);

        try {
            if (!isset($loggers[$file])) {
                // Validate file extension before save. Allowed file extensions: log, txt, html, csv
                $_allowedFileExtensions = Mage_Core_Helper_Log::getAllowedFileExtensions();
                if (! ($extension = pathinfo($file, PATHINFO_EXTENSION)) || ! in_array($extension, $_allowedFileExtensions)) {
                    return;
                }

                $logDir = Mage::getBaseDir('var') . DS . 'log';
                $logFile = $logDir . DS . $file;

                if (!is_dir($logDir)) {
                    mkdir($logDir);
                    chmod($logDir, 0750);
                }

                if (!file_exists($logFile)) {
                    file_put_contents($logFile, '');
                    chmod($logFile, 0640);
                }

                $handler = Mage_Core_Helper_Log::getHandler(Mage::app(), $logFile);

                $logger = new Logger('OpenMage');
                $logger->pushHandler($handler);
                $loggers[$file] = $logger;
            }

            if (is_array($message) || is_object($message)) {
                $message = print_r($message, true);
            }

            $message = addcslashes($message, '<?');
            $loggers[$file]->log($levelValue, $message, $context);
        } catch (Exception) {
        }
    }

    /**
     * Log exception wrapper
     */
    public function logException(Exception $e)
    {
        Mage::logException($e);
    }
}
