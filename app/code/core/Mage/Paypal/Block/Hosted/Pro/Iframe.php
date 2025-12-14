<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Hosted Pro iframe block
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Hosted_Pro_Iframe extends Mage_Paypal_Block_Iframe
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_paymentMethodCode = Mage_Paypal_Model_Config::METHOD_HOSTEDPRO;
    }

    /**
     * Get iframe action URL
     * @return string
     */
    public function getFrameActionUrl()
    {
        return $this->_getOrder()
            ->getPayment()
            ->getAdditionalInformation('secure_form_url');
    }
}
