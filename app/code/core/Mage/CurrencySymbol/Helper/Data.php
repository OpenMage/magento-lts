<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_CurrencySymbol
 */

/**
 * Currency Symbol helper
 *
 * @category   Mage
 * @package    Mage_CurrencySymbol
 */
class Mage_CurrencySymbol_Helper_Data extends Mage_Core_Helper_Data
{
    protected $_moduleName = 'Mage_CurrencySymbol';

    /**
     * Get currency display options
     *
     * @param string $baseCode
     * @return array
     */
    public function getCurrencyOptions($baseCode)
    {
        $currencyOptions = [];
        $currencySymbol = Mage::getModel('currencysymbol/system_currencysymbol');
        if ($currencySymbol) {
            $customCurrencySymbol = $currencySymbol->getCurrencySymbol($baseCode);

            if ($customCurrencySymbol) {
                $currencyOptions['symbol']  = $customCurrencySymbol;
                $currencyOptions['display'] = Zend_Currency::USE_SYMBOL;
            }
        }

        return $currencyOptions;
    }
}
