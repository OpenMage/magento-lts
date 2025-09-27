<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Captcha
 */

/**
 * Captcha image model
 *
 * @package    Mage_Captcha
 */
class Mage_Captcha_Model_Config_Mode
{
    /**
     * Get options for captcha mode selection field
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => Mage::helper('captcha')->__('Always'),
                'value' => Mage_Captcha_Helper_Data::MODE_ALWAYS,
            ],
            [
                'label' => Mage::helper('captcha')->__('After number of attempts to login'),
                'value' => Mage_Captcha_Helper_Data::MODE_AFTER_FAIL,
            ],
        ];
    }
}
