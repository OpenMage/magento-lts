<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Api2
 */

/**
 * Operation source model
 *
 * @category   Mage
 * @package    Mage_Api2
 */
class Mage_Api2_Model_Acl_Filter_Attribute_Operation
{
    /**
     * Get options paramets
     *
     * @return array
     */
    public static function toOptionArray()
    {
        return [
            [
                'value' => Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ,
                'label' => Mage::helper('api2')->__('Read'),
            ],
            [
                'value' => Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_WRITE,
                'label' => Mage::helper('api2')->__('Write'),
            ],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public static function toArray()
    {
        return [
            Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ  => Mage::helper('api2')->__('Read'),
            Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_WRITE => Mage::helper('api2')->__('Write'),
        ];
    }
}
