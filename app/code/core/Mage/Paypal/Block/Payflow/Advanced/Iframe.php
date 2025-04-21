<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Payflow Advanced iframe block
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Payflow_Advanced_Iframe extends Mage_Paypal_Block_Payflow_Link_Iframe
{
    /**
     * Set payment method code
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_paymentMethodCode = Mage_Paypal_Model_Config::METHOD_PAYFLOWADVANCED;
    }

    /**
     * Get frame action URL
     * @return string
     */
    public function getFrameActionUrl()
    {
        return $this->getTransactionUrl() . '?SECURETOKEN=' . $this->getSecureToken() . '&SECURETOKENID='
            . $this->getSecureTokenId() . '&MODE=' . ($this->isTestMode() ? 'TEST' : 'LIVE');
    }

    /**
     * Check sandbox mode
     *
     * @return bool
     */
    public function isTestMode()
    {
        $mode = Mage::helper('payment')
            ->getMethodInstance(Mage_Paypal_Model_Config::METHOD_PAYFLOWADVANCED)
            ->getConfigData('sandbox_flag');
        return (bool) $mode;
    }
}
