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
 * Class for handling density conversions
 *
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage Zend_Measure_Density
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Density extends Zend_Measure_Abstract
{
    const STANDARD = 'KILOGRAM_PER_CUBIC_METER';

    const ALUMINIUM                      = 'ALUMINIUM';
    const COPPER                         = 'COPPER';
    const GOLD                           = 'GOLD';
    const GRAIN_PER_CUBIC_FOOT           = 'GRAIN_PER_CUBIC_FOOT';
    const GRAIN_PER_CUBIC_INCH           = 'GRAIN_PER_CUBIC_INCH';
    const GRAIN_PER_CUBIC_YARD           = 'GRAIN_PER_CUBIC_YARD';
    const GRAIN_PER_GALLON               = 'GRAIN_PER_GALLON';
    const GRAIN_PER_GALLON_US            = 'GRAIN_PER_GALLON_US';
    const GRAM_PER_CUBIC_CENTIMETER      = 'GRAM_PER_CUBIC_CENTIMETER';
    const GRAM_PER_CUBIC_DECIMETER       = 'GRAM_PER_CUBIC_DECIMETER';
    const GRAM_PER_CUBIC_METER           = 'GRAM_PER_CUBIC_METER';
    const GRAM_PER_LITER                 = 'GRAM_PER_LITER';
    const GRAM_PER_MILLILITER            = 'GRAM_PER_MILLILITER';
    const IRON                           = 'IRON';
    const KILOGRAM_PER_CUBIC_CENTIMETER  = 'KILOGRAM_PER_CUBIC_CENTIMETER';
    const KILOGRAM_PER_CUBIC_DECIMETER   = 'KILOGRAM_PER_CUBIC_DECIMETER';
    const KILOGRAM_PER_CUBIC_METER       = 'KILOGRAM_PER_CUBIC_METER';
    const KILOGRAM_PER_CUBIC_MILLIMETER  = 'KILOGRAM_PER_CUBIC_MILLIMETER';
    const KILOGRAM_PER_LITER             = 'KILOGRAM_PER_LITER';
    const KILOGRAM_PER_MILLILITER        = 'KILOGRAM_PER_MILLILITER';
    const LEAD                           = 'LEAD';
    const MEGAGRAM_PER_CUBIC_CENTIMETER  = 'MEGAGRAM_PER_CUBIC_CENTIMETER';
    const MEGAGRAM_PER_CUBIC_DECIMETER   = 'MEGAGRAM_PER_CUBIC_DECIMETER';
    const MEGAGRAM_PER_CUBIC_METER       = 'MEGAGRAM_PER_CUBIC_METER';
    const MEGAGRAM_PER_LITER             = 'MEGAGRAM_PER_LITER';
    const MEGAGRAM_PER_MILLILITER        = 'MEGAGRAM_PER_MILLILITER';
    const MICROGRAM_PER_CUBIC_CENTIMETER = 'MICROGRAM_PER_CUBIC_CENTIMETER';
    const MICROGRAM_PER_CUBIC_DECIMETER  = 'MICROGRAM_PER_CUBIC_DECIMETER';
    const MICROGRAM_PER_CUBIC_METER      = 'MICROGRAM_PER_CUBIC_METER';
    const MICROGRAM_PER_LITER            = 'MICROGRAM_PER_LITER';
    const MICROGRAM_PER_MILLILITER       = 'MICROGRAM_PER_MILLILITER';
    const MILLIGRAM_PER_CUBIC_CENTIMETER = 'MILLIGRAM_PER_CUBIC_CENTIMETER';
    const MILLIGRAM_PER_CUBIC_DECIMETER  = 'MILLIGRAM_PER_CUBIC_DECIMETER';
    const MILLIGRAM_PER_CUBIC_METER      = 'MILLIGRAM_PER_CUBIC_METER';
    const MILLIGRAM_PER_LITER            = 'MILLIGRAM_PER_LITER';
    const MILLIGRAM_PER_MILLILITER       = 'MILLIGRAM_PER_MILLILITER';
    const OUNCE_PER_CUBIC_FOOT           = 'OUNCE_PER_CUBIC_FOOT';
    const OUNCR_PER_CUBIC_FOOT_TROY      = 'OUNCE_PER_CUBIC_FOOT_TROY';
    const OUNCE_PER_CUBIC_INCH           = 'OUNCE_PER_CUBIC_INCH';
    const OUNCE_PER_CUBIC_INCH_TROY      = 'OUNCE_PER_CUBIC_INCH_TROY';
    const OUNCE_PER_CUBIC_YARD           = 'OUNCE_PER_CUBIC_YARD';
    const OUNCE_PER_CUBIC_YARD_TROY      = 'OUNCE_PER_CUBIC_YARD_TROY';
    const OUNCE_PER_GALLON               = 'OUNCE_PER_GALLON';
    const OUNCE_PER_GALLON_US            = 'OUNCE_PER_GALLON_US';
    const OUNCE_PER_GALLON_TROY          = 'OUNCE_PER_GALLON_TROY';
    const OUNCE_PER_GALLON_US_TROY       = 'OUNCE_PER_GALLON_US_TROY';
    const POUND_PER_CIRCULAR_MIL_FOOT    = 'POUND_PER_CIRCULAR_MIL_FOOT';
    const POUND_PER_CUBIC_FOOT           = 'POUND_PER_CUBIC_FOOT';
    const POUND_PER_CUBIC_INCH           = 'POUND_PER_CUBIC_INCH';
    const POUND_PER_CUBIC_YARD           = 'POUND_PER_CUBIC_YARD';
    const POUND_PER_GALLON               = 'POUND_PER_GALLON';
    const POUND_PER_KILOGALLON           = 'POUND_PER_KILOGALLON';
    const POUND_PER_MEGAGALLON           = 'POUND_PER_MEGAGALLON';
    const POUND_PER_GALLON_US            = 'POUND_PER_GALLON_US';
    const POUND_PER_KILOGALLON_US        = 'POUND_PER_KILOGALLON_US';
    const POUND_PER_MEGAGALLON_US        = 'POUND_PER_MEGAGALLON_US';
    const SILVER                         = 'SILVER';
    const SLUG_PER_CUBIC_FOOT            = 'SLUG_PER_CUBIC_FOOT';
    const SLUG_PER_CUBIC_INCH            = 'SLUG_PER_CUBIC_INCH';
    const SLUG_PER_CUBIC_YARD            = 'SLUG_PER_CUBIC_YARD';
    const SLUG_PER_GALLON                = 'SLUG_PER_GALLON';
    const SLUG_PER_GALLON_US             = 'SLUG_PER_GALLON_US';
    const TON_PER_CUBIC_FOOT_LONG        = 'TON_PER_CUBIC_FOOT_LONG';
    const TON_PER_CUBIC_FOOT             = 'TON_PER_CUBIC_FOOT';
    const TON_PER_CUBIC_INCH_LONG        = 'TON_PER_CUBIC_INCH_LONG';
    const TON_PER_CUBIC_INCH             = 'TON_PER_CUBIC_INCH';
    const TON_PER_CUBIC_YARD_LONG        = 'TON_PER_CUBIC_YARD_LONG';
    const TON_PER_CUBIC_YARD             = 'TON_PER_CUBIC_YARD';
    const TON_PER_GALLON_LONG            = 'TON_PER_GALLON_LONG';
    const TON_PER_GALLON_US_LONG         = 'TON_PER_GALLON_US_LONG';
    const TON_PER_GALLON                 = 'TON_PER_GALLON';
    const TON_PER_GALLON_US              = 'TON_PER_GALLON_US';
    const TONNE_PER_CUBIC_CENTIMETER     = 'TONNE_PER_CUBIC_CENTIMETER';
    const TONNE_PER_CUBIC_DECIMETER      = 'TONNE_PER_CUBIC_DECIMETER';
    const TONNE_PER_CUBIC_METER          = 'TONNE_PER_CUBIC_METER';
    const TONNE_PER_LITER                = 'TONNE_PER_LITER';
    const TONNE_PER_MILLILITER           = 'TONNE_PER_MILLILITER';
    const WATER                          = 'WATER';

    /**
     * Calculations for all density units
     *
     * @var array
     */
    protected $_units = [
        'ALUMINIUM'                 => ['2643',           'aluminium'],
        'COPPER'                    => ['8906',           'copper'],
        'GOLD'                      => ['19300',          'gold'],
        'GRAIN_PER_CUBIC_FOOT'      => ['0.0022883519',   'gr/ft³'],
        'GRAIN_PER_CUBIC_INCH'      => ['3.9542721',      'gr/in³'],
        'GRAIN_PER_CUBIC_YARD'      => ['0.000084753774', 'gr/yd³'],
        'GRAIN_PER_GALLON'          => ['0.014253768',    'gr/gal'],
        'GRAIN_PER_GALLON_US'       => ['0.017118061',    'gr/gal'],
        'GRAM_PER_CUBIC_CENTIMETER' => ['1000',           'g/cm³'],
        'GRAM_PER_CUBIC_DECIMETER'  => ['1',              'g/dm³'],
        'GRAM_PER_CUBIC_METER'      => ['0.001',          'g/m³'],
        'GRAM_PER_LITER'            => ['1',              'g/l'],
        'GRAM_PER_MILLILITER'       => ['1000',           'g/ml'],
        'IRON'                      => ['7658',           'iron'],
        'KILOGRAM_PER_CUBIC_CENTIMETER' => ['1000000',    'kg/cm³'],
        'KILOGRAM_PER_CUBIC_DECIMETER'  => ['1000',       'kg/dm³'],
        'KILOGRAM_PER_CUBIC_METER'  => ['1',              'kg/m³'],
        'KILOGRAM_PER_CUBIC_MILLIMETER' => ['1000000000', 'kg/l'],
        'KILOGRAM_PER_LITER'        => ['1000',           'kg/ml'],
        'KILOGRAM_PER_MILLILITER'   => ['1000000',        'kg/ml'],
        'LEAD'                      => ['11370',          'lead'],
        'MEGAGRAM_PER_CUBIC_CENTIMETER' => ['1.0e+9',     'Mg/cm³'],
        'MEGAGRAM_PER_CUBIC_DECIMETER'  => ['1000000',    'Mg/dm³'],
        'MEGAGRAM_PER_CUBIC_METER'  => ['1000',           'Mg/m³'],
        'MEGAGRAM_PER_LITER'        => ['1000000',        'Mg/l'],
        'MEGAGRAM_PER_MILLILITER'   => ['1.0e+9',         'Mg/ml'],
        'MICROGRAM_PER_CUBIC_CENTIMETER' => ['0.001',     'µg/cm³'],
        'MICROGRAM_PER_CUBIC_DECIMETER'  => ['1.0e-6',    'µg/dm³'],
        'MICROGRAM_PER_CUBIC_METER' => ['1.0e-9',         'µg/m³'],
        'MICROGRAM_PER_LITER'       => ['1.0e-6',         'µg/l'],
        'MICROGRAM_PER_MILLILITER'  => ['0.001',          'µg/ml'],
        'MILLIGRAM_PER_CUBIC_CENTIMETER' => ['1',         'mg/cm³'],
        'MILLIGRAM_PER_CUBIC_DECIMETER'  => ['0.001',     'mg/dm³'],
        'MILLIGRAM_PER_CUBIC_METER' => ['0.000001',       'mg/m³'],
        'MILLIGRAM_PER_LITER'       => ['0.001',          'mg/l'],
        'MILLIGRAM_PER_MILLILITER'  => ['1',              'mg/ml'],
        'OUNCE_PER_CUBIC_FOOT'      => ['1.001154',       'oz/ft³'],
        'OUNCE_PER_CUBIC_FOOT_TROY' => ['1.0984089',      'oz/ft³'],
        'OUNCE_PER_CUBIC_INCH'      => ['1729.994',       'oz/in³'],
        'OUNCE_PER_CUBIC_INCH_TROY' => ['1898.0506',      'oz/in³'],
        'OUNCE_PER_CUBIC_YARD'      => ['0.037079776',    'oz/yd³'],
        'OUNCE_PER_CUBIC_YARD_TROY' => ['0.040681812',    'oz/yd³'],
        'OUNCE_PER_GALLON'          => ['6.2360233',      'oz/gal'],
        'OUNCE_PER_GALLON_US'       => ['7.4891517',      'oz/gal'],
        'OUNCE_PER_GALLON_TROY'     => ['6.8418084',      'oz/gal'],
        'OUNCE_PER_GALLON_US_TROY'  => ['8.2166693',      'oz/gal'],
        'POUND_PER_CIRCULAR_MIL_FOOT' => ['2.9369291',    'lb/cmil ft'],
        'POUND_PER_CUBIC_FOOT'      => ['16.018463',      'lb/in³'],
        'POUND_PER_CUBIC_INCH'      => ['27679.905',      'lb/in³'],
        'POUND_PER_CUBIC_YARD'      => ['0.59327642',     'lb/yd³'],
        'POUND_PER_GALLON'          => ['99.776373',      'lb/gal'],
        'POUND_PER_KILOGALLON'      => ['0.099776373',    'lb/kgal'],
        'POUND_PER_MEGAGALLON'      => ['0.000099776373', 'lb/Mgal'],
        'POUND_PER_GALLON_US'       => ['119.82643',      'lb/gal'],
        'POUND_PER_KILOGALLON_US'   => ['0.11982643',     'lb/kgal'],
        'POUND_PER_MEGAGALLON_US'   => ['0.00011982643',  'lb/Mgal'],
        'SILVER'                    => ['10510',          'silver'],
        'SLUG_PER_CUBIC_FOOT'       => ['515.37882',      'slug/ft³'],
        'SLUG_PER_CUBIC_INCH'       => ['890574.6',       'slug/in³'],
        'SLUG_PER_CUBIC_YARD'       => ['19.088104',      'slug/yd³'],
        'SLUG_PER_GALLON'           => ['3210.2099',      'slug/gal'],
        'SLUG_PER_GALLON_US'        => ['3855.3013',      'slug/gal'],
        'TON_PER_CUBIC_FOOT_LONG'   => ['35881.358',      't/ft³'],
        'TON_PER_CUBIC_FOOT'        => ['32036.927',      't/ft³'],
        'TON_PER_CUBIC_INCH_LONG'   => ['6.2202987e+7',   't/in³'],
        'TON_PER_CUBIC_INCH'        => ['5.5359809e+7',   't/in³'],
        'TON_PER_CUBIC_YARD_LONG'   => ['1328.9392',      't/yd³'],
        'TON_PER_CUBIC_YARD'        => ['1186.5528',      't/yd³'],
        'TON_PER_GALLON_LONG'       => ['223499.07',      't/gal'],
        'TON_PER_GALLON_US_LONG'    => ['268411.2',       't/gal'],
        'TON_PER_GALLON'            => ['199522.75',      't/gal'],
        'TON_PER_GALLON_US'         => ['239652.85',      't/gal'],
        'TONNE_PER_CUBIC_CENTIMETER' => ['1.0e+9',        't/cm³'],
        'TONNE_PER_CUBIC_DECIMETER'  => ['1000000',       't/dm³'],
        'TONNE_PER_CUBIC_METER'     => ['1000',           't/m³'],
        'TONNE_PER_LITER'           => ['1000000',        't/l'],
        'TONNE_PER_MILLILITER'      => ['1.0e+9',         't/ml'],
        'WATER'                     => ['1000',           'water'],
        'STANDARD'                  => 'KILOGRAM_PER_CUBIC_METER'
    ];
}
