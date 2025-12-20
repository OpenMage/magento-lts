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
use Monolog\Handler\FormattableHandlerInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;

/**
 * Core logging helper
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

    public const LOG_FORMAT_MONOLOG                    = 1;

    public const LOG_FORMAT_RFC5424                    = 2;

    public const LOG_FORMAT_PSR_3                      = 3;

    protected $_moduleName = 'Mage_Core';

    /**
     * Retrieve allowed file extensions for log files
     *
     * @return string[]
     */
    public static function getConfigAllowedFileExtensions(): array
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
     * Retrieve log file name from configuration
     */
    public static function getConfigLogFile(): string
    {
        return (string) Mage::getConfig()->getNode(
            self::XML_PATH_DEV_LOG_FILE,
            Mage_Core_Model_Store::DEFAULT_CODE,
        );
    }

    /**
     * Retrieve log handler instance
     */
    public static function getHandler(string $logFile, Level $logLevel = Level::Debug): HandlerInterface
    {
        $writerModel = (string) Mage::getConfig()->getNode('global/log/core/writer_model');
        if (!Mage::app() || !$writerModel) {
            $handler = new StreamHandler($logFile, $logLevel);
        } else {
            $handler = new $writerModel($logFile, $logLevel);
        }

        if ($handler instanceof FormattableHandlerInterface) {
            $format = '%datetime% %level_name% (%level%): %message% %context% %extra%' . PHP_EOL;
            $handler->setFormatter(Mage_Core_Helper_Log::getLineFormatter(
                format: $format,
                allowInlineLineBreaks: true,
                ignoreEmptyContextAndExtra: true,
                includeStacktraces: true,
            ));
        }

        return $handler;
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
        return new LineFormatter(
            format: $format,
            dateFormat: $dateFormat,
            allowInlineLineBreaks: $allowInlineLineBreaks,
            ignoreEmptyContextAndExtra: $ignoreEmptyContextAndExtra,
            includeStacktraces: $includeStacktraces,
        );
    }

    /**
     * Retrieve log level value
     *
     * @param self::LOG_FORMAT_* $format
     */
    public static function getLogLevelValue(null|int|Level|string $level, int $format = self::LOG_FORMAT_RFC5424): int|string
    {
        return match ($format) {
            self::LOG_FORMAT_MONOLOG => self::getLogLevel($level)->value,
            self::LOG_FORMAT_PSR_3 => self::getLogLevel($level)->toPsrLogLevel(),
            self::LOG_FORMAT_RFC5424 => self::getLogLevel($level)->toRFC5424Level(),
        };
    }

    /**
     * Normalize log level to Monolog Level integer value
     */
    public static function getLogLevel(null|int|Level|string $level): Level
    {
        if (is_null($level)) {
            return Level::Debug;
        }

        if ($level instanceof Level) {
            return $level;
        }

        if (is_numeric($level)) {
            $level = (int) $level;
        }

        if (is_string($level)) {
            // PSR 3 Log level
            try {
                return Level::fromName($level);
            } catch (UnhandledMatchError) {
                return Level::Debug;
            }
        } else {
            // change Monolog into RFC 5424 Log Level
            return (match ($level) {
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
   }

    /**
     * Retrieve maximum log level from configuration
     */
    public static function getLogLevelMaxValue(): int
    {
        try {
            $maxLogLevel = Mage::getStoreConfigAsInt(self::XML_PATH_DEV_LOG_MAX_LEVEL);
        } catch (Throwable) {
            $maxLogLevel = Level::Debug;
        }

        return self::getLogLevelValue($maxLogLevel);
    }
}
