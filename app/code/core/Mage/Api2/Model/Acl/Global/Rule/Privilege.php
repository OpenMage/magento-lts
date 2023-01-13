<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Api2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Privilege of rule source model
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
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
                'label' => Mage::helper('api2')->__('Create')
            ],
            [
                'value' => Mage_Api2_Model_Resource::OPERATION_RETRIEVE,
                'label' => Mage::helper('api2')->__('Retrieve')
            ],
            [
                'value' => Mage_Api2_Model_Resource::OPERATION_UPDATE,
                'label' => Mage::helper('api2')->__('Update')
            ],
            [
                'value' => Mage_Api2_Model_Resource::OPERATION_DELETE,
                'label' => Mage::helper('api2')->__('Delete')
            ]
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
            Mage_Api2_Model_Resource::OPERATION_DELETE   => Mage::helper('api2')->__('Delete')
        ];
    }
}
