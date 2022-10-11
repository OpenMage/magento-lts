<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Object destructor
 *
 * @param mixed $object
 */
function destruct($object)
{
    if (is_array($object)) {
        foreach ($object as $obj) {
            destruct($obj);
        }
    }
    unset($object);
}

/**
 * Translator function
 *
 * @return string
 * @deprecated 1.3
 */
function __()
{
    return Mage::app()->getTranslator()->translate(func_get_args());
}

/**
 * Tiny function to enhance functionality of ucwords
 *
 * Will capitalize first letters and convert separators if needed
 *
 * @param string $str
 * @param string $destSep
 * @param string $srcSep
 * @return string
 */
function uc_words($str, $destSep = '_', $srcSep = '_')
{
    return str_replace(' ', $destSep, ucwords(str_replace($srcSep, ' ', $str)));
}

/**
 * Simple sql format date
 *
 * @param bool $dayOnly
 * @return string
 * @deprecated use equivalent Varien method directly
 * @see Varien_Date::now()
 */
function now($dayOnly = false)
{
    return Varien_Date::now($dayOnly);
}

/**
 * Check whether sql date is empty
 *
 * @param string $date
 * @return boolean
 */
function is_empty_date($date)
{
    return preg_replace('#[ 0:-]#', '', $date)==='';
}

/**
 * @param string $class
 * @return bool|string
 */
function mageFindClassFile($class)
{
    $classFile = uc_words($class, DIRECTORY_SEPARATOR).'.php';
    $found = false;
    foreach (explode(PS, get_include_path()) as $path) {
        $fileName = $path.DS.$classFile;
        if (file_exists($fileName)) {
            $found = $fileName;
            break;
        }
    }
    return $found;
}

/**
 * Custom error handler
 *
 * @param integer $errno
 * @param string $errstr
 * @param string $errfile
 * @param integer $errline
 * @return bool
 */
function mageCoreErrorHandler($errno, $errstr, $errfile, $errline)
{
    if (strpos($errstr, 'DateTimeZone::__construct')!==false) {
        // there's no way to distinguish between caught system exceptions and warnings
        return false;
    }

    $errno = $errno & error_reporting();
    if ($errno == 0) {
        return false;
    }
    if (!defined('E_STRICT')) {
        define('E_STRICT', 2048);
    }
    if (!defined('E_RECOVERABLE_ERROR')) {
        define('E_RECOVERABLE_ERROR', 4096);
    }
    if (!defined('E_DEPRECATED')) {
        define('E_DEPRECATED', 8192);
    }

    // Suppress deprecation warnings on PHP 7.x
    if ($errno == E_DEPRECATED && version_compare(PHP_VERSION, '7.0.0', '>=')) {
        return true;
    }

    // PEAR specific message handling
    if (stripos($errfile.$errstr, 'pear') !== false) {
         // ignore strict and deprecated notices
        if (($errno == E_STRICT) || ($errno == E_DEPRECATED)) {
            return true;
        }
        // ignore attempts to read system files when open_basedir is set
        if ($errno == E_WARNING && stripos($errstr, 'open_basedir') !== false) {
            return true;
        }
    }

    $errorMessage = '';

    switch ($errno) {
        case E_ERROR:
            $errorMessage .= "Error";
            break;
        case E_WARNING:
            $errorMessage .= "Warning";
            break;
        case E_PARSE:
            $errorMessage .= "Parse Error";
            break;
        case E_NOTICE:
            $errorMessage .= "Notice";
            break;
        case E_CORE_ERROR:
            $errorMessage .= "Core Error";
            break;
        case E_CORE_WARNING:
            $errorMessage .= "Core Warning";
            break;
        case E_COMPILE_ERROR:
            $errorMessage .= "Compile Error";
            break;
        case E_COMPILE_WARNING:
            $errorMessage .= "Compile Warning";
            break;
        case E_USER_ERROR:
            $errorMessage .= "User Error";
            break;
        case E_USER_WARNING:
            $errorMessage .= "User Warning";
            break;
        case E_USER_NOTICE:
            $errorMessage .= "User Notice";
            break;
        case E_STRICT:
            $errorMessage .= "Strict Notice";
            break;
        case E_RECOVERABLE_ERROR:
            $errorMessage .= "Recoverable Error";
            break;
        case E_DEPRECATED:
            $errorMessage .= "Deprecated functionality";
            break;
        default:
            $errorMessage .= "Unknown error ($errno)";
            break;
    }

    $errorMessage .= ": {$errstr}  in {$errfile} on line {$errline}";
    if (Mage::getIsDeveloperMode()) {
        throw new Exception($errorMessage);
    } else {
        Mage::log($errorMessage, Zend_Log::ERR);
    }
}

/**
 * @param bool $return
 * @param bool $html
 * @param bool $showFirst
 * @return string
 */
function mageDebugBacktrace($return = false, $html = true, $showFirst = false)
{
    $d = debug_backtrace();
    $out = '';
    if ($html) {
        $out .= "<pre>";
    }
    foreach ($d as $i => $r) {
        if (!$showFirst && $i==0) {
            continue;
        }
        // sometimes there is undefined index 'file'
        @$out .= "[$i] {$r['file']}:{$r['line']}\n";
    }
    if ($html) {
        $out .= "</pre>";
    }
    if ($return) {
        return $out;
    } else {
        echo $out;
    }
}

function mageSendErrorHeader()
{
    return;
    if (!isset($_SERVER['SCRIPT_NAME'])) {
        return;
    }
    $action = Mage::app()->getRequest()->getBasePath()."bugreport.php";
    echo '<form id="error_report" method="post" style="display:none" action="'.$action.'"><textarea name="error">';
}

function mageSendErrorFooter()
{
    return;
    if (!isset($_SERVER['SCRIPT_NAME'])) {
        return;
    }
    echo '</textarea></form><script type="text/javascript">document.getElementById("error_report").submit()</script>';
    exit;
}

/**
 * @param string $path
 */
function mageDelTree($path)
{
    if (is_dir($path)) {
        $entries = scandir($path);
        foreach ($entries as $entry) {
            if ($entry != '.' && $entry != '..') {
                mageDelTree($path.DS.$entry);
            }
        }
        @rmdir($path);
    } else {
        @unlink($path);
    }
}

/**
 * @param string $string
 * @param string $delimiter
 * @param string $enclosure
 * @param string $escape
 * @return array
 */
function mageParseCsv($string, $delimiter = ",", $enclosure = '"', $escape = '\\')
{
    $elements = explode($delimiter, $string);
    for ($i = 0; $i < count($elements); $i++) {
        $nquotes = substr_count($elements[$i], $enclosure);
        if ($nquotes %2 == 1) {
            for ($j = $i+1; $j < count($elements); $j++) {
                if (substr_count($elements[$j], $enclosure) > 0) {
                    // Put the quoted string's pieces back together again
                    array_splice(
                        $elements,
                        $i,
                        $j-$i+1,
                        implode($delimiter, array_slice($elements, $i, $j-$i+1))
                    );
                    break;
                }
            }
        }
        if ($nquotes > 0) {
            // Remove first and last quotes, then merge pairs of quotes
            $qstr =& $elements[$i];
            $qstr = substr_replace($qstr, '', strpos($qstr, $enclosure), 1);
            $qstr = substr_replace($qstr, '', strrpos($qstr, $enclosure), 1);
            $qstr = str_replace($enclosure.$enclosure, $enclosure, $qstr);
        }
    }
    return $elements;
}

/**
 * @param string $dir
 * @return bool
 */
function is_dir_writeable($dir)
{
    if (is_dir($dir) && is_writable($dir)) {
        if (stripos(PHP_OS, 'win') === 0) {
            $dir    = ltrim($dir, DIRECTORY_SEPARATOR);
            $file   = $dir . DIRECTORY_SEPARATOR . uniqid(mt_rand()).'.tmp';
            $exist  = file_exists($file);
            $fp     = @fopen($file, 'a');
            if ($fp === false) {
                return false;
            }
            fclose($fp);
            if (!$exist) {
                unlink($file);
            }
        }
        return true;
    }
    return false;
}

if (!function_exists('sys_get_temp_dir')) {
    // Based on http://www.phpit.net/
    // article/creating-zip-tar-archives-dynamically-php/2/
    /**
     * @return bool|string
     */
    function sys_get_temp_dir()
    {
        // Try to get from environment variable
        if (!empty($_ENV['TMP'])) {
            return realpath($_ENV['TMP']);
        } elseif (!empty($_ENV['TMPDIR'])) {
            return realpath($_ENV['TMPDIR']);
        } elseif (!empty($_ENV['TEMP'])) {
            return realpath($_ENV['TEMP']);
        } else {
            // Try to use system's temporary directory
            // as random name shouldn't exist
            $temp_file = tempnam(md5(uniqid(rand(), true)), '');
            if ($temp_file) {
                $temp_dir = realpath(dirname($temp_file));
                unlink($temp_file);
                return $temp_dir;
            } else {
                return false;
            }
        }
    }
}

if (!function_exists('hash_equals')) {
    /**
     * Compares two strings using the same time whether they're equal or not.
     * A difference in length will leak
     *
     * @param string $known_string
     * @param string $user_string
     * @return boolean Returns true when the two strings are equal, false otherwise.
     */
    function hash_equals($known_string, $user_string)
    {
        $result = 0;

        if (!is_string($known_string)) {
            trigger_error("hash_equals(): Expected known_string to be a string", E_USER_WARNING);
            return false;
        }

        if (!is_string($user_string)) {
            trigger_error("hash_equals(): Expected user_string to be a string", E_USER_WARNING);
            return false;
        }

        if (strlen($known_string) != strlen($user_string)) {
            return false;
        }

        for ($i = 0; $i < strlen($known_string); $i++) {
            $result |= (ord($known_string[$i]) ^ ord($user_string[$i]));
        }

        return $result === 0;
    }
}

/**
 * polyfill for PHP 8.0 function "str_contains"
 */
if (!function_exists('str_contains')) {
    /**
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    function str_contains($haystack, $needle)
    {
        return $needle === '' || strpos($haystack, $needle) !== false;
    }
}

/**
 * polyfill for PHP 8.0 function "str_starts_with"
 */
if (!function_exists('str_starts_with')) {
    /**
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    function str_starts_with($haystack, $needle)
    {
        return strncmp($haystack, $needle, \strlen($needle)) === 0;
    }
}

/**
 * polyfill for PHP 8.0 function "str_ends_with"
 */
if (!function_exists('str_ends_with')) {
    /**
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    function str_ends_with($haystack,  $needle)
    {
        return $needle === '' || ($haystack !== '' && substr_compare($haystack, $needle, -\strlen($needle)) === 0);
    }
}

/**
 * polyfill for PHP 7.3 function "is_countable"
 */
if (!function_exists('is_countable')) {
    /**
     * @param mixed $value
     * @return bool
     */
    function is_countable($value) {
        return is_array($value) || $value instanceof Countable || $value instanceof ResourceBundle || $value instanceof SimpleXMLElement;
    }
}
