<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
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
 * @SuppressWarnings("PHPMD.ShortMethodName")
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
 * @return bool
 */
function is_empty_date($date)
{
    return $date === null || preg_replace('#[ 0:-]#', '', $date) === '';
}

/**
 * @param string $class
 * @return bool|string
 */
function mageFindClassFile($class)
{
    $classFile = uc_words($class, DIRECTORY_SEPARATOR) . '.php';
    $found = false;
    foreach (explode(PS, get_include_path()) as $path) {
        $fileName = $path . DS . $classFile;
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
 * @param int $errno
 * @param string $errstr
 * @param string $errfile
 * @param int $errline
 * @return bool|null
 */
function mageCoreErrorHandler($errno, $errstr, $errfile, $errline)
{
    if (str_contains($errstr, 'DateTimeZone::__construct')) {
        // there's no way to distinguish between caught system exceptions and warnings
        return false;
    }

    $errno = $errno & error_reporting();
    if ($errno == 0) {
        return false;
    }

    // PEAR specific message handling
    if (stripos($errfile . $errstr, 'pear') !== false) {
        // ignore strict and deprecated notices
        if ((PHP_VERSION_ID < 80400 && $errno == E_STRICT) || ($errno == E_DEPRECATED)) {
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
            $errorMessage .= 'Error';
            break;
        case E_WARNING:
            $errorMessage .= 'Warning';
            break;
        case E_PARSE:
            $errorMessage .= 'Parse Error';
            break;
        case E_NOTICE:
            $errorMessage .= 'Notice';
            break;
        case E_CORE_ERROR:
            $errorMessage .= 'Core Error';
            break;
        case E_CORE_WARNING:
            $errorMessage .= 'Core Warning';
            break;
        case E_COMPILE_ERROR:
            $errorMessage .= 'Compile Error';
            break;
        case E_COMPILE_WARNING:
            $errorMessage .= 'Compile Warning';
            break;
        case E_USER_ERROR:
            $errorMessage .= 'User Error';
            break;
        case E_USER_WARNING:
            $errorMessage .= 'User Warning';
            break;
        case E_USER_NOTICE:
            $errorMessage .= 'User Notice';
            break;
        case 2048: // E_STRICT prior to PHP8.4
            $errorMessage .= 'Strict Notice';
            break;
        case E_RECOVERABLE_ERROR:
            $errorMessage .= 'Recoverable Error';
            break;
        case E_DEPRECATED:
            $errorMessage .= 'Deprecated functionality';
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
        return null;
    }
}

/**
 * @param bool $return
 * @param bool $html
 * @param bool $showFirst
 * @return string|null
 *
 * @SuppressWarnings("PHPMD.ErrorControlOperator")
 */
function mageDebugBacktrace($return = false, $html = true, $showFirst = false)
{
    $d = debug_backtrace();
    $out = '';
    if ($html) {
        $out .= '<pre>';
    }
    foreach ($d as $i => $r) {
        if (!$showFirst && $i == 0) {
            continue;
        }
        // sometimes there is undefined index 'file'
        @$out .= "[$i] {$r['file']}:{$r['line']}\n";
    }
    if ($html) {
        $out .= '</pre>';
    }
    if ($return) {
        return $out;
    } else {
        echo $out;
        return null;
    }
}

function mageSendErrorHeader()
{
    return;
}

function mageSendErrorFooter()
{
    return;
}

/**
 * @param string $path
 *
 * @SuppressWarnings("PHPMD.ErrorControlOperator")
 */
function mageDelTree($path)
{
    if (is_dir($path)) {
        $entries = scandir($path);
        foreach ($entries as $entry) {
            if ($entry != '.' && $entry != '..') {
                mageDelTree($path . DS . $entry);
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
function mageParseCsv($string, $delimiter = ',', $enclosure = '"', $escape = '\\')
{
    $elements = explode($delimiter, $string);
    for ($i = 0; $i < count($elements); $i++) {
        $nquotes = substr_count($elements[$i], $enclosure);
        if ($nquotes % 2 == 1) {
            for ($j = $i + 1; $j < count($elements); $j++) {
                if (substr_count($elements[$j], $enclosure) > 0) {
                    // Put the quoted string's pieces back together again
                    array_splice(
                        $elements,
                        $i,
                        $j - $i + 1,
                        implode($delimiter, array_slice($elements, $i, $j - $i + 1)),
                    );
                    break;
                }
            }
        }
        if ($nquotes > 0) {
            // Remove first and last quotes, then merge pairs of quotes
            $qstr = & $elements[$i];
            $qstr = substr_replace($qstr, '', strpos($qstr, $enclosure), 1);
            $qstr = substr_replace($qstr, '', strrpos($qstr, $enclosure), 1);
            $qstr = str_replace($enclosure . $enclosure, $enclosure, $qstr);
        }
    }
    return $elements;
}

/**
 * @param string $dir
 * @return bool
 *
 * @SuppressWarnings("PHPMD.ErrorControlOperator")
 */
function isDirWriteable($dir)
{
    if (is_dir($dir) && is_writable($dir)) {
        if (stripos(PHP_OS, 'win') === 0) {
            $dir    = ltrim($dir, DIRECTORY_SEPARATOR);
            $file   = $dir . DIRECTORY_SEPARATOR . uniqid(mt_rand()) . '.tmp';
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


/**
 * @param string $dir
 * @return bool
 * @deprecated avoid php_codesniffer error
 *
 *     An error occurred during processing; checking has been aborted. The error message was: Undefined index: ^is_dir/i_writeab in
 *     /var/www/html/vendor/squizlabs/php_codesniffer/src/Standards/Generic/Sniffs/PHP/ForbiddenFunctionsSniff.php on line 228
 *     The error originated in the Generic.PHP.ForbiddenFunctions sniff on line 228. (Internal.Exception)
 *
 * @see isDirWriteable()
 */
function is_dir_writeable($dir)
{
    return isDirWriteable($dir);
}
