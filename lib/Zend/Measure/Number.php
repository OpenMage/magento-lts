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
 * @version    $Id: Number.php 8276 2008-02-22 08:09:33Z thomas $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Implement needed classes
 */
#require_once 'Zend/Measure/Abstract.php';
#require_once 'Zend/Locale.php';


/**
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage Zend_Measure_Number
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * This class can only handle numbers without precission
 */
class Zend_Measure_Number extends Zend_Measure_Abstract
{
    // Number definitions
    const STANDARD = 'DECIMAL';

    const BINARY      = 'BINARY';
    const TERNARY     = 'TERNARY';
    const QUATERNARY  = 'QUATERNARY';
    const QUINARY     = 'QUINARY';
    const SENARY      = 'SENARY';
    const SEPTENARY   = 'SEPTENARY';
    const OCTAL       = 'OCTAL';
    const NONARY      = 'NONARY';
    const DECIMAL     = 'DECIMAL';
    const DUODECIMAL  = 'DUODECIMAL';
    const HEXADECIMAL = 'HEXADECIMAL';
    const ROMAN       = 'ROMAN';

    protected $_UNITS = array(
        'BINARY'      => array(2,  '⑵'),
        'TERNARY'     => array(3,  '⑶'),
        'QUATERNARY'  => array(4,  '⑷'),
        'QUINARY'     => array(5,  '⑸'),
        'SENARY'      => array(6,  '⑹'),
        'SEPTENARY'   => array(7,  '⑺'),
        'OCTAL'       => array(8,  '⑻'),
        'NONARY'      => array(9,  '⑼'),
        'DECIMAL'     => array(10, '⑽'),
        'DUODECIMAL'  => array(12, '⑿'),
        'HEXADECIMAL' => array(16, '⒃'),
        'ROMAN'       => array(99, ''),
        'STANDARD'    => 'DECIMAL'
    );


    // Definition of all roman signs
    private static $_ROMAN = array(
        'I' => 1,
        'A' => 4,
        'V' => 5,
        'B' => 9,
        'X' => 10,
        'E' => 40,
        'L' => 50,
        'F' => 90,
        'C' => 100,
        'G' => 400,
        'D' => 500,
        'H' => 900,
        'M' => 1000,
        'J' => 4000,
        'P' => 5000,
        'K' => 9000,
        'Q' => 10000,
        'N' => 40000,
        'R' => 50000,
        'W' => 90000,
        'S' => 100000,
        'Y' => 400000,
        'T' => 500000,
        'Z' => 900000,
        'U' => 1000000
    );


    // Convertion table for roman signs
    private static $_ROMANCONVERT = array(
        '/_V/' => '/P/',
        '/_X/' => '/Q/',
        '/_L/' => '/R/',
        '/_C/' => '/S/',
        '/_D/' => '/T/',
        '/_M/' => '/U/',
        '/IV/' => '/A/',
        '/IX/' => '/B/',
        '/XL/' => '/E/',
        '/XC/' => '/F/',
        '/CD/' => '/G/',
        '/CM/' => '/H/',
        '/M_V/'=> '/J/',
        '/MQ/' => '/K/',
        '/QR/' => '/N/',
        '/QS/' => '/W/',
        '/ST/' => '/Y/',
        '/SU/' => '/Z/'
    );


    /**
     * Zend_Measure_Abstract is an abstract class for the different measurement types
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Area Type
     * @param  $locale locale - OPTIONAL a Zend_Locale Type
     * @throws Zend_Measure_Exception
     */
    public function __construct($value, $type, $locale = null)
    {
        if (Zend_Locale::isLocale($type)) {
            $locale = $type;
            $type = null;
        }

        if ($locale === null) {
            $locale = new Zend_Locale();
        }

        if ($locale instanceof Zend_Locale) {
            $locale = $locale->toString();
        }

        if (!$this->_Locale = Zend_Locale::isLocale($locale, true)) {
            #require_once 'Zend/Measure/Exception.php';
            throw new Zend_Measure_Exception("Language ($locale) is unknown");
        }

        $this->_Locale = $locale;

        if ($type === null) {
            $type = $this->_UNITS['STANDARD'];
        }

        if (!array_key_exists($type, $this->_UNITS)) {
            #require_once 'Zend/Measure/Exception.php';
            throw new Zend_Measure_Exception("Type ($type) is unknown");
        }
        $this->setValue($value, $type, $this->_Locale);
    }


    /**
     * Set a new value
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Number Type
     * @param  $locale locale - OPTIONAL a Zend_Locale Type
     * @throws Zend_Measure_Exception
     */
    public function setValue($value, $type = null, $locale = null)
    {
        if (empty( $locale )) {
            $locale = $this->_Locale;
        }

        if (empty($this->_UNITS[$type])) {
            #require_once 'Zend/Measure/Exception.php';
            throw new Zend_Measure_Exception('unknown type of number:' . $type);
        }

        switch( $type ) {
            case 'BINARY' :
                preg_match('/[01]+/', $value, $ergebnis);
                $value = $ergebnis[0];
                break;
            case 'TERNARY' :
                preg_match('/[012]+/', $value, $ergebnis);
                $value = $ergebnis[0];
                break;
            case 'QUATERNARY' :
                preg_match('/[0123]+/', $value, $ergebnis);
                $value = $ergebnis[0];
                break;
            case 'QUINARY' :
                preg_match('/[01234]+/', $value, $ergebnis);
                $value = $ergebnis[0];
                break;
            case 'SENARY' :
                preg_match('/[012345]+/', $value, $ergebnis);
                $value = $ergebnis[0];
                break;
            case 'SEPTENARY' :
                preg_match('/[0123456]+/', $value, $ergebnis);
                $value = $ergebnis[0];
                break;
            case 'OCTAL' :
                preg_match('/[01234567]+/', $value, $ergebnis);
                $value = $ergebnis[0];
                break;
            case 'NONARY' :
                preg_match('/[012345678]+/', $value, $ergebnis);
                $value = $ergebnis[0];
                break;
            case 'DUODECIMAL' :
                preg_match('/[0123456789AB]+/', strtoupper( $value ), $ergebnis);
                $value = $ergebnis[0];
                break;
            case 'HEXADECIMAL' :
                preg_match('/[0123456789ABCDEF]+/', strtoupper( $value ), $ergebnis);
                $value = $ergebnis[0];
                break;
            case 'ROMAN' :
                preg_match('/[IVXLCDM_]+/', strtoupper( $value ), $ergebnis);
                $value = $ergebnis[0];
                break;
            default:
                try {
                    $value = Zend_Locale_Format::getInteger($value, array('locale' => $locale));
                } catch (Exception $e) {
                    #require_once 'Zend/Measure/Exception.php';
                    throw new Zend_Measure_Exception($e->getMessage());
                }
                if (call_user_func(Zend_Locale_Math::$comp, $value, 0) < 0) {
                    $value = call_user_func(Zend_Locale_Math::$sqrt, call_user_func(Zend_Locale_Math::$pow, $value, 2));
                }
                break;
        }

        $this->_value = $value;
        $this->_type  = $type;
    }


    /**
     * Convert input to decimal value string
     *
     * @param $input mixed  - input string
     * @param $type  type   - type from which to convert to decimal
     * @return  string
     */
    private function toDecimal($input, $type)
    {
        $value = "";
        // Convert base xx values
        if ($this->_UNITS[$type][0] <= 16) {

            $split = str_split( $input );
            $length = strlen( $input );
            for($X = 0; $X < $length; ++$X) {
                $split[$X] = hexdec( $split[$X] );
                $value = call_user_func(Zend_Locale_Math::$add, $value,
                            call_user_func(Zend_Locale_Math::$mul, $split[$X],
                            call_user_func(Zend_Locale_Math::$pow, $this->_UNITS[$type][0], ($length - $X - 1))));
            }
        }

        // Convert roman numbers
        if ($type == 'ROMAN') {

            $input = strtoupper( $input );
            $input = preg_replace( array_keys(self::$_ROMANCONVERT), array_values(self::$_ROMANCONVERT), $input);

            $split = preg_split('//', strrev($input), -1, PREG_SPLIT_NO_EMPTY);

            for ($X=0; $X < sizeof($split); $X++) {
                if ($split[$X] == '/') {
                    continue;
                }
                $num = self::$_ROMAN[$split[$X]];
                if (($X > 0 and ($split[$X-1] != '/') and ($num < self::$_ROMAN[$split[$X-1]]))) {
                    $num -= $num;
                }
                $value += $num;
            }
            str_replace('/', '', $value);
        }
        return $value;
    }


    /**
     * Convert input to type value string
     *
     * @param $input mixed  - input string
     * @param $type  type   - type to convert to
     * @return string
     */
    private function fromDecimal($value, $type)
    {
        $tempvalue = $value;
        if ($this->_UNITS[$type][0] <= 16) {
            $newvalue = '';
            $count = 200;
            $base = $this->_UNITS[$type][0];
            
            while (call_user_func(Zend_Locale_Math::$comp, $value, 0, 25) <> 0) {
                $target = call_user_func(Zend_Locale_Math::$mod, $value, $base);

                $newvalue = strtoupper( dechex($target) ) . $newvalue;
                
                $value = call_user_func(Zend_Locale_Math::$sub, $value, $target, 0);
                $value = call_user_func(Zend_Locale_Math::$div, $value, $base,   0);

                --$count;
                if ($count == 0) {
                    #require_once 'Zend/Measure/Exception.php';
                    throw new Zend_Measure_Exception("Your value '$tempvalue' cannot be processed because it extends 200 digits");
                }
            }
            
            if ($newvalue == '') {
                $newvalue = '0';
            }
        }

        if ($type == 'ROMAN') {
            $i = 0;
            $newvalue = "";
            $romanval = array_values( array_reverse(self::$_ROMAN) );
            $romankey = array_keys( array_reverse(self::$_ROMAN) );
            $count = 200;
            while(call_user_func(Zend_Locale_Math::$comp, $value, 0, 25) <> 0) {

                while ($value >= $romanval[$i]) {
                    $value    -= $romanval[$i];
                    $newvalue .= $romankey[$i];

                    if ($value < 1) {
                        break; 
                    }
                    --$count;
                    if ($count == 0) {
                        #require_once 'Zend/Measure/Exception.php';
                        throw new Zend_Measure_Exception("Your value '$tempvalue' cannot be processed because it extends 200 digits");
                    }
                }
                $i++;

            }

            $newvalue = str_replace("/", "", preg_replace(array_values(self::$_ROMANCONVERT), array_keys(self::$_ROMANCONVERT), $newvalue));
        }

        return $newvalue;
    }

    /**
     * Set a new type, and convert the value
     *
     * @param $type  new type to set
     * @throws Zend_Measure_Exception
     */
    public function setType( $type )
    {
        if (empty($this->_UNITS[$type])) {
            #require_once 'Zend/Measure/Exception.php';
            throw new Zend_Measure_Exception('Unknown type of number:' . $type);
        }

        $value = $this->toDecimal($this->getValue(-1), $this->getType(-1));
        $value = $this->fromDecimal($value, $type);

        $this->_value = $value;
        $this->_type  = $type;
    }


    /**
     * Alias function for setType returning the converted unit
     * Default is 0 as this class only handles numbers without precision
     *
     * @param $type   type
     * @param $round  integer  OPTIONAL Precision to add, will always be 0
     * @return string
     */
    public function convertTo($type, $round = 0)
    {
        $this->setType($type);
        return $this->toString($round);
    }
}
