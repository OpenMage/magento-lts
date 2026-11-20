<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Autoload
 */

/**
 * Classes source autoload
 */
class Varien_Autoload
{
    /**
     * @var Varien_Autoload
     */
    protected static $_instance;

    private static ?bool $canAskOpcache = null;

    /**
     * Singleton pattern implementation
     *
     * @return Varien_Autoload
     */
    public static function instance()
    {
        if (!self::$_instance) {
            self::$_instance = new Varien_Autoload();
        }

        return self::$_instance;
    }

    /**
     * Register SPL autoload function
     */
    public static function register()
    {
        spl_autoload_register([self::instance(), 'autoload']);
    }

    /**
     * Load class source code
     *
     * @param string $class
     */
    public function autoload($class)
    {
        $path = str_replace(' ', DIRECTORY_SEPARATOR, ucwords(str_replace('_', ' ', $class))) . '.php';

        // A class file OPcache already holds under its relative path is included by that path: OPcache then
        // serves it from memory without looking it up in every include_path directory first.
        if (self::canAskOpcache() && opcache_is_script_cached($path)) {
            return include $path;
        }

        /** @see https://stackoverflow.com/a/5504486/716029 */
        $found = stream_resolve_include_path($path);
        if ($found !== false) {
            // With OPcache, include the relative path, so the next request finds the file under that key
            return include self::canAskOpcache() ? $path : $found;
        }
    }

    /**
     * Whether opcache_is_script_cached() can answer for this process: OPcache is enabled with shared memory,
     * and the API isn't restricted (opcache.restrict_api would raise a warning on every call)
     */
    private static function canAskOpcache(): bool
    {
        return self::$canAskOpcache ??= function_exists('opcache_is_script_cached')
            && filter_var(ini_get(PHP_SAPI === 'cli' ? 'opcache.enable_cli' : 'opcache.enable'), FILTER_VALIDATE_BOOLEAN)
            && !filter_var(ini_get('opcache.file_cache_only'), FILTER_VALIDATE_BOOLEAN)
            && (string) ini_get('opcache.restrict_api') === '';
    }
}
