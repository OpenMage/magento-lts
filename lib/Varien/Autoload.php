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

        // Include the relative path directly instead of resolving it first: OPcache can then serve
        // the script from its include_path cache without stat-ing every include path entry.
        // A missing file only raises warnings from this file; those are ignored, so other autoloaders
        // and class_exists() keep working. Errors from the included file reach the previous handler.
        $previousHandler = set_error_handler(
            static function (int $errno, string $errstr, string $errfile = '', int $errline = 0) use (&$previousHandler): mixed {
                if ($errno === E_WARNING && $errfile === __FILE__) {
                    return true;
                }

                // Pass the return value on unchanged: PHP's own handler only runs when it is exactly false
                return $previousHandler === null ? false : $previousHandler($errno, $errstr, $errfile, $errline);
            },
        );

        try {
            $result = include $path;
        } finally {
            restore_error_handler();
        }

        if ($result !== false) {
            return $result;
        }
    }
}
