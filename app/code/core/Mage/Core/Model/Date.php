<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

use Carbon\Carbon;

/**
 * Date conversion model
 *
 * @package    Mage_Core
 */
class Mage_Core_Model_Date
{
    /**
     * Current config offset in seconds
     *
     * @var int
     */
    private $_offset = 0;

    /**
     * Init offset
     */
    public function __construct()
    {
        $this->_offset = $this->calculateOffset($this->_getConfigTimezone());
    }

    /**
     * Gets the store config timezone
     *
     * @return string
     */
    protected function _getConfigTimezone()
    {
        return Mage::app()->getStore()->getConfig('general/locale/timezone');
    }

    /**
     * Calculates timezone offset
     *
     * @param  string $timezone
     * @return int    offset between timezone and gmt
     *
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    public function calculateOffset($timezone = null)
    {
        $result = true;
        $offset = 0;

        if (!is_null($timezone)) {
            $oldzone = @date_default_timezone_get();
            $result = date_default_timezone_set($timezone);
        }

        if ($result === true) {
            $offset = (int) Carbon::now()->format('Z');
        }

        if (!is_null($timezone)) {
            date_default_timezone_set($oldzone);
        }

        return $offset;
    }

    /**
     * Forms GMT date
     *
     * @param  string       $format
     * @param  int|string   $input  date in current timezone
     * @return false|string
     */
    public function gmtDate($format = null, $input = null)
    {
        if (is_null($format)) {
            $format = Varien_Date::DATETIME_PHP_FORMAT;
        }

        $date = $this->gmtTimestamp($input);

        if ($date === false) {
            return false;
        }

        return Carbon::createFromTimestamp((int) $date)->format($format);
    }

    /**
     * Converts input date into date with timezone offset
     * Input date must be in GMT timezone
     *
     * @param  string     $format
     * @param  int|string $input  date in GMT timezone
     * @return string
     */
    public function date($format = null, $input = null)
    {
        if (is_null($format)) {
            $format = Varien_Date::DATETIME_PHP_FORMAT;
        }

        return Carbon::createFromTimestamp($this->timestamp($input))->format($format);
    }

    /**
     * Forms GMT timestamp
     *
     * @param  int|string       $input date in current timezone
     * @return false|int|string
     */
    public function gmtTimestamp($input = null)
    {
        if (is_null($input)) {
            return gmdate('U');
        }

        if (is_numeric($input)) {
            $result = $input;
        } else {
            $result = Carbon::parse($input)->getTimestamp();
        }

        if ($result === false) {
            // strtotime() unable to parse string (it's not a date or has incorrect format)
            return false;
        }

        $date      = Mage::app()->getLocale()->date($result);
        $timestamp = $date->get(Zend_Date::TIMESTAMP) - $date->get(Zend_Date::TIMEZONE_SECS);

        unset($date);
        return $timestamp;
    }

    /**
     * Converts input date into timestamp with timezone offset
     * Input date must be in GMT timezone
     *
     * @param  int|string $input date in GMT timezone
     * @return int
     */
    public function timestamp($input = null)
    {
        if (is_null($input)) {
            $result = $this->gmtTimestamp();
        } elseif (is_numeric($input)) {
            $result = $input;
        } else {
            $result = Carbon::parse($input)->getTimestamp();
        }

        $date      = Mage::app()->getLocale()->date($result);
        $timestamp = $date->get(Zend_Date::TIMESTAMP) + $date->get(Zend_Date::TIMEZONE_SECS);

        unset($date);
        return $timestamp;
    }

    /**
     * Get current timezone offset in seconds/minutes/hours
     *
     * @param  string $type
     * @return int
     */
    public function getGmtOffset($type = 'seconds')
    {
        $result = $this->_offset;
        switch ($type) {
            case 'seconds':
            default:
                break;

            case 'minutes':
                $result /= 60;
                break;

            case 'hours':
                $result = $result / 60 / 60;
                break;
        }

        return $result;
    }

    /**
     * @param  int  $year
     * @param  int  $month
     * @param  int  $day
     * @param  int  $hour
     * @param  int  $minute
     * @param  int  $second
     * @return bool
     * @deprecated since 1.1.7
     */
    public function checkDateTime($year, $month, $day, $hour = 0, $minute = 0, $second = 0)
    {
        if (!checkdate($month, $day, $year)) {
            return false;
        }

        foreach (['hour' => 23, 'minute' => 59, 'second' => 59] as $var => $maxValue) {
            $value = (int) ${$var};
            if (($value < 0) || ($value > $maxValue)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  string              $dateTimeString
     * @param  string              $dateTimeFormat
     * @return array
     * @throws Mage_Core_Exception
     * @deprecated since 1.1.7
     */
    public function parseDateTime($dateTimeString, $dateTimeFormat)
    {
        // look for supported format
        $isSupportedFormatFound = false;

        $formats = [
            // priority is important!
            '%m/%d/%y %I:%M' => [
                '/^(\d{1,2})\/(\d{1,2})\/(\d{1,2}) (\d{1,2}):(\d{1,2})/',
                ['y' => 3, 'm' => 1, 'd' => 2, 'h' => 4, 'i' => 5],
            ],
            'm/d/y h:i' => [
                '/^(\d{1,2})\/(\d{1,2})\/(\d{1,2}) (\d{1,2}):(\d{1,2})/',
                ['y' => 3, 'm' => 1, 'd' => 2, 'h' => 4, 'i' => 5],
            ],
            '%m/%d/%y' => ['/^(\d{1,2})\/(\d{1,2})\/(\d{1,2})/', ['y' => 3, 'm' => 1, 'd' => 2]],
            'm/d/y' => ['/^(\d{1,2})\/(\d{1,2})\/(\d{1,2})/', ['y' => 3, 'm' => 1, 'd' => 2]],
        ];

        foreach ($formats as $supportedFormat => $regRule) {
            if (str_contains($dateTimeFormat, $supportedFormat)) {
                $isSupportedFormatFound = true;
                break;
            }
        }

        if (!$isSupportedFormatFound) {
            Mage::throwException(Mage::helper('core')->__('Date/time format "%s" is not supported.', $dateTimeFormat));
        }

        // apply reg rule to found format
        $regex = array_shift($regRule);
        $mask  = array_shift($regRule);
        if (!preg_match($regex, $dateTimeString, $matches)) {
            Mage::throwException(Mage::helper('core')->__('Specified date/time "%1$s" do not match format "%2$s".', $dateTimeString, $dateTimeFormat));
        }

        // make result
        $result = [];
        foreach (['y', 'm', 'd', 'h', 'i', 's'] as $key) {
            $value = 0;
            if (isset($mask[$key]) && isset($matches[$mask[$key]])) {
                $value = (int) $matches[$mask[$key]];
            }

            $result[] = $value;
        }

        // make sure to return full year
        if ($result[0] < 100) {
            $result[0] = 2000 + $result[0];
        }

        return $result;
    }
}
