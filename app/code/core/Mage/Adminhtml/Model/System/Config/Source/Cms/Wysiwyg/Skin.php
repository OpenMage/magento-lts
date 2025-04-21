<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Configuration source model for Wysiwyg skin
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Cms_Wysiwyg_Skin
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'oxide',
                'label' => 'Oxide',
            ],
            [
                'value' => 'oxide-dark',
                'label' => 'Oxide Dark',
            ],
            [
                'value' => 'tinymce-5',
                'label' => 'Tinymce 5',
            ],
            [
                'value' => 'tinymce-5-dark',
                'label' => 'Tinymce 5 Dark',
            ],
        ];
    }
}
