<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Paypal
 */

/**
 * Payflow link iframe block
 *
 * @category   Mage
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Payflow_Link_Iframe extends Mage_Paypal_Block_Iframe
{
    /**
     * Set payment method code
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_paymentMethodCode = Mage_Paypal_Model_Config::METHOD_PAYFLOWLINK;
    }

    /**
     * Get frame action URL
     *
     * @return string
     */
    public function getFrameActionUrl()
    {
        return $this->getTransactionUrl() . '?SECURETOKEN=' . $this->getSecureToken() . '&SECURETOKENID='
            . $this->getSecureTokenId() . '&MODE=' . ($this->isTestMode() ? 'TEST' : 'LIVE');
    }

    /**
     * Get secure token
     *
     * @return string
     */
    public function getSecureToken()
    {
        return $this->_getOrder()
            ->getPayment()
            ->getAdditionalInformation('secure_token');
    }

    /**
     * Get secure token ID
     *
     * @return string
     */
    public function getSecureTokenId()
    {
        return $this->_getOrder()
            ->getPayment()
            ->getAdditionalInformation('secure_token_id');
    }

    /**
     * Get payflow transaction URL
     *
     * @return string
     */
    public function getTransactionUrl()
    {
        return Mage_Paypal_Model_Payflowlink::TRANSACTION_PAYFLOW_URL;
    }

    /**
     * Check sandbox mode
     *
     * @return bool
     */
    public function isTestMode()
    {
        $mode = Mage::helper('payment')
            ->getMethodInstance($this->_paymentMethodCode)
            ->getConfigData('sandbox_flag');
        return (bool) $mode;
    }
}
