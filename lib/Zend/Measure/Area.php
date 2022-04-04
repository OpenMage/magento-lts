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
 * Class for handling area conversions
 *
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage Zend_Measure_Area
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Area extends Zend_Measure_Abstract
{
    const STANDARD = 'SQUARE_METER';

    const ACRE                       = 'ACRE';
    const ACRE_COMMERCIAL            = 'ACRE_COMMERCIAL';
    const ACRE_SURVEY                = 'ACRE_SURVEY';
    const ACRE_IRELAND               = 'ACRE_IRELAND';
    const ARE                        = 'ARE';
    const ARPENT                     = 'ARPENT';
    const BARN                       = 'BARN';
    const BOVATE                     = 'BOVATE';
    const BUNDER                     = 'BUNDER';
    const CABALLERIA                 = 'CABALLERIA';
    const CABALLERIA_AMERICA         = 'CABALLERIA_AMERICA';
    const CABALLERIA_CUBA            = 'CABALLERIA_CUBA';
    const CARREAU                    = 'CARREAU';
    const CARUCATE                   = 'CARUCATE';
    const CAWNEY                     = 'CAWNEY';
    const CENTIARE                   = 'CENTIARE';
    const CONG                       = 'CONG';
    const COVER                      = 'COVER';
    const CUERDA                     = 'CUERDA';
    const DEKARE                     = 'DEKARE';
    const DESSIATINA                 = 'DESSIATINA';
    const DHUR                       = 'DHUR';
    const DUNUM                      = 'DUNUM';
    const DUNHAM                     = 'DUNHAM';
    const FALL_SCOTS                 = 'FALL_SCOTS';
    const FALL                       = 'FALL';
    const FANEGA                     = 'FANEGA';
    const FARTHINGDALE               = 'FARTHINGDALE';
    const HACIENDA                   = 'HACIENDA';
    const HECTARE                    = 'HECTARE';
    const HIDE                       = 'HIDE';
    const HOMESTEAD                  = 'HOMESTEAD';
    const HUNDRED                    = 'HUNDRED';
    const JERIB                      = 'JERIB';
    const JITRO                      = 'JITRO';
    const JOCH                       = 'JOCH';
    const JUTRO                      = 'JUTRO';
    const JO                         = 'JO';
    const KAPPLAND                   = 'KAPPLAND';
    const KATTHA                     = 'KATTHA';
    const LABOR                      = 'LABOR';
    const LEGUA                      = 'LEGUA';
    const MANZANA_COSTA_RICA         = 'MANZANA_COSTA_RICA';
    const MANZANA                    = 'MANZANA';
    const MORGEN                     = 'MORGEN';
    const MORGEN_AFRICA              = 'MORGEN_AFRICA';
    const MU                         = 'MU';
    const NGARN                      = 'NGARN';
    const NOOK                       = 'NOOK';
    const OXGANG                     = 'OXGANG';
    const PERCH                      = 'PERCH';
    const PERCHE                     = 'PERCHE';
    const PING                       = 'PING';
    const PYONG                      = 'PYONG';
    const RAI                        = 'RAI';
    const ROOD                       = 'ROOD';
    const SECTION                    = 'SECTION';
    const SHED                       = 'SHED';
    const SITIO                      = 'SITIO';
    const SQUARE                     = 'SQUARE';
    const SQUARE_ANGSTROM            = 'SQUARE_ANGSTROM';
    const SQUARE_ASTRONOMICAL_UNIT   = 'SQUARE_ASTRONOMICAL_UNIT';
    const SQUARE_ATTOMETER           = 'SQUARE_ATTOMETER';
    const SQUARE_BICRON              = 'SQUARE_BICRON';
    const SQUARE_CENTIMETER          = 'SQUARE_CENTIMETER';
    const SQUARE_CHAIN               = 'SQUARE_CHAIN';
    const SQUARE_CHAIN_ENGINEER      = 'SQUARE_CHAIN_ENGINEER';
    const SQUARE_CITY_BLOCK_US_EAST  = 'SQUARE_CITY_BLOCK_US_EAST';
    const SQUARE_CITY_BLOCK_US_WEST  = 'SQUARE_CITY_BLOCK_US_WEST';
    const SQUARE_CITY_BLOCK_US_SOUTH = 'SQUARE_CITY_BLOCK_US_SOUTH';
    const SQUARE_CUBIT               = 'SQUARE_CUBIT';
    const SQUARE_DECIMETER           = 'SQUARE_DECIMETER';
    const SQUARE_DEKAMETER           = 'SQUARE_DEKAMETER';
    const SQUARE_EXAMETER            = 'SQUARE_EXAMETER';
    const SQUARE_FATHOM              = 'SQUARE_FATHOM';
    const SQUARE_FEMTOMETER          = 'SQUARE_FEMTOMETER';
    const SQUARE_FERMI               = 'SQUARE_FERMI';
    const SQUARE_FOOT                = 'SQUARE_FOOT';
    const SQUARE_FOOT_SURVEY         = 'SQUARE_FOOT_SURVEY';
    const SQUARE_FURLONG             = 'SQUARE_FURLONG';
    const SQUARE_GIGAMETER           = 'SQUARE_GIGAMETER';
    const SQUARE_HECTOMETER          = 'SQUARE_HECTOMETER';
    const SQUARE_INCH                = 'SQUARE_INCH';
    const SQUARE_INCH_SURVEY         = 'SQUARE_INCH_SURVEY';
    const SQUARE_KILOMETER           = 'SQUARE_KILOMETER';
    const SQUARE_LEAGUE_NAUTIC       = 'SQUARE_LEAGUE_NAUTIC';
    const SQUARE_LEAGUE              = 'SQUARE_LEAGUE';
    const SQUARE_LIGHT_YEAR          = 'SQUARE_LIGHT_YEAR';
    const SQUARE_LINK                = 'SQUARE_LINK';
    const SQUARE_LINK_ENGINEER       = 'SQUARE_LINK_ENGINEER';
    const SQUARE_MEGAMETER           = 'SQUARE_MEGAMETER';
    const SQUARE_METER               = 'SQUARE_METER';
    const SQUARE_MICROINCH           = 'SQUARE_MICROINCH';
    const SQUARE_MICROMETER          = 'SQUARE_MICROMETER';
    const SQUARE_MICROMICRON         = 'SQUARE_MICROMICRON';
    const SQUARE_MICRON              = 'SQUARE_MICRON';
    const SQUARE_MIL                 = 'SQUARE_MIL';
    const SQUARE_MILE                = 'SQUARE_MILE';
    const SQUARE_MILE_NAUTIC         = 'SQUARE_MILE_NAUTIC';
    const SQUARE_MILE_SURVEY         = 'SQUARE_MILE_SURVEY';
    const SQUARE_MILLIMETER          = 'SQUARE_MILLIMETER';
    const SQUARE_MILLIMICRON         = 'SQUARE_MILLIMICRON';
    const SQUARE_MYRIAMETER          = 'SQUARE_MYRIAMETER';
    const SQUARE_NANOMETER           = 'SQUARE_NANOMETER';
    const SQUARE_PARIS_FOOT          = 'SQUARE_PARIS_FOOT';
    const SQUARE_PARSEC              = 'SQUARE_PARSEC';
    const SQUARE_PERCH               = 'SQUARE_PERCH';
    const SQUARE_PERCHE              = 'SQUARE_PERCHE';
    const SQUARE_PETAMETER           = 'SQUARE_PETAMETER';
    const SQUARE_PICOMETER           = 'SQUARE_PICOMETER';
    const SQUARE_ROD                 = 'SQUARE_ROD';
    const SQUARE_TENTHMETER          = 'SQUARE_TENTHMETER';
    const SQUARE_TERAMETER           = 'SQUARE_TERAMETER';
    const SQUARE_THOU                = 'SQUARE_THOU';
    const SQUARE_VARA                = 'SQUARE_VARA';
    const SQUARE_VARA_TEXAS          = 'SQUARE_VARA_TEXAS';
    const SQUARE_YARD                = 'SQUARE_YARD';
    const SQUARE_YARD_SURVEY         = 'SQUARE_YARD_SURVEY';
    const SQUARE_YOCTOMETER          = 'SQUARE_YOCTOMETER';
    const SQUARE_YOTTAMETER          = 'SQUARE_YOTTAMETER';
    const STANG                      = 'STANG';
    const STREMMA                    = 'STREMMA';
    const TAREA                      = 'TAREA';
    const TATAMI                     = 'TATAMI';
    const TONDE_LAND                 = 'TONDE_LAND';
    const TOWNSHIP                   = 'TOWNSHIP';
    const TSUBO                      = 'TSUBO';
    const TUNNLAND                   = 'TUNNLAND';
    const YARD                       = 'YARD';
    const VIRGATE                    = 'VIRGATE';

    /**
     * Calculations for all area units
     *
     * @var array
     */
    protected $_units = [
        'ACRE'               => ['4046.856422',      'A'],
        'ACRE_COMMERCIAL'    => ['3344.50944',       'A'],
        'ACRE_SURVEY'        => ['4046.872627',      'A'],
        'ACRE_IRELAND'       => ['6555',             'A'],
        'ARE'                => ['100',              'a'],
        'ARPENT'             => ['3418.89',          'arpent'],
        'BARN'               => ['1e-28',            'b'],
        'BOVATE'             => ['60000',            'bovate'],
        'BUNDER'             => ['10000',            'bunder'],
        'CABALLERIA'         => ['400000',           'caballeria'],
        'CABALLERIA_AMERICA' => ['450000',           'caballeria'],
        'CABALLERIA_CUBA'    => ['134200',           'caballeria'],
        'CARREAU'            => ['12900',            'carreau'],
        'CARUCATE'           => ['486000',           'carucate'],
        'CAWNEY'             => ['5400',             'cawney'],
        'CENTIARE'           => ['1',                'ca'],
        'CONG'               => ['1000',             'cong'],
        'COVER'              => ['2698',             'cover'],
        'CUERDA'             => ['3930',             'cda'],
        'DEKARE'             => ['1000',             'dekare'],
        'DESSIATINA'         => ['10925',            'dessiantina'],
        'DHUR'               => ['16.929',           'dhur'],
        'DUNUM'              => ['1000',             'dunum'],
        'DUNHAM'             => ['1000',             'dunham'],
        'FALL_SCOTS'         => ['32.15',            'fall'],
        'FALL'               => ['47.03',            'fall'],
        'FANEGA'             => ['6430',             'fanega'],
        'FARTHINGDALE'       => ['1012',             'farthingdale'],
        'HACIENDA'           => ['89600000',         'hacienda'],
        'HECTARE'            => ['10000',            'ha'],
        'HIDE'               => ['486000',           'hide'],
        'HOMESTEAD'          => ['647500',           'homestead'],
        'HUNDRED'            => ['50000000',         'hundred'],
        'JERIB'              => ['2000',             'jerib'],
        'JITRO'              => ['5755',             'jitro'],
        'JOCH'               => ['5755',             'joch'],
        'JUTRO'              => ['5755',             'jutro'],
        'JO'                 => ['1.62',             'jo'],
        'KAPPLAND'           => ['154.26',           'kappland'],
        'KATTHA'             => ['338',              'kattha'],
        'LABOR'              => ['716850',           'labor'],
        'LEGUA'              => ['17920000',         'legua'],
        'MANZANA_COSTA_RICA' => ['6988.96',          'manzana'],
        'MANZANA'            => ['10000',            'manzana'],
        'MORGEN'             => ['2500',             'morgen'],
        'MORGEN_AFRICA'      => ['8567',             'morgen'],
        'MU'                 => [['' => '10000', '/' => '15'], 'mu'],
        'NGARN'              => ['400',              'ngarn'],
        'NOOK'               => ['80937.128',        'nook'],
        'OXGANG'             => ['60000',            'oxgang'],
        'PERCH'              => ['25.29285264',      'perch'],
        'PERCHE'             => ['34.19',            'perche'],
        'PING'               => ['3.305',            'ping'],
        'PYONG'              => ['3.306',            'pyong'],
        'RAI'                => ['1600',             'rai'],
        'ROOD'               => ['1011.7141',        'rood'],
        'SECTION'            => ['2589998.5',        'sec'],
        'SHED'               => ['10e-52',           'shed'],
        'SITIO'              => ['18000000',         'sitio'],
        'SQUARE'             => ['9.290304',         'sq'],
        'SQUARE_ANGSTROM'    => ['1.0e-20',          'A²'],
        'SQUARE_ASTRONOMICAL_UNIT'   => ['2.2379523e+22', 'AU²'],
        'SQUARE_ATTOMETER'   => ['1.0e-36',          'am²'],
        'SQUARE_BICRON'      => ['1.0e-24',          'µµ²'],
        'SQUARE_CENTIMETER'  => ['0.0001',           'cm²'],
        'SQUARE_CHAIN'       => ['404.68726',        'ch²'],
        'SQUARE_CHAIN_ENGINEER'      => ['929.03412',   'ch²'],
        'SQUARE_CITY_BLOCK_US_EAST'  => ['4.97027584',  'sq block'],
        'SQUARE_CITY_BLOCK_US_WEST'  => ['17.141056',   'sq block'],
        'SQUARE_CITY_BLOCK_US_SOUTH' => ['99.88110336', 'sq block'],
        'SQUARE_CUBIT'       => ['0.20903184',       'sq cubit'],
        'SQUARE_DECIMETER'   => ['0.01',             'dm²'],
        'SQUARE_DEKAMETER'   => ['100',              'dam²'],
        'SQUARE_EXAMETER'    => ['1.0e+36',          'Em²'],
        'SQUARE_FATHOM'      => ['3.3445228',        'fth²'],
        'SQUARE_FEMTOMETER'  => ['1.0e-30',          'fm²'],
        'SQUARE_FERMI'       => ['1.0e-30',          'f²'],
        'SQUARE_FOOT'        => ['0.09290304',       'ft²'],
        'SQUARE_FOOT_SURVEY' => ['0.092903412',      'ft²'],
        'SQUARE_FURLONG'     => ['40468.726',        'fur²'],
        'SQUARE_GIGAMETER'   => ['1.0e+18',          'Gm²'],
        'SQUARE_HECTOMETER'  => ['10000',            'hm²'],
        'SQUARE_INCH'        => [['' => '0.09290304','/' => '144'],  'in²'],
        'SQUARE_INCH_SURVEY' => [['' => '0.092903412','/' => '144'], 'in²'],
        'SQUARE_KILOMETER'   => ['1000000',          'km²'],
        'SQUARE_LEAGUE_NAUTIC' => ['3.0869136e+07',  'sq league'],
        'SQUARE_LEAGUE'      => ['2.3309986e+07',    'sq league'],
        'SQUARE_LIGHT_YEAR'  => ['8.9505412e+31',    'ly²'],
        'SQUARE_LINK'        => ['0.040468726',      'sq link'],
        'SQUARE_LINK_ENGINEER' => ['0.092903412',    'sq link'],
        'SQUARE_MEGAMETER'   => ['1.0e+12',          'Mm²'],
        'SQUARE_METER'       => ['1',                'm²'],
        'SQUARE_MICROINCH'   => [['' => '1.0e-6','*' => '6.4516e-10'], 'µin²'],
        'SQUARE_MICROMETER'  => ['1.0e-12',          'µm²'],
        'SQUARE_MICROMICRON' => ['1.0e-24',          'µµ²'],
        'SQUARE_MICRON'      => ['1.0e-12',          'µ²'],
        'SQUARE_MIL'         => ['6.4516e-10',       'sq mil'],
        'SQUARE_MILE'        => [['' => '0.09290304','*' => '27878400'], 'mi²'],
        'SQUARE_MILE_NAUTIC' => ['3429904',          'mi²'],
        'SQUARE_MILE_SURVEY' => ['2589998.5',        'mi²'],
        'SQUARE_MILLIMETER'  => ['0.000001',         'mm²'],
        'SQUARE_MILLIMICRON' => ['1.0e-18',          'mµ²'],
        'SQUARE_MYRIAMETER'  => ['1.0e+8',           'mym²'],
        'SQUARE_NANOMETER'   => ['1.0e-18',          'nm²'],
        'SQUARE_PARIS_FOOT'  => ['0.1055',           'sq paris foot'],
        'SQUARE_PARSEC'      => ['9.5214087e+32',    'pc²'],
        'SQUARE_PERCH'       => ['25.292954',        'sq perch'],
        'SQUARE_PERCHE'      => ['51.072',           'sq perche'],
        'SQUARE_PETAMETER'   => ['1.0e+30',          'Pm²'],
        'SQUARE_PICOMETER'   => ['1.0e-24',          'pm²'],
        'SQUARE_ROD'         => [['' => '0.092903412','*' => '272.25'], 'rd²'],
        'SQUARE_TENTHMETER'  => ['1.0e-20',          'sq tenth-meter'],
        'SQUARE_TERAMETER'   => ['1.0e+24',          'Tm²'],
        'SQUARE_THOU'        => ['6.4516e-10',       'sq thou'],
        'SQUARE_VARA'        => ['0.70258205',       'sq vara'],
        'SQUARE_VARA_TEXAS'  => ['0.71684731',       'sq vara'],
        'SQUARE_YARD'        => ['0.83612736',       'yd²'],
        'SQUARE_YARD_SURVEY' => ['0.836130708',      'yd²'],
        'SQUARE_YOCTOMETER'  => ['1.0e-48',          'ym²'],
        'SQUARE_YOTTAMETER'  => ['1.0e+48',          'Ym²'],
        'STANG'              => ['2709',             'stang'],
        'STREMMA'            => ['1000',             'stremma'],
        'TAREA'              => ['628.8',            'tarea'],
        'TATAMI'             => ['1.62',             'tatami'],
        'TONDE_LAND'         => ['5516',             'tonde land'],
        'TOWNSHIP'           => ['93239945.3196288', 'twp'],
        'TSUBO'              => ['3.3058',           'tsubo'],
        'TUNNLAND'           => ['4936.4',           'tunnland'],
        'YARD'               => ['0.83612736',       'yd'],
        'VIRGATE'            => ['120000',           'virgate'],
        'STANDARD'           => 'SQUARE_METER'
    ];
}
