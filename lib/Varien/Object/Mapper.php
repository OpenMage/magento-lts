<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Object
 */

/**
 * Utility class for mapping data between objects or arrays
 */
class Varien_Object_Mapper
{
    /**
     * Convert data from source to target item using map array
     *
     * Will get or set data with generic or magic, or specified Varien Object methods, or with array keys
     * from or to Varien_Object or array
     * :)
     *
     * Map must either be associative array of keys from=>to
     * or a numeric array of keys, assuming from = to
     *
     * Defaults must be assoc array of keys => values. Target will get default, if the value is not present in source
     * If the source has getter defined instead of magic method, the value will be taken only if not empty
     *
     * Callbacks explanation (when $source or $target is not array):
     *   for $source:
     *     <Varien_Object> => $from->getData($key) (default)
     *     array(<Varien_Object>, <method>) => $from->$method($key)
     *   for $target (makes sense only for Varien_Object):
     *     <Varien_Object> => $from->setData($key, <from>)
     *     array(<Varien_Object>, <method>) => $from->$method($key, <from>)
     *
     * @param  array|Varien_Object $source
     * @param  array|Varien_Object $target
     * @return array|Varien_Object
     */
    public static function &accumulateByMap($source, $target, array $map, array $defaults = [])
    {
        $get = 'getData';
        if (is_array($source) && isset($source[0]) && is_object($source[0]) && isset($source[1]) && is_string($source[1]) && is_callable($source)) {
            [$source, $get] = $source;
        }

        $fromIsArray = is_array($source);
        $fromIsVO    = $source instanceof Varien_Object;

        $set = 'setData';
        if (is_array($target) && isset($target[0]) && is_object($target[0]) && isset($target[1]) && is_string($target[1]) && is_callable($target)) {
            [$target, $set] = $target;
        }

        $toIsArray = is_array($target);
        $toIsVO    = $target instanceof Varien_Object;

        foreach ($map as $keyFrom => $keyTo) {
            if (!is_string($keyFrom)) {
                $keyFrom = $keyTo;
            }

            if ($fromIsArray) {
                if (array_key_exists($keyFrom, $source)) {
                    if ($toIsArray) {
                        $target[$keyTo] = $source[$keyFrom];
                    } elseif ($toIsVO) {
                        $target->$set($keyTo, $source[$keyFrom]);
                    }
                }
            } elseif ($fromIsVO) {
                // get value if (any) value is found as in magic data or a non-empty value with declared getter
                $value = null;
                if ($shouldGet = $source->hasData($keyFrom)) {
                    $value = $source->$get($keyFrom);
                } elseif (method_exists($source, $get)) {
                    $value = $source->$get($keyFrom);
                    if ($value) {
                        $shouldGet = true;
                    }
                }

                if ($shouldGet) {
                    if ($toIsArray) {
                        $target[$keyTo] = $value;
                    } elseif ($toIsVO) {
                        $target->$set($keyTo, $value);
                    }
                }
            }
        }

        foreach ($defaults as $keyTo => $value) {
            if ($toIsArray) {
                if (!isset($target[$keyTo])) {
                    $target[$keyTo] = $value;
                }
            } elseif ($toIsVO) {
                if (!$target->hasData($keyTo)) {
                    $target->$set($keyTo, $value);
                }
            }
        }

        return $target;
    }
}
