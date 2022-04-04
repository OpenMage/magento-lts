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
 * Class for handling acceleration conversions
 *
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage Zend_Measure_Volume
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Volume extends Zend_Measure_Abstract
{
    const STANDARD = 'CUBIC_METER';

    const ACRE_FOOT           = 'ACRE_FOOT';
    const ACRE_FOOT_SURVEY    = 'ACRE_FOOT_SURVEY';
    const ACRE_INCH           = 'ACRE_INCH';
    const BARREL_WINE         = 'BARREL_WINE';
    const BARREL              = 'BARREL';
    const BARREL_US_DRY       = 'BARREL_US_DRY';
    const BARREL_US_FEDERAL   = 'BARREL_US_FEDERAL';
    const BARREL_US           = 'BARREL_US';
    const BARREL_US_PETROLEUM = 'BARREL_US_PETROLEUM';
    const BOARD_FOOT          = 'BOARD_FOOT';
    const BUCKET              = 'BUCKET';
    const BUCKET_US           = 'BUCKET_US';
    const BUSHEL              = 'BUSHEL';
    const BUSHEL_US           = 'BUSHEL_US';
    const CENTILTER           = 'CENTILITER';
    const CORD                = 'CORD';
    const CORD_FOOT           = 'CORD_FOOT';
    const CUBIC_CENTIMETER    = 'CUBIC_CENTIMETER';
    const CUBIC_CUBIT         = 'CUBIC_CUBIT';
    const CUBIC_DECIMETER     = 'CUBIC_DECIMETER';
    const CUBIC_DEKAMETER     = 'CUBIC_DEKAMETER';
    const CUBIC_FOOT          = 'CUBIC_FOOT';
    const CUBIC_INCH          = 'CUBIC_INCH';
    const CUBIC_KILOMETER     = 'CUBIC_KILOMETER';
    const CUBIC_METER         = 'CUBIC_METER';
    const CUBIC_MILE          = 'CUBIC_MILE';
    const CUBIC_MICROMETER    = 'CUBIC_MICROMETER';
    const CUBIC_MILLIMETER    = 'CUBIC_MILLIMETER';
    const CUBIC_YARD          = 'CUBIC_YARD';
    const CUP_CANADA          = 'CUP_CANADA';
    const CUP                 = 'CUP';
    const CUP_US              = 'CUP_US';
    const DECILITER           = 'DECILITER';
    const DEKALITER           = 'DEKALITER';
    const DRAM                = 'DRAM';
    const DRUM_US             = 'DRUM_US';
    const DRUM                = 'DRUM';
    const FIFTH               = 'FIFTH';
    const GALLON              = 'GALLON';
    const GALLON_US_DRY       = 'GALLON_US_DRY';
    const GALLON_US           = 'GALLON_US';
    const GILL                = 'GILL';
    const GILL_US             = 'GILL_US';
    const HECTARE_METER       = 'HECTARE_METER';
    const HECTOLITER          = 'HECTOLITER';
    const HOGSHEAD            = 'HOGSHEAD';
    const HOGSHEAD_US         = 'HOGSHEAD_US';
    const JIGGER              = 'JIGGER';
    const KILOLITER           = 'KILOLITER';
    const LITER               = 'LITER';
    const MEASURE             = 'MEASURE';
    const MEGALITER           = 'MEGALITER';
    const MICROLITER          = 'MICROLITER';
    const MILLILITER          = 'MILLILITER';
    const MINIM               = 'MINIM';
    const MINIM_US            = 'MINIM_US';
    const OUNCE               = 'OUNCE';
    const OUNCE_US            = 'OUNCE_US';
    const PECK                = 'PECK';
    const PECK_US             = 'PECK_US';
    const PINT                = 'PINT';
    const PINT_US_DRY         = 'PINT_US_DRY';
    const PINT_US             = 'PINT_US';
    const PIPE                = 'PIPE';
    const PIPE_US             = 'PIPE_US';
    const PONY                = 'PONY';
    const QUART_GERMANY       = 'QUART_GERMANY';
    const QUART_ANCIENT       = 'QUART_ANCIENT';
    const QUART               = 'QUART';
    const QUART_US_DRY        = 'QUART_US_DRY';
    const QUART_US            = 'QUART_US';
    const QUART_UK            = 'QUART_UK';
    const SHOT                = 'SHOT';
    const STERE               = 'STERE';
    const TABLESPOON          = 'TABLESPOON';
    const TABLESPOON_UK       = 'TABLESPOON_UK';
    const TABLESPOON_US       = 'TABLESPOON_US';
    const TEASPOON            = 'TEASPOON';
    const TEASPOON_UK         = 'TEASPOON_UK';
    const TEASPOON_US         = 'TEASPOON_US';
    const YARD                = 'YARD';

    /**
     * Calculations for all volume units
     *
     * @var array
     */
    protected $_units = [
        'ACRE_FOOT'           => ['1233.48185532', 'ac ft'],
        'ACRE_FOOT_SURVEY'    => ['1233.489',      'ac ft'],
        'ACRE_INCH'           => ['102.79015461',  'ac in'],
        'BARREL_WINE'         => ['0.143201835',   'bbl'],
        'BARREL'              => ['0.16365924',    'bbl'],
        'BARREL_US_DRY'       => [['' => '26.7098656608', '/' => '231'], 'bbl'],
        'BARREL_US_FEDERAL'   => ['0.1173477658',  'bbl'],
        'BARREL_US'           => ['0.1192404717',  'bbl'],
        'BARREL_US_PETROLEUM' => ['0.1589872956',  'bbl'],
        'BOARD_FOOT'          => [['' => '6.5411915904', '/' => '2772'], 'board foot'],
        'BUCKET'              => ['0.01818436',    'bucket'],
        'BUCKET_US'           => ['0.018927059',   'bucket'],
        'BUSHEL'              => ['0.03636872',    'bu'],
        'BUSHEL_US'           => ['0.03523907',    'bu'],
        'CENTILITER'          => ['0.00001',       'cl'],
        'CORD'                => ['3.624556416',   'cd'],
        'CORD_FOOT'           => ['0.453069552',   'cd ft'],
        'CUBIC_CENTIMETER'    => ['0.000001',      'cm³'],
        'CUBIC_CUBIT'         => ['0.144',         'cubit³'],
        'CUBIC_DECIMETER'     => ['0.001',         'dm³'],
        'CUBIC_DEKAMETER'     => ['1000',          'dam³'],
        'CUBIC_FOOT'          => [['' => '6.54119159', '/' => '231'],   'ft³'],
        'CUBIC_INCH'          => [['' => '0.0037854118', '/' => '231'], 'in³'],
        'CUBIC_KILOMETER'     => ['1.0e+9',        'km³'],
        'CUBIC_METER'         => ['1',             'm³'],
        'CUBIC_MILE'          => [['' => '0.0037854118', '/' => '231', '*' => '75271680', '*' => '3379200'],
                                       'mi³'],
        'CUBIC_MICROMETER'    => ['1.0e-18',       'µm³'],
        'CUBIC_MILLIMETER'    => ['1.0e-9',        'mm³'],
        'CUBIC_YARD'          => [['' => '0.0037854118', '/' => '231', '*' => '46656'], 'yd³'],
        'CUP_CANADA'          => ['0.0002273045',  'c'],
        'CUP'                 => ['0.00025',       'c'],
        'CUP_US'              => [['' => '0.0037854118', '/' => '16'], 'c'],
        'DECILITER'           => ['0.0001',        'dl'],
        'DEKALITER'           => ['0.001',         'dal'],
        'DRAM'                => [['' => '0.0037854118', '/' => '1024'], 'dr'],
        'DRUM_US'             => ['0.208197649',   'drum'],
        'DRUM'                => ['0.2',           'drum'],
        'FIFTH'               => ['0.00075708236', 'fifth'],
        'GALLON'              => ['0.00454609',    'gal'],
        'GALLON_US_DRY'       => ['0.0044048838',  'gal'],
        'GALLON_US'           => ['0.0037854118',  'gal'],
        'GILL'                => [['' => '0.00454609', '/' => '32'],   'gi'],
        'GILL_US'             => [['' => '0.0037854118', '/' => '32'], 'gi'],
        'HECTARE_METER'       => ['10000',         'ha m'],
        'HECTOLITER'          => ['0.1',           'hl'],
        'HOGSHEAD'            => ['0.28640367',    'hhd'],
        'HOGSHEAD_US'         => ['0.2384809434',  'hhd'],
        'JIGGER'              => [['' => '0.0037854118', '/' => '128', '*' => '1.5'], 'jigger'],
        'KILOLITER'           => ['1',             'kl'],
        'LITER'               => ['0.001',         'l'],
        'MEASURE'             => ['0.0077',        'measure'],
        'MEGALITER'           => ['1000',          'Ml'],
        'MICROLITER'          => ['1.0e-9',        'µl'],
        'MILLILITER'          => ['0.000001',      'ml'],
        'MINIM'               => [['' => '0.00454609', '/' => '76800'],  'min'],
        'MINIM_US'            => [['' => '0.0037854118','/' => '61440'], 'min'],
        'OUNCE'               => [['' => '0.00454609', '/' => '160'],    'oz'],
        'OUNCE_US'            => [['' => '0.0037854118', '/' => '128'],  'oz'],
        'PECK'                => ['0.00909218',    'pk'],
        'PECK_US'             => ['0.0088097676',  'pk'],
        'PINT'                => [['' => '0.00454609', '/' => '8'],   'pt'],
        'PINT_US_DRY'         => [['' => '0.0044048838', '/' => '8'], 'pt'],
        'PINT_US'             => [['' => '0.0037854118', '/' => '8'], 'pt'],
        'PIPE'                => ['0.49097772',    'pipe'],
        'PIPE_US'             => ['0.4769618868',  'pipe'],
        'PONY'                => [['' => '0.0037854118', '/' => '128'], 'pony'],
        'QUART_GERMANY'       => ['0.00114504',    'qt'],
        'QUART_ANCIENT'       => ['0.00108',       'qt'],
        'QUART'               => [['' => '0.00454609', '/' => '4'],   'qt'],
        'QUART_US_DRY'        => [['' => '0.0044048838', '/' => '4'], 'qt'],
        'QUART_US'            => [['' => '0.0037854118', '/' => '4'], 'qt'],
        'QUART_UK'            => ['0.29094976',    'qt'],
        'SHOT'                => [['' => '0.0037854118', '/' => '128'], 'shot'],
        'STERE'               => ['1',             'st'],
        'TABLESPOON'          => ['0.000015',      'tbsp'],
        'TABLESPOON_UK'       => [['' => '0.00454609', '/' => '320'],   'tbsp'],
        'TABLESPOON_US'       => [['' => '0.0037854118', '/' => '256'], 'tbsp'],
        'TEASPOON'            => ['0.000005',      'tsp'],
        'TEASPOON_UK'         => [['' => '0.00454609', '/' => '1280'],    'tsp'],
        'TEASPOON_US'         => [['' => '0.0037854118', '/' => '768'],   'tsp'],
        'YARD'                => [['' => '176.6121729408', '/' => '231'], 'yd'],
        'STANDARD'            => 'CUBIC_METER'
    ];
}
