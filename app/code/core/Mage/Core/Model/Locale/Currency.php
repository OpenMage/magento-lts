<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Locale model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Locale_Currency extends Zend_Currency
{
    const XML_PATH_TRIM_CURRENCY_SIGN = 'currency/options/trim_sign';
    const US_LOCALE = 'en_US';
    protected $_locale;

    /**
     * Creates a currency instance. Every supressed parameter is used from the actual or the given locale.
     *
     * @param  string             $currency OPTIONAL currency short name
     * @param  string|Zend_Locale $locale   OPTIONAL locale name
     * @throws Zend_Currency_Exception When currency is invalid
     */
    public function __construct($currency = null, $locale = null)
    {
        parent::__construct($currency, $locale);
        $this->_options['symbol_choice'] = self::getSymbolChoice($currency, $this->_locale);
    }

    /**
     * Returns the actual or details of available currency symbol choice,
     *
     * @param  string             $currency (Optional) Currency name
     * @param  string|Zend_Locale $locale   (Optional) Locale to display informations
     * @return string
     */
    public function getSymbolChoice($currency = null, $locale = null)
    {
        if (($currency === null) and ($locale === null)) {
            return $this->_options['symbol_choice'];
        }

        $params = self::_checkParams($currency, $locale);

        //Get the symbol choice
        $symbolChoice = Zend_Locale_Data::getContent($params['locale'], 'currencysymbolchoice', $params['currency']);
        if (empty($symbolChoice) === true) {
            $symbolChoice = Zend_Locale_Data::getContent($params['locale'], 'currencysymbolchoice', $params['name']);
        }
        if (empty($symbolChoice) === true) {
            return null;
        }
        return $symbolChoice;
    }

    public function setLocale($locale = null)
    {
        $this->_locale = $locale;
        parent::setLocale($locale);
        return $this;
    }

    /**
     * Place the sign next to the number
     *
     * @param string $value
     * @param string $sign
     * @param array $options
     * @return string
     */
    protected function _concatSign($value, $sign, $options)
    {
        $trimSign = $this->getStore()->getConfig(self::XML_PATH_TRIM_CURRENCY_SIGN);
        if (is_null($trimSign) && $this->_locale && $this->_locale == self::US_LOCALE) {
            $trimSign = true;
        }
        if ($trimSign) {
            $sign = trim($sign);
        }

        // Place the sign next to the number
        if ($options['position'] === self::RIGHT) {
            $result = $value . $sign;
        } else if ($options['position'] === self::LEFT) {
            // Do not place sign before minus. And do not allow space between minus and sign
            if (0 === strpos($value, '-', 0)) {
                $result = '-' . ltrim($sign) . substr($value, 1);
            } else {
                $result = $sign . $value;
            }
        }
        return $result;
    }

    /**
     * Get store instance
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore();
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
