<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

class Mage_Paypal_Block_Adminhtml_System_Config_Field_Country extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $countries = Mage::getSingleton('adminhtml/system_config_source_country')
            ->toOptionArray(false);

        $element->setValues($countries)
            ->setClass('paypal-merchant-country');

        return parent::_getElementHtml($element);
    }
}
