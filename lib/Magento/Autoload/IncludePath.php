<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Magento
 * @package    Magento_Autoload
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Autoload;

class IncludePath
{
    /**
     * Namespaces separator
     */
    public const NS_SEPARATOR = '\\';

    /**
     * Find a file in include path
     *
     * @param string $class
     * @return string|bool
     */
    public static function getFile($class)
    {
        $relativePath = self::getFilePath($class);
        return stream_resolve_include_path($relativePath);
    }

    /**
     * Get relative file path for specified class
     *
     * @static
     * @param string $class
     * @return string
     */
    public static function getFilePath($class)
    {
        if (strpos($class, self::NS_SEPARATOR) !== false) {
            $class = ltrim(str_replace(self::NS_SEPARATOR, '_', $class), '_');
        }
        return str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
    }

    /**
     * Add specified path(s) to the current include_path
     *
     * @param string|array $path
     * @param bool $prepend Whether to prepend paths or to append them
     */
    public static function addIncludePath($path, $prepend = true)
    {
        $includePathExtra = implode(PATH_SEPARATOR, (array)$path);
        $includePath = get_include_path();
        $pathSeparator = $includePath && $includePathExtra ? PATH_SEPARATOR : '';
        if ($prepend) {
            $includePath = $includePathExtra . $pathSeparator . $includePath;
        } else {
            $includePath = $includePath . $pathSeparator . $includePathExtra;
        }
        set_include_path($includePath);
    }

    /**
     * Resolve a class file and include it
     *
     * @param $class
     */
    public static function load($class)
    {
        $file = self::getFile($class);
        if ($file) {
            include $file;
        }
    }
}
