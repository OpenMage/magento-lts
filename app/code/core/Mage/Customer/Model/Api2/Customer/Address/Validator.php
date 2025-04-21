<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * API2 class for customer address rest
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Api2_Customer_Address_Validator extends Mage_Api2_Model_Resource_Validator_Eav
{
    /**
     * Separator for multistreet
     */
    public const STREET_SEPARATOR = '; ';

    /**
     * Filter request data.
     *
     * @return array Filtered data
     */
    public function filter(array $data)
    {
        $filteredData = parent::filter($data);

        // If the array contains more than two elements, then combine the extra elements in a string
        if (isset($filteredData['street']) && is_array($filteredData['street']) && count($filteredData['street']) > 2) {
            $filteredData['street'][1] .= self::STREET_SEPARATOR
                . implode(self::STREET_SEPARATOR, array_slice($filteredData['street'], 2));
            $filteredData['street'] = array_slice($filteredData['street'], 0, 2);
        }
        // pass default addresses info
        if (isset($data['is_default_billing'])) {
            $filteredData['is_default_billing'] = $data['is_default_billing'];
        }
        if (isset($data['is_default_shipping'])) {
            $filteredData['is_default_shipping'] = $data['is_default_shipping'];
        }
        return $filteredData;
    }

    /**
     * Validate data for create association with the country
     *
     * @return bool
     */
    public function isValidDataForCreateAssociationWithCountry(array $data)
    {
        return $this->_checkRegion($data, Mage::getModel('directory/country')->loadByCode($data['country_id']));
    }

    /**
     * Validate data for change association with the country
     *
     * @return bool
     */
    public function isValidDataForChangeAssociationWithCountry(Mage_Customer_Model_Address $address, array $data)
    {
        if (!isset($data['country_id']) && !isset($data['region'])) {
            return true;
        }
        // If country is in data - it has been already validated. If no - load current country.
        if (isset($data['country_id'])) {
            $country = Mage::getModel('directory/country')->loadByCode($data['country_id']);
        } else {
            $country = $address->getCountryModel();
        }
        return $this->_checkRegion($data, $country);
    }

    /**
     * Check region
     *
     * @param array $data
     * @return bool
     */
    protected function _checkRegion($data, Mage_Directory_Model_Country $country)
    {
        $regions = $country->getRegions();
        // Is it the country with predefined regions?
        if ($regions->count()) {
            if (!array_key_exists('region', $data) || empty($data['region'])) {
                $this->_addError('"State/Province" is required.');
                return false;
            }

            if (!is_string($data['region'])) {
                $this->_addError('Invalid "State/Province" type.');
                return false;
            }

            $count = $regions->addFieldToFilter(['default_name', 'code'], [$data['region'], $data['region']])
                ->clear()
                ->count();
            if (!$count) {
                $this->_addError('State/Province does not exist.');
                return false;
            }
        } else {
            if (array_key_exists('region', $data) && !is_string($data['region'])) {
                $this->_addError('Invalid "State/Province" type.');
                return false;
            }
        }

        return true;
    }
}
