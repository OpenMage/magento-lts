<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Field renderer for hidden fields
 *
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
