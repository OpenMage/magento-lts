<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Varien
 * @package    Varien_Date
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Converter of date formats
 * Internal dates
 *
 * @category Varien
 * @package  Varien_Date
 */
class Varien_Date
{
    /**
     * Date format, used as default. Compatible with Zend_Date
     *
     */
    public const DATETIME_INTERNAL_FORMAT = 'yyyy-MM-dd HH:mm:ss';
    public const DATE_INTERNAL_FORMAT = 'yyyy-MM-dd';

    public const DATETIME_PHP_FORMAT       = 'Y-m-d H:i:s';
    public const DATE_PHP_FORMAT           = 'Y-m-d';

    /**
     * Zend Date To local date according Map array
     *
     * @var array
     */
    private static $_convertZendToStrftimeDate = [
        'yyyy-MM-ddTHH:mm:ssZZZZ' => '%c',
        'EEEE' => '%A',
        'EEE'  => '%a',
        'D'    => '%j',
        'MMMM' => '%B',
        'MMM'  => '%b',
        'MM'   => '%m',
        'M'    => '%m',
        'dd'   => '%d',
        'd'    => '%e',
        'yyyy' => '%Y',
        'yy'   => '%Y',
        'y'    => '%Y'
    ];
    /**
     * Zend Date To local time according Map array
     *
     * @var array
     */
    private static $_convertZendToStrftimeTime = [
        'a'  => '%p',
        'hh' => '%I',
        'h'  => '%I',
        'HH' => '%H',
        'H'  => '%H',
        'mm' => '%M',
        'ss' => '%S',
        'z'  => '%Z',
        'v'  => '%Z'
    ];

    /**
     * Convert Zend Date format to local time/date according format
     *
     * @param string $value
     * @param boolean $convertDate
     * @param boolean $convertTime
     * @return string
     */
    public static function convertZendToStrftime($value, $convertDate = true, $convertTime = true)
    {
        if ($convertTime) {
            $value = self::_convert($value, self::$_convertZendToStrftimeTime);
        }
        if ($convertDate) {
            $value = self::_convert($value, self::$_convertZendToStrftimeDate);
        }
        return $value;
    }

    /**
     * Convert value by dictionary
     *
     * @param string $value
     * @param array $dictionary
     * @return string
     */
    protected static function _convert($value, $dictionary)
    {
        foreach ($dictionary as $search => $replace) {
            $value = preg_replace('/(^|[^%])' . $search . '/', '$1' . $replace, $value);
        }
        return $value;
    }
    /**
     * Convert date to UNIX timestamp
     * Returns current UNIX timestamp if date is true
     *
     * @param Zend_Date|string|true $date
     * @return int
     */
    public static function toTimestamp($date)
    {
        if ($date instanceof Zend_Date) {
            return $date->getTimestamp();
        }

        if ($date === true) {
            return time();
        }

        return strtotime($date);
    }

    /**
     * Retrieve current date in internal format
     *
     * @param boolean $withoutTime day only flag
     * @return string
     */
    public static function now($withoutTime = false)
    {
        $format = $withoutTime ? self::DATE_PHP_FORMAT : self::DATETIME_PHP_FORMAT;
        return date($format);
    }

    /**
     * Format date to internal format
     *
     * @param int|string|Zend_Date|bool|null $date
     * @param bool $includeTime
     * @return string|null
     */
    public static function formatDate($date, $includeTime = true)
    {
        if ($date === true) {
            return self::now(!$includeTime);
        }

        if ($date instanceof Zend_Date) {
            if ($includeTime) {
                return $date->toString(self::DATETIME_INTERNAL_FORMAT);
            } else {
                return $date->toString(self::DATE_INTERNAL_FORMAT);
            }
        }

        if (empty($date)) {
            return null;
        }

        if (!is_numeric($date)) {
            $date = self::toTimestamp($date);
        }

        $format = $includeTime ? self::DATETIME_PHP_FORMAT : self::DATE_PHP_FORMAT;
        return date($format, $date);
    }
}
