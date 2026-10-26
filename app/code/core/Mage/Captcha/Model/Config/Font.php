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
class Mage_Captcha_Model_Config_Font
{
    /**
     * Get options for font selection field
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        foreach (Mage::helper('captcha')->getFonts() as $fontName => $fontData) {
            $optionArray[] = ['label' => $fontData['label'], 'value' => $fontName];
        }

        return $optionArray;
    }
}
