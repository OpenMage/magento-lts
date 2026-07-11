<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Payment_Allowedmethods extends Mage_Adminhtml_Model_System_Config_Source_Payment_Allmethods
{
    protected function _getPaymentMethods()
    {
        return Mage::getSingleton('payment/config')->getActiveMethods();
    }
}
