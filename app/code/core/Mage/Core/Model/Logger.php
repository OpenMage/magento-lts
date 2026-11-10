<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

use Mage_Core_Helper_Log as HelperLog;
use Monolog\Handler\HandlerInterface;
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
        $file ??= '';

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
                $logger = $this->createLogger($file, $useStdout);
                if (!$logger instanceof Logger) {
                    return;
                }

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
     * Create the logger of a log file with the configured handler
     *
     * The channel is the log file name without extension (e.g. "system", "exception").
     * If syslog is configured but its handler cannot be created, the file handler is used
     * and the reason is logged there, so a wrong setting never drops messages.
     */
    private function createLogger(string $file, bool $useStdout): ?Logger
    {
        $handler = null;
        $fallbackReason = null;

        // without a log name there is no destination: the file branch below returns null
        $handlerType = $useStdout || $file === '' ? HelperLog::HANDLER_FILE : HelperLog::getConfigHandler();
        if ($handlerType !== HelperLog::HANDLER_FILE) {
            try {
                $handler = HelperLog::getSyslogHandler($handlerType);
            } catch (Exception $exception) {
                $fallbackReason = $exception->getMessage();
            }
        }

        if (!$handler instanceof HandlerInterface) {
            $logFile = $useStdout ? $file : HelperLog::getLogFilePath($file);
            if ($logFile === null) {
                return null;
            }

            $handler = HelperLog::getHandler($logFile);
        }

        $logger = new Logger(pathinfo($file, PATHINFO_FILENAME));
        $logger->pushHandler($handler);
        if ($fallbackReason !== null) {
            $logger->warning('Syslog handler unavailable, logging to file instead: ' . $fallbackReason);
        }

        return $logger;
    }

    /**
     * Log exception wrapper
     */
    public function logException(Exception $exception)
    {
        Mage::logException($exception);
    }
}
