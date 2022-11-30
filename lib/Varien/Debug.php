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
 * @category   Varien
 * @package    Varien_Debug
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Varien Debug methods
 *
 * @category   Varien
 * @package    Varien_Debug
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Varien_Debug
{
    public static $argLength = 16;
    /**
     * Magento Root path
     *
     * @var string
     */
    protected static $_filePath;

    /**
     * Retrieve real root path with last directory separator
     *
     * @return string
     */
    public static function getRootPath()
    {
        if (is_null(self::$_filePath)) {
            if (defined('BP')) {
                self::$_filePath = BP;
            } else {
                self::$_filePath = dirname(__DIR__);
            }
        }
        return self::$_filePath;
    }

    /**
     * Prints or return a backtrace
     *
     * @param bool $return      return or print
     * @param bool $html        output in HTML format
     * @param bool $withArgs    add short argumets of methods
     * @return string|bool
     */
    public static function backtrace($return = false, $html = true, $withArgs = true)
    {
        $trace  = debug_backtrace();
        return self::trace($trace, $return, $html, $withArgs);
    }

    /**
     * Prints or return a trace
     *
     * @param array $trace      trace array
     * @param bool $return      return or print
     * @param bool $html        output in HTML format
     * @param bool $withArgs    add short argumets of methods
     * @return string|bool
     */
    public static function trace(array $trace, $return = false, $html = true, $withArgs = true)
    {
        $out    = '';
        if ($html) {
            $out .= '<pre>';
        }

        foreach ($trace as $i => $data) {
            // skip self
            if ($i == 0) {
                continue;
            }

            // prepare method argments
            $args = [];
            if (isset($data['args']) && $withArgs) {
                foreach ($data['args'] as $arg) {
                    $args[] = self::_formatCalledArgument($arg);
                }
            }

            // prepare method's name
            if (isset($data['class']) && isset($data['function'])) {
                if (isset($data['object']) && get_class($data['object']) != $data['class']) {
                    $className = get_class($data['object']) . '[' . $data['class'] . ']';
                } else {
                    $className = $data['class'];
                }
                if (isset($data['object'])) {
                    $className .= sprintf('#%s#', spl_object_hash($data['object']));
                }

                $methodName = sprintf(
                    '%s%s%s(%s)',
                    $className,
                    isset($data['type']) ? $data['type'] : '->',
                    $data['function'],
                    implode(', ', $args)
                );
            } elseif (isset($data['function'])) {
                $methodName = sprintf('%s(%s)', $data['function'], implode(', ', $args));
            }

            if (isset($data['file'])) {
                $pos = strpos($data['file'], self::getRootPath());
                if ($pos !== false) {
                    $data['file'] = substr($data['file'], strlen(self::getRootPath()) + 1);
                }
                $fileName = sprintf('%s:%d', $data['file'], $data['line']);
            } else {
                $fileName = false;
            }

            if ($fileName) {
                $out .= sprintf('#%d %s called at [%s]', $i, $methodName, $fileName);
            } else {
                $out .= sprintf('#%d %s', $i, $methodName);
            }

            $out .= "\n";
        }

        if ($html) {
            $out .= '</pre>';
        }

        if ($return) {
            return $out;
        } else {
            echo $out;
            return true;
        }
    }

    /**
     * Format argument in called method
     *
     * @param mixed $arg
     */
    protected static function _formatCalledArgument($arg)
    {
        $out = '';
        if (is_object($arg)) {
            $out .= sprintf("&%s#%s#", get_class($arg), spl_object_hash($arg));
        } elseif (is_resource($arg)) {
            $out .= '#[' . get_resource_type($arg) . ']';
        } elseif (is_array($arg)) {
            $isAssociative = false;
            $args = [];
            foreach ($arg as $k => $v) {
                if (!is_numeric($k)) {
                    $isAssociative = true;
                }
                $args[$k] = self::_formatCalledArgument($v);
            }
            if ($isAssociative) {
                $arr = [];
                foreach ($args as $k => $v) {
                    $arr[] = self::_formatCalledArgument($k) . ' => ' . $v;
                }
                $out .= 'array(' . implode(', ', $arr) . ')';
            } else {
                $out .= 'array(' . implode(', ', $args) . ')';
            }
        } elseif (is_null($arg)) {
            $out .= 'NULL';
        } elseif (is_numeric($arg) || is_float($arg)) {
            $out .= $arg;
        } elseif (is_string($arg)) {
            if (strlen($arg) > self::$argLength) {
                $arg = substr($arg, 0, self::$argLength) . "...";
            }
            $arg = strtr($arg, ["\t" => '\t', "\r" => '\r', "\n" => '\n', "'" => '\\\'']);
            $out .= "'" . $arg . "'";
        } elseif (is_bool($arg)) {
            $out .= $arg === true ? 'true' : 'false';
        }

        return $out;
    }
}
