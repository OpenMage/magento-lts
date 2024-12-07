<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Api2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Permission source model
 *
 * @category   Mage
 * @package    Mage_Api2
 */
class Mage_Api2_Model_Acl_Global_Rule_Permission
{
    /**
     * Source keys
     */
    public const TYPE_ALLOW = 1;
    public const TYPE_DENY  = 0;

    /**
     * Get options parameters
     *
     * @return array
     */
    public static function toOptionArray()
    {
        return [
            [
                'value' => self::TYPE_DENY,
                'label' => Mage::helper('api2')->__('Deny')
            ],
            [
                'value' => self::TYPE_ALLOW,
                'label' => Mage::helper('api2')->__('Allow')
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
            self::TYPE_DENY  => Mage::helper('api2')->__('Deny'),
            self::TYPE_ALLOW => Mage::helper('api2')->__('Allow'),
        ];
    }
}
