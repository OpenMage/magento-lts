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
 * @package    Zend_Currency
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Currency.php 6137 2007-08-19 14:55:27Z shreef $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * include needed classes
 */
#require_once 'Zend/Locale.php';
#require_once 'Zend/Locale/Data.php';
#require_once 'Zend/Locale/Format.php';


/**
 * @category   Zend
 * @package    Zend_Currency
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Currency
{
    // constants for defining what currency symbol should be displayed
    const NO_SYMBOL     = 1;
    const USE_SYMBOL    = 2;
    const USE_SHORTNAME = 3;
    const USE_NAME      = 4;

    // constants for defining the position of the currencysign
    const STANDARD = 8;
    const RIGHT    = 16;
    const LEFT     = 32;

    /**
     * locale for this currency
     *
     * @var string
     */
    private $_locale = null;

    protected $_options = array(
        'position'  => self::STANDARD,  // position for the currency sign
        'script'    => null,            // script for output
        'format'    => null,            // locale for numeric output
        'display'   => self::NO_SYMBOL, // currency detail to show
        'precision' => 2,               // precision for currency
        'name'      => null,            // name for this currency
        'currency'  => null,            // 3 lettered international abbreviation
        'symbol'    => null             // currency symbol
    );

    /**
     * Creates a currency instance. Every supressed parameter is used from the actual or the given locale.
     *
     * @param  string              $currency  OPTIONAL currency short name
     * @param  string|Zend_Locale  $locale    OPTIONAL locale name
     * @return Zend_Currency
     * @throws Zend_Currency_Exception
     */
    public function __construct($currency = null, $locale = null)
    {
        if (Zend_Locale::isLocale($currency)) {
            $temp     = $locale;
            $locale   = $currency;
            $currency = $temp;
        }

        $this->setLocale($locale);

        // get currency details
        $this->_options['currency']      = self::getShortName   ($currency, $this->_locale);
        $this->_options['name']          = self::getName        ($currency, $this->_locale);
        $this->_options['symbol']        = self::getSymbol      ($currency, $this->_locale);
        $this->_options['symbol_choice'] = self::getSymbolChoice($currency, $this->_locale);

        if (($this->_options['currency'] === null) and ($this->_options['name'] === null)) {
            #require_once 'Zend/Currency/Exception.php';
            throw new Zend_Currency_Exception("Currency '$currency' not found");
        }
        // get the format
        $this->_options['position'] = $this->_updateFormat();
        $this->_options['display']     = self::NO_SYMBOL;
        if (!empty($this->_options['symbol'])) {
            $this->_options['display'] = self::USE_SYMBOL;
        } else if (!empty($this->_options['currency'])) {
            $this->_options['display'] = self::USE_SHORTNAME;
        }
        return $this;
    }


    /**
     * Gets the information required for formating the currency from Zend_Locale
     *
     * @return Zend_Currency
     * @throws Zend_Currency_Exception
     */
    protected function _updateFormat()
    {
        $locale = empty($this->_options['format']) ? $this->_locale : $this->_options['format'];

        //getting the format information of the currency
        $format = Zend_Locale_Data::getContent($locale, 'currencynumber');

        iconv_set_encoding('internal_encoding', 'UTF-8');
        if (iconv_strpos($format, ';')) {
            $format = iconv_substr($format, 0, iconv_strpos($format, ';'));
        }

        //knowing the sign positioning information
        if (iconv_strpos($format, '¤') == 0) {
            $position = self::LEFT;
        } else if (iconv_strpos($format, '¤') == iconv_strlen($format)-1) {
            $position = self::RIGHT;
        }

        return $position;
    }


    /**
     * Returns a localized currency string
     *
     * @param  int|float  $value    Currency value
     * @param  array      $options  OPTIONAL options to set temporary
     * @return string
     */
    public function toCurrency($value, array $options = array())
    {
        //validate the passed number
        if (!isset($value) || !is_numeric($value)) {
            #require_once 'Zend/Currency/Exception.php';
            throw new Zend_Currency_Exception("Value '$value' has to be numeric");
        }

        $options = array_merge($this->_options, $this->checkOptions($options));

        //format the number
        if (empty($options['format'])) {
            $options['format'] = $this->_locale;
        }

        // select currency symbol if needed
        if ($options['symbol_choice']) {
            $symbols = explode('|', $options['symbol']);
            if (is_array($symbols)) {
                foreach ($symbols as $symbol) {
                    $type = $position = null;
                    if (($tmp = iconv_strpos($symbol, '≤')) !== false) {
                        $type = 1;
                        $position = $tmp;
                    }
                    if (($tmp = iconv_strpos($symbol, '<')) !== false) {
                        $type = 2;
                        $position = $tmp;
                    }

                    if (!is_null($position)) {
                        $number = iconv_substr($symbol, 0, $position);
                        $sign = iconv_substr($symbol, $position+1);

                        if (($type == 1 && $number <= $value) || ($type == 2 && $number < $value)) {
                            $options['symbol'] = $sign;
                        }
                    }
                }
            }
        }

        $value = Zend_Locale_Format::toNumber($value, array('locale' => $options['format'], 'precision' => $options['precision']));

        //localize the number digits
        if (!empty ($options['script'])) {
            $value = Zend_Locale_Format::convertNumerals($value, 'Latn', $options['script']);
        }

        //get the sign to be placed next to the number
        if (!is_numeric($options['display'])) {
            $sign = " " . $options['display'] . " ";
        } else {
            switch($options['display']) {
                case self::USE_SYMBOL:
                    $sign = " " . $options['symbol'] . " ";
                    break;
                case self::USE_SHORTNAME:
                    $sign = " " . $options['currency'] . " ";
                    break;
                case self::USE_NAME:
                    $sign = " " . $options['name'] . " ";
                    break;
                default:
                    $sign = "";
                    break;
            }
        }

        //place the sign next to the number
        if ($options['position'] == self::RIGHT) {
            $value = $value . $sign;
        } else if ($options['position'] == self::LEFT) {
            $value = $sign . $value;
        }
        return trim($value);
    }


    /**
     * Sets the formating options of the localized currency string
     * If no parameter is passed, the standard setting of the
     * actual set locale will be used
     *
     * @param  const|string        $rules   OPTIONAL formating rules for currency
     *                  - USE_SYMBOL|NOSYMBOL : display currency symbol
     *                  - USE_NAME|NONAME     : display currency name
     *                  - STANDARD|RIGHT|LEFT : where to display currency symbol/name
     *                  - string: gives the currency string/name/sign to set
     * @param  string              $script  OPTIONAL Number script to use for output
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for output formatting
     * @return Zend_Currency
     */
    public function setFormat(array $options = array())
    {
        $this->_options = array_merge($this->_options, $this->checkOptions($options));
        return $this;
    }

    /**
     * Internal function for checking static given locale parameter
     *
     * @param  string              $currency  OPTIONAL Currency name
     * @param  string|Zend_Locale  $locale    OPTIONAL Locale to display informations
     * @return string                         the extracted locale representation as string
     * @throws Zend_Currency_Exception
     */
    private function _checkParams($currency = null, $locale = null)
    {
        //manage the params
        if (empty($locale) && !empty($currency) && (Zend_Locale::isLocale($currency))) {
            $locale   = $currency;
            $currency = null;
        }

        if ($locale instanceof Zend_Locale) {
            $locale = $locale->toString();
        }

        //validate the locale and get the country short name
        $country = null;
        if ($locale = Zend_Locale::isLocale($locale) and (strlen($locale) > 4)) {
            $country = substr($locale, strpos($locale, '_')+1 );
        } else {
            #require_once 'Zend/Currency/Exception.php';
            throw new Zend_Currency_Exception("No region found within the locale '$locale'");
        }

        //get the available currencies for this country
        $data = Zend_Locale_Data::getContent($locale, 'currencytoregion', $country);
        if (!empty($currency) and (!empty($data))) {
            $abbreviation = $currency;
        } else {
            $abbreviation = $data;
        }

        return array('locale' => $locale, 'currency' => $currency, 'name' => $abbreviation, 'country' => $country);
    }

    /**
     * Returns the actual or details of other currency symbols,
     * when no symbol is available it returns the currency shortname (f.e. FIM for Finnian Mark)
     *
     * @param  string              $currency   OPTIONAL Currency name
     * @param  string|Zend_Locale  $locale     OPTIONAL Locale to display informations
     * @return string
     * @throws Zend_Currency_Exception
     */
    public function getSymbol($currency = null, $locale = null)
    {
        if (($currency === null) and ($locale === null)) {
            return $this->_options['symbol'];
        }

        $params = self::_checkParams($currency, $locale);

        //get the symbol
        $symbol = Zend_Locale_Data::getContent($params['locale'], 'currencysymbol', $params['currency']);
        if (empty($symbol)) {
            $symbol = Zend_Locale_Data::getContent($params['locale'], 'currencysymbol', $params['name']);
        }
        if (empty($symbol)) {
            return null;
        }
        return $symbol;
    }


    public function getSymbolChoice($currency = null, $locale = null)
    {
        if (($currency === null) and ($locale === null)) {
            return $this->_options['symbol_choice'];
        }

        $params = self::_checkParams($currency, $locale);

        //get the symbol

        $symbol = Zend_Locale_Data::getContent($params['locale'], 'currencysymbolchoice', $params['currency']);
        if (empty($symbol)) {
            $symbol = Zend_Locale_Data::getContent($params['locale'], 'currencysymbolchoice', $params['name']);
        }
        if (empty($symbol)) {
            return null;
        }
        return $symbol;
    }

    /**
     * Returns the actual or details of other currency shortnames
     *
     * @param  string              $currency   OPTIONAL Currency's name
     * @param  string|Zend_Locale  $locale     OPTIONAL the locale
     * @return string
     * @throws Zend_Currency_Exception
     */
    public function getShortName($currency = null, $locale = null)
    {
        if (($currency === null) and ($locale === null)) {
            return $this->_options['currency'];
        }

        $params = self::_checkParams($currency, $locale);

        //get the shortname
        if (empty($params['currency'])) {
            return $params['name'];
        }
        $list = Zend_Locale_Data::getContent($params['locale'], 'currencytoname', $params['currency']);
        if (empty($list)) {
            $list = Zend_Locale_Data::getContent($params['locale'], 'nametocurrency', $params['currency']);
            if (!empty($list)) {
                $list = $params['currency'];
            }
        }
        if (empty($list)) {
            return null;
        }
        return $list;
    }


    /**
     * Returns the actual or details of other currency names
     *
     * @param  string              $currency   OPTIONAL Currency's short name
     * @param  string|Zend_Locale  $locale     OPTIONAL the locale
     * @return string
     * @throws Zend_Currency_Exception
     */
    public function getName($currency = null, $locale = null)
    {
        if (($currency === null) and ($locale === null)) {
            return $this->_options['name'];
        }

        $params = self::_checkParams($currency, $locale);

        //get the name
        $name = Zend_Locale_Data::getContent($params['locale'], 'nametocurrency', $params['currency']);
        if (empty($name)) {
            $name = Zend_Locale_Data::getContent($params['locale'], 'nametocurrency', $params['name']);
        }
        if (empty($name)) {
            return null;
        }
        return $name;
    }


    /**
     * Returns a list of regions where this currency is or was known
     *
     * @param  string  $currency  OPTIONAL Currency's short name
     * @return array List of regions
     */
    public function getRegionList($currency = null)
    {
        if ($currency === null) {
            $currency = $this->_options['currency'];
        }
        if (empty($currency)) {
            #require_once 'Zend/Currency/Exception.php';
            throw new Zend_Currency_Exception("No currency defined");
        }
        $data = Zend_Locale_Data::getContent('', 'regiontocurrency', $currency);

        $result = explode(' ', $data);
        return $result;
    }


    /**
     * Returns a list of currencies which are used in this region
     * a region name should be 2 charachters only (f.e. EG, DE, US)
     * If no region is given, the actual region is used
     *
     * @param  string  $region  OPTIONAL Region to return the currencies for
     * @return array  List of currencies
     */
    public function getCurrencyList($region = null)
    {
        if (empty($region)) {
            if (strlen($this->_locale) > 4) {
                $region = substr($this->_locale, strpos($this->_locale, '_')+1 );
            }
        }
        return Zend_Locale_Data::getList('', 'regiontocurrency', $region);
    }


    /**
     * Returns the actual currency name
     *
     * @return string
     */
    public function toString()
    {
        return !empty($this->_options['name']) ? $this->_options['name'] : $this->_options['currency'];
    }


    /**
     * Returns the currency name
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * sets a cache for Zend_Currency
     *
     * @param Zend_Cache_Core $cache  Cache to set
     */
    public static function setCache(Zend_Cache_Core $cache)
    {
        Zend_Locale_Data::setCache($cache);
    }


    /**
     * Sets a new locale for data retreivement
     * Returned is the really set locale.
     * Example: 'de_XX' will be set to 'de' because 'de_XX' does not exist
     * 'xx_YY' will be set to 'root' because 'xx' does not exist
     *
     * @param  string|Zend_Locale     $locale  OPTIONAL Locale for parsing input
     * @return string
     */
    public function setLocale($locale = null)
    {
        if ($locale instanceof Zend_Locale) {
            $this->_locale = $locale->toString();
        } else if (!$this->_locale = Zend_Locale::isLocale($locale, true)) {
            #require_once 'Zend/Currency/Exception.php';
            throw new Zend_Currency_Exception("Given locale ($locale) does not exist");
        }

        // get currency details
        $this->_options['currency'] = $this->getShortName(null, $this->_locale);
        $this->_options['name']     = $this->getName     (null, $this->_locale);
        $this->_options['symbol']   = $this->getSymbol   (null, $this->_locale);

        return $this->getLocale();
    }


    /**
     * Returns the actual set locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->_locale;
    }

    /**
     * Internal method for checking the options array
     *
     * @param  array $options
     * @return array
     * @throws Zend_Currency_Exception
     */
    private function checkOptions(array $options = array())
    {
        if (count($options) == 0) {
            return $this->_options;
        }
        foreach($options as $name => $value) {
            $name = strtolower($name);
            if ($name !== 'format') {
                if (gettype($value) === 'string') {
                    $value = strtolower($value);
                }
            }
            if (array_key_exists($name, $this->_options)) {
                switch($name) {
                    case 'position' :
                        if (($value !== self::STANDARD) and ($value !== self::RIGHT) and ($value !== self::LEFT)) {
                            #require_once 'Zend/Currency/Exception.php';
                            throw new Zend_Currency_Exception("Unknown position '" . $value . "'");
                        }
                        if ($value === self::STANDARD) {
                            $options['position'] = $this->_updateFormat();
                        }
                        break;
                    case 'format' :
                        if (!empty($value) && (!Zend_Locale::isLocale($value))) {
                            #require_once 'Zend/Currency/Exception.php';
                            throw new Zend_Currency_Exception("'" .
                                (gettype($value) === 'object' ? get_class($value) : $value)
                                . "' is not a known locale.");
                        }
                        break;
                    case 'display' :
                        if (is_numeric($value) and ($value !== self::NO_SYMBOL) and ($value !== self::USE_SYMBOL) and
                            ($value !== self::USE_SHORTNAME) and ($value !== self::USE_NAME)) {
                            #require_once 'Zend/Currency/Exception.php';
                            throw new Zend_Currency_Exception("Unknown display '$value'");
                        }
                        break;
                    case 'precision' :
                        if ($value === NULL) {
                            $value = -1;
                        }
                        if (($value < -1) || ($value > 30)) {
                            #require_once 'Zend/Currency/Exception.php';
                            throw new Zend_Currency_Exception("'$value' precision has to be between -1 and 30.");
                        }
                        break;
                    case 'script' :
                        try {
                            Zend_Locale_Format::convertNumerals(0,$options['script']);
                        } catch (Zend_Locale_Exception $e) {
                            #require_once 'Zend/Currency/Exception.php';
                            throw new Zend_Currency_Exception($e->getMessage());
                        }
                        break;
                }
            }
            else {
                #require_once 'Zend/Currency/Exception.php';
                throw new Zend_Currency_Exception("Unknown option: '$name' = '$value'");
            }
        }
        return $options;
    }
}