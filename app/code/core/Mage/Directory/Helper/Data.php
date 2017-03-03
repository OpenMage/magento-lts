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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Directory
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Directory data helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Directory_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Config value that lists ISO2 country codes which have optional Zip/Postal pre-configured
     */
    const OPTIONAL_ZIP_COUNTRIES_CONFIG_PATH = 'general/country/optional_zip_countries';

    /*
     * Path to config value, which lists countries, for which state is required.
     */
    const XML_PATH_STATES_REQUIRED = 'general/region/state_required';

    /*
     * Path to config value, which detects whether or not display the state for the country, if it is not required
     */
    const XML_PATH_DISPLAY_ALL_STATES = 'general/region/display_all';

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
    protected $_currencyCache = array();

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

    /**
     * Constructor for Mage_Directory_Helper_Data
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        $this->_factory = !empty($args['factory']) ? $args['factory'] : Mage::getSingleton('core/factory');
        $this->_app = !empty($args['app']) ? $args['app'] : Mage::app();
    }

    /**
     * Retrieve region collection
     *
     * @return Mage_Directory_Model_Resource_Region_Collection
     */
    public function getRegionCollection()
    {
        if (!$this->_regionCollection) {
            $this->_regionCollection = Mage::getModel('directory/region')->getResourceCollection()
                ->addCountryFilter($this->getAddress()->getCountryId())
                ->load();
        }
        return $this->_regionCollection;
    }

    /**
     * Retrieve country collection
     *
     * @return Mage_Directory_Model_Resource_Country_Collection
     */
    public function getCountryCollection()
    {
        if (!$this->_countryCollection) {
            $this->_countryCollection = $this->_factory->getModel('directory/country')->getResourceCollection();
        }
        return $this->_countryCollection;
    }

    /**
     * Retrieve regions data json
     *
     * @deprecated after 1.7.0.2
     * @see Mage_Directory_Helper_Data::getRegionJsonByStore()
     * @return string
     */
    public function getRegionJson()
    {
        return $this->getRegionJsonByStore();
    }

    /**
     * Retrieve regions data json
     *
     * @param int|null $storeId
     * @return array()
     */
    public function getRegionJsonByStore($storeId = null)
    {
        Varien_Profiler::start('TEST: '.__METHOD__);
        if (!$this->_regionJson) {
            $store = $this->_app->getStore($storeId);
            $cacheKey = 'DIRECTORY_REGIONS_JSON_STORE' . (string)$store->getId();
            if ($this->_app->useCache('config')) {
                $json = $this->_app->loadCache($cacheKey);
            }
            if (empty($json)) {
                $regions = $this->_getRegions($storeId);
                $helper = $this->_factory->getHelper('core');
                $json = $helper->jsonEncode($regions);

                if ($this->_app->useCache('config')) {
                    $this->_app->saveCache($json, $cacheKey, array('config'));
                }
            }
            $this->_regionJson = $json;
        }

        Varien_Profiler::stop('TEST: ' . __METHOD__);
        return $this->_regionJson;
    }

    /**
     * Get Regions for specific Countries
     * @param string $storeId
     * @return array|null
     */
    protected function _getRegions($storeId)
    {
        $countryIds = array();

        $countryCollection = $this->getCountryCollection()->loadByStore($storeId);
        foreach ($countryCollection as $country) {
            $countryIds[] = $country->getCountryId();
        }

        /** @var $regionModel Mage_Directory_Model_Region */
        $regionModel = $this->_factory->getModel('directory/region');
        /** @var $collection Mage_Directory_Model_Resource_Region_Collection */
        $collection = $regionModel->getResourceCollection()
            ->addCountryFilter($countryIds)
            ->load();

        $regions = array(
            'config' => array(
                'show_all_regions' => $this->getShowNonRequiredState(),
                'regions_required' => $this->getCountriesWithStatesRequired()
            )
        );
        foreach ($collection as $region) {
            if (!$region->getRegionId()) {
                continue;
            }
            $regions[$region->getCountryId()][$region->getRegionId()] = array(
                'code' => $region->getCode(),
                'name' => $this->__($region->getName())
            );
        }
        return $regions;
    }

    /**
     * Convert currency
     *
     * @param float $amount
     * @param string $from
     * @param string $to
     * @return float
     */
    public function currencyConvert($amount, $from, $to = null)
    {
        if (empty($this->_currencyCache[$from])) {
            $this->_currencyCache[$from] = Mage::getModel('directory/currency')->load($from);
        }
        if (is_null($to)) {
            $to = Mage::app()->getStore()->getCurrentCurrencyCode();
        }
        $converted = $this->_currencyCache[$from]->convert($amount, $to);
        return $converted;
    }

    /**
     * Return ISO2 country codes, which have optional Zip/Postal pre-configured
     *
     * @param bool $asJson
     * @return array|string
     */
    public function getCountriesWithOptionalZip($asJson = false)
    {
        if (null === $this->_optionalZipCountries) {
            $this->_optionalZipCountries = preg_split('/\,/',
                Mage::getStoreConfig(self::OPTIONAL_ZIP_COUNTRIES_CONFIG_PATH), 0, PREG_SPLIT_NO_EMPTY);
        }
        if ($asJson) {
            return Mage::helper('core')->jsonEncode($this->_optionalZipCountries);
        }
        return $this->_optionalZipCountries;
    }

    /**
     * Check whether zip code is optional for specified country code
     *
     * @param string $countryCode
     * @return boolean
     */
    public function isZipCodeOptional($countryCode)
    {
        $this->getCountriesWithOptionalZip();
        return in_array($countryCode, $this->_optionalZipCountries);
    }

    /**
     * Returns the list of countries, for which region is required
     *
     * @param boolean $asJson
     * @return array
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
        return (boolean)Mage::getStoreConfig(self::XML_PATH_DISPLAY_ALL_STATES);
    }

    /**
     * Returns flag, which indicates whether region is required for specified country
     *
     * @param string $countryId
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
}
