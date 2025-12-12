<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

use Monolog\Level;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;

/**
 * Core data helper
 *
 * @package    Mage_Core
 */
class Mage_Core_Helper_Log extends Mage_Core_Helper_Abstract
{
    public const XML_PATH_DEV_LOG_ENABLED              = 'dev/log/active';

    public const XML_PATH_DEV_LOG_ALLOWED_EXTENSIONS   = 'dev/log/allowedFileExtensions';

    public const XML_PATH_DEV_LOG_FILE                 = 'dev/log/file';

    public const XML_PATH_DEV_LOG_EXCEPTION_FILE       = 'dev/log/exception_file';

    public const XML_PATH_DEV_LOG_MAX_LEVEL            = 'dev/log/max_level';

    protected $_moduleName = 'Mage_Core';

    /**
     * Retrieve allowed file extensions for log files
     *
     * @return string[]
     */
    public static function getAllowedFileExtensions(): array
    {
        return explode(
            ',',
            (string) Mage::getConfig()->getNode(
                self::XML_PATH_DEV_LOG_ALLOWED_EXTENSIONS,
                Mage_Core_Model_Store::DEFAULT_CODE,
            ),
        );
    }

    /**
     * Retrieve log handler instance
     */
    public static function getHandler(null|Mage_Core_Model_App $app, string $logFile, Level $logLevel = Level::Debug): HandlerInterface
    {
        $writerModel = (string) Mage::getConfig()->getNode('global/log/core/writer_model');
        if (!$app || !$writerModel) {
            return new StreamHandler($logFile, $logLevel);
        } else {
            return new $writerModel($logFile, $logLevel);
        }
    }

    /**
     * Retrieve line formatter instance
     */
    public static function getLineFormatter(
        ?string $format = null,
        ?string $dateFormat = null,
        bool $allowInlineLineBreaks = false,
        bool $ignoreEmptyContextAndExtra = false,
        bool $includeStacktraces = false,
    ): FormatterInterface {
        return new LineFormatter($format, $dateFormat, $allowInlineLineBreaks, $ignoreEmptyContextAndExtra, $includeStacktraces);
    }

    /**
     * Retrieve log file name from configuration
     */
    public static function getLogFile(): string
    {
        return (string) Mage::getConfig()->getNode(
            self::XML_PATH_DEV_LOG_FILE,
            Mage_Core_Model_Store::DEFAULT_CODE,
        );
    }

    /**
     * Normalize log level to Monolog Level integer value
     *
     * @param null|int|Level::*|string $level
     */
    public static function getLogLevel($level): int
    {
        if (is_numeric($level)) {
            $level = (int) $level;
        }

        if ($level instanceof Level) {
            $levelValue = $level;
        } elseif (is_null($level)) {
            $levelValue = Level::Debug;
        } elseif (is_string($level)) {
            // PSR 3 Log level
            try {
                $levelValue = Level::fromName($level);
            } catch (UnhandledMatchError) {
                $levelValue = Level::Debug; // fallback to debug level
            }
        } else {
            // change RFC 5424 Log Level into Monolog.
            $levelValue = (match ($level) {
                7, 100 => Level::Debug,
                6, 200 => Level::Info,
                5, 250 => Level::Notice,
                4, 300 => Level::Warning,
                3, 400 => Level::Error,
                2, 500 => Level::Critical,
                1, 550 => Level::Alert,
                0, 600 => Level::Emergency,
                default => Level::Debug,
            });
        }

        return $levelValue->value;
    }

    /**
     * Retrieve maximum log level from configuration
     */
    public static function getLogLevelMax(): int
    {
        try {
            $maxLogLevel = Mage::getStoreConfigAsInt(self::XML_PATH_DEV_LOG_MAX_LEVEL);
        } catch (Throwable) {
            $maxLogLevel = Level::Debug;
        }

        return self::getLogLevel($maxLogLevel);
    }
}
