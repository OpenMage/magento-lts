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
 * Class for handling cooking volume conversions
 *
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage Zend_Measure_Cooking_Volume
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Cooking_Volume extends Zend_Measure_Abstract
{
    const STANDARD = 'CUBIC_METER';

    const CAN_2POINT5       = 'CAN_2POINT5';
    const CAN_10            = 'CAN_10';
    const BARREL_WINE       = 'BARREL_WINE';
    const BARREL            = 'BARREL';
    const BARREL_US_DRY     = 'BARREL_US_DRY';
    const BARREL_US_FEDERAL = 'BARREL_US_FEDERAL';
    const BARREL_US         = 'BARREL_US';
    const BUCKET            = 'BUCKET';
    const BUCKET_US         = 'BUCKET_US';
    const BUSHEL            = 'BUSHEL';
    const BUSHEL_US         = 'BUSHEL_US';
    const CENTILITER        = 'CENTILITER';
    const COFFEE_SPOON      = 'COFFEE_SPOON';
    const CUBIC_CENTIMETER  = 'CUBIC_CENTIMETER';
    const CUBIC_DECIMETER   = 'CUBIC_DECIMETER';
    const CUBIC_FOOT        = 'CUBIC_FOOT';
    const CUBIC_INCH        = 'CUBIC_INCH';
    const CUBIC_METER       = 'CUBIC_METER';
    const CUBIC_MICROMETER  = 'CUBIC_MICROMETER';
    const CUBIC_MILLIMETER  = 'CUBIC_MILLIMETER';
    const CUP_CANADA        = 'CUP_CANADA';
    const CUP               = 'CUP';
    const CUP_US            = 'CUP_US';
    const DASH              = 'DASH';
    const DECILITER         = 'DECILITER';
    const DEKALITER         = 'DEKALITER';
    const DEMI              = 'DEMI';
    const DRAM              = 'DRAM';
    const DROP              = 'DROP';
    const FIFTH             = 'FIFTH';
    const GALLON            = 'GALLON';
    const GALLON_US_DRY     = 'GALLON_US_DRY';
    const GALLON_US         = 'GALLON_US';
    const GILL              = 'GILL';
    const GILL_US           = 'GILL_US';
    const HECTOLITER        = 'HECTOLITER';
    const HOGSHEAD          = 'HOGSHEAD';
    const HOGSHEAD_US       = 'HOGSHEAD_US';
    const JIGGER            = 'JIGGER';
    const KILOLITER         = 'KILOLITER';
    const LITER             = 'LITER';
    const MEASURE           = 'MEASURE';
    const MEGALITER         = 'MEGALITER';
    const MICROLITER        = 'MICROLITER';
    const MILLILITER        = 'MILLILITER';
    const MINIM             = 'MINIM';
    const MINIM_US          = 'MINIM_US';
    const OUNCE             = 'OUNCE';
    const OUNCE_US          = 'OUNCE_US';
    const PECK              = 'PECK';
    const PECK_US           = 'PECK_US';
    const PINCH             = 'PINCH';
    const PINT              = 'PINT';
    const PINT_US_DRY       = 'PINT_US_DRY';
    const PINT_US           = 'PINT_US';
    const PIPE              = 'PIPE';
    const PIPE_US           = 'PIPE_US';
    const PONY              = 'PONY';
    const QUART_GERMANY     = 'QUART_GERMANY';
    const QUART_ANCIENT     = 'QUART_ANCIENT';
    const QUART             = 'QUART';
    const QUART_US_DRY      = 'QUART_US_DRY';
    const QUART_US          = 'QUART_US';
    const SHOT              = 'SHOT';
    const TABLESPOON        = 'TABLESPOON';
    const TABLESPOON_UK     = 'TABLESPOON_UK';
    const TABLESPOON_US     = 'TABLESPOON_US';
    const TEASPOON          = 'TEASPOON';
    const TEASPOON_UK       = 'TEASPOON_UK';
    const TEASPOON_US       = 'TEASPOON_US';

    /**
     * Calculations for all cooking volume units
     *
     * @var array
     */
    protected $_units = [
        'CAN_2POINT5'       => [['' => '0.0037854118', '/' => '16', '' => '3.5'], '2.5th can'],
        'CAN_10'            => [['' => '0.0037854118', '*' => '0.75'],          '10th can'],
        'BARREL_WINE'       => ['0.143201835',   'bbl'],
        'BARREL'            => ['0.16365924',    'bbl'],
        'BARREL_US_DRY'     => [['' => '26.7098656608', '/' => '231'], 'bbl'],
        'BARREL_US_FEDERAL' => ['0.1173477658',  'bbl'],
        'BARREL_US'         => ['0.1192404717',  'bbl'],
        'BUCKET'            => ['0.01818436',    'bucket'],
        'BUCKET_US'         => ['0.018927059',   'bucket'],
        'BUSHEL'            => ['0.03636872',    'bu'],
        'BUSHEL_US'         => ['0.03523907',    'bu'],
        'CENTILITER'        => ['0.00001',       'cl'],
        'COFFEE_SPOON'      => [['' => '0.0037854118', '/' => '1536'], 'coffee spoon'],
        'CUBIC_CENTIMETER'  => ['0.000001',      'cm³'],
        'CUBIC_DECIMETER'   => ['0.001',         'dm³'],
        'CUBIC_FOOT'        => [['' => '6.54119159', '/' => '231'],   'ft³'],
        'CUBIC_INCH'        => [['' => '0.0037854118', '/' => '231'], 'in³'],
        'CUBIC_METER'       => ['1',             'm³'],
        'CUBIC_MICROMETER'  => ['1.0e-18',       'µm³'],
        'CUBIC_MILLIMETER'  => ['1.0e-9',        'mm³'],
        'CUP_CANADA'        => ['0.0002273045',  'c'],
        'CUP'               => ['0.00025',       'c'],
        'CUP_US'            => [['' => '0.0037854118', '/' => '16'],   'c'],
        'DASH'              => [['' => '0.0037854118', '/' => '6144'], 'ds'],
        'DECILITER'         => ['0.0001',        'dl'],
        'DEKALITER'         => ['0.001',         'dal'],
        'DEMI'              => ['0.00025',       'demi'],
        'DRAM'              => [['' => '0.0037854118', '/' => '1024'],  'dr'],
        'DROP'              => [['' => '0.0037854118', '/' => '73728'], 'ggt'],
        'FIFTH'             => ['0.00075708236', 'fifth'],
        'GALLON'            => ['0.00454609',    'gal'],
        'GALLON_US_DRY'     => ['0.0044048838',  'gal'],
        'GALLON_US'         => ['0.0037854118',  'gal'],
        'GILL'              => [['' => '0.00454609', '/' => '32'],   'gi'],
        'GILL_US'           => [['' => '0.0037854118', '/' => '32'], 'gi'],
        'HECTOLITER'        => ['0.1',           'hl'],
        'HOGSHEAD'          => ['0.28640367',    'hhd'],
        'HOGSHEAD_US'       => ['0.2384809434',  'hhd'],
        'JIGGER'            => [['' => '0.0037854118', '/' => '128', '*' => '1.5'], 'jigger'],
        'KILOLITER'         => ['1',             'kl'],
        'LITER'             => ['0.001',         'l'],
        'MEASURE'           => ['0.0077',        'measure'],
        'MEGALITER'         => ['1000',          'Ml'],
        'MICROLITER'        => ['1.0e-9',        'µl'],
        'MILLILITER'        => ['0.000001',      'ml'],
        'MINIM'             => [['' => '0.00454609', '/' => '76800'],  'min'],
        'MINIM_US'          => [['' => '0.0037854118','/' => '61440'], 'min'],
        'OUNCE'             => [['' => '0.00454609', '/' => '160'],    'oz'],
        'OUNCE_US'          => [['' => '0.0037854118', '/' => '128'],  'oz'],
        'PECK'              => ['0.00909218',    'pk'],
        'PECK_US'           => ['0.0088097676',  'pk'],
        'PINCH'             => [['' => '0.0037854118', '/' => '12288'], 'pinch'],
        'PINT'              => [['' => '0.00454609', '/' => '8'],       'pt'],
        'PINT_US_DRY'       => [['' => '0.0044048838', '/' => '8'],     'pt'],
        'PINT_US'           => [['' => '0.0037854118', '/' => '8'],     'pt'],
        'PIPE'              => ['0.49097772',    'pipe'],
        'PIPE_US'           => ['0.4769618868',  'pipe'],
        'PONY'              => [['' => '0.0037854118', '/' => '128'], 'pony'],
        'QUART_GERMANY'     => ['0.00114504',    'qt'],
        'QUART_ANCIENT'     => ['0.00108',       'qt'],
        'QUART'             => [['' => '0.00454609', '/' => '4'],     'qt'],
        'QUART_US_DRY'      => [['' => '0.0044048838', '/' => '4'],   'qt'],
        'QUART_US'          => [['' => '0.0037854118', '/' => '4'],   'qt'],
        'SHOT'              => [['' => '0.0037854118', '/' => '128'], 'shot'],
        'TABLESPOON'        => ['0.000015',      'tbsp'],
        'TABLESPOON_UK'     => [['' => '0.00454609', '/' => '320'],   'tbsp'],
        'TABLESPOON_US'     => [['' => '0.0037854118', '/' => '256'], 'tbsp'],
        'TEASPOON'          => ['0.000005',      'tsp'],
        'TEASPOON_UK'       => [['' => '0.00454609', '/' => '1280'],  'tsp'],
        'TEASPOON_US'       => [['' => '0.0037854118', '/' => '768'], 'tsp'],
        'STANDARD'          => 'CUBIC_METER'
    ];
}
