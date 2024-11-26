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
 * Directory Country Resource Collection
 *
 * @category   Mage
 * @package    Mage_Directory
 *
 * @property Mage_Directory_Model_Country[] $_items
 */
class Mage_Directory_Model_Resource_Country_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('directory/country');
    }

    /**
     * Get Store Config
     *
     * @param string $path
     * @param mixed|null $store
     * @return string
     */
    protected function _getStoreConfig($path, $store = null)
    {
        return Mage::getStoreConfig($path, $store);
    }

    /**
     * Load allowed countries for specific store
     *
     * @param mixed $store
     * @return $this
     */
    public function loadByStore($store = null)
    {
        $allowCountries = explode(',', (string)$this->_getStoreConfig('general/country/allow', $store));
        if (!empty($allowCountries)) {
            $this->addFieldToFilter('country_id', ['in' => $allowCountries]);
        }
        return $this;
    }

    /**
     * Loads Item By Id
     *
     * @param string $countryId
     * @return Mage_Directory_Model_Resource_Country|Mage_Directory_Model_Country
     */
    public function getItemById($countryId)
    {
        foreach ($this->_items as $country) {
            if ($country->getCountryId() == $countryId) {
                return $country;
            }
        }
        return Mage::getResourceModel('directory/country');
    }

    /**
     * Add filter by country code to collection.
     * $countryCode can be either array of country codes or string representing one country code.
     * $iso can be either array containing 'iso2', 'iso3' values or string with containing one of that values directly.
     * The collection will contain countries where at least one of country $iso fields matches $countryCode.
     *
     * @param string|array $countryCode
     * @param string|array $iso
     * @return $this
     */
    public function addCountryCodeFilter($countryCode, $iso = ['iso3', 'iso2'])
    {
        if (!empty($countryCode)) {
            if (is_array($countryCode)) {
                if (is_array($iso)) {
                    $whereOr = [];
                    foreach ($iso as $isoType) {
                        $whereOr[] = $this->_getConditionSql("{$isoType}_code", ['in' => $countryCode]);
                    }
                    $this->_select->where('(' . implode(') OR (', $whereOr) . ')');
                } else {
                    $this->addFieldToFilter("{$iso}_code", ['in' => $countryCode]);
                }
            } else {
                if (is_array($iso)) {
                    $whereOr = [];
                    foreach ($iso as $isoType) {
                        $whereOr[] = $this->_getConditionSql("{$isoType}_code", $countryCode);
                    }
                    $this->_select->where('(' . implode(') OR (', $whereOr) . ')');
                } else {
                    $this->addFieldToFilter("{$iso}_code", $countryCode);
                }
            }
        }
        return $this;
    }

    /**
     * Add filter by country code(s) to collection
     *
     * @param string|array $countryId
     * @return $this
     */
    public function addCountryIdFilter($countryId)
    {
        if (!empty($countryId)) {
            if (is_array($countryId)) {
                $this->addFieldToFilter('country_id', ['in' => $countryId]);
            } else {
                $this->addFieldToFilter('country_id', $countryId);
            }
        }
        return $this;
    }

    /**
     * Convert collection items to select options array
     *
     * @param string $emptyLabel
     * @return array
     */
    public function toOptionArray($emptyLabel = ' ')
    {
        $options = $this->_toOptionArray('country_id', 'name', ['title' => 'iso2_code']);

        $sort = [];
        foreach ($options as $data) {
            $name = Mage::app()->getLocale()->getCountryTranslation($data['value']);
            if (!empty($name)) {
                $sort[$name] = $data['value'];
            }
        }

        Mage::helper('core/string')->ksortMultibyte($sort);
        $options = [];
        foreach ($sort as $label => $value) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }

        if (count($options) > 0 && $emptyLabel !== false) {
            array_unshift($options, ['value' => '', 'label' => $emptyLabel]);
        }

        return $options;
    }
}
