<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
