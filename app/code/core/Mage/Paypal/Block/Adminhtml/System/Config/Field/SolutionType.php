<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Paypal
 */

/**
 * Field renderer for hidden fields
 *
 * @category   Mage
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Adminhtml_System_Config_Field_SolutionType extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $countryCode = Mage::helper('paypal')->getConfigurationCountryCode();
        if ($countryCode === 'DE') {
            /** @var Mage_Paypal_Block_Adminhtml_System_Config_Field_Hidden $block */
            $block = Mage::getBlockSingleton('paypal/adminhtml_System_config_field_hidden');
            return $block->render($element);
        }

        return parent::render($element);
    }
}
