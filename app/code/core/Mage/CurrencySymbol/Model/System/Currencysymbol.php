<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CurrencySymbol
 */

/**
 * Custom currency symbol model
 *
 * @package    Mage_CurrencySymbol
 *
 * @method $this resetValues()
 */
class Mage_CurrencySymbol_Model_System_Currencysymbol
{
    /**
     * Custom currency symbol properties
     *
     * @var array
     */
    protected $_symbolsData = [];

    /**
     * Store id
     *
     * @var null|int
     */
    protected $_storeId;

    /**
     * Website id
     *
     * @var null|int
     */
    protected $_websiteId;

    /**
     * Cache types which should be invalidated
     *
     * @var array
     */
    protected $_cacheTypes = [
        'config',
        'block_html',
        'layout',
    ];

    /**
     * Config path to custom currency symbol value
     */
    public const XML_PATH_CUSTOM_CURRENCY_SYMBOL = 'currency/options/customsymbol';

    public const XML_PATH_ALLOWED_CURRENCIES     = 'currency/options/allow';

    /**
     * Separator used in config in allowed currencies list
     */
    public const ALLOWED_CURRENCIES_CONFIG_SEPARATOR = ',';

    /**
     * Config currency section
     */
    public const CONFIG_SECTION = 'currency';

    /**
     * Sets store Id
     *
     * @param  int   $storeId
     * @return $this
     */
    public function setStoreId($storeId = null)
    {
        $this->_storeId = $storeId;
        $this->_symbolsData = [];

        return $this;
    }

    /**
     * Sets website Id
     *
     * @param  int   $websiteId
     * @return $this
     */
    public function setWebsiteId($websiteId = null)
    {
        $this->_websiteId = $websiteId;
        $this->_symbolsData = [];

        return $this;
    }

    /**
     * Returns currency symbol properties array based on config values
     *
     * @return array
     */
    public function getCurrencySymbolsData()
    {
        if ($this->_symbolsData) {
            return $this->_symbolsData;
        }

        $this->_symbolsData = [];

        $allowedCurrencies = explode(
            self::ALLOWED_CURRENCIES_CONFIG_SEPARATOR,
            Mage::getStoreConfig(self::XML_PATH_ALLOWED_CURRENCIES, null),
        );

        $storeModel = Mage::getSingleton('adminhtml/system_store');
        foreach ($storeModel->getWebsiteCollection() as $website) {
            $websiteShow = false;
            foreach ($storeModel->getGroupCollection() as $group) {
                if ($group->getWebsiteId() != $website->getId()) {
                    continue;
                }

                foreach ($storeModel->getStoreCollection() as $store) {
                    if ($store->getGroupId() != $group->getId()) {
                        continue;
                    }

                    if (!$websiteShow) {
                        $websiteShow = true;
                        $websiteSymbols  = $website->getConfig(self::XML_PATH_ALLOWED_CURRENCIES);
                        $allowedCurrencies = array_merge($allowedCurrencies, explode(
                            self::ALLOWED_CURRENCIES_CONFIG_SEPARATOR,
                            $websiteSymbols,
                        ));
                    }

                    $storeSymbols = Mage::getStoreConfig(self::XML_PATH_ALLOWED_CURRENCIES, $store);
                    $allowedCurrencies = array_merge($allowedCurrencies, explode(
                        self::ALLOWED_CURRENCIES_CONFIG_SEPARATOR,
                        $storeSymbols,
                    ));
                }
            }
        }

        ksort($allowedCurrencies);

        $currentSymbols = $this->_unserializeStoreConfig(self::XML_PATH_CUSTOM_CURRENCY_SYMBOL);

        $locale = Mage::app()->getLocale();
        foreach ($allowedCurrencies as $code) {
            if (!$symbol = $locale->getTranslation($code, 'currencysymbol')) {
                $symbol = $code;
            }

            $name = $locale->getTranslation($code, 'nametocurrency');
            if (!$name) {
                $name = $code;
            }

            $this->_symbolsData[$code] = [
                'parentSymbol'  => $symbol,
                'displayName' => $name,
            ];

            if (isset($currentSymbols[$code]) && !empty($currentSymbols[$code])) {
                $this->_symbolsData[$code]['displaySymbol'] = $currentSymbols[$code];
            } else {
                $this->_symbolsData[$code]['displaySymbol'] = $this->_symbolsData[$code]['parentSymbol'];
            }

            if ($this->_symbolsData[$code]['parentSymbol'] == $this->_symbolsData[$code]['displaySymbol']) {
                $this->_symbolsData[$code]['inherited'] = true;
            } else {
                $this->_symbolsData[$code]['inherited'] = false;
            }
        }

        return $this->_symbolsData;
    }

    /**
     * Saves currency symbol to config
     *
     * @param  array $symbols
     * @return $this
     */
    public function setCurrencySymbolsData($symbols = [])
    {
        foreach ($this->getCurrencySymbolsData() as $code => $values) {
            if (isset($symbols[$code])) {
                if ($symbols[$code] == $values['parentSymbol'] || empty($symbols[$code])) {
                    unset($symbols[$code]);
                }
            }
        }

        if ($symbols) {
            $value['options']['fields']['customsymbol']['value'] = serialize($symbols);
        } else {
            $value['options']['fields']['customsymbol']['inherit'] = 1;
        }

        Mage::getModel('adminhtml/config_data')
            ->setSection(self::CONFIG_SECTION)
            ->setWebsite(null)
            ->setStore(null)
            ->setGroups($value)
            ->save();

        Mage::dispatchEvent(
            'admin_system_config_changed_section_currency_before_reinit',
            ['website' => $this->_websiteId, 'store' => $this->_storeId],
        );

        // reinit configuration
        Mage::getConfig()->reinit();
        Mage::app()->reinitStores();

        $this->clearCache();

        Mage::dispatchEvent(
            'admin_system_config_changed_section_currency',
            ['website' => $this->_websiteId, 'store' => $this->_storeId],
        );

        return $this;
    }

    /**
     * Returns custom currency symbol by currency code
     *
     * @param  string       $code
     * @return false|string
     */
    public function getCurrencySymbol($code)
    {
        $customSymbols = $this->_unserializeStoreConfig(self::XML_PATH_CUSTOM_CURRENCY_SYMBOL);
        if (array_key_exists($code, $customSymbols)) {
            return $customSymbols[$code];
        }

        return false;
    }

    /**
     * Clear translate cache
     *
     * @return $this
     */
    public function clearCache()
    {
        // clear cache for frontend
        foreach ($this->_cacheTypes as $cacheType) {
            Mage::app()->getCacheInstance()->invalidateType($cacheType);
        }

        return $this;
    }

    /**
     * Unserialize data from Store Config.
     *
     * @param  string $configPath
     * @param  int    $storeId
     * @return array
     */
    protected function _unserializeStoreConfig($configPath, $storeId = null)
    {
        $result = [];
        $configData = (string) Mage::getStoreConfig($configPath, $storeId);
        if ($configData) {
            try {
                $result = Mage::helper('core/unserializeArray')->unserialize($configData);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        return is_array($result) ? $result : [];
    }
}
