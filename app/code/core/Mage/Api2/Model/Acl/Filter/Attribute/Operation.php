<?php

/**
 * @category   Mage
 * @package    Mage_Api2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
