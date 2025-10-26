<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */

/**
 * Permission source model
 *
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
                'label' => Mage::helper('api2')->__('Deny'),
            ],
            [
                'value' => self::TYPE_ALLOW,
                'label' => Mage::helper('api2')->__('Allow'),
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
