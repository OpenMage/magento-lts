<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Measure
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Acceleration.php 8064 2008-02-16 10:58:39Z thomas $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Implement needed classes
 */
#require_once 'Zend/Measure/Exception.php';
#require_once 'Zend/Measure/Abstract.php';
#require_once 'Zend/Locale.php';


/**
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage Zend_Measure_Acceleration
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Acceleration extends Zend_Measure_Abstract
{
    // Acceleration definitions
    const STANDARD = 'METER_PER_SQUARE_SECOND';

    const CENTIGAL                     = 'CENTIGAL';                 // Metric
    const CENTIMETER_PER_SQUARE_SECOND = 'CENTIMETER_PER_SQUARE_SECOND'; // Metric
    const DECIGAL                      = 'DECIGAL';                  // Metric
    const DECIMETER_PER_SQUARE_SECOND  = 'DECIMETER_PER_SQUARE_SECOND';  // Metric
    const DEKAMETER_PER_SQUARE_SECOND  = 'DEKAMETER_PER_SQUARE_SECOND';  // Metric
    const FOOT_PER_SQUARE_SECOND       = 'FOOT_PER_SQUARE_SECOND';       // US
    const G                            = 'G';                        // Gravity
    const GAL                          = 'GAL';                      // Metric = 1cm/s²
    const GALILEO                      = 'GALILEO';                  // Metric = 1cm/s²
    const GRAV                         = 'GRAV';                     // Gravity
    const HECTOMETER_PER_SQUARE_SECOND = 'HECTOMETER_PER_SQUARE_SECOND'; // Metric
    const INCH_PER_SQUARE_SECOND       = 'INCH_PER_SQUARE_SECOND';       // US
    const KILOMETER_PER_HOUR_SECOND    = 'KILOMETER_PER_HOUR_SECOND';    // Metric
    const KILOMETER_PER_SQUARE_SECOND  = 'KILOMETER_PER_SQUARE_SECOND';  // Metric
    const METER_PER_SQUARE_SECOND      = 'METER_PER_SQUARE_SECOND';      // Metric
    const MILE_PER_HOUR_MINUTE         = 'MILE_PER_HOUR_MINUTE';         // US
    const MILE_PER_HOUR_SECOND         = 'MILE_PER_HOUR_SECOND';         // US
    const MILE_PER_SQUARE_SECOND       = 'MILE_PER_SQUARE_SECOND';       // US
    const MILLIGAL                     = 'MILLIGAL';                 // Metric
    const MILLIMETER_PER_SQUARE_SECOND = 'MILLIMETER_PER_SQUARE_SECOND'; // Metric

    protected $_UNITS = array(
        'CENTIGAL'                     => array('0.0001',   'cgal'),
        'CENTIMETER_PER_SQUARE_SECOND' => array('0.01',     'cm/s²'),
        'DECIGAL'                      => array('0.001',    'dgal'),
        'DECIMETER_PER_SQUARE_SECOND'  => array('0.1',      'dm/s²'),
        'DEKAMETER_PER_SQUARE_SECOND'  => array('10',       'dam/s²'),
        'FOOT_PER_SQUARE_SECOND'       => array('0.3048',   'ft/s²'),
        'G'                            => array('9.80665',  'g'),
        'GAL'                          => array('0.01',     'gal'),
        'GALILEO'                      => array('0.01',     'gal'),
        'GRAV'                         => array('9.80665',  'g'),
        'HECTOMETER_PER_SQUARE_SECOND' => array('100',      'h/s²'),
        'INCH_PER_SQUARE_SECOND'       => array('0.0254',   'in/s²'),
        'KILOMETER_PER_HOUR_SECOND'    => array(array('' => '5','/' => '18'), 'km/h²'),
        'KILOMETER_PER_SQUARE_SECOND'  => array('1000',     'km/s²'),
        'METER_PER_SQUARE_SECOND'      => array('1',        'm/s²'),
        'MILE_PER_HOUR_MINUTE'         => array(array('' => '22', '/' => '15', '*' => '0.3048', '/' => '60'), 'mph/m'),
        'MILE_PER_HOUR_SECOND'         => array(array('' => '22', '/' => '15', '*' => '0.3048'), 'mph/s'),
        'MILE_PER_SQUARE_SECOND'       => array('1609.344', 'mi/s²'),
        'MILLIGAL'                     => array('0.00001',  'mgal'),
        'MILLIMETER_PER_SQUARE_SECOND' => array('0.001',    'mm/s²'),
        'STANDARD'                     => 'METER_PER_SQUARE_SECOND'
    );
}
