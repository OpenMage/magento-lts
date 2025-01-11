<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Directory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Currency filter
 *
 * @category   Mage
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
     * @param int $rate
     */
    public function __construct($code, $rate = 1)
    {
        $this->_currency = Mage::app()->getLocale()->currency($code);
        $this->_rate = $rate;
    }

    /**
     * Set filter rate
     *
     * @param double $rate
     */
    public function setRate($rate)
    {
        $this->_rate = $rate;
    }

    /**
     * Filter value
     *
     * @param   double $value
     * @return  string
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
