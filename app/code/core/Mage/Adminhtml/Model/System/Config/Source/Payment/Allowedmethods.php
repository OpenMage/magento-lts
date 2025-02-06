<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Payment_Allowedmethods extends Mage_Adminhtml_Model_System_Config_Source_Payment_Allmethods
{
    protected function _getPaymentMethods()
    {
        return Mage::getSingleton('payment/config')->getActiveMethods();
    }
}
