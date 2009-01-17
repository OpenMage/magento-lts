<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

#error_log('========================'."\n", 3, 'var/log/magento.log');


/**
 * Disable magic quotes in runtime if needed
 *
 * @link http://us3.php.net/manual/en/security.magicquotes.disabling.php
 */
if (get_magic_quotes_gpc()) {
    function mageUndoMagicQuotes($array, $topLevel=true) {
        $newArray = array();
        foreach($array as $key => $value) {
            if (!$topLevel) {
                $newKey = stripslashes($key);
                if ($newKey!==$key) {
                    unset($array[$key]);
                }
                $key = $newKey;
            }
            $newArray[$key] = is_array($value) ? mageUndoMagicQuotes($value, false) : stripslashes($value);
        }
        return $newArray;
    }
    $_GET = mageUndoMagicQuotes($_GET);
    $_POST = mageUndoMagicQuotes($_POST);
    $_COOKIE = mageUndoMagicQuotes($_COOKIE);
    $_REQUEST = mageUndoMagicQuotes($_REQUEST);
}

/**
 * Class autoload
 *
 * @todo change to spl_autoload_register
 * @param string $class
 */
function __autoload($class)
{
    if (strpos($class, '/')!==false) {
        return;
    }
    $classFile = uc_words($class, DS).'.php';

    //$a = explode('_', $class);
    //Varien_Profiler::start('AUTOLOAD');
    //Varien_Profiler::start('AUTOLOAD: '.$a[0]);

    include($classFile);

    //Varien_Profiler::stop('AUTOLOAD');
    //Varien_Profiler::stop('AUTOLOAD: '.$a[0]);
}

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
    } elseif (is_object($object)) {
        if (in_array('__destruct', get_class_methods($object))) {
            $object->__destruct();
        }
    }
    unset($object);
}

/**
 * Translator function
 *
 * @param string $text the text to translate
 * @param mixed optional parameters to use in sprintf
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
function uc_words($str, $destSep='_', $srcSep='_')
{
    return str_replace(' ', $destSep, ucwords(str_replace($srcSep, ' ', $str)));
}

/**
 * Simple sql format date
 *
 * @param string $format
 * @return string
 */
function now($dayOnly=false)
{
    return date($dayOnly ? 'Y-m-d' : 'Y-m-d H:i:s');
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

function mageFindClassFile($class)
{
    $classFile = uc_words($class, DS).'.php';
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
 */
function mageCoreErrorHandler($errno, $errstr, $errfile, $errline){
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

    // PEAR specific message handling
    if (stripos($errfile.$errstr, 'pear') !== false) {
         // ignore strict notices
        if ($errno == E_STRICT) {
            return false;
        }
        // ignore attempts to read system files when open_basedir is set
        if ($errno == E_WARNING && stripos($errstr, 'open_basedir') !== false) {
            return false;
        }
    }

    $errorMessage = '';

    switch($errno){
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
        default:
            $errorMessage .= "Unknown error ($errno)";
            break;
    }

    $errorMessage .= ": {$errstr}  in {$errfile} on line {$errline}";

    throw new Exception($errorMessage);
}

function mageDebugBacktrace($return=false, $html=true, $showFirst=false)
{
    $d = debug_backtrace();
    $out = '';
    if ($html) $out .= "<pre>";
    foreach ($d as $i=>$r) {
        if (!$showFirst && $i==0) {
            continue;
        }
        // sometimes there is undefined index 'file'
        @$out .= "[$i] {$r['file']}:{$r['line']}\n";
    }
    if ($html) $out .= "</pre>";
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

function mageDelTree($path) {
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

function mageParseCsv($string, $delimiter=",", $enclosure='"', $escape='\\')
{
    $elements = explode($delimiter, $string);
    for ($i = 0; $i < count($elements); $i++) {
        $nquotes = substr_count($elements[$i], $enclosure);
        if ($nquotes %2 == 1) {
            for ($j = $i+1; $j < count($elements); $j++) {
                if (substr_count($elements[$j], $enclosure) > 0) {
                    // Put the quoted string's pieces back together again
                    array_splice($elements, $i, $j-$i+1,
                        implode($delimiter, array_slice($elements, $i, $j-$i+1)));
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


if ( !function_exists('sys_get_temp_dir') ) {
    // Based on http://www.phpit.net/
    // article/creating-zip-tar-archives-dynamically-php/2/
    function sys_get_temp_dir()
    {
        // Try to get from environment variable
        if ( !empty($_ENV['TMP']) ) {
            return realpath( $_ENV['TMP'] );
        }
        else if ( !empty($_ENV['TMPDIR']) ) {
            return realpath( $_ENV['TMPDIR'] );
        }
        else if ( !empty($_ENV['TEMP']) ) {
            return realpath( $_ENV['TEMP'] );
        }

        // Detect by creating a temporary file
        else {
            // Try to use system's temporary directory
            // as random name shouldn't exist
            $temp_file = tempnam( md5(uniqid(rand(), TRUE)), '' );
            if ( $temp_file ) {
                $temp_dir = realpath( dirname($temp_file) );
                unlink( $temp_file );
                return $temp_dir;
            }
            else {
                return FALSE;
            }
        }
    }
}