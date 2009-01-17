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

/**
 * Core data helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Helper_String extends Mage_Core_Helper_Abstract
{
    const ICONV_CHARSET = 'UTF-8';

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

        $originalLength = iconv_strlen($string, self::ICONV_CHARSET);
        if ($originalLength > $length) {
            $length -= iconv_strlen($etc, self::ICONV_CHARSET);
            if ($length <= 0) {
                return '';
            }
            $preparedString = $string;
            $preparedlength = $length;
            if (!$breakWords) {
                $preparedString = preg_replace('/\s+?(\S+)?$/', '', iconv_substr($string, 0, $length + 1, self::ICONV_CHARSET));
                $preparedlength = iconv_strlen($preparedString, self::ICONV_CHARSET);
            }
            $remainder = iconv_substr($string, $preparedlength, $originalLength, self::ICONV_CHARSET);
            return iconv_substr($preparedString, 0, $length, self::ICONV_CHARSET) . $etc;
        }

        return $string;
    }

    /**
     * Passthrough to iconv_strlen()
     *
     * @param string $str
     * @return int
     */
    public function strlen($str)
    {
        return iconv_strlen($str, self::ICONV_CHARSET);
    }

    /**
     * Passthrough to iconv_substr()
     *
     * @param string $str
     * @param int $offset
     * @param int $length
     * @return string
     */
    public function substr($str, $offset, $length = null)
    {
        if (is_null($length)) {
            $length = iconv_strlen($str, self::ICONV_CHARSET) - $offset;
        }
        return iconv_substr($str, $offset, $length, self::ICONV_CHARSET);
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
                $lastDelimetr = iconv_strpos(strrev($part), $needle, null, self::ICONV_CHARSET);
                $tmpNewStr = '';
                $tmpNewStr = $this->substr(strrev($part), 0, $lastDelimetr) . $insert . $this->substr(strrev($part), $lastDelimetr);
                $newStr .= strrev($tmpNewStr);
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
            $result .= iconv_substr($str, $i, 1, self::ICONV_CHARSET);
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
            $str = trim(preg_replace('/\s{2,}/is', ' ', $str));
        }
        // do a usual str_split, but safe for our encoding
        if ((!$keepWords) || ($length < 2)) {
            for ($offset = 0; $offset < $strlen; $offset += $length) {
                $result[] = iconv_substr($str, $offset, $length, self::ICONV_CHARSET);
            }
        }
        // split smartly, keeping words
        else {
            $split = preg_split('/(' . $wordSeparatorRegex . '+)/is', $str, null, PREG_SPLIT_DELIM_CAPTURE);
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
                if (empty($result[$i])) {
                    $currentLength = 0;
                    $result[$i]    = '';
                    $space         = '';
                    $spaceLen      = 0;
                }
                else {
                    $currentLength = iconv_strlen($result[$i], self::ICONV_CHARSET);
                }
                $partLength = iconv_strlen($part, self::ICONV_CHARSET);
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
            if (empty($result[$count - 1])) {
                unset($result[$count - 1]);
            }
        }
        // remove first element, if empty
        if (isset($result[0]) && empty($result[0])) {
            array_shift($result);
        }
        return $result;
    }
}
