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
 * @category  Zend
 * @package   Zend_Measure
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id$
 */

/**
 * Implement needed classes
 */
#require_once 'Zend/Measure/Abstract.php';
#require_once 'Zend/Locale.php';

/**
 * Class for handling power conversions
 *
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage Zend_Measure_Power
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Power extends Zend_Measure_Abstract
{
    const STANDARD = 'WATT';

    const ATTOWATT                               = 'ATTOWATT';
    const BTU_PER_HOUR                           = 'BTU_PER_HOUR';
    const BTU_PER_MINUTE                         = 'BTU_PER_MINUTE';
    const BTU_PER_SECOND                         = 'BTU_PER_SECOND';
    const CALORIE_PER_HOUR                       = 'CALORIE_PER_HOUR';
    const CALORIE_PER_MINUTE                     = 'CALORIE_PER_MINUTE';
    const CALORIE_PER_SECOND                     = 'CALORIE_PER_SECOND';
    const CENTIWATT                              = 'CENTIWATT';
    const CHEVAL_VAPEUR                          = 'CHEVAL_VAPEUR';
    const CLUSEC                                 = 'CLUSEC';
    const DECIWATT                               = 'DECIWATT';
    const DEKAWATT                               = 'DEKAWATT';
    const DYNE_CENTIMETER_PER_HOUR               = 'DYNE_CENTIMETER_PER_HOUR';
    const DYNE_CENTIMETER_PER_MINUTE             = 'DYNE_CENTIMETER_PER_MINUTE';
    const DYNE_CENTIMETER_PER_SECOND             = 'DYNE_CENTIMETER_PER_SECOND';
    const ERG_PER_HOUR                           = 'ERG_PER_HOUR';
    const ERG_PER_MINUTE                         = 'ERG_PER_MINUTE';
    const ERG_PER_SECOND                         = 'ERG_PER_SECOND';
    const EXAWATT                                = 'EXAWATT';
    const FEMTOWATT                              = 'FEMTOWATT';
    const FOOT_POUND_FORCE_PER_HOUR              = 'FOOT_POUND_FORCE_PER_HOUR';
    const FOOT_POUND_FORCE_PER_MINUTE            = 'FOOT_POUND_FORCE_PER_MINUTE';
    const FOOT_POUND_FORCE_PER_SECOND            = 'FOOT_POUND_FORCE_PER_SECOND';
    const FOOT_POUNDAL_PER_HOUR                  = 'FOOT_POUNDAL_PER_HOUR';
    const FOOT_POUNDAL_PER_MINUTE                = 'FOOT_POUNDAL_PER_MINUTE';
    const FOOT_POUNDAL_PER_SECOND                = 'FOOT_POUNDAL_PER_SECOND';
    const GIGAWATT                               = 'GIGAWATT';
    const GRAM_FORCE_CENTIMETER_PER_HOUR         = 'GRAM_FORCE_CENTIMETER_PER_HOUR';
    const GRAM_FORCE_CENTIMETER_PER_MINUTE       = 'GRAM_FORCE_CENTIMETER_PER_MINUTE';
    const GRAM_FORCE_CENTIMETER_PER_SECOND       = 'GRAM_FORCE_CENTIMETER_PER_SECOND';
    const HECTOWATT                              = 'HECTOWATT';
    const HORSEPOWER_INTERNATIONAL               = 'HORSEPOWER_INTERNATIONAL';
    const HORSEPOWER_ELECTRIC                    = 'HORSEPOWER_ELECTRIC';
    const HORSEPOWER                             = 'HORSEPOWER';
    const HORSEPOWER_WATER                       = 'HORSEPOWER_WATER';
    const INCH_OUNCE_FORCE_REVOLUTION_PER_MINUTE = 'INCH_OUNCH_FORCE_REVOLUTION_PER_MINUTE';
    const JOULE_PER_HOUR                         = 'JOULE_PER_HOUR';
    const JOULE_PER_MINUTE                       = 'JOULE_PER_MINUTE';
    const JOULE_PER_SECOND                       = 'JOULE_PER_SECOND';
    const KILOCALORIE_PER_HOUR                   = 'KILOCALORIE_PER_HOUR';
    const KILOCALORIE_PER_MINUTE                 = 'KILOCALORIE_PER_MINUTE';
    const KILOCALORIE_PER_SECOND                 = 'KILOCALORIE_PER_SECOND';
    const KILOGRAM_FORCE_METER_PER_HOUR          = 'KILOGRAM_FORCE_METER_PER_HOUR';
    const KILOGRAM_FORCE_METER_PER_MINUTE        = 'KILOGRAM_FORCE_METER_PER_MINUTE';
    const KILOGRAM_FORCE_METER_PER_SECOND        = 'KILOGRAM_FORCE_METER_PER_SECOND';
    const KILOPOND_METER_PER_HOUR                = 'KILOPOND_METER_PER_HOUR';
    const KILOPOND_METER_PER_MINUTE              = 'KILOPOND_METER_PER_MINUTE';
    const KILOPOND_METER_PER_SECOND              = 'KILOPOND_METER_PER_SECOND';
    const KILOWATT                               = 'KILOWATT';
    const MEGAWATT                               = 'MEGAWATT';
    const MICROWATT                              = 'MICROWATT';
    const MILLION_BTU_PER_HOUR                   = 'MILLION_BTU_PER_HOUR';
    const MILLIWATT                              = 'MILLIWATT';
    const NANOWATT                               = 'NANOWATT';
    const NEWTON_METER_PER_HOUR                  = 'NEWTON_METER_PER_HOUR';
    const NEWTON_METER_PER_MINUTE                = 'NEWTON_METER_PER_MINUTE';
    const NEWTON_METER_PER_SECOND                = 'NEWTON_METER_PER_SECOND';
    const PETAWATT                               = 'PETAWATT';
    const PFERDESTAERKE                          = 'PFERDESTAERKE';
    const PICOWATT                               = 'PICOWATT';
    const PONCELET                               = 'PONCELET';
    const POUND_SQUARE_FOOR_PER_CUBIC_SECOND     = 'POUND_SQUARE_FOOT_PER_CUBIC_SECOND';
    const TERAWATT                               = 'TERAWATT';
    const TON_OF_REFRIGERATION                   = 'TON_OF_REFRIGERATION';
    const WATT                                   = 'WATT';
    const YOCTOWATT                              = 'YOCTOWATT';
    const YOTTAWATT                              = 'YOTTAWATT';
    const ZEPTOWATT                              = 'ZEPTOWATT';
    const ZETTAWATT                              = 'ZETTAWATT';

    /**
     * Calculations for all power units
     *
     * @var array
     */
    protected $_units = [
        'ATTOWATT'                    => ['1.0e-18',           'aW'],
        'BTU_PER_HOUR'                => ['0.29307197',        'BTU/h'],
        'BTU_PER_MINUTE'              => ['17.5843182',        'BTU/m'],
        'BTU_PER_SECOND'              => ['1055.059092',       'BTU/s'],
        'CALORIE_PER_HOUR'            => [['' => '11630', '*' => '1.0e-7'],    'cal/h'],
        'CALORIE_PER_MINUTE'          => [['' => '697800', '*' => '1.0e-7'],   'cal/m'],
        'CALORIE_PER_SECOND'          => [['' => '41868000', '*' => '1.0e-7'], 'cal/s'],
        'CENTIWATT'                   => ['0.01',              'cW'],
        'CHEVAL_VAPEUR'               => ['735.49875',         'cv'],
        'CLUSEC'                      => ['0.0000013332237',   'clusec'],
        'DECIWATT'                    => ['0.1',               'dW'],
        'DEKAWATT'                    => ['10',                'daW'],
        'DYNE_CENTIMETER_PER_HOUR'    => [['' => '1.0e-7','/' => '3600'], 'dyn cm/h'],
        'DYNE_CENTIMETER_PER_MINUTE'  => [['' => '1.0e-7','/' => '60'],   'dyn cm/m'],
        'DYNE_CENTIMETER_PER_SECOND'  => ['1.0e-7',            'dyn cm/s'],
        'ERG_PER_HOUR'                => [['' => '1.0e-7','/' => '3600'], 'erg/h'],
        'ERG_PER_MINUTE'              => [['' => '1.0e-7','/' => '60'],   'erg/m'],
        'ERG_PER_SECOND'              => ['1.0e-7',            'erg/s'],
        'EXAWATT'                     => ['1.0e+18',           'EW'],
        'FEMTOWATT'                   => ['1.0e-15',           'fW'],
        'FOOT_POUND_FORCE_PER_HOUR'   => [['' => '1.3558179', '/' => '3600'], 'ft lb/h'],
        'FOOT_POUND_FORCE_PER_MINUTE' => [['' => '1.3558179', '/' => '60'],   'ft lb/m'],
        'FOOT_POUND_FORCE_PER_SECOND' => ['1.3558179',         'ft lb/s'],
        'FOOT_POUNDAL_PER_HOUR'       => [['' => '0.04214011','/' => '3600'], 'ft pdl/h'],
        'FOOT_POUNDAL_PER_MINUTE'     => [['' => '0.04214011', '/' => '60'],  'ft pdl/m'],
        'FOOT_POUNDAL_PER_SECOND'     => ['0.04214011',        'ft pdl/s'],
        'GIGAWATT'                    => ['1.0e+9',            'GW'],
        'GRAM_FORCE_CENTIMETER_PER_HOUR' => [['' => '0.0000980665','/' => '3600'], 'gf cm/h'],
        'GRAM_FORCE_CENTIMETER_PER_MINUTE' => [['' => '0.0000980665','/' => '60'], 'gf cm/m'],
        'GRAM_FORCE_CENTIMETER_PER_SECOND' => ['0.0000980665', 'gf cm/s'],
        'HECTOWATT'                   => ['100',               'hW'],
        'HORSEPOWER_INTERNATIONAL'    => ['745.69987',         'hp'],
        'HORSEPOWER_ELECTRIC'         => ['746',               'hp'],
        'HORSEPOWER'                  => ['735.49875',         'hp'],
        'HORSEPOWER_WATER'            => ['746.043',           'hp'],
        'INCH_OUNCH_FORCE_REVOLUTION_PER_MINUTE' => ['0.00073948398',    'in ocf/m'],
        'JOULE_PER_HOUR'              => [['' => '1', '/' => '3600'], 'J/h'],
        'JOULE_PER_MINUTE'            => [['' => '1', '/' => '60'],   'J/m'],
        'JOULE_PER_SECOND'            => ['1',                 'J/s'],
        'KILOCALORIE_PER_HOUR'        => ['1.163',             'kcal/h'],
        'KILOCALORIE_PER_MINUTE'      => ['69.78',             'kcal/m'],
        'KILOCALORIE_PER_SECOND'      => ['4186.8',            'kcal/s'],
        'KILOGRAM_FORCE_METER_PER_HOUR' => [['' => '9.80665', '/' => '3600'], 'kgf m/h'],
        'KILOGRAM_FORCE_METER_PER_MINUTE' => [['' => '9.80665', '/' => '60'], 'kfg m/m'],
        'KILOGRAM_FORCE_METER_PER_SECOND' => ['9.80665',       'kfg m/s'],
        'KILOPOND_METER_PER_HOUR'     => [['' => '9.80665', '/' => '3600'], 'kp/h'],
        'KILOPOND_METER_PER_MINUTE'   => [['' => '9.80665', '/' => '60'],   'kp/m'],
        'KILOPOND_METER_PER_SECOND'   => ['9.80665',           'kp/s'],
        'KILOWATT'                    => ['1000',              'kW'],
        'MEGAWATT'                    => ['1000000',           'MW'],
        'MICROWATT'                   => ['0.000001',          'µW'],
        'MILLION_BTU_PER_HOUR'        => ['293071.07',         'mio BTU/h'],
        'MILLIWATT'                   => ['0.001',             'mM'],
        'NANOWATT'                    => ['1.0e-9',            'nN'],
        'NEWTON_METER_PER_HOUR'       => [['' => '1', '/' => '3600'], 'Nm/h'],
        'NEWTON_METER_PER_MINUTE'     => [['' => '1', '/' => '60'],   'Nm/m'],
        'NEWTON_METER_PER_SECOND'     => ['1',                 'Nm/s'],
        'PETAWATT'                    => ['1.0e+15',           'PW'],
        'PFERDESTAERKE'               => ['735.49875',         'PS'],
        'PICOWATT'                    => ['1.0e-12',           'pW'],
        'PONCELET'                    => ['980.665',           'p'],
        'POUND_SQUARE_FOOT_PER_CUBIC_SECOND' => ['0.04214011', 'lb ft²/s³'],
        'TERAWATT'                    => ['1.0e+12',           'TW'],
        'TON_OF_REFRIGERATION'        => ['3516.85284',        'RT'],
        'WATT'                        => ['1',                 'W'],
        'YOCTOWATT'                   => ['1.0e-24',           'yW'],
        'YOTTAWATT'                   => ['1.0e+24',           'YW'],
        'ZEPTOWATT'                   => ['1.0e-21',           'zW'],
        'ZETTAWATT'                   => ['1.0e+21',           'ZW'],
        'STANDARD'                    => 'WATT'
    ];
}
