<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Country grid filter
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Country extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    protected function _getOptions()
    {
        $options = Mage::getResourceModel('directory/country_collection')->load()->toOptionArray(false);
        array_unshift($options, ['value' => '', 'label' => Mage::helper('cms')->__('All Countries')]);
        return $options;
    }
}
