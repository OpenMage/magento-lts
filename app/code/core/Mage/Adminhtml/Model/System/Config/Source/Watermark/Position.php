<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
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
