<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

use Mage_Core_Helper_Log as HelperLog;
use Monolog\Level;
use Monolog\Logger;

/**
 * Logger model
 *
 * @package    Mage_Core
 */
class Mage_Core_Model_Logger
{
    /** Loggers storage
     *
     * @var Logger[]
     */
    private static array $loggers = [];

    /**
     * Log wrapper
     *
     * @param string   $message
     * @param Level::* $level
     * @param string   $file
     * @param bool     $forceLog
     * @param array    $context  additional context for the log entry
     * @SuppressWarnings("PHPMD.DevelopmentCodeFragment")
     */
    public function log($message, $level = null, $file = '', $forceLog = false, array $context = [])
    {
        if (is_null($file)) {
            $file = '';
        }

        $useStdout = in_array($file, ['php://stdout', 'php://stderr'], true);

        if (!(bool) $forceLog) {
            $forceLog = Mage::getIsDeveloperMode();
        }

        try {
            $logActive = Mage::getStoreConfigFlag(HelperLog::XML_PATH_DEV_LOG_ENABLED);
            if ($file === '') {
                $file = Mage::getStoreConfig(HelperLog::XML_PATH_DEV_LOG_FILE);
            }
        } catch (Throwable) {
            $logActive = true;
        }

        if (!$logActive && !$forceLog) {
            return;
        }

        $levelValue = HelperLog::getLogLevelValue($level);

        if ($levelValue > HelperLog::getLogLevelMaxValue() && !$forceLog) {
            return;
        }

        if (!$useStdout) {
            $file = $file === '' ? HelperLog::getConfigLogFile() : basename($file);
        }

        try {
            if (!isset(self::$loggers[$file])) {
                if ($useStdout) {
                    $logFile = $file;
                } else {
                    $logFile = HelperLog::getLogFilePath($file);
                    if ($logFile === null) {
                        return;
                    }
                }

                $handler = HelperLog::getHandler($logFile);

                $logger = new Logger('OpenMage');
                $logger->pushHandler($handler);
                self::$loggers[$file] = $logger;
            }

            if (is_array($message) || is_object($message)) {
                $message = print_r($message, true);
            }

            $message = addcslashes($message, '<?');
            self::$loggers[$file]->log($levelValue, $message, $context);
        } catch (Exception) {
            // Silent catch
        }
    }

    /**
     * Log exception wrapper
     */
    public function logException(Exception $exception)
    {
        Mage::logException($exception);
    }
}
