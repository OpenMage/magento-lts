<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */

/**
 * Privilege of rule source model
 *
 * @package    Mage_Api2
 */
class Mage_Api2_Model_Acl_Global_Rule_Privilege
{
    /**
     * @return array
     */
    public static function toOptionArray()
    {
        return [
            [
                'value' => Mage_Api2_Model_Resource::OPERATION_CREATE,
                'label' => Mage::helper('api2')->__('Create'),
            ],
            [
                'value' => Mage_Api2_Model_Resource::OPERATION_RETRIEVE,
                'label' => Mage::helper('api2')->__('Retrieve'),
            ],
            [
                'value' => Mage_Api2_Model_Resource::OPERATION_UPDATE,
                'label' => Mage::helper('api2')->__('Update'),
            ],
            [
                'value' => Mage_Api2_Model_Resource::OPERATION_DELETE,
                'label' => Mage::helper('api2')->__('Delete'),
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
            Mage_Api2_Model_Resource::OPERATION_CREATE   => Mage::helper('api2')->__('Create'),
            Mage_Api2_Model_Resource::OPERATION_RETRIEVE => Mage::helper('api2')->__('Retrieve'),
            Mage_Api2_Model_Resource::OPERATION_UPDATE   => Mage::helper('api2')->__('Update'),
            Mage_Api2_Model_Resource::OPERATION_DELETE   => Mage::helper('api2')->__('Delete'),
        ];
    }
}
