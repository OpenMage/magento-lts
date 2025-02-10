<?php
/**
 * Currency Symbol Observer
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
