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

    public function setLocale($locale = null)
    {
        $this->_locale = $locale;
        parent::setLocale($locale);
        return $this;
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
        //return parent::toCurrency($value, $options);
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

        $trimSettings = $this->getStore()->getConfig(self::XML_PATH_TRIM_CURRENCY_SIGN);
        if (is_null($trimSettings) && $this->_locale && $this->_locale->toString() == self::US_LOCALE) {
            $trimSettings = true;
        }
        if ($trimSettings) {
        	$sign = trim($sign);
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