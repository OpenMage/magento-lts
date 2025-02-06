<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Captcha
 */

/**
 * Captcha image model
 *
 * @category   Mage
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
