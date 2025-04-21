<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Watermark position config source model
 *
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
