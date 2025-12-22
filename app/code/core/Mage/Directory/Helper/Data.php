<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Directory
 */

/**
 * Directory data helper
 *
 * @package    Mage_Directory
 */
class Mage_Directory_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Config value that lists ISO2 country codes which have optional Zip/Postal pre-configured
     */
    public const OPTIONAL_ZIP_COUNTRIES_CONFIG_PATH = 'general/country/optional_zip_countries';

    /**
     * Path to config value, which lists countries, for which state is required.
     */
    public const XML_PATH_STATES_REQUIRED = 'general/region/state_required';

    /**
     * Path to config value, which detects whether or not display the state for the country, if it is not required
     */
    public const XML_PATH_DISPLAY_ALL_STATES = 'general/region/display_all';

    protected $_moduleName = 'Mage_Directory';

    /**
     * Country collection
     *
     * @var Mage_Directory_Model_Resource_Country_Collection
     */
    protected $_countryCollection;

    /**
     * Region collection
     *
     * @var Mage_Directory_Model_Resource_Region_Collection
     */
    protected $_regionCollection;

    /**
     * Json representation of regions data
     *
     * @var string
     */
    protected $_regionJson;

    /**
     * Currency cache
     *
     * @var array
     */
    protected $_currencyCache = [];

    /**
     * ISO2 country codes which have optional Zip/Postal pre-configured
     *
     * @var array
     */
    protected $_optionalZipCountries = null;

    /**
     * Factory instance
     *
     * @var Mage_Core_Model_Factory
     */
    protected $_factory;

    /**
     * Application instance
     *
     * @var Mage_Core_Model_App
     */
    protected $_app;

    public function __construct(array $args = [])
    {
        $this->_factory = empty($args['factory']) ? Mage::getSingleton('core/factory') : $args['factory'];
        $this->_app = empty($args['app']) ? Mage::app() : $args['app'];
    }

    /**
     * Retrieve region collection
     * @param  null|array|string                               $countryFilter if string, accepts iso2_code; if array, accepts iso2_code[]
     * @return Mage_Directory_Model_Resource_Region_Collection
     * @throws Mage_Core_Exception
     */
    public function getRegionCollection($countryFilter = null)
    {
        if (!$this->_regionCollection) {
            $this->_regionCollection = Mage::getModel('directory/region')->getResourceCollection()
                ->addCountryFilter($countryFilter)
                ->load();
        }

        return $this->_regionCollection;
    }

    /**
     * Retrieve country collection
     *
     * @return Mage_Directory_Model_Resource_Country_Collection
     * @throws Mage_Core_Exception
     */
    public function getCountryCollection()
    {
        if (!$this->_countryCollection) {
            /** @var Mage_Directory_Model_Country $model */
            $model = $this->_factory->getModel('directory/country');
            $this->_countryCollection = $model->getResourceCollection();
        }

        return $this->_countryCollection;
    }

    /**
     * Retrieve regions data json
     *
     * @return string
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     * @deprecated after 1.7.0.2
     * @see Mage_Directory_Helper_Data::getRegionJsonByStore()
     */
    public function getRegionJson()
    {
        return $this->getRegionJsonByStore();
    }

    /**
     * Retrieve regions data json
     *
     * @param  null|int                        $storeId
     * @return string
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getRegionJsonByStore($storeId = null)
    {
        Varien_Profiler::start('TEST: ' . __METHOD__);
        if (!$this->_regionJson) {
            $store = $this->_app->getStore($storeId);
            $cacheKey = 'DIRECTORY_REGIONS_JSON_STORE' . $store->getId();
            if ($this->_app->useCache('config')) {
                $json = $this->_app->loadCache($cacheKey);
            }

            if (empty($json)) {
                $regions = $this->_getRegions($storeId);
                /** @var Mage_Core_Helper_Data $helper */
                $helper = $this->_factory->getHelper('core');
                $json = $helper->jsonEncode($regions);

                if ($this->_app->useCache('config')) {
                    $this->_app->saveCache($json, $cacheKey, ['config']);
                }
            }

            $this->_regionJson = $json;
        }

        Varien_Profiler::stop('TEST: ' . __METHOD__);
        return $this->_regionJson;
    }

    /**
     * Get Regions for specific Countries
     * @param  null|int|string     $storeId
     * @return null|array
     * @throws Mage_Core_Exception
     */
    protected function _getRegions($storeId)
    {
        $countryIds = [];

        $countryCollection = $this->getCountryCollection()->loadByStore($storeId);
        /** @var Mage_Directory_Model_Country $country */
        foreach ($countryCollection as $country) {
            $countryIds[] = $country->getCountryId();
        }

        /** @var Mage_Directory_Model_Region $regionModel */
        $regionModel = $this->_factory->getModel('directory/region');
        $collection = $regionModel->getResourceCollection()
            ->addCountryFilter($countryIds)
            ->load();

        $regions = [
            'config' => [
                'show_all_regions' => $this->getShowNonRequiredState(),
                'regions_required' => $this->getCountriesWithStatesRequired(),
            ],
        ];
        foreach ($collection as $region) {
            if (!$region->getRegionId()) {
                continue;
            }

            $regions[$region->getCountryId()][$region->getRegionId()] = [
                'code' => $region->getCode(),
                'name' => $this->__($region->getName()),
            ];
        }

        return $regions;
    }

    /**
     * Convert currency
     *
     * @param  float                           $amount
     * @param  string                          $from
     * @param  string                          $to
     * @return float
     * @throws Mage_Core_Model_Store_Exception
     */
    public function currencyConvert($amount, $from, $to = null)
    {
        if (empty($this->_currencyCache[$from])) {
            $this->_currencyCache[$from] = Mage::getModel('directory/currency')->load($from);
        }

        if (is_null($to)) {
            $to = Mage::app()->getStore()->getCurrentCurrencyCode();
        }

        return $this->_currencyCache[$from]->convert($amount, $to);
    }

    /**
     * Return ISO2 country codes, which have optional Zip/Postal pre-configured
     *
     * @param  bool         $asJson
     * @return array|string
     */
    public function getCountriesWithOptionalZip($asJson = false)
    {
        if ($this->_optionalZipCountries === null) {
            $this->_optionalZipCountries = preg_split(
                '/\,/',
                Mage::getStoreConfig(self::OPTIONAL_ZIP_COUNTRIES_CONFIG_PATH),
                0,
                PREG_SPLIT_NO_EMPTY,
            );
        }

        if ($asJson) {
            return Mage::helper('core')->jsonEncode($this->_optionalZipCountries);
        }

        return $this->_optionalZipCountries;
    }

    /**
     * Check whether zip code is optional for specified country code
     *
     * @param  string $countryCode
     * @return bool
     */
    public function isZipCodeOptional($countryCode)
    {
        $this->getCountriesWithOptionalZip();
        return in_array($countryCode, $this->_optionalZipCountries);
    }

    /**
     * Returns the list of countries, for which region is required
     *
     * @param  bool         $asJson
     * @return array|string
     */
    public function getCountriesWithStatesRequired($asJson = false)
    {
        $countryList = explode(',', Mage::getStoreConfig(self::XML_PATH_STATES_REQUIRED));
        if ($asJson) {
            return Mage::helper('core')->jsonEncode($countryList);
        }

        return $countryList;
    }

    /**
     * Return flag, which indicates whether or not non required state should be shown
     *
     * @return bool
     */
    public function getShowNonRequiredState()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_DISPLAY_ALL_STATES);
    }

    /**
     * Returns flag, which indicates whether region is required for specified country
     *
     * @param  string $countryId
     * @return bool
     */
    public function isRegionRequired($countryId)
    {
        $countyList = $this->getCountriesWithStatesRequired();
        if (!is_array($countyList)) {
            return false;
        }

        return in_array($countryId, $countyList);
    }

    public static function getConfigCurrencyBase(): string
    {
        return (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);
    }

    /** @return list<string> */
    public function getTopCountryCodes(): array
    {
        $topCountries = array_filter(explode(',', (string) Mage::getStoreConfig('general/country/top_countries')));

        $transportObject = new Varien_Object();
        $transportObject->setData('top_countries', $topCountries);
        Mage::dispatchEvent('directory_get_top_countries', ['topCountries' => $transportObject]);

        return $transportObject->getData('top_countries');
    }
}
