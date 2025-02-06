<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Allregion
{
    protected $_countries;
    protected $_options;

    public function toOptionArray($isMultiselect = false)
    {
        if (!$this->_options) {
            $countriesArray = Mage::getResourceModel('directory/country_collection')->load()
                ->toOptionArray(false);
            $this->_countries = [];
            foreach ($countriesArray as $a) {
                $this->_countries[$a['value']] = $a['label'];
            }

            $countryRegions = [];
            $regionsCollection = Mage::getResourceModel('directory/region_collection')->load();
            foreach ($regionsCollection as $region) {
                $countryRegions[$region->getCountryId()][$region->getId()] = $region->getDefaultName();
            }
            uksort($countryRegions, [$this, 'sortRegionCountries']);

            $this->_options = [];
            foreach ($countryRegions as $countryId => $regions) {
                $regionOptions = [];
                foreach ($regions as $regionId => $regionName) {
                    $regionOptions[] = ['label' => $regionName, 'value' => $regionId];
                }
                $this->_options[] = ['label' => $this->_countries[$countryId], 'value' => $regionOptions];
            }
        }
        $options = $this->_options;
        if (!$isMultiselect) {
            array_unshift($options, ['value' => '', 'label' => '']);
        }

        return $options;
    }

    public function sortRegionCountries($a, $b)
    {
        return strcmp($this->_countries[$a], $this->_countries[$b]);
    }
}
