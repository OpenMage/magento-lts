<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Source model for Require Billing Address
 *
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
