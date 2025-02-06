<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Paypal
 */

/**
 * Source model for Require Billing Address
 *
 * @category   Mage
 * @package    Mage_Paypal
 */
class Mage_Paypal_Model_System_Config_Source_RequireBillingAddress
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var Mage_Paypal_Model_Config $configModel */
        $configModel = Mage::getModel('paypal/config');
        return $configModel->getRequireBillingAddressOptions();
    }
}
