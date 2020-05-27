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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Core data helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Helper_String extends Mage_Core_Helper_Abstract
{
    const ICONV_CHARSET = 'UTF-8';

    /**
     * @var Mage_Core_Helper_Array
     */
    protected $_arrayHelper;

    /**
     * Truncate a string to a certain length if necessary, appending the $etc string.
     * $remainder will contain the string that has been replaced with $etc.
     *
     * @param string $string
     * @param int $length
     * @param string $etc
     * @param string &$remainder
     * @param bool $breakWords
     * @return string
     */
    public function truncate($string, $length = 80, $etc = '...', &$remainder = '', $breakWords = true)
    {
        $remainder = '';
        if (0 == $length) {
            return '';
        }

        $originalLength = $this->strlen($string);
        if ($originalLength > $length) {
            $length -= $this->strlen($etc);
            if ($length <= 0) {
                return '';
            }
            $preparedString = $string;
            $preparedlength = $length;
            if (!$breakWords) {
                $preparedString = preg_replace('/\s+?(\S+)?$/u', '', $this->substr($string, 0, $length + 1));
                $preparedlength = $this->strlen($preparedString);
            }
            $remainder = $this->substr($string, $preparedlength, $originalLength);
            return $this->substr($preparedString, 0, $length) . $etc;
        }

        return $string;
    }

    /**
     * Retrieve string length using default charset
     *
     * @param string $string
     * @return int
     */
    public function strlen($string)
    {
        return iconv_strlen($string, self::ICONV_CHARSET);
    }

    /**
     * Passthrough to iconv_substr()
     *
     * @param string $string
     * @param int $offset
     * @param int $length
     * @return string
     */
    public function substr($string, $offset, $length = null)
    {
        $string = $this->cleanString($string);
        if (is_null($length)) {
            $length = $this->strlen($string) - $offset;
        }
        return iconv_substr($string, $offset, $length, self::ICONV_CHARSET);
    }

    /**
     * Split string and appending $insert string after $needle
     *
     * @param string $str
     * @param integer $length
     * @param string $needle
     * @param string $insert
     * @return string
     */
    public function splitInjection($str, $length = 50, $needle = '-', $insert = ' ')
    {
        $str = $this->str_split($str, $length);
        $newStr = '';
        foreach ($str as $part) {
            if ($this->strlen($part) >= $length) {
                $lastDelimetr = $this->strpos($this->strrev($part), $needle);
                $tmpNewStr = '';
                $tmpNewStr = $this->substr($this->strrev($part), 0, $lastDelimetr)
                    . $insert . $this->substr($this->strrev($part), $lastDelimetr);
                $newStr .= $this->strrev($tmpNewStr);
            } else {
                $newStr .= $part;
            }
        }
        return $newStr;
    }

    /**
     * Binary-safe strrev()
     *
     * @param string $str
     * @return string
     */
    public function strrev($str)
    {
        $result = '';
        $strlen = $this->strlen($str);
        if (!$strlen) {
            return $result;
        }
        for ($i = $strlen-1; $i >= 0; $i--) {
            $result .= $this->substr($str, $i, 1);
        }
        return $result;
    }

    /**
     * Binary-safe variant of str_split()
     * + option not to break words
     * + option to trim spaces (between each word)
     * + option to set character(s) (pcre pattern) to be considered as words separator
     *
     * @param string $str
     * @param int $length
     * @param bool $keepWords
     * @param bool $trim
     * @param string $wordSeparatorRegex
     * @return array
     */
    public function str_split($str, $length = 1, $keepWords = false, $trim = false, $wordSeparatorRegex = '\s')
    {
        $result = array();
        $strlen = $this->strlen($str);
        if ((!$strlen) || (!is_int($length)) || ($length <= 0)) {
            return $result;
        }
        // trim
        if ($trim) {
            $str = trim(preg_replace('/\s{2,}/siu', ' ', $str));
            /**
             * In cases like:
             * Mage::helper('core/string')->str_split('0 1 2   ', 2, false, true);
             * the result array have elements with boolean "false" value.
             * So it fixed by
             */
            $strlen = $this->strlen($str);
        }
        // do a usual str_split, but safe for our encoding
        if ((!$keepWords) || ($length < 2)) {
            for ($offset = 0; $offset < $strlen; $offset += $length) {
                $result[] = $this->substr($str, $offset, $length);
            }
        }
        // split smartly, keeping words
        else {
            $split = preg_split('/(' . $wordSeparatorRegex . '+)/siu', $str, null, PREG_SPLIT_DELIM_CAPTURE);
            $i        = 0;
            $space    = '';
            $spaceLen = 0;
            foreach ($split as $key => $part) {
                if ($trim) {
                    // ignore spaces (even keys)
                    if ($key % 2) {
                        continue;
                    }
                    $space    = ' ';
                    $spaceLen = 1;
                }
                /**
                 * The empty($result[$i]) is not appropriate, because in case with empty("0") expression returns "true",
                 * so in cases when string have "0" symbol, the "0" will lost.
                 * Try Mage::helper('core/string')->str_split("0 aa", 2, true);
                 * Therefore the empty($result[$i]) expression
                 * replaced by !isset($result[$i]) || isset($result[$i]) && $result[$i] === ''
                 */
                if (!isset($result[$i]) || isset($result[$i]) && $result[$i] === '') {
                    $currentLength = 0;
                    $result[$i]    = '';
                    $space         = '';
                    $spaceLen      = 0;
                }
                else {
                    $currentLength = $this->strlen($result[$i]);
                }
                $partLength = $this->strlen($part);
                // add part to current last element
                if (($currentLength + $spaceLen + $partLength) <= $length) {
                    $result[$i] .= $space . $part;
                }
                // add part to new element
                elseif ($partLength <= $length) {
                    $i++;
                    $result[$i] = $part;
                }
                // break too long part recursively
                else {
                    foreach ($this->str_split($part, $length, false, $trim, $wordSeparatorRegex) as $subpart) {
                        $i++;
                        $result[$i] = $subpart;
                    }
                }
            }
        }
        // remove last element, if empty
        if ($count = count($result)) {
            if ($result[$count - 1] === '') {
                unset($result[$count - 1]);
            }
        }
        // remove first element, if empty
        if (isset($result[0]) && $result[0] === '') {
            array_shift($result);
        }
        return $result;
    }

    /**
     * Split words
     *
     * @param string $str The source string
     * @param bool $uniqueOnly Unique words only
     * @param int $maxWordLength Limit words count
     * @param string $wordSeparatorRegexp
     * @return array
     */
    public function splitWords($str, $uniqueOnly = false, $maxWordLength = 0, $wordSeparatorRegexp = '\s')
    {
        $result = array();
        $split = preg_split('#' . $wordSeparatorRegexp . '#siu', $str, null, PREG_SPLIT_NO_EMPTY);
        foreach ($split as $word) {
            if ($uniqueOnly) {
                $result[$word] = $word;
            }
            else {
                $result[] = $word;
            }
        }
        if ($maxWordLength && count($result) > $maxWordLength) {
            $result = array_slice($result, 0, $maxWordLength);
        }
        return $result;
    }

    /**
     * Clean non UTF-8 characters
     *
     * @param string $string
     * @return string
     */
    public function cleanString($string)
    {
        return '"libiconv"' == ICONV_IMPL ?
            iconv(self::ICONV_CHARSET, self::ICONV_CHARSET . '//IGNORE', $string) : $string;
    }

    /**
     * Find position of first occurrence of a string
     *
     * @param string $haystack
     * @param string $needle
     * @param int $offset
     * @return int|false
     */
    public function strpos($haystack, $needle, $offset = null)
    {
        return iconv_strpos($haystack, $needle, $offset, self::ICONV_CHARSET);
    }

    /**
     * Sorts array with multibyte string keys
     *
     * @param array $sort
     * @return array
     */
    public function ksortMultibyte(array &$sort)
    {
        if (empty($sort)) {
            return false;
        }
        $oldLocale = setlocale(LC_COLLATE, "0");
        $localeCode = Mage::app()->getLocale()->getLocaleCode();
        // use fallback locale if $localeCode is not available
        setlocale(LC_COLLATE,  $localeCode . '.UTF8', 'C.UTF-8', 'en_US.utf8');
        ksort($sort, SORT_LOCALE_STRING);
        setlocale(LC_COLLATE, $oldLocale);

        return $sort;
    }

    /**
     * Parse query string to array
     *
     * @param string $str
     * @return array
     */
    public function parseQueryStr($str)
    {
        $argSeparator = '&';
        $result = array();
        $partsQueryStr = explode($argSeparator, $str);

        foreach ($partsQueryStr as $partQueryStr) {
            if ($this->_validateQueryStr($partQueryStr)) {
                $param = $this->_explodeAndDecodeParam($partQueryStr);
                $param = $this->_handleRecursiveParamForQueryStr($param);
                $result = $this->_appendParam($result, $param);
            }
        }
        return $result;
    }

    /**
     * Validate query pair string
     *
     * @param string $str
     * @return bool
     */
    protected function _validateQueryStr($str)
    {
        if (!$str || (strpos($str, '=') === false)) {
            return false;
        }
        return true;
    }

    /**
     * Prepare param
     *
     * @param string $str
     * @return array
     */
    protected function _explodeAndDecodeParam($str)
    {
        $preparedParam = array();
        $param = explode('=', $str);
        $preparedParam['key'] = urldecode(array_shift($param));
        $preparedParam['value'] = urldecode(array_shift($param));

        return $preparedParam;
    }

    /**
     * Append param to general result
     *
     * @param array $result
     * @param array $param
     * @return array
     */
    protected function _appendParam(array $result, array $param)
    {
        $key   = $param['key'];
        $value = $param['value'];

        if ($key) {
            if (is_array($value) && array_key_exists($key, $result)) {
                $helper = $this->getArrayHelper();
                $result[$key] = $helper->mergeRecursiveWithoutOverwriteNumKeys($result[$key], $value);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Handle param recursively
     *
     * @param array $param
     * @return array
     */
    protected function _handleRecursiveParamForQueryStr(array $param)
    {
        $value = $param['value'];
        $key = $param['key'];

        $subKeyBrackets = $this->_getLastSubkey($key);
        $subKey = $this->_getLastSubkey($key, false);
        if ($subKeyBrackets) {
            if ($subKey) {
                $param['value'] = array($subKey => $value);
            } else {
                $param['value'] = array($value);
            }
            $param['key'] = $this->_removeSubkeyPartFromKey($key, $subKeyBrackets);
            $param = $this->_handleRecursiveParamForQueryStr($param);
        }

        return $param;
    }

    /**
     * Remove subkey part from key
     *
     * @param string $key
     * @param string $subKeyBrackets
     * @return string
     */
    protected function _removeSubkeyPartFromKey($key, $subKeyBrackets)
    {
        return substr($key, 0, strrpos($key, $subKeyBrackets));
    }

    /**
     * Get last part key from query array
     *
     * @param string $key
     * @param bool $withBrackets
     * @return string
     */
    protected function _getLastSubkey($key, $withBrackets = true)
    {
        $subKey = '';
        $leftBracketSymbol  = '[';
        $rightBracketSymbol = ']';

        $firstPos = strrpos($key, $leftBracketSymbol);
        $lastPos  = strrpos($key, $rightBracketSymbol);

        if (($firstPos !== false || $lastPos !== false)
            && $firstPos < $lastPos
        ) {
            $keyLenght = $lastPos - $firstPos + 1;
            $subKey = substr($key, $firstPos, $keyLenght);
            if (!$withBrackets) {
                $subKey = ltrim($subKey, $leftBracketSymbol);
                $subKey = rtrim($subKey, $rightBracketSymbol);
            }
        }
        return $subKey;
    }

    /**
     * Set array helper
     *
     * @param Mage_Core_Helper_Abstract $helper
     * @return $this
     */
    public function setArrayHelper(Mage_Core_Helper_Abstract $helper)
    {
        $this->_arrayHelper = $helper;
        return $this;
    }

    /**
     * Get Array Helper
     *
     * @return Mage_Core_Helper_Array
     */
    public function getArrayHelper()
    {
        if (!$this->_arrayHelper) {
            $this->_arrayHelper = Mage::helper('core/array');
        }
        return $this->_arrayHelper;
    }

    /**
     * Unicode compatible ord() method
     *
     * @param  string $c char to get value from
     * @return integer
     */
    public function uniOrd($c)
    {
        $ord = 0;
        $h   = ord($c[0]);

        if ($h <= 0x7F) {
            $ord = $h;
        } else if ($h < 0xC2) {
            $ord = 0;
        } else if ($h <= 0xDF) {
            $ord = (($h & 0x1F) << 6 | (ord($c[1]) & 0x3F));
        } else if ($h <= 0xEF) {
            $ord = (($h & 0x0F) << 12 | (ord($c[1]) & 0x3F) << 6 | (ord($c[2]) & 0x3F));
        } else if ($h <= 0xF4) {
            $ord = (($h & 0x0F) << 18 | (ord($c[1]) & 0x3F) << 12 |
                (ord($c[2]) & 0x3F) << 6 | (ord($c[3]) & 0x3F));
        }

        return $ord;
    }

    /**
     * UnSerialize string
     * @param $str
     * @return mixed|null
     * @throws Exception
     */
    public function unserialize($str)
    {
        $reader = new Unserialize_Reader_ArrValue('data');
        $prevChar = null;
        for ($i = 0; $i < strlen($str); $i++) {
            $char = $str[$i];
            $result = $reader->read($char, $prevChar);
            if (!is_null($result)) {
                return $result;
            }
            $prevChar = $char;
        }
    }

    /**
     * Detect serialization of data Array or Object
     *
     * @param mixed $data
     * @return bool
     */
    public function isSerializedArrayOrObject($data)
    {
        $pattern =
            '/^a:\d+:\{(i:\d+;|s:\d+:\".+\";|N;|O:\d+:\"\w+\":\d+:\{\w:\d+:)+|^O:\d+:\"\w+\":\d+:\{(s:\d+:\"|i:\d+;)/';
        return is_string($data) && preg_match($pattern, $data);
    }

    /**
     * Validate is Serialized Data Object in string
     *
     * @param string $str
     * @return bool
     */
    public function validateSerializedObject($str)
    {
        if ($this->isSerializedArrayOrObject($str)) {
            try {
                $this->unserialize($str);
            } catch (Exception $e) {
                return false;
            }
        }

        return true;
    }
}
