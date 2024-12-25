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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Watermark position config source model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Watermark_Position
{
    /**
     * Get available options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'stretch',         'label' => Mage::helper('catalog')->__('Stretch')],
            ['value' => 'tile',            'label' => Mage::helper('catalog')->__('Tile')],
            ['value' => 'top-left',        'label' => Mage::helper('catalog')->__('Top/Left')],
            ['value' => 'top-right',       'label' => Mage::helper('catalog')->__('Top/Right')],
            ['value' => 'bottom-left',     'label' => Mage::helper('catalog')->__('Bottom/Left')],
            ['value' => 'bottom-right',    'label' => Mage::helper('catalog')->__('Bottom/Right')],
            ['value' => 'center',          'label' => Mage::helper('catalog')->__('Center')],
        ];
    }
}
