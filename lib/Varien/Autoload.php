<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Varien
 * @package    Varien_Autoload
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2018-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
