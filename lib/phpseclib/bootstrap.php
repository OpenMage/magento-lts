<?php
/**
 * Bootstrapping File for phpseclib
 *
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 */
if (extension_loaded('mbstring')) {
    // 2 - MB_OVERLOAD_STRING
    if (ini_get('mbstring.func_overload') & 2) {
        throw new \UnexpectedValueException(
            'Overloading of string functions using mbstring.func_overload ' .
            'is not supported by phpseclib.'
        );
    }
}

spl_autoload_register(function($className) {
    $path = explode('\\', $className);
    if (array_shift($path) == 'phpseclib') {
        return include str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
    }
});
