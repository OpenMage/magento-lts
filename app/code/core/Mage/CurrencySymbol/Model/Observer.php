<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_CurrencySymbol
 */

/**
 * Currency Symbol Observer
 *
 * @category   Mage
 * @package    Mage_CurrencySymbol
 */
class Mage_CurrencySymbol_Model_Observer
{
    /**
     * Generate options for currency displaying with custom currency symbol
     *
     * @return $this
     */
    public function currencyDisplayOptions(Varien_Event_Observer $observer)
    {
        $baseCode = $observer->getEvent()->getBaseCode();
        $currencyOptions = $observer->getEvent()->getCurrencyOptions();
        $currencyOptions->setData(Mage::helper('currencysymbol')->getCurrencyOptions($baseCode));

        return $this;
    }
}
