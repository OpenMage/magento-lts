<?php

/**
 * PayPal payment information block for frontend order view
 */
class Mage_Paypal_Block_Info extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('paypal/info.phtml');
    }

    /**
     * Get transaction ID
     *
     * @return string|null
     */
    public function getTransactionId()
    {
        return $this->getInfo()->getLastTransId();
    }

    /**
     * Get PayPal transaction URL
     *
     * @return string|null
     */
    public function getTransactionUrl()
    {
        // Frontend doesn't show transaction URL
        return null;
    }

    /**
     * Get additional information from payment
     *
     * @return array
     */
    public function getPaymentInfo()
    {
        // Frontend shows minimal information
        return [];
    }
}
