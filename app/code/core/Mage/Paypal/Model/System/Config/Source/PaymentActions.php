<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Paypal
 */

/**
 * Source model for available payment actions
 *
 * @category   Mage
 * @package    Mage_Paypal
 */
class Mage_Paypal_Model_System_Config_Source_PaymentActions
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $configModel = Mage::getModel('paypal/config');
        return $configModel->getPaymentActions();
    }
}
