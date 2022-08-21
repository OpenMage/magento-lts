<?php

namespace OpenMage\Scripts;

use Composer\Script\Event;

class Devel
{
    /** @var Composer\Composer $composer */
    static $composer;

    /** @var string $vendorPath */
    static $vendorPath;

    /** @var string $binPath */
    static $binPath;

    private static function _init(Event $event)
    {
        self::$composer = $event->getComposer();
        self::$vendorPath = self::$composer->getConfig()->get('vendor-dir');
        self::$binPath = self::$composer->getConfig()->get('bin-dir');
    }

    /**
     * Setup PHP_CodeSniffer Environment
     *
     * @param Event $event
     */
    public static function setupPhpCs(Event $event)
    {
        self::_init($event);

        if ($event->isDevMode() === false) {
            return;
        }

        if (!function_exists('exec')) {
            echo "exec() has been disabled for security reasons, skipping...\n";
            return;
        }

        $phpcs = PHP_BINARY . ' ' . self::$binPath . DIRECTORY_SEPARATOR . 'phpcs';

        $paths = implode(',', [
            self::$vendorPath . '/phpcompatibility/php-compatibility',
            self::$vendorPath . '/magento-ecg/coding-standard',
        ]);

        $output = [];

        exec("$phpcs --config-set installed_paths $paths", $output);
        exec("$phpcs -i", $output);

        echo implode("\n", $output) . "\n";
    }
}
