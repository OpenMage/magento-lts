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
        /** @see https://stackoverflow.com/a/5504486/716029 */
        $found = stream_resolve_include_path($path);
        if ($found !== false) {
            return include $found;
        }
    }
}
