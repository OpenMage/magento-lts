<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Core data helper
 *
 * @package    Mage_Core
 */
class Mage_Core_Helper_String extends Mage_Core_Helper_Abstract
{
    public const ICONV_CHARSET = 'UTF-8';

    protected $_moduleName = 'Mage_Core';

    /**
     * @var Mage_Core_Helper_Array
     */
    protected $_arrayHelper;

    /**
     * Truncate a string to a certain length if necessary, appending the $etc string.
     * $remainder will contain the string that has been replaced with $etc.
     *
     * @param  null|string $string
     * @param  int         $length
     * @param  string      $etc
     * @param  string      &$remainder
     * @param  bool        $breakWords
     * @return string
     */
    public function truncate($string, $length = 80, $etc = '...', &$remainder = '', $breakWords = true)
    {
        $remainder = '';
        if (is_null($string) || $length == 0) {
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
     * @param  string $string
     * @return int
     */
    public function strlen($string)
    {
        return is_null($string) ? 0 : iconv_strlen($string, self::ICONV_CHARSET);
    }

    /**
     * Passthrough to iconv_substr()
     *
     * @param  string $string
     * @param  int    $offset
     * @param  int    $length
     * @return string
     */
    public function substr($string, $offset, $length = null)
    {
        if (is_null($string)) {
            return '';
        }

        $string = $this->cleanString($string);
        if (is_null($length)) {
            $length = $this->strlen($string) - $offset;
        }

        return iconv_substr($string, $offset, $length, self::ICONV_CHARSET);
    }

    /**
     * Split string and appending $insert string after $needle
     *
     * @param  string $str
     * @param  int    $length
     * @param  string $needle
     * @param  string $insert
     * @return string
     */
    public function splitInjection($str, $length = 50, $needle = '-', $insert = ' ')
    {
        $str = $this->str_split($str, $length);
        $newStr = '';
        foreach ($str as $part) {
            if ($this->strlen($part) >= $length) {
                $lastDelimetr = $this->strpos($this->strrev($part), $needle);
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
     * @param  string $str
     * @return string
     */
    public function strrev($str)
    {
        $result = '';
        $strlen = $this->strlen($str);
        if (!$strlen) {
            return $result;
        }

        for ($i = $strlen - 1; $i >= 0; $i--) {
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
     * @param  string $str
     * @param  int    $length
     * @param  bool   $keepWords
     * @param  bool   $trim
     * @param  string $wordSeparatorRegex
     * @return array
     * @SuppressWarnings("PHPMD.CamelCaseMethodName")
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function str_split($str, $length = 1, $keepWords = false, $trim = false, $wordSeparatorRegex = '\s')
    {
        $result = [];
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
        } else { // split smartly, keeping words
            $split = preg_split('/(' . $wordSeparatorRegex . '+)/siu', $str, -1, PREG_SPLIT_DELIM_CAPTURE);
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
                } else {
                    $currentLength = $this->strlen($result[$i]);
                }

                $partLength = $this->strlen($part);
                // add part to current last element
                if (($currentLength + $spaceLen + $partLength) <= $length) {
                    $result[$i] .= $space . $part;
                } elseif ($partLength <= $length) { // add part to new element
                    $i++;
                    $result[$i] = $part;
                } else { // break too long part recursively
                    foreach ($this->str_split($part, $length, false, $trim, $wordSeparatorRegex) as $subpart) {
                        $i++;
                        $result[$i] = $subpart;
                    }
                }
            }
        }

        // remove last element, if empty
        if (($count = count($result)) && $result[$count - 1] === '') {
            unset($result[$count - 1]);
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
     * @param  null|string $str                 The source string
     * @param  bool        $uniqueOnly          Unique words only
     * @param  int         $maxWordLength       Limit words count
     * @param  string      $wordSeparatorRegexp
     * @return array
     */
    public function splitWords($str, $uniqueOnly = false, $maxWordLength = 0, $wordSeparatorRegexp = '\s')
    {
        if (is_null($str)) {
            return [];
        }

        $result = [];
        $split = preg_split('#' . $wordSeparatorRegexp . '#siu', $str, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($split as $word) {
            if ($uniqueOnly) {
                $result[$word] = $word;
            } else {
                $result[] = $word;
            }
        }

        if ($maxWordLength && count($result) > $maxWordLength) {
            return array_slice($result, 0, $maxWordLength);
        }

        return $result;
    }

    /**
     * Clean non UTF-8 characters
     *
     * @param  null|string $string
     * @return string
     */
    public function cleanString($string)
    {
        if (is_null($string)) {
            return '';
        }

        return '"libiconv"' == ICONV_IMPL
            ? iconv(self::ICONV_CHARSET, self::ICONV_CHARSET . '//IGNORE', $string)
            : $string;
    }

    /**
     * Find position of first occurrence of a string
     *
     * @param  string    $haystack
     * @param  string    $needle
     * @param  int       $offset
     * @return false|int
     */
    public function strpos($haystack, $needle, $offset = 0)
    {
        return iconv_strpos((string) $haystack, (string) $needle, $offset, self::ICONV_CHARSET);
    }

    /**
     * Sorts array with multibyte string keys
     *
     * @return array|false
     */
    public function ksortMultibyte(array &$sort)
    {
        if ($sort === []) {
            return false;
        }

        $oldLocale = setlocale(LC_COLLATE, '0');
        $localeCode = Mage::app()->getLocale()->getLocaleCode();
        // use fallback locale if $localeCode is not available
        setlocale(LC_COLLATE, $localeCode . '.UTF8', 'C.UTF-8', 'en_US.utf8');
        ksort($sort, SORT_LOCALE_STRING);
        setlocale(LC_COLLATE, $oldLocale);

        return $sort;
    }

    /**
     * Parse query string to array
     *
     * @param  string $str
     * @return array
     */
    public function parseQueryStr($str)
    {
        if (is_null($str)) {
            return [];
        }

        $argSeparator = '&';
        $result = [];
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
     * @param  string $str
     * @return bool
     */
    protected function _validateQueryStr($str)
    {
        return !(!$str || !str_contains($str, '='));
    }

    /**
     * Prepare param
     *
     * @param  string                $str
     * @return array<string, string>
     */
    protected function _explodeAndDecodeParam($str)
    {
        $preparedParam = [];
        $param = explode('=', $str);
        $preparedParam['key'] = urldecode(array_shift($param));
        $preparedParam['value'] = urldecode(array_shift($param));

        return $preparedParam;
    }

    /**
     * Append param to general result
     *
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
     * @param  array<string, array<int|string, string>|string> $param
     * @return array
     */
    protected function _handleRecursiveParamForQueryStr(array $param)
    {
        $value = $param['value'];
        $key = $param['key'];

        $subKeyBrackets = $this->_getLastSubkey($key);
        $subKey = $this->_getLastSubkey($key, false);
        if ($subKeyBrackets) {
            $param['value'] = $subKey ? [$subKey => $value] : [$value];

            $param['key'] = $this->_removeSubkeyPartFromKey($key, $subKeyBrackets);
            $param = $this->_handleRecursiveParamForQueryStr($param);
        }

        return $param;
    }

    /**
     * Remove subkey part from key
     *
     * @param  string $key
     * @param  string $subKeyBrackets
     * @return string
     */
    protected function _removeSubkeyPartFromKey($key, $subKeyBrackets)
    {
        return substr($key, 0, strrpos($key, $subKeyBrackets));
    }

    /**
     * Get last part key from query array
     *
     * @param  string $key
     * @param  bool   $withBrackets
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
     * @param  Mage_Core_Helper_Array $helper
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
     * @param  string $char char to get value from
     * @return int
     */
    public function uniOrd($char)
    {
        $ord = 0;
        $num = ord($char[0]);

        if ($num <= 0x7F) {
            $ord = $num;
        } elseif ($num < 0xC2) {
            $ord = 0;
        } elseif ($num <= 0xDF) {
            $ord = (($num & 0x1F) << 6 | (ord($char[1]) & 0x3F));
        } elseif ($num <= 0xEF) {
            $ord = (($num & 0x0F) << 12 | (ord($char[1]) & 0x3F) << 6 | (ord($char[2]) & 0x3F));
        } elseif ($num <= 0xF4) {
            $ord = (($num & 0x0F) << 18 | (ord($char[1]) & 0x3F) << 12
                | (ord($char[2]) & 0x3F) << 6 | (ord($char[3]) & 0x3F));
        }

        return $ord;
    }

    /**
     * UnSerialize string
     * @param  null|string $str
     * @return null|void
     * @throws Exception
     */
    public function unserialize($str)
    {
        if (is_null($str)) {
            return null;
        }

        $reader = new Unserialize_Reader_ArrValue('data');
        $prevChar = null;

        $strLen = strlen($str);
        for ($i = 0; $i < $strLen; $i++) {
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
     * @param  mixed $data
     * @return bool
     */
    public function isSerializedArrayOrObject($data)
    {
        $pattern
            = '/^a:\d+:\{(i:\d+;|s:\d+:\".+\";|N;|O:\d+:\"\w+\":\d+:\{\w:\d+:)+|^O:\d+:\"\w+\":\d+:\{(s:\d+:\"|i:\d+;)/';
        return is_string($data) && preg_match($pattern, $data);
    }

    /**
     * Validate is Serialized Data Object in string
     *
     * @param  string $str
     * @return bool
     */
    public function validateSerializedObject($str)
    {
        if ($this->isSerializedArrayOrObject($str)) {
            try {
                $this->unserialize($str);
            } catch (Exception) {
                return false;
            }
        }

        return true;
    }
}
