<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * PayPal common payment info block
 * Uses default templates
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Payment_Info extends Mage_Payment_Block_Info_Cc
{
    /**
     * Don't show CC type for non-CC methods
     *
     * @return string|null
     */
    public function getCcTypeName()
    {
        if (Mage_Paypal_Model_Config::getIsCreditCardMethod($this->getInfo()->getMethod())) {
            return parent::getCcTypeName();
        }
        return null;
    }

    /**
     * Prepare PayPal-specific payment information
     *
     * @param Varien_Object|array $transport
     * return Varien_Object
     * @return Varien_Object
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        $transport = parent::_prepareSpecificInformation($transport);
        $payment = $this->getInfo();
        $paypalInfo = Mage::getModel('paypal/info');
        if (!$this->getIsSecureMode()) {
            $info = $paypalInfo->getPaymentInfo($payment, true);
        } else {
            $info = $paypalInfo->getPublicPaymentInfo($payment, true);
        }
        return $transport->addData($info);
    }
}
