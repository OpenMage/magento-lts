<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CurrencySymbol
 */

/**
 * Currency Symbol Observer
 *
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
