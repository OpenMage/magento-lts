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
 * Class for handling energy conversions
 *
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage Zend_Measure_Energy
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Energy extends Zend_Measure_Abstract
{
    const STANDARD = 'JOULE';

    const ATTOJOULE                  = 'ATTOJOULE';
    const BOARD_OF_TRADE_UNIT        = 'BOARD_OF_TRADE_UNIT';
    const BTU                        = 'BTU';
    const BTU_THERMOCHEMICAL         = 'BTU_TERMOCHEMICAL';
    const CALORIE                    = 'CALORIE';
    const CALORIE_15C                = 'CALORIE_15C';
    const CALORIE_NUTRITIONAL        = 'CALORIE_NUTRITIONAL';
    const CALORIE_THERMOCHEMICAL     = 'CALORIE_THERMOCHEMICAL';
    const CELSIUS_HEAT_UNIT          = 'CELSIUS_HEAT_UNIT';
    const CENTIJOULE                 = 'CENTIJOULE';
    const CHEVAL_VAPEUR_HEURE        = 'CHEVAL_VAPEUR_HEURE';
    const DECIJOULE                  = 'DECIJOULE';
    const DEKAJOULE                  = 'DEKAJOULE';
    const DEKAWATT_HOUR              = 'DEKAWATT_HOUR';
    const DEKATHERM                  = 'DEKATHERM';
    const ELECTRONVOLT               = 'ELECTRONVOLT';
    const ERG                        = 'ERG';
    const EXAJOULE                   = 'EXAJOULE';
    const EXAWATT_HOUR               = 'EXAWATT_HOUR';
    const FEMTOJOULE                 = 'FEMTOJOULE';
    const FOOT_POUND                 = 'FOOT_POUND';
    const FOOT_POUNDAL               = 'FOOT_POUNDAL';
    const GALLON_UK_AUTOMOTIVE       = 'GALLON_UK_AUTOMOTIVE';
    const GALLON_US_AUTOMOTIVE       = 'GALLON_US_AUTOMOTIVE';
    const GALLON_UK_AVIATION         = 'GALLON_UK_AVIATION';
    const GALLON_US_AVIATION         = 'GALLON_US_AVIATION';
    const GALLON_UK_DIESEL           = 'GALLON_UK_DIESEL';
    const GALLON_US_DIESEL           = 'GALLON_US_DIESEL';
    const GALLON_UK_DISTILATE        = 'GALLON_UK_DISTILATE';
    const GALLON_US_DISTILATE        = 'GALLON_US_DISTILATE';
    const GALLON_UK_KEROSINE_JET     = 'GALLON_UK_KEROSINE_JET';
    const GALLON_US_KEROSINE_JET     = 'GALLON_US_KEROSINE_JET';
    const GALLON_UK_LPG              = 'GALLON_UK_LPG';
    const GALLON_US_LPG              = 'GALLON_US_LPG';
    const GALLON_UK_NAPHTA           = 'GALLON_UK_NAPHTA';
    const GALLON_US_NAPHTA           = 'GALLON_US_NAPHTA';
    const GALLON_UK_KEROSENE         = 'GALLON_UK_KEROSINE';
    const GALLON_US_KEROSENE         = 'GALLON_US_KEROSINE';
    const GALLON_UK_RESIDUAL         = 'GALLON_UK_RESIDUAL';
    const GALLON_US_RESIDUAL         = 'GALLON_US_RESIDUAL';
    const GIGAELECTRONVOLT           = 'GIGAELECTRONVOLT';
    const GIGACALORIE                = 'GIGACALORIE';
    const GIGACALORIE_15C            = 'GIGACALORIE_15C';
    const GIGAJOULE                  = 'GIGAJOULE';
    const GIGAWATT_HOUR              = 'GIGAWATT_HOUR';
    const GRAM_CALORIE               = 'GRAM_CALORIE';
    const HARTREE                    = 'HARTREE';
    const HECTOJOULE                 = 'HECTOJOULE';
    const HECTOWATT_HOUR             = 'HECTOWATT_HOUR';
    const HORSEPOWER_HOUR            = 'HORSEPOWER_HOUR';
    const HUNDRED_CUBIC_FOOT_GAS     = 'HUNDRED_CUBIC_FOOT_GAS';
    const INCH_OUNCE                 = 'INCH_OUNCE';
    const INCH_POUND                 = 'INCH_POUND';
    const JOULE                      = 'JOULE';
    const KILOCALORIE_15C            = 'KILOCALORIE_15C';
    const KILOCALORIE                = 'KILOCALORIE';
    const KILOCALORIE_THERMOCHEMICAL = 'KILOCALORIE_THERMOCHEMICAL';
    const KILOELECTRONVOLT           = 'KILOELECTRONVOLT';
    const KILOGRAM_CALORIE           = 'KILOGRAM_CALORIE';
    const KILOGRAM_FORCE_METER       = 'KILOGRAM_FORCE_METER';
    const KILOJOULE                  = 'KILOJOULE';
    const KILOPOND_METER             = 'KILOPOND_METER';
    const KILOTON                    = 'KILOTON';
    const KILOWATT_HOUR              = 'KILOWATT_HOUR';
    const LITER_ATMOSPHERE           = 'LITER_ATMOSPHERE';
    const MEGAELECTRONVOLT           = 'MEGAELECTRONVOLT';
    const MEGACALORIE                = 'MEGACALORIE';
    const MEGACALORIE_15C            = 'MEGACALORIE_15C';
    const MEGAJOULE                  = 'MEGAJOULE';
    const MEGALERG                   = 'MEGALERG';
    const MEGATON                    = 'MEGATON';
    const MEGAWATTHOUR               = 'MEGAWATTHOUR';
    const METER_KILOGRAM_FORCE       = 'METER_KILOGRAM_FORCE';
    const MICROJOULE                 = 'MICROJOULE';
    const MILLIJOULE                 = 'MILLIJOULE';
    const MYRIAWATT_HOUR             = 'MYRIAWATT_HOUR';
    const NANOJOULE                  = 'NANOJOULE';
    const NEWTON_METER               = 'NEWTON_METER';
    const PETAJOULE                  = 'PETAJOULE';
    const PETAWATTHOUR               = 'PETAWATTHOUR';
    const PFERDESTAERKENSTUNDE       = 'PFERDESTAERKENSTUNDE';
    const PICOJOULE                  = 'PICOJOULE';
    const Q_UNIT                     = 'Q_UNIT';
    const QUAD                       = 'QUAD';
    const TERAELECTRONVOLT           = 'TERAELECTRONVOLT';
    const TERAJOULE                  = 'TERAJOULE';
    const TERAWATTHOUR               = 'TERAWATTHOUR';
    const THERM                      = 'THERM';
    const THERM_US                   = 'THERM_US';
    const THERMIE                    = 'THERMIE';
    const TON                        = 'TON';
    const TONNE_COAL                 = 'TONNE_COAL';
    const TONNE_OIL                  = 'TONNE_OIL';
    const WATTHOUR                   = 'WATTHOUR';
    const WATTSECOND                 = 'WATTSECOND';
    const YOCTOJOULE                 = 'YOCTOJOULE';
    const YOTTAJOULE                 = 'YOTTAJOULE';
    const YOTTAWATTHOUR              = 'YOTTAWATTHOUR';
    const ZEPTOJOULE                 = 'ZEPTOJOULE';
    const ZETTAJOULE                 = 'ZETTAJOULE';
    const ZETTAWATTHOUR              = 'ZETTAWATTHOUR';

    /**
     * Calculations for all energy units
     *
     * @var array
     */
    protected $_units = [
        'ATTOJOULE'              => ['1.0e-18',           'aJ'],
        'BOARD_OF_TRADE_UNIT'    => ['3600000',           'BOTU'],
        'BTU'                    => ['1055.0559',         'Btu'],
        'BTU_TERMOCHEMICAL'      => ['1054.3503',         'Btu'],
        'CALORIE'                => ['4.1868',            'cal'],
        'CALORIE_15C'            => ['6.1858',            'cal'],
        'CALORIE_NUTRITIONAL'    => ['4186.8',            'cal'],
        'CALORIE_THERMOCHEMICAL' => ['4.184',             'cal'],
        'CELSIUS_HEAT_UNIT'      => ['1899.1005',         'Chu'],
        'CENTIJOULE'             => ['0.01',              'cJ'],
        'CHEVAL_VAPEUR_HEURE'    => ['2647795.5',         'cv heure'],
        'DECIJOULE'              => ['0.1',               'dJ'],
        'DEKAJOULE'              => ['10',                'daJ'],
        'DEKAWATT_HOUR'          => ['36000',             'daWh'],
        'DEKATHERM'              => ['1.055057e+9',       'dathm'],
        'ELECTRONVOLT'           => ['1.6021773e-19',     'eV'],
        'ERG'                    => ['0.0000001',         'erg'],
        'EXAJOULE'               => ['1.0e+18',           'EJ'],
        'EXAWATT_HOUR'           => ['3.6e+21',           'EWh'],
        'FEMTOJOULE'             => ['1.0e-15',           'fJ'],
        'FOOT_POUND'             => ['1.3558179',         'ft lb'],
        'FOOT_POUNDAL'           => ['0.04214011',        'ft poundal'],
        'GALLON_UK_AUTOMOTIVE'   => ['158237172',         'gal car gasoline'],
        'GALLON_US_AUTOMOTIVE'   => ['131760000',         'gal car gasoline'],
        'GALLON_UK_AVIATION'     => ['158237172',         'gal jet gasoline'],
        'GALLON_US_AVIATION'     => ['131760000',         'gal jet gasoline'],
        'GALLON_UK_DIESEL'       => ['175963194',         'gal diesel'],
        'GALLON_US_DIESEL'       => ['146520000',         'gal diesel'],
        'GALLON_UK_DISTILATE'    => ['175963194',         'gal destilate fuel'],
        'GALLON_US_DISTILATE'    => ['146520000',         'gal destilate fuel'],
        'GALLON_UK_KEROSINE_JET' => ['170775090',         'gal jet kerosine'],
        'GALLON_US_KEROSINE_JET' => ['142200000',         'gal jet kerosine'],
        'GALLON_UK_LPG'          => ['121005126.0865275', 'gal lpg'],
        'GALLON_US_LPG'          => ['100757838.45',      'gal lpg'],
        'GALLON_UK_NAPHTA'       => ['160831224',         'gal jet fuel'],
        'GALLON_US_NAPHTA'       => ['133920000',         'gal jet fuel'],
        'GALLON_UK_KEROSINE'     => ['170775090',         'gal kerosine'],
        'GALLON_US_KEROSINE'     => ['142200000',         'gal kerosine'],
        'GALLON_UK_RESIDUAL'     => ['189798138',         'gal residual fuel'],
        'GALLON_US_RESIDUAL'     => ['158040000',         'gal residual fuel'],
        'GIGAELECTRONVOLT'       => ['1.6021773e-10',     'GeV'],
        'GIGACALORIE'            => ['4186800000',        'Gcal'],
        'GIGACALORIE_15C'        => ['4185800000',        'Gcal'],
        'GIGAJOULE'              => ['1.0e+9',            'GJ'],
        'GIGAWATT_HOUR'          => ['3.6e+12',           'GWh'],
        'GRAM_CALORIE'           => ['4.1858',            'g cal'],
        'HARTREE'                => ['4.3597482e-18',     'Eh'],
        'HECTOJOULE'             => ['100',               'hJ'],
        'HECTOWATT_HOUR'         => ['360000',            'hWh'],
        'HORSEPOWER_HOUR'        => ['2684519.5',         'hph'],
        'HUNDRED_CUBIC_FOOT_GAS' => ['108720000',         'hundred ft� gas'],
        'INCH_OUNCE'             => ['0.0070615518',      'in oc'],
        'INCH_POUND'             => ['0.112984825',       'in lb'],
        'JOULE'                  => ['1',                 'J'],
        'KILOCALORIE_15C'        => ['4185.8',            'kcal'],
        'KILOCALORIE'            => ['4186','8',          'kcal'],
        'KILOCALORIE_THERMOCHEMICAL' => ['4184',          'kcal'],
        'KILOELECTRONVOLT'       => ['1.6021773e-16',     'keV'],
        'KILOGRAM_CALORIE'       => ['4185.8',            'kg cal'],
        'KILOGRAM_FORCE_METER'   => ['9.80665',           'kgf m'],
        'KILOJOULE'              => ['1000',              'kJ'],
        'KILOPOND_METER'         => ['9.80665',           'kp m'],
        'KILOTON'                => ['4.184e+12',         'kt'],
        'KILOWATT_HOUR'          => ['3600000',           'kWh'],
        'LITER_ATMOSPHERE'       => ['101.325',           'l atm'],
        'MEGAELECTRONVOLT'       => ['1.6021773e-13',     'MeV'],
        'MEGACALORIE'            => ['4186800',           'Mcal'],
        'MEGACALORIE_15C'        => ['4185800',           'Mcal'],
        'MEGAJOULE'              => ['1000000',           'MJ'],
        'MEGALERG'               => ['0.1',               'megalerg'],
        'MEGATON'                => ['4.184e+15',         'Mt'],
        'MEGAWATTHOUR'           => ['3.6e+9',            'MWh'],
        'METER_KILOGRAM_FORCE'   => ['9.80665',           'm kgf'],
        'MICROJOULE'             => ['0.000001',          '�J'],
        'MILLIJOULE'             => ['0.001',             'mJ'],
        'MYRIAWATT_HOUR'         => ['3.6e+7',            'myWh'],
        'NANOJOULE'              => ['1.0e-9',            'nJ'],
        'NEWTON_METER'           => ['1',                 'Nm'],
        'PETAJOULE'              => ['1.0e+15',           'PJ'],
        'PETAWATTHOUR'           => ['3.6e+18',           'PWh'],
        'PFERDESTAERKENSTUNDE'   => ['2647795.5',         'ps h'],
        'PICOJOULE'              => ['1.0e-12',           'pJ'],
        'Q_UNIT'                 => ['1.0550559e+21',     'Q unit'],
        'QUAD'                   => ['1.0550559e+18',     'quad'],
        'TERAELECTRONVOLT'       => ['1.6021773e-7',      'TeV'],
        'TERAJOULE'              => ['1.0e+12',           'TJ'],
        'TERAWATTHOUR'           => ['3.6e+15',           'TWh'],
        'THERM'                  => ['1.0550559e+8',      'thm'],
        'THERM_US'               => ['1.054804e+8',       'thm'],
        'THERMIE'                => ['4185800',           'th'],
        'TON'                    => ['4.184e+9',          'T explosive'],
        'TONNE_COAL'             => ['2.93076e+10',       'T coal'],
        'TONNE_OIL'              => ['4.1868e+10',        'T oil'],
        'WATTHOUR'               => ['3600',              'Wh'],
        'WATTSECOND'             => ['1',                 'Ws'],
        'YOCTOJOULE'             => ['1.0e-24',           'yJ'],
        'YOTTAJOULE'             => ['1.0e+24',           'YJ'],
        'YOTTAWATTHOUR'          => ['3.6e+27',           'YWh'],
        'ZEPTOJOULE'             => ['1.0e-21',           'zJ'],
        'ZETTAJOULE'             => ['1.0e+21',           'ZJ'],
        'ZETTAWATTHOUR'          => ['3.6e+24',           'ZWh'],
        'STANDARD'               => 'JOULE'
    ];
}
