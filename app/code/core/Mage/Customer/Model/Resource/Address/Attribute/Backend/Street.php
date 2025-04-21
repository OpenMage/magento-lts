<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Customer password attribute backend
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Resource_Address_Attribute_Backend_Street extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Prepare object for save
     *
     * @param Mage_Customer_Model_Address_Abstract $object
     * @return $this
     */
    public function beforeSave($object)
    {
        $street = $object->getStreet(-1);
        if ($street) {
            $object->implodeStreetAddress();
        }
        return $this;
    }
}
