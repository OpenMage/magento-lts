<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Configuration source model for Wysiwyg toggling
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Cms_Wysiwyg_Enabled
{
    public function toOptionArray()
    {
        return [
            [
                'value' => Mage_Cms_Model_Wysiwyg_Config::WYSIWYG_ENABLED,
                'label' => Mage::helper('cms')->__('Enabled by Default'),
            ],
            [
                'value' => Mage_Cms_Model_Wysiwyg_Config::WYSIWYG_HIDDEN,
                'label' => Mage::helper('cms')->__('Disabled by Default'),
            ],
            [
                'value' => Mage_Cms_Model_Wysiwyg_Config::WYSIWYG_DISABLED,
                'label' => Mage::helper('cms')->__('Disabled Completely'),
            ],
        ];
    }
}
