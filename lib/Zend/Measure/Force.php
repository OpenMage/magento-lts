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
 * Class for handling force conversions
 *
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage Zend_Measure_Force
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Force extends Zend_Measure_Abstract
{
    const STANDARD = 'NEWTON';

    const ATTONEWTON      = 'ATTONEWTON';
    const CENTINEWTON     = 'CENTINEWTON';
    const DECIGRAM_FORCE  = 'DECIGRAM_FORCE';
    const DECINEWTON      = 'DECINEWTON';
    const DEKAGRAM_FORCE  = 'DEKAGRAM_FORCE';
    const DEKANEWTON      = 'DEKANEWTON';
    const DYNE            = 'DYNE';
    const EXANEWTON       = 'EXANEWTON';
    const FEMTONEWTON     = 'FEMTONEWTON';
    const GIGANEWTON      = 'GIGANEWTON';
    const GRAM_FORCE      = 'GRAM_FORCE';
    const HECTONEWTON     = 'HECTONEWTON';
    const JOULE_PER_METER = 'JOULE_PER_METER';
    const KILOGRAM_FORCE  = 'KILOGRAM_FORCE';
    const KILONEWTON      = 'KILONEWTON';
    const KILOPOND        = 'KILOPOND';
    const KIP             = 'KIP';
    const MEGANEWTON      = 'MEGANEWTON';
    const MEGAPOND        = 'MEGAPOND';
    const MICRONEWTON     = 'MICRONEWTON';
    const MILLINEWTON     = 'MILLINEWTON';
    const NANONEWTON      = 'NANONEWTON';
    const NEWTON          = 'NEWTON';
    const OUNCE_FORCE     = 'OUNCE_FORCE';
    const PETANEWTON      = 'PETANEWTON';
    const PICONEWTON      = 'PICONEWTON';
    const POND            = 'POND';
    const POUND_FORCE     = 'POUND_FORCE';
    const POUNDAL         = 'POUNDAL';
    const STHENE          = 'STHENE';
    const TERANEWTON      = 'TERANEWTON';
    const TON_FORCE_LONG  = 'TON_FORCE_LONG';
    const TON_FORCE       = 'TON_FORCE';
    const TON_FORCE_SHORT = 'TON_FORCE_SHORT';
    const YOCTONEWTON     = 'YOCTONEWTON';
    const YOTTANEWTON     = 'YOTTANEWTON';
    const ZEPTONEWTON     = 'ZEPTONEWTON';
    const ZETTANEWTON     = 'ZETTANEWTON';

    /**
     * Calculations for all force units
     *
     * @var array
     */
    protected $_units = [
        'ATTONEWTON'      => ['1.0e-18',     'aN'],
        'CENTINEWTON'     => ['0.01',        'cN'],
        'DECIGRAM_FORCE'  => ['0.000980665', 'dgf'],
        'DECINEWTON'      => ['0.1',         'dN'],
        'DEKAGRAM_FORCE'  => ['0.0980665',   'dagf'],
        'DEKANEWTON'      => ['10',          'daN'],
        'DYNE'            => ['0.00001',     'dyn'],
        'EXANEWTON'       => ['1.0e+18',     'EN'],
        'FEMTONEWTON'     => ['1.0e-15',     'fN'],
        'GIGANEWTON'      => ['1.0e+9',      'GN'],
        'GRAM_FORCE'      => ['0.00980665',  'gf'],
        'HECTONEWTON'     => ['100',         'hN'],
        'JOULE_PER_METER' => ['1',           'J/m'],
        'KILOGRAM_FORCE'  => ['9.80665',     'kgf'],
        'KILONEWTON'      => ['1000',        'kN'],
        'KILOPOND'        => ['9.80665',     'kp'],
        'KIP'             => ['4448.2216',   'kip'],
        'MEGANEWTON'      => ['1000000',     'Mp'],
        'MEGAPOND'        => ['9806.65',     'MN'],
        'MICRONEWTON'     => ['0.000001',    'µN'],
        'MILLINEWTON'     => ['0.001',       'mN'],
        'NANONEWTON'      => ['0.000000001', 'nN'],
        'NEWTON'          => ['1',           'N'],
        'OUNCE_FORCE'     => ['0.27801385',  'ozf'],
        'PETANEWTON'      => ['1.0e+15',     'PN'],
        'PICONEWTON'      => ['1.0e-12',     'pN'],
        'POND'            => ['0.00980665',  'pond'],
        'POUND_FORCE'     => ['4.4482216',   'lbf'],
        'POUNDAL'         => ['0.13825495',  'pdl'],
        'STHENE'          => ['1000',        'sn'],
        'TERANEWTON'      => ['1.0e+12',     'TN'],
        'TON_FORCE_LONG'  => ['9964.016384', 'tnf'],
        'TON_FORCE'       => ['9806.65',     'tnf'],
        'TON_FORCE_SHORT' => ['8896.4432',   'tnf'],
        'YOCTONEWTON'     => ['1.0e-24',     'yN'],
        'YOTTANEWTON'     => ['1.0e+24',     'YN'],
        'ZEPTONEWTON'     => ['1.0e-21',     'zN'],
        'ZETTANEWTON'     => ['1.0e+21',     'ZN'],
        'STANDARD'        => 'NEWTON'
    ];
}
