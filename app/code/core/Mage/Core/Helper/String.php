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
        $str = str_split($str, $length);
        $newStr = '';
        foreach ($str as $part) {
            if ($this->strlen($part) >= $length) {
                $lastDelimetr = strpos(strrev($part), $needle);
                $tmpNewStr = '';
                $tmpNewStr = $this->substr(strrev($part), 0, $lastDelimetr).$insert.substr(strrev($part), $lastDelimetr);
                $newStr .= strrev($tmpNewStr);
            } else {
                $newStr .= $part;
            }
        }
        return $newStr;
    }
}
