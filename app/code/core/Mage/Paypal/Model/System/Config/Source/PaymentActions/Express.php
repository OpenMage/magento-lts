<?php
/**
 * Source model for available paypal express payment actions
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Paypal
 */
class Mage_Paypal_Model_System_Config_Source_PaymentActions_Express
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $configModel = Mage::getModel('paypal/config');
        $configModel->setMethod(Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS);
        return $configModel->getPaymentActions();
    }
}
