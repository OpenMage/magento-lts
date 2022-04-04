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
 * Class for handling pressure conversions
 *
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage Zend_Measure_Pressure
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Pressure extends Zend_Measure_Abstract
{
    const STANDARD = 'NEWTON_PER_SQUARE_METER';

    const ATMOSPHERE                           = 'ATMOSPHERE';
    const ATMOSPHERE_TECHNICAL                 = 'ATMOSPHERE_TECHNICAL';
    const ATTOBAR                              = 'ATTOBAR';
    const ATTOPASCAL                           = 'ATTOPASCAL';
    const BAR                                  = 'BAR';
    const BARAD                                = 'BARAD';
    const BARYE                                = 'BARYE';
    const CENTIBAR                             = 'CENTIBAR';
    const CENTIHG                              = 'CENTIHG';
    const CENTIMETER_MERCURY_0C                = 'CENTIMETER_MERCURY_0C';
    const CENTIMETER_WATER_4C                  = 'CENTIMETER_WATER_4C';
    const CENTIPASCAL                          = 'CENTIPASCAL';
    const CENTITORR                            = 'CENTITORR';
    const DECIBAR                              = 'DECIBAR';
    const DECIPASCAL                           = 'DECIPASCAL';
    const DECITORR                             = 'DECITORR';
    const DEKABAR                              = 'DEKABAR';
    const DEKAPASCAL                           = 'DEKAPASCAL';
    const DYNE_PER_SQUARE_CENTIMETER           = 'DYNE_PER_SQUARE_CENTIMETER';
    const EXABAR                               = 'EXABAR';
    const EXAPASCAL                            = 'EXAPASCAL';
    const FEMTOBAR                             = 'FEMTOBAR';
    const FEMTOPASCAL                          = 'FEMTOPASCAL';
    const FOOT_AIR_0C                          = 'FOOT_AIR_0C';
    const FOOT_AIR_15C                         = 'FOOT_AIR_15C';
    const FOOT_HEAD                            = 'FOOT_HEAD';
    const FOOT_MERCURY_0C                      = 'FOOT_MERCURY_0C';
    const FOOT_WATER_4C                        = 'FOOT_WATER_4C';
    const GIGABAR                              = 'GIGABAR';
    const GIGAPASCAL                           = 'GIGAPASCAL';
    const GRAM_FORCE_SQUARE_CENTIMETER         = 'GRAM_FORCE_SQUARE_CENTIMETER';
    const HECTOBAR                             = 'HECTOBAR';
    const HECTOPASCAL                          = 'HECTOPASCAL';
    const INCH_AIR_0C                          = 'INCH_AIR_0C';
    const INCH_AIR_15C                         = 'INCH_AIR_15C';
    const INCH_MERCURY_0C                      = 'INCH_MERCURY_0C';
    const INCH_WATER_4C                        = 'INCH_WATER_4C';
    const KILOBAR                              = 'KILOBAR';
    const KILOGRAM_FORCE_PER_SQUARE_CENTIMETER = 'KILOGRAM_FORCE_PER_SQUARE_CENTIMETER';
    const KILOGRAM_FORCE_PER_SQUARE_METER      = 'KILOGRAM_FORCE_PER_SQUARE_METER';
    const KILOGRAM_FORCE_PER_SQUARE_MILLIMETER = 'KILOGRAM_FORCE_PER_SQUARE_MILLIMETER';
    const KILONEWTON_PER_SQUARE_METER          = 'KILONEWTON_PER_SQUARE_METER';
    const KILOPASCAL                           = 'KILOPASCAL';
    const KILOPOND_PER_SQUARE_CENTIMETER       = 'KILOPOND_PER_SQUARE_CENTIMETER';
    const KILOPOND_PER_SQUARE_METER            = 'KILOPOND_PER_SQUARE_METER';
    const KILOPOND_PER_SQUARE_MILLIMETER       = 'KILOPOND_PER_SQUARE_MILLIMETER';
    const KIP_PER_SQUARE_FOOT                  = 'KIP_PER_SQUARE_FOOT';
    const KIP_PER_SQUARE_INCH                  = 'KIP_PER_SQUARE_INCH';
    const MEGABAR                              = 'MEGABAR';
    const MEGANEWTON_PER_SQUARE_METER          = 'MEGANEWTON_PER_SQUARE_METER';
    const MEGAPASCAL                           = 'MEGAPASCAL';
    const METER_AIR_0C                         = 'METER_AIR_0C';
    const METER_AIR_15C                        = 'METER_AIR_15C';
    const METER_HEAD                           = 'METER_HEAD';
    const MICROBAR                             = 'MICROBAR';
    const MICROMETER_MERCURY_0C                = 'MICROMETER_MERCURY_0C';
    const MICROMETER_WATER_4C                  = 'MICROMETER_WATER_4C';
    const MICRON_MERCURY_0C                    = 'MICRON_MERCURY_0C';
    const MICROPASCAL                          = 'MICROPASCAL';
    const MILLIBAR                             = 'MILLIBAR';
    const MILLIHG                              = 'MILLIHG';
    const MILLIMETER_MERCURY_0C                = 'MILLIMETER_MERCURY_0C';
    const MILLIMETER_WATER_4C                  = 'MILLIMETER_WATER_4C';
    const MILLIPASCAL                          = 'MILLIPASCAL';
    const MILLITORR                            = 'MILLITORR';
    const NANOBAR                              = 'NANOBAR';
    const NANOPASCAL                           = 'NANOPASCAL';
    const NEWTON_PER_SQUARE_METER              = 'NEWTON_PER_SQUARE_METER';
    const NEWTON_PER_SQUARE_MILLIMETER         = 'NEWTON_PER_SQUARE_MILLIMETER';
    const OUNCE_PER_SQUARE_INCH                = 'OUNCE_PER_SQUARE_INCH';
    const PASCAL                               = 'PASCAL';
    const PETABAR                              = 'PETABAR';
    const PETAPASCAL                           = 'PETAPASCAL';
    const PICOBAR                              = 'PICOBAR';
    const PICOPASCAL                           = 'PICOPASCAL';
    const PIEZE                                = 'PIEZE';
    const POUND_PER_SQUARE_FOOT                = 'POUND_PER_SQUARE_FOOT';
    const POUND_PER_SQUARE_INCH                = 'POUND_PER_SQUARE_INCH';
    const POUNDAL_PER_SQUARE_FOOT              = 'POUNDAL_PER_SQUARE_FOOT';
    const STHENE_PER_SQUARE_METER              = 'STHENE_PER_SQUARE_METER';
    const TECHNICAL_ATMOSPHERE                 = 'TECHNICAL_ATMOSPHERE';
    const TERABAR                              = 'TERABAR';
    const TERAPASCAL                           = 'TERAPASCAL';
    const TON_PER_SQUARE_FOOT                  = 'TON_PER_SQUARE_FOOT';
    const TON_PER_SQUARE_FOOT_SHORT            = 'TON_PER_SQUARE_FOOT_SHORT';
    const TON_PER_SQUARE_INCH                  = 'TON_PER_SQUARE_INCH';
    const TON_PER_SQUARE_INCH_SHORT            = 'TON_PER_SQUARE_INCH_SHORT';
    const TON_PER_SQUARE_METER                 = 'TON_PER_SQUARE_METER';
    const TORR                                 = 'TORR';
    const WATER_COLUMN_CENTIMETER              = 'WATER_COLUMN_CENTIMETER';
    const WATER_COLUMN_INCH                    = 'WATER_COLUMN_INCH';
    const WATER_COLUMN_MILLIMETER              = 'WATER_COLUMN_MILLIMETER';
    const YOCTOBAR                             = 'YOCTOBAR';
    const YOCTOPASCAL                          = 'YOCTOPASCAL';
    const YOTTABAR                             = 'YOTTABAR';
    const YOTTAPASCAL                          = 'YOTTAPASCAL';
    const ZEPTOBAR                             = 'ZEPTOBAR';
    const ZEPTOPASCAL                          = 'ZEPTOPASCAL';
    const ZETTABAR                             = 'ZETTABAR';
    const ZETTAPASCAL                          = 'ZETTAPASCAL';

    /**
     * Calculations for all pressure units
     *
     * @var array
     */
    protected $_units = [
        'ATMOSPHERE'            => ['101325.01', 'atm'],
        'ATMOSPHERE_TECHNICAL'  => ['98066.5',   'atm'],
        'ATTOBAR'               => ['1.0e-13',   'ab'],
        'ATTOPASCAL'            => ['1.0e-18',   'aPa'],
        'BAR'                   => ['100000',    'b'],
        'BARAD'                 => ['0.1',       'barad'],
        'BARYE'                 => ['0.1',       'ba'],
        'CENTIBAR'              => ['1000',      'cb'],
        'CENTIHG'               => ['1333.2239', 'cHg'],
        'CENTIMETER_MERCURY_0C' => ['1333.2239', 'cm mercury (0°C)'],
        'CENTIMETER_WATER_4C'   => ['98.0665',   'cm water (4°C)'],
        'CENTIPASCAL'           => ['0.01',      'cPa'],
        'CENTITORR'             => ['1.3332237', 'cTorr'],
        'DECIBAR'               => ['10000',     'db'],
        'DECIPASCAL'            => ['0.1',       'dPa'],
        'DECITORR'              => ['13.332237', 'dTorr'],
        'DEKABAR'               => ['1000000',   'dab'],
        'DEKAPASCAL'            => ['10',        'daPa'],
        'DYNE_PER_SQUARE_CENTIMETER' => ['0.1',  'dyn/cm²'],
        'EXABAR'                => ['1.0e+23',   'Eb'],
        'EXAPASCAL'             => ['1.0e+18',   'EPa'],
        'FEMTOBAR'              => ['1.0e-10',   'fb'],
        'FEMTOPASCAL'           => ['1.0e-15',   'fPa'],
        'FOOT_AIR_0C'           => ['3.8640888', 'ft air (0°C)'],
        'FOOT_AIR_15C'          => ['3.6622931', 'ft air (15°C)'],
        'FOOT_HEAD'             => ['2989.0669', 'ft head'],
        'FOOT_MERCURY_0C'       => ['40636.664', 'ft mercury (0°C)'],
        'FOOT_WATER_4C'         => ['2989.0669', 'ft water (4°C)'],
        'GIGABAR'               => ['1.0e+14',   'Gb'],
        'GIGAPASCAL'            => ['1.0e+9',    'GPa'],
        'GRAM_FORCE_SQUARE_CENTIMETER' => ['98.0665', 'gf'],
        'HECTOBAR'              => ['1.0e+7',    'hb'],
        'HECTOPASCAL'           => ['100',       'hPa'],
        'INCH_AIR_0C'           => [['' => '3.8640888', '/' => '12'], 'in air (0°C)'],
        'INCH_AIR_15C'          => [['' => '3.6622931', '/' => '12'], 'in air (15°C)'],
        'INCH_MERCURY_0C'       => [['' => '40636.664', '/' => '12'], 'in mercury (0°C)'],
        'INCH_WATER_4C'         => [['' => '2989.0669', '/' => '12'], 'in water (4°C)'],
        'KILOBAR'               => ['1.0e+8',    'kb'],
        'KILOGRAM_FORCE_PER_SQUARE_CENTIMETER' => ['98066.5', 'kgf/cm²'],
        'KILOGRAM_FORCE_PER_SQUARE_METER'      => ['9.80665', 'kgf/m²'],
        'KILOGRAM_FORCE_PER_SQUARE_MILLIMETER' => ['9806650', 'kgf/mm²'],
        'KILONEWTON_PER_SQUARE_METER'          => ['1000',    'kN/m²'],
        'KILOPASCAL'            => ['1000',      'kPa'],
        'KILOPOND_PER_SQUARE_CENTIMETER' => ['98066.5', 'kp/cm²'],
        'KILOPOND_PER_SQUARE_METER'      => ['9.80665', 'kp/m²'],
        'KILOPOND_PER_SQUARE_MILLIMETER' => ['9806650', 'kp/mm²'],
        'KIP_PER_SQUARE_FOOT'   => [['' => '430.92233', '/' => '0.009'],   'kip/ft²'],
        'KIP_PER_SQUARE_INCH'   => [['' => '62052.81552', '/' => '0.009'], 'kip/in²'],
        'MEGABAR'               => ['1.0e+11',    'Mb'],
        'MEGANEWTON_PER_SQUARE_METER' => ['1000000', 'MN/m²'],
        'MEGAPASCAL'            => ['1000000',    'MPa'],
        'METER_AIR_0C'          => ['12.677457',  'm air (0°C)'],
        'METER_AIR_15C'         => ['12.015397',  'm air (15°C)'],
        'METER_HEAD'            => ['9804.139432', 'm head'],
        'MICROBAR'              => ['0.1',        'µb'],
        'MICROMETER_MERCURY_0C' => ['0.13332239', 'µm mercury (0°C)'],
        'MICROMETER_WATER_4C'   => ['0.00980665', 'µm water (4°C)'],
        'MICRON_MERCURY_0C'     => ['0.13332239', 'µ mercury (0°C)'],
        'MICROPASCAL'           => ['0.000001',   'µPa'],
        'MILLIBAR'              => ['100',        'mb'],
        'MILLIHG'               => ['133.32239',  'mHg'],
        'MILLIMETER_MERCURY_0C' => ['133.32239',  'mm mercury (0°C)'],
        'MILLIMETER_WATER_4C'   => ['9.80665',    'mm water (0°C)'],
        'MILLIPASCAL'           => ['0.001',      'mPa'],
        'MILLITORR'             => ['0.13332237', 'mTorr'],
        'NANOBAR'               => ['0.0001',     'nb'],
        'NANOPASCAL'            => ['1.0e-9',     'nPa'],
        'NEWTON_PER_SQUARE_METER'      => ['1',   'N/m²'],
        'NEWTON_PER_SQUARE_MILLIMETER' => ['1000000',   'N/mm²'],
        'OUNCE_PER_SQUARE_INCH'        => ['430.92233', 'oz/in²'],
        'PASCAL'                => ['1',          'Pa'],
        'PETABAR'               => ['1.0e+20',    'Pb'],
        'PETAPASCAL'            => ['1.0e+15',    'PPa'],
        'PICOBAR'               => ['0.0000001',  'pb'],
        'PICOPASCAL'            => ['1.0e-12',    'pPa'],
        'PIEZE'                 => ['1000',       'pz'],
        'POUND_PER_SQUARE_FOOT' => [['' => '430.92233', '/' => '9'], 'lb/ft²'],
        'POUND_PER_SQUARE_INCH' => ['6894.75728', 'lb/in²'],
        'POUNDAL_PER_SQUARE_FOOT' => ['1.4881639', 'pdl/ft²'],
        'STHENE_PER_SQUARE_METER' => ['1000',     'sn/m²'],
        'TECHNICAL_ATMOSPHERE'  => ['98066.5',    'at'],
        'TERABAR'               => ['1.0e+17',    'Tb'],
        'TERAPASCAL'            => ['1.0e+12',    'TPa'],
        'TON_PER_SQUARE_FOOT'   => [['' => '120658.2524', '/' => '1.125'],      't/ft²'],
        'TON_PER_SQUARE_FOOT_SHORT' => [['' => '430.92233', '/' => '0.0045'],   't/ft²'],
        'TON_PER_SQUARE_INCH'   => [['' => '17374788.3456', '/' => '1.125'],    't/in²'],
        'TON_PER_SQUARE_INCH_SHORT' => [['' => '62052.81552', '/' => '0.0045'], 't/in²'],
        'TON_PER_SQUARE_METER'  => ['9806.65',    't/m²'],
        'TORR'                  => ['133.32237',  'Torr'],
        'WATER_COLUMN_CENTIMETER' => ['98.0665',  'WC (cm)'],
        'WATER_COLUMN_INCH'       => [['' => '2989.0669', '/' => '12'], 'WC (in)'],
        'WATER_COLUMN_MILLIMETER' => ['9.80665',  'WC (mm)'],
        'YOCTOBAR'              => ['1.0e-19',    'yb'],
        'YOCTOPASCAL'           => ['1.0e-24',    'yPa'],
        'YOTTABAR'              => ['1.0e+29',    'Yb'],
        'YOTTAPASCAL'           => ['1.0e+24',    'YPa'],
        'ZEPTOBAR'              => ['1.0e-16',    'zb'],
        'ZEPTOPASCAL'           => ['1.0e-21',    'zPa'],
        'ZETTABAR'              => ['1.0e+26',    'Zb'],
        'ZETTAPASCAL'           => ['1.0e+21',    'ZPa'],
        'STANDARD'              => 'NEWTON_PER_SQUARE_METER'
    ];
}
