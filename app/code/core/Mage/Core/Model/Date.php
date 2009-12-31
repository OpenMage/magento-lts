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
 * Date conversion model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
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
     * Current system offset in seconds
     *
     * @var int
     */
    private $_systemOffset = 0;

    /**
     * Init offset
     *
     */
    public function __construct()
    {
        $this->_offset = $this->calculateOffset($this->_getConfigTimezone());
        $this->_systemOffset = $this->calculateOffset();
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
     * @var string $timezone
     * @return int offset between timezone and gmt
     */
    public function calculateOffset($timezone = null)
    {
        $result = true;
        $offset = 0;

        if (!is_null($timezone)){
            $oldzone = @date_default_timezone_get();
            $result = date_default_timezone_set($timezone);
        }

        if ($result === true) {
            $offset = gmmktime(0, 0, 0, 1, 2, 1970) - mktime(0, 0, 0, 1, 2, 1970);
        }

        if (!is_null($timezone)){
            date_default_timezone_set($oldzone);
        }

        return $offset;
    }

    /**
     * Forms GMT date
     *
     * @param string $format
     * @param int || string $input date in current timezone
     * @return string
     */
    public function gmtDate($format = null, $input = null)
    {
        if (is_null($format)) {
            $format = 'Y-m-d H:i:s';
        }

        $result = date($format, $this->gmtTimestamp($input));
        return $result;
    }

    /**
     * Converts input date into date with timezone offset
     * Input date must be in GMT timezone
     *
     * @param string $format
     * @param int || string $input date in GMT timezone
     */
    public function date($format = null, $input = null)
    {
        if (is_null($format)) {
            $format = 'Y-m-d H:i:s';
        }

        $result = date($format, $this->timestamp($input));
        return $result;
    }

    /**
     * Forms GMT timestamp
     *
     * @param int || string $input date in current timezone
     */
    public function gmtTimestamp($input = null)
    {
        if (is_null($input)) {
            return gmdate('U');
        } else if (is_numeric($input)) {
            $result = $input;
        } else {
            $result = strtotime($input);
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
     * @param int || string $input date in GMT timezone
     */
    public function timestamp($input = null)
    {
        if (is_null($input)) {
            $result = $this->gmtTimestamp();
        } else if (is_numeric($input)) {
            $result = $input;
        } else {
            $result = strtotime($input);
        }

        $date      = Mage::app()->getLocale()->date($result);
        $timestamp = $date->get(Zend_Date::TIMESTAMP) + $date->get(Zend_Date::TIMEZONE_SECS);

        unset($date);
        return $timestamp;
    }

    /**
     * Get current timezone offset in seconds/minutes/hours
     *
     * @param string $type
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
                $result = $result / 60;
                break;

            case 'hours':
                $result = $result / 60 / 60;
                break;
        }
        return $result;
    }

    /**
     * Deprecated since 1.1.7
     */
    public function checkDateTime($year, $month, $day, $hour = 0, $minute = 0, $second = 0)
    {
        if (!checkdate($month, $day, $year)) {
            return false;
        }
        foreach (array('hour' => 23, 'minute' => 59, 'second' => 59) as $var => $maxValue) {
            $value = (int)$$var;
            if (($value < 0) || ($value > $maxValue)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Deprecated since 1.1.7
     */
    public function parseDateTime($dateTimeString, $dateTimeFormat)
    {
        // look for supported format
        $isSupportedFormatFound = false;
        foreach (array(
            // priority is important!
            '%m/%d/%y %I:%M' => array('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2})/', array('y' => 3, 'm' => 1, 'd' => 2, 'h' => 4, 'i' => 5)),
            'm/d/y h:i'      => array('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2})/', array('y' => 3, 'm' => 1, 'd' => 2, 'h' => 4, 'i' => 5)),
            '%m/%d/%y'       => array('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{1,2})/', array('y' => 3, 'm' => 1, 'd' => 2)),
            'm/d/y'          => array('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{1,2})/', array('y' => 3, 'm' => 1, 'd' => 2)),
            ) as $supportedFormat => $regRule) {
            if (false !== strpos($dateTimeFormat, $supportedFormat, 0)) {
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
        $result = array();
        foreach (array('y', 'm', 'd', 'h', 'i', 's') as $key) {
            $value = 0;
            if (isset($mask[$key]) && isset($matches[$mask[$key]])) {
                $value = (int)$matches[$mask[$key]];
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