<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Paypal
 */

/**
 * Source model for available logo types
 *
 * @category   Mage
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
