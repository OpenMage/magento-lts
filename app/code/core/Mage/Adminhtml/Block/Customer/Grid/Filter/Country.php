<?php
/**
 * Country customer grid column filter
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
