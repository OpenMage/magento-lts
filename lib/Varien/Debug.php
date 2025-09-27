<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Debug
 */

/**
 * Varien Debug methods
 *
 * @package    Varien_Debug
 */
class Varien_Debug
{
    public static $argLength = 16;
    /**
     * Magento Root path
     *
     * @var string|null
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

            // prepare method arguments
            $args = [];
            if (isset($data['args']) && $withArgs) {
                foreach ($data['args'] as $arg) {
                    $args[] = self::_formatCalledArgument($arg);
                }
            }

            // prepare method's name
            $methodName = '';
            if (isset($data['class']) && isset($data['function'])) {
                if (isset($data['object']) && $data['object']::class != $data['class']) {
                    $className = $data['object']::class . '[' . $data['class'] . ']';
                } else {
                    $className = $data['class'];
                }
                if (isset($data['object'])) {
                    $className .= sprintf('#%s#', spl_object_hash($data['object']));
                }

                $methodName = sprintf(
                    '%s%s%s(%s)',
                    $className,
                    $data['type'] ?? '->',
                    $data['function'],
                    implode(', ', $args),
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
            $out .= sprintf('&%s#%s#', $arg::class, spl_object_hash($arg));
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
        } elseif (is_numeric($arg)) {
            $out .= $arg;
        } elseif (is_string($arg)) {
            if (strlen($arg) > self::$argLength) {
                $arg = substr($arg, 0, self::$argLength) . '...';
            }
            $arg = strtr($arg, ["\t" => '\t', "\r" => '\r', "\n" => '\n', "'" => '\\\'']);
            $out .= "'" . $arg . "'";
        } elseif (is_bool($arg)) {
            $out .= $arg === true ? 'true' : 'false';
        }

        return $out;
    }
}
