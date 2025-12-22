<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Directory
 */

/**
 * Currency dropdown block
 *
 * @package    Mage_Directory
 */
class Mage_Directory_Block_Currency extends Mage_Core_Block_Template
{
    /**
     * Retrieve count of currencies
     * Return 0 if only one currency
     *
     * @return int
     */
    public function getCurrencyCount()
    {
        return count($this->getCurrencies());
    }

    /**
     * Retrieve currencies array
     * Return array: code => currency name
     * Return empty array if only one currency
     *
     * @return array
     */
    public function getCurrencies()
    {
        $currencies = $this->getData('currencies');
        if (is_null($currencies)) {
            $currencies = [];
            $codes = Mage::app()->getStore()->getAvailableCurrencyCodes(true);
            if (is_array($codes) && count($codes) > 1) {
                $rates = Mage::getModel('directory/currency')->getCurrencyRates(
                    Mage::app()->getStore()->getBaseCurrency(),
                    $codes,
                );

                foreach ($codes as $code) {
                    if (isset($rates[$code])) {
                        $currencies[$code] = Mage::app()->getLocale()
                            ->getTranslation($code, 'nametocurrency');
                    }
                }
            }

            $this->setData('currencies', $currencies);
        }

        return $currencies;
    }

    /**
     * Retrieve Currency Switch URL
     *
     * @return string
     */
    public function getSwitchUrl()
    {
        return $this->getUrl('directory/currency/switch');
    }

    /**
     * Return URL for specified currency to switch
     *
     * @param  string $code Currency code
     * @return string
     */
    public function getSwitchCurrencyUrl($code)
    {
        return Mage::helper('directory/url')->getSwitchCurrencyUrl(['currency' => $code]);
    }

    /**
     * Retrieve Current Currency code
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        if (is_null($this->_getData('current_currency_code'))) {
            // do not use Mage::app()->getStore()->getCurrentCurrencyCode() because of probability
            // to get an invalid (without base rate) currency from code saved in session
            $this->setData('current_currency_code', Mage::app()->getStore()->getCurrentCurrency()->getCode());
        }

        return $this->_getData('current_currency_code');
    }
}
