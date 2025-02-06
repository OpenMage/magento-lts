<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Configurable product associated products in stock filter
 *
 * @category   Mage
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
