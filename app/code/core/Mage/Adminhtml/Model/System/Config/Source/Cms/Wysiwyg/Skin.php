<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Configuration source model for Wysiwyg skin
 *
 * @category   Mage
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
