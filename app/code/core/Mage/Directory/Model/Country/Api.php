<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Directory
 */

/**
 * Directory Country Api
 *
 * @package    Mage_Directory
 */
class Mage_Directory_Model_Country_Api extends Mage_Api_Model_Resource_Abstract
{
    /**
     * Retrieve countries list
     *
     * @return array
     */
    public function items()
    {
        $collection = Mage::getModel('directory/country')->getCollection();

        $result = [];
        foreach ($collection as $country) {
            /** @var Mage_Directory_Model_Country $country */
            $country->getName(); // Loading name in default locale
            $result[] = $country->toArray(['country_id', 'iso2_code', 'iso3_code', 'name']);
        }

        return $result;
    }
}
