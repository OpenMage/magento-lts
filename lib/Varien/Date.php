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
 * @category   Varien
 * @package    Varien_Date
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Converter of date formats
 *
 * @category Varien
 * @package  Varien_Date
 * @author   Magento Core Team <core@magentocommerce.com>
 */
class Varien_Date
{
    /**
     * Date format, used as default. Compatible with Zend_Date
     *
     */
    const DATETIME_INTERNAL_FORMAT = 'yyyy-MM-dd HH:mm:ss';
    const DATE_INTERNAL_FORMAT = 'yyyy-MM-dd';

    private static $_convertZendToStrftimeDate = array(
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
        'yy'   => '%y',
        'y'    => '%Y'
    );
    private static $_convertZendToStrftimeTime = array(
        'a'  => '%p',
        'hh' => '%I',
        'h'  => '%I',
        'HH' => '%H',
        'H'  => '%H',
        'mm' => '%M',
        'ss' => '%S',
        'z'  => '%Z',
        'v'  => '%Z'
    );

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

    protected static function _convert($value, $dictionary)
    {
        foreach ($dictionary as $search => $replace) {
            $value = preg_replace('/(^|[^%])' . $search . '/', '$1' . $replace, $value);
        }
        return $value;
    }
}