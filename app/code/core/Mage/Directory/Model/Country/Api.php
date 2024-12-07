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
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Directory Country Api
 *
 * @category   Mage
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
