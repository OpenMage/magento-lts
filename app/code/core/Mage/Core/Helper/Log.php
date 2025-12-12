<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

use Monolog\Level;

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
     * Normalize log level to Monolog Level integer value
     *
     * @param null|int|Level::*|string $level
     */
    public static function getLogLevel($level): int
    {
        if ($level instanceof Level) {
            $levelValue = $level;
        } elseif (is_null($level)) {
            $levelValue = Level::Debug;
        } elseif (is_string($level) && !is_numeric($level)) {
            // PSR 3 Log level
            try {
                $levelValue = Level::fromName($level);
            } catch (UnhandledMatchError) {
                $levelValue = Level::Debug; // fallback to debug level
            }
        } else {
            $levelValue = (int) $level;
            // change RFC 5424 Log Level into Monolog.
            if ($levelValue >= 0 && $levelValue <= 7) {
                $levelValue = (match ($levelValue) {
                    7 => Level::Debug,
                    6 => Level::Info,
                    5 => Level::Notice,
                    4 => Level::Warning,
                    3 => Level::Error,
                    2 => Level::Critical,
                    1 => Level::Alert,
                    0 => Level::Emergency,
                });
            } else {
                // unknown levels are treated as debug
                $levelValue = Level::Debug; // fallback to debug level
            }
        }

        return $levelValue->value;
    }

    /**
     * Retrieve allowed file extensions for log files
     *
     * @return string[]
     */
    public static function getAlowedFileExtensions(): array
    {
        return explode(
            ',',
            (string) Mage::getConfig()->getNode(
                self::XML_PATH_DEV_LOG_ALLOWED_EXTENSIONS,
                Mage_Core_Model_Store::DEFAULT_CODE,
            ),
        );
    }

    public static function getLogFile(): string
    {
        return (string) Mage::getConfig()->getNode(
            self::XML_PATH_DEV_LOG_FILE,
            Mage_Core_Model_Store::DEFAULT_CODE,
        );
    }

    /**
     * Retrieve maximum log level from configuration
     */
    public static function getLogLevelMax(): int
    {
        try {
            $maxLogLevel = Mage::getStoreConfigAsInt(self::XML_PATH_DEV_LOG_MAX_LEVEL);
        } catch (Throwable) {
            $maxLogLevel = Level::Debug->value;
        }

        return self::getLogLevel($maxLogLevel);
    }
}
