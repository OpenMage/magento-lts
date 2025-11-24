<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
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
