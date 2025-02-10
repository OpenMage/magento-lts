<?php
/**
 * Source model for available logo types
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Paypal
 */
class Mage_Paypal_Model_System_Config_Source_Logo
{
    public function toOptionArray()
    {
        $result = ['' => Mage::helper('paypal')->__('No Logo')];
        return $result + Mage::getModel('paypal/config')->getAdditionalOptionsLogoTypes();
    }
}
