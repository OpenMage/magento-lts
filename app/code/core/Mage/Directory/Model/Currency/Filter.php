<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Directory
 */

/**
 * Currency filter
 *
 * @package    Mage_Directory
 */
class Mage_Directory_Model_Currency_Filter implements Zend_Filter_Interface
{
    /**
     * Rate value
     *
     * @var float
     */
    protected $_rate;

    /**
     * Currency object
     *
     * @var Zend_Currency
     */
    protected $_currency;

    /**
     * Mage_Directory_Model_Currency_Filter constructor.
     * @param string $code
     * @param int    $rate
     */
    public function __construct($code, $rate = 1)
    {
        $this->_currency = Mage::app()->getLocale()->currency($code);
        $this->_rate = $rate;
    }

    /**
     * Set filter rate
     *
     * @param float $rate
     */
    public function setRate($rate)
    {
        $this->_rate = $rate;
    }

    /**
     * Filter value
     *
     * @param  float  $value
     * @return string
     */
    public function filter($value)
    {
        $value = Mage::app()->getLocale()->getNumber($value);
        $value = Mage::app()->getStore()->roundPrice($this->_rate * $value);
        //$value = round($value, 2);
        $value = sprintf('%F', $value);
        return $this->_currency->toCurrency($value);
    }
}
