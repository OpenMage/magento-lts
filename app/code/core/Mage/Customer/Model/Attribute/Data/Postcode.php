<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Customer
 */

/**
 * Customer Address Postal/Zip Code Attribute Data Model
 * This Data Model Has to Be Set Up in additional EAV attribute table
 *
 * @category   Mage
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Attribute_Data_Postcode extends Mage_Eav_Model_Attribute_Data_Text
{
    /**
     * Validate postal/zip code
     * Return true and skip validation if country zip code is optional
     *
     * @param array|string $value
     * @return bool|array
     */
    public function validateValue($value)
    {
        $countryId      = $this->getExtractedData('country_id');
        $optionalZip    = Mage::helper('directory')->getCountriesWithOptionalZip();
        if (!in_array($countryId, $optionalZip)) {
            return parent::validateValue($value);
        }
        return true;
    }
}
