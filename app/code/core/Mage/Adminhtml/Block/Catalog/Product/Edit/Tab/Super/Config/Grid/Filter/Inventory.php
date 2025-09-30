<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Configurable product associated products in stock filter
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid_Filter_Inventory extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    /**
     * @return array
     */
    protected function _getOptions()
    {
        return [
            [
                'value' =>  '',
                'label' =>  '',
            ],
            [
                'value' =>  1,
                'label' =>  Mage::helper('catalog')->__('In Stock'),
            ],
            [
                'value' =>  0,
                'label' =>  Mage::helper('catalog')->__('Out of Stock'),
            ],
        ];
    }
}
