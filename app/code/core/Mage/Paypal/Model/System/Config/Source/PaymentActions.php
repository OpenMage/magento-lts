<?php

/**
 * @category   Mage
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
