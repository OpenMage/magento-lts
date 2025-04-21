<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Source model for available logo types
 *
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
