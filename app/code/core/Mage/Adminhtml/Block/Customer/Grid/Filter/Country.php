<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Country customer grid column filter
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Grid_Filter_Country extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    protected function _getOptions()
    {
        $options = Mage::getResourceModel('directory/country_collection')->load()->toOptionArray();
        array_unshift($options, ['value' => '', 'label' => Mage::helper('customer')->__('All countries')]);
        return $options;
    }
}
