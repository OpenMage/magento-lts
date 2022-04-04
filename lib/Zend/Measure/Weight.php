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
 * Class for handling weight conversions
 *
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage Zend_Measure_Weigth
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Weight extends Zend_Measure_Abstract
{
    const STANDARD = 'KILOGRAM';

    const ARRATEL                 = 'ARRATEL';
    const ARTEL                   = 'ARTEL';
    const ARROBA_PORTUGUESE       = 'ARROBA_PORTUGUESE';
    const ARROBA                  = 'ARROBA';
    const AS_                     = 'AS_';
    const ASS                     = 'ASS';
    const ATOMIC_MASS_UNIT_1960   = 'ATOMIC_MASS_UNIT_1960';
    const ATOMIC_MASS_UNIT_1973   = 'ATOMIC_MASS_UNIT_1973';
    const ATOMIC_MASS_UNIT_1986   = 'ATOMIC_MASS_UNIT_1986';
    const ATOMIC_MASS_UNIT        = 'ATOMIC_MASS_UNIT';
    const AVOGRAM                 = 'AVOGRAM';
    const BAG                     = 'BAG';
    const BAHT                    = 'BAHT';
    const BALE                    = 'BALE';
    const BALE_US                 = 'BALE_US';
    const BISMAR_POUND            = 'BISMAR_POUND';
    const CANDY                   = 'CANDY';
    const CARAT_INTERNATIONAL     = 'CARAT_INTERNATIONAL';
    const CARAT                   = 'CARAT';
    const CARAT_UK                = 'CARAT_UK';
    const CARAT_US_1913           = 'CARAT_US_1913';
    const CARGA                   = 'CARGA';
    const CATTI                   = 'CATTI';
    const CATTI_JAPANESE          = 'CATTI_JAPANESE';
    const CATTY                   = 'CATTY';
    const CATTY_JAPANESE          = 'CATTY_JAPANESE';
    const CATTY_THAI              = 'CATTY_THAI';
    const CENTAL                  = 'CENTAL';
    const CENTIGRAM               = 'CENTIGRAM';
    const CENTNER                 = 'CENTNER';
    const CENTNER_RUSSIAN         = 'CENTNER_RUSSIAN';
    const CHALDER                 = 'CHALDER';
    const CHALDRON                = 'CHALDRON';
    const CHIN                    = 'CHIN';
    const CHIN_JAPANESE           = 'CHIN_JAPANESE';
    const CLOVE                   = 'CLOVE';
    const CRITH                   = 'CRITH';
    const DALTON                  = 'DALTON';
    const DAN                     = 'DAN';
    const DAN_JAPANESE            = 'DAN_JAPANESE';
    const DECIGRAM                = 'DECIGRAM';
    const DECITONNE               = 'DECITONNE';
    const DEKAGRAM                = 'DEKAGRAM';
    const DEKATONNE               = 'DEKATONNE';
    const DENARO                  = 'DENARO';
    const DENIER                  = 'DENIER';
    const DRACHME                 = 'DRACHME';
    const DRAM                    = 'DRAM';
    const DRAM_APOTHECARIES       = 'DRAM_APOTHECARIES';
    const DYNE                    = 'DYNE';
    const ELECTRON                = 'ELECTRON';
    const ELECTRONVOLT            = 'ELECTRONVOLT';
    const ETTO                    = 'ETTO';
    const EXAGRAM                 = 'EXAGRAM';
    const FEMTOGRAM               = 'FEMTOGRAM';
    const FIRKIN                  = 'FIRKIN';
    const FLASK                   = 'FLASK';
    const FOTHER                  = 'FOTHER';
    const FOTMAL                  = 'FOTMAL';
    const FUNT                    = 'FUNT';
    const FUNTE                   = 'FUNTE';
    const GAMMA                   = 'GAMMA';
    const GIGAELECTRONVOLT        = 'GIGAELECTRONVOLT';
    const GIGAGRAM                = 'GIGAGRAM';
    const GIGATONNE               = 'GIGATONNE';
    const GIN                     = 'GIN';
    const GIN_JAPANESE            = 'GIN_JAPANESE';
    const GRAIN                   = 'GRAIN';
    const GRAM                    = 'GRAM';
    const GRAN                    = 'GRAN';
    const GRANO                   = 'GRANO';
    const GRANI                   = 'GRANI';
    const GROS                    = 'GROS';
    const HECTOGRAM               = 'HECTOGRAM';
    const HUNDRETWEIGHT           = 'HUNDRETWEIGHT';
    const HUNDRETWEIGHT_US        = 'HUNDRETWEIGHT_US';
    const HYL                     = 'HYL';
    const JIN                     = 'JIN';
    const JUPITER                 = 'JUPITER';
    const KATI                    = 'KATI';
    const KATI_JAPANESE           = 'KATI_JAPANESE';
    const KEEL                    = 'KEEL';
    const KEG                     = 'KEG';
    const KILODALTON              = 'KILODALTON';
    const KILOGRAM                = 'KILOGRAM';
    const KILOGRAM_FORCE          = 'KILOGRAM_FORCE';
    const KILOTON                 = 'KILOTON';
    const KILOTON_US              = 'KILOTON_US';
    const KILOTONNE               = 'KILOTONNE';
    const KIN                     = 'KIN';
    const KIP                     = 'KIP';
    const KOYAN                   = 'KOYAN';
    const KWAN                    = 'KWAN';
    const LAST_GERMANY            = 'LAST_GERMANY';
    const LAST                    = 'LAST';
    const LAST_WOOL               = 'LAST_WOOL';
    const LB                      = 'LB';
    const LBS                     = 'LBS';
    const LIANG                   = 'LIANG';
    const LIBRA_ITALIAN           = 'LIBRE_ITALIAN';
    const LIBRA_SPANISH           = 'LIBRA_SPANISH';
    const LIBRA_PORTUGUESE        = 'LIBRA_PORTUGUESE';
    const LIBRA_ANCIENT           = 'LIBRA_ANCIENT';
    const LIBRA                   = 'LIBRA';
    const LIVRE                   = 'LIVRE';
    const LONG_TON                = 'LONG_TON';
    const LOT                     = 'LOT';
    const MACE                    = 'MACE';
    const MAHND                   = 'MAHND';
    const MARC                    = 'MARC';
    const MARCO                   = 'MARCO';
    const MARK                    = 'MARK';
    const MARK_GERMAN             = 'MARK_GERMANY';
    const MAUND                   = 'MAUND';
    const MAUND_PAKISTAN          = 'MAUND_PAKISTAN';
    const MEGADALTON              = 'MEGADALTON';
    const MEGAGRAM                = 'MEGAGRAM';
    const MEGATONNE               = 'MEGATONNE';
    const MERCANTILE_POUND        = 'MERCANTILE_POUND';
    const METRIC_TON              = 'METRIC_TON';
    const MIC                     = 'MIC';
    const MICROGRAM               = 'MICROGRAM';
    const MILLIDALTON             = 'MILLIDALTON';
    const MILLIER                 = 'MILLIER';
    const MILLIGRAM               = 'MILLIGRAM';
    const MILLIMASS_UNIT          = 'MILLIMASS_UNIT';
    const MINA                    = 'MINA';
    const MOMME                   = 'MOMME';
    const MYRIAGRAM               = 'MYRIAGRAM';
    const NANOGRAM                = 'NANOGRAM';
    const NEWTON                  = 'NEWTON';
    const OBOL                    = 'OBOL';
    const OBOLOS                  = 'OBOLOS';
    const OBOLUS                  = 'OBOLUS';
    const OBOLOS_ANCIENT          = 'OBOLOS_ANCIENT';
    const OBOLUS_ANCIENT          = 'OBOLUS_ANCIENT';
    const OKA                     = 'OKA';
    const ONCA                    = 'ONCA';
    const ONCE                    = 'ONCE';
    const ONCIA                   = 'ONCIA';
    const ONZA                    = 'ONZA';
    const ONS                     = 'ONS';
    const OUNCE                   = 'OUNCE';
    const OUNCE_FORCE             = 'OUNCE_FORCE';
    const OUNCE_TROY              = 'OUNCE_TROY';
    const PACKEN                  = 'PACKEN';
    const PENNYWEIGHT             = 'PENNYWEIGHT';
    const PETAGRAM                = 'PETAGRAM';
    const PFUND                   = 'PFUND';
    const PICOGRAM                = 'PICOGRAM';
    const POINT                   = 'POINT';
    const POND                    = 'POND';
    const POUND                   = 'POUND';
    const POUND_FORCE             = 'POUND_FORCE';
    const POUND_METRIC            = 'POUND_METRIC';
    const POUND_TROY              = 'POUND_TROY';
    const PUD                     = 'PUD';
    const POOD                    = 'POOD';
    const PUND                    = 'PUND';
    const QIAN                    = 'QIAN';
    const QINTAR                  = 'QINTAR';
    const QUARTER                 = 'QUARTER';
    const QUARTER_US              = 'QUARTER_US';
    const QUARTER_TON             = 'QUARTER_TON';
    const QUARTERN                = 'QUARTERN';
    const QUARTERN_LOAF           = 'QUARTERN_LOAF';
    const QUINTAL_FRENCH          = 'QUINTAL_FRENCH';
    const QUINTAL                 = 'QUINTAL';
    const QUINTAL_PORTUGUESE      = 'QUINTAL_PORTUGUESE';
    const QUINTAL_SPAIN           = 'QUINTAL_SPAIN';
    const REBAH                   = 'REBAH';
    const ROTL                    = 'ROTL';
    const ROTEL                   = 'ROTEL';
    const ROTTLE                  = 'ROTTLE';
    const RATEL                   = 'RATEL';
    const SACK                    = 'SACK';
    const SCRUPLE                 = 'SCRUPLE';
    const SEER                    = 'SEER';
    const SEER_PAKISTAN           = 'SEER_PAKISTAN';
    const SHEKEL                  = 'SHEKEL';
    const SHORT_TON               = 'SHORT_TON';
    const SLINCH                  = 'SLINCH';
    const SLUG                    = 'SLUG';
    const STONE                   = 'STONE';
    const TAEL                    = 'TAEL';
    const TAHIL_JAPANESE          = 'TAHIL_JAPANESE';
    const TAHIL                   = 'TAHIL';
    const TALENT                  = 'TALENT';
    const TAN                     = 'TAN';
    const TECHNISCHE_MASS_EINHEIT = 'TECHNISCHE_MASS_EINHEIT';
    const TERAGRAM                = 'TERAGRAM';
    const TETRADRACHM             = 'TETRADRACHM';
    const TICAL                   = 'TICAL';
    const TOD                     = 'TOD';
    const TOLA                    = 'TOLA';
    const TOLA_PAKISTAN           = 'TOLA_PAKISTAN';
    const TON_UK                  = 'TON_UK';
    const TON                     = 'TON';
    const TON_US                  = 'TON_US';
    const TONELADA_PORTUGUESE     = 'TONELADA_PORTUGUESE';
    const TONELADA                = 'TONELADA';
    const TONNE                   = 'TONNE';
    const TONNEAU                 = 'TONNEAU';
    const TOVAR                   = 'TOVAR';
    const TROY_OUNCE              = 'TROY_OUNCE';
    const TROY_POUND              = 'TROY_POUND';
    const TRUSS                   = 'TRUSS';
    const UNCIA                   = 'UNCIA';
    const UNZE                    = 'UNZE';
    const VAGON                   = 'VAGON';
    const YOCTOGRAM               = 'YOCTOGRAM';
    const YOTTAGRAM               = 'YOTTAGRAM';
    const ZENTNER                 = 'ZENTNER';
    const ZEPTOGRAM               = 'ZEPTOGRAM';
    const ZETTAGRAM               = 'ZETTAGRAM';

    /**
     * Calculations for all weight units
     *
     * @var array
     */
    protected $_units = [
        'ARRATEL'               => ['0.5',            'arratel'],
        'ARTEL'                 => ['0.5',            'artel'],
        'ARROBA_PORTUGUESE'     => ['14.69',          'arroba'],
        'ARROBA'                => ['11.502',         '@'],
        'AS_'                   => ['0.000052',       'as'],
        'ASS'                   => ['0.000052',       'ass'],
        'ATOMIC_MASS_UNIT_1960' => ['1.6603145e-27',  'amu'],
        'ATOMIC_MASS_UNIT_1973' => ['1.6605655e-27',  'amu'],
        'ATOMIC_MASS_UNIT_1986' => ['1.6605402e-27',  'amu'],
        'ATOMIC_MASS_UNIT'      => ['1.66053873e-27', 'amu'],
        'AVOGRAM'               => ['1.6605402e-27',  'avogram'],
        'BAG'                   => ['42.63768278',    'bag'],
        'BAHT'                  => ['0.015',          'baht'],
        'BALE'                  => ['326.5865064',    'bl'],
        'BALE_US'               => ['217.7243376',    'bl'],
        'BISMAR_POUND'          => ['5.993',          'bismar pound'],
        'CANDY'                 => ['254',            'candy'],
        'CARAT_INTERNATIONAL'   => ['0.0002',         'ct'],
        'CARAT'                 => ['0.0002',         'ct'],
        'CARAT_UK'              => ['0.00025919564',  'ct'],
        'CARAT_US_1913'         => ['0.0002053',      'ct'],
        'CARGA'                 => ['140',            'carga'],
        'CATTI'                 => ['0.604875',       'catti'],
        'CATTI_JAPANESE'        => ['0.594',          'catti'],
        'CATTY'                 => ['0.5',            'catty'],
        'CATTY_JAPANESE'        => ['0.6',            'catty'],
        'CATTY_THAI'            => ['0.6',            'catty'],
        'CENTAL'                => ['45.359237',      'cH'],
        'CENTIGRAM'             => ['0.00001',        'cg'],
        'CENTNER'               => ['50',             'centner'],
        'CENTNER_RUSSIAN'       => ['100',            'centner'],
        'CHALDER'               => ['2692.52',        'chd'],
        'CHALDRON'              => ['2692.52',        'chd'],
        'CHIN'                  => ['0.5',            'chin'],
        'CHIN_JAPANESE'         => ['0.6',            'chin'],
        'CLOVE'                 => ['3.175',          'clove'],
        'CRITH'                 => ['0.000089885',    'crith'],
        'DALTON'                => ['1.6605402e-27',  'D'],
        'DAN'                   => ['50',             'dan'],
        'DAN_JAPANESE'          => ['60',             'dan'],
        'DECIGRAM'              => ['0.0001',         'dg'],
        'DECITONNE'             => ['100',            'dt'],
        'DEKAGRAM'              => ['0.01',           'dag'],
        'DEKATONNE'             => ['10000',          'dat'],
        'DENARO'                => ['0.0011',         'denaro'],
        'DENIER'                => ['0.001275',       'denier'],
        'DRACHME'               => ['0.0038',         'drachme'],
        'DRAM'                  => [['' => '0.45359237', '/' => '256'], 'dr'],
        'DRAM_APOTHECARIES'     => ['0.0038879346',   'dr'],
        'DYNE'                  => ['1.0197162e-6',   'dyn'],
        'ELECTRON'              => ['9.109382e-31',   'e−'],
        'ELECTRONVOLT'          => ['1.782662e-36',   'eV'],
        'ETTO'                  => ['0.1',            'hg'],
        'EXAGRAM'               => ['1.0e+15',        'Eg'],
        'FEMTOGRAM'             => ['1.0e-18',        'fg'],
        'FIRKIN'                => ['25.40117272',    'fir'],
        'FLASK'                 => ['34.7',           'flask'],
        'FOTHER'                => ['979.7595192',    'fother'],
        'FOTMAL'                => ['32.65865064',    'fotmal'],
        'FUNT'                  => ['0.4095',         'funt'],
        'FUNTE'                 => ['0.4095',         'funte'],
        'GAMMA'                 => ['0.000000001',    'gamma'],
        'GIGAELECTRONVOLT'      => ['1.782662e-27',   'GeV'],
        'GIGAGRAM'              => ['1000000',        'Gg'],
        'GIGATONNE'             => ['1.0e+12',        'Gt'],
        'GIN'                   => ['0.6',            'gin'],
        'GIN_JAPANESE'          => ['0.594',          'gin'],
        'GRAIN'                 => ['0.00006479891',  'gr'],
        'GRAM'                  => ['0.001',          'g'],
        'GRAN'                  => ['0.00082',        'gran'],
        'GRANO'                 => ['0.00004905',     'grano'],
        'GRANI'                 => ['0.00004905',     'grani'],
        'GROS'                  => ['0.003824',       'gros'],
        'HECTOGRAM'             => ['0.1',            'hg'],
        'HUNDRETWEIGHT'         => ['50.80234544',    'cwt'],
        'HUNDRETWEIGHT_US'      => ['45.359237',      'cwt'],
        'HYL'                   => ['9.80665',        'hyl'],
        'JIN'                   => ['0.5',            'jin'],
        'JUPITER'               => ['1.899e+27',      'jupiter'],
        'KATI'                  => ['0.5',            'kati'],
        'KATI_JAPANESE'         => ['0.6',            'kati'],
        'KEEL'                  => ['21540.19446656', 'keel'],
        'KEG'                   => ['45.359237',      'keg'],
        'KILODALTON'            => ['1.6605402e-24',  'kD'],
        'KILOGRAM'              => ['1',              'kg'],
        'KILOGRAM_FORCE'        => ['1',              'kgf'],
        'KILOTON'               => ['1016046.9088',   'kt'],
        'KILOTON_US'            => ['907184.74',      'kt'],
        'KILOTONNE'             => ['1000000',        'kt'],
        'KIN'                   => ['0.6',            'kin'],
        'KIP'                   => ['453.59237',      'kip'],
        'KOYAN'                 => ['2419',           'koyan'],
        'KWAN'                  => ['3.75',           'kwan'],
        'LAST_GERMANY'          => ['2000',           'last'],
        'LAST'                  => ['1814.36948',     'last'],
        'LAST_WOOL'             => ['1981.29147216',  'last'],
        'LB'                    => ['0.45359237',     'lb'],
        'LBS'                   => ['0.45359237',     'lbs'],
        'LIANG'                 => ['0.05',           'liang'],
        'LIBRE_ITALIAN'         => ['0.339',          'lb'],
        'LIBRA_SPANISH'         => ['0.459',          'lb'],
        'LIBRA_PORTUGUESE'      => ['0.459',          'lb'],
        'LIBRA_ANCIENT'         => ['0.323',          'lb'],
        'LIBRA'                 => ['1',              'lb'],
        'LIVRE'                 => ['0.4895',         'livre'],
        'LONG_TON'              => ['1016.0469088',   't'],
        'LOT'                   => ['0.015',          'lot'],
        'MACE'                  => ['0.003778',       'mace'],
        'MAHND'                 => ['0.9253284348',   'mahnd'],
        'MARC'                  => ['0.24475',        'marc'],
        'MARCO'                 => ['0.23',           'marco'],
        'MARK'                  => ['0.2268',         'mark'],
        'MARK_GERMANY'          => ['0.2805',         'mark'],
        'MAUND'                 => ['37.3242',        'maund'],
        'MAUND_PAKISTAN'        => ['40',             'maund'],
        'MEGADALTON'            => ['1.6605402e-21',  'MD'],
        'MEGAGRAM'              => ['1000',           'Mg'],
        'MEGATONNE'             => ['1.0e+9',         'Mt'],
        'MERCANTILE_POUND'      => ['0.46655',        'lb merc'],
        'METRIC_TON'            => ['1000',           't'],
        'MIC'                   => ['1.0e-9',         'mic'],
        'MICROGRAM'             => ['1.0e-9',         '�g'],
        'MILLIDALTON'           => ['1.6605402e-30',  'mD'],
        'MILLIER'               => ['1000',           'millier'],
        'MILLIGRAM'             => ['0.000001',       'mg'],
        'MILLIMASS_UNIT'        => ['1.6605402e-30',  'mmu'],
        'MINA'                  => ['0.499',          'mina'],
        'MOMME'                 => ['0.00375',        'momme'],
        'MYRIAGRAM'             => ['10',             'myg'],
        'NANOGRAM'              => ['1.0e-12',        'ng'],
        'NEWTON'                => ['0.101971621',    'N'],
        'OBOL'                  => ['0.0001',         'obol'],
        'OBOLOS'                => ['0.0001',         'obolos'],
        'OBOLUS'                => ['0.0001',         'obolus'],
        'OBOLOS_ANCIENT'        => ['0.0005',         'obolos'],
        'OBOLUS_ANCIENT'        => ['0.00057',        'obolos'],
        'OKA'                   => ['1.28',           'oka'],
        'ONCA'                  => ['0.02869',        'onca'],
        'ONCE'                  => ['0.03059',        'once'],
        'ONCIA'                 => ['0.0273',         'oncia'],
        'ONZA'                  => ['0.02869',        'onza'],
        'ONS'                   => ['0.1',            'ons'],
        'OUNCE'                 => [['' => '0.45359237', '/' => '16'],    'oz'],
        'OUNCE_FORCE'           => [['' => '0.45359237', '/' => '16'],    'ozf'],
        'OUNCE_TROY'            => [['' => '65.31730128', '/' => '2100'], 'oz'],
        'PACKEN'                => ['490.79',         'packen'],
        'PENNYWEIGHT'           => [['' => '65.31730128', '/' => '42000'], 'dwt'],
        'PETAGRAM'              => ['1.0e+12',        'Pg'],
        'PFUND'                 => ['0.5',            'pfd'],
        'PICOGRAM'              => ['1.0e-15',        'pg'],
        'POINT'                 => ['0.000002',       'pt'],
        'POND'                  => ['0.5',            'pond'],
        'POUND'                 => ['0.45359237',     'lb'],
        'POUND_FORCE'           => ['0.4535237',      'lbf'],
        'POUND_METRIC'          => ['0.5',            'lb'],
        'POUND_TROY'            => [['' => '65.31730128', '/' => '175'], 'lb'],
        'PUD'                   => ['16.3',           'pud'],
        'POOD'                  => ['16.3',           'pood'],
        'PUND'                  => ['0.5',            'pund'],
        'QIAN'                  => ['0.005',          'qian'],
        'QINTAR'                => ['50',             'qintar'],
        'QUARTER'               => ['12.70058636',    'qtr'],
        'QUARTER_US'            => ['11.33980925',    'qtr'],
        'QUARTER_TON'           => ['226.796185',     'qtr'],
        'QUARTERN'              => ['1.587573295',    'quartern'],
        'QUARTERN_LOAF'         => ['1.81436948',     'quartern-loaf'],
        'QUINTAL_FRENCH'        => ['48.95',          'q'],
        'QUINTAL'               => ['100',            'q'],
        'QUINTAL_PORTUGUESE'    => ['58.752',         'q'],
        'QUINTAL_SPAIN'         => ['45.9',           'q'],
        'REBAH'                 => ['0.2855',         'rebah'],
        'ROTL'                  => ['0.5',            'rotl'],
        'ROTEL'                 => ['0.5',            'rotel'],
        'ROTTLE'                => ['0.5',            'rottle'],
        'RATEL'                 => ['0.5',            'ratel'],
        'SACK'                  => ['165.10762268',   'sack'],
        'SCRUPLE'               => [['' => '65.31730128', '/' => '50400'], 's'],
        'SEER'                  => ['0.933105',       'seer'],
        'SEER_PAKISTAN'         => ['1',              'seer'],
        'SHEKEL'                => ['0.01142',        'shekel'],
        'SHORT_TON'             => ['907.18474',      'st'],
        'SLINCH'                => ['175.126908',     'slinch'],
        'SLUG'                  => ['14.593903',      'slug'],
        'STONE'                 => ['6.35029318',     'st'],
        'TAEL'                  => ['0.03751',        'tael'],
        'TAHIL_JAPANESE'        => ['0.03751',        'tahil'],
        'TAHIL'                 => ['0.05',           'tahil'],
        'TALENT'                => ['30',             'talent'],
        'TAN'                   => ['50',             'tan'],
        'TECHNISCHE_MASS_EINHEIT' => ['9.80665',      'TME'],
        'TERAGRAM'              => ['1.0e+9',         'Tg'],
        'TETRADRACHM'           => ['0.014',          'tetradrachm'],
        'TICAL'                 => ['0.0164',         'tical'],
        'TOD'                   => ['12.70058636',    'tod'],
        'TOLA'                  => ['0.0116638125',   'tola'],
        'TOLA_PAKISTAN'         => ['0.0125',         'tola'],
        'TON_UK'                => ['1016.0469088',   't'],
        'TON'                   => ['1000',           't'],
        'TON_US'                => ['907.18474',      't'],
        'TONELADA_PORTUGUESE'   => ['793.15',         'tonelada'],
        'TONELADA'              => ['919.9',          'tonelada'],
        'TONNE'                 => ['1000',           't'],
        'TONNEAU'               => ['979',            'tonneau'],
        'TOVAR'                 => ['128.8',          'tovar'],
        'TROY_OUNCE'            => [['' => '65.31730128', '/' => '2100'], 'troy oz'],
        'TROY_POUND'            => [['' => '65.31730128', '/' => '175'],  'troy lb'],
        'TRUSS'                 => ['25.40117272',    'truss'],
        'UNCIA'                 => ['0.0272875',      'uncia'],
        'UNZE'                  => ['0.03125',        'unze'],
        'VAGON'                 => ['10000',          'vagon'],
        'YOCTOGRAM'             => ['1.0e-27',        'yg'],
        'YOTTAGRAM'             => ['1.0e+21',        'Yg'],
        'ZENTNER'               => ['50',             'Ztr'],
        'ZEPTOGRAM'             => ['1.0e-24',        'zg'],
        'ZETTAGRAM'             => ['1.0e+18',        'Zg'],
        'STANDARD'              => 'KILOGRAM'
    ];
}
