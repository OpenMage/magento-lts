<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Directory
 */

/**
 * Directory Region Api
 *
 * @category   Mage
 * @package    Mage_Directory
 */
class Mage_Directory_Model_Region_Api extends Mage_Api_Model_Resource_Abstract
{
    /**
     * Retrieve regions list
     *
     * @param string $country
     * @return array
     */
    public function items($country)
    {
        try {
            $country = Mage::getModel('directory/country')->loadByCode($country);
        } catch (Mage_Core_Exception $e) {
            $this->_fault('country_not_exists', $e->getMessage());
        }

        if (!$country->getId()) {
            $this->_fault('country_not_exists');
        }

        $result = [];
        foreach ($country->getRegions() as $region) {
            $result[] = [
                'region_id' => $region->getRegionId(),
                'code' => $region->getCode(),
                'name' => $region->getName(), //use the logic of default name
            ];
        }

        return $result;
    }
}
