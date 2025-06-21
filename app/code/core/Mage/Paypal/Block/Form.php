<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

use PaypalServerSdkLib\Models\CheckoutPaymentIntent;

/**
 * PayPal payment form block
 */
class Mage_Paypal_Block_Form extends Mage_Payment_Block_Form
{
    /**
     * Set template and init PayPal config
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('paypal/payment.phtml');
    }

    /**
     * Get PayPal button configuration
     *
     * @return array
     */
    public function getButtonConfig()
    {
        return Mage::helper('paypal')->getButtonConfig();
    }

    /**
     * Get PayPal endpoint URL
     *
     * @return string
     */
    public function getEndpointUrl()
    {
        return Mage::helper('paypal')->getConfig()->getEndpoint();
    }

    /**
     * Get PayPal client ID
     *
     * @return string
     */
    public function getClientId()
    {
        return Mage::helper('paypal')->getConfig()->getApiCredentials()['client_id'];
    }

    /**
     * Get PayPal SDK URL
     *
     * @return string
     */
    public function getSdkUrl()
    {
        $intent = Mage::getSingleton('paypal/config')->getPaymentAction();

        $params = [
            'client-id' => $this->getClientId(),
            'components' => 'buttons',
            'intent' => $intent,
            'currency' => Mage::app()->getStore()->getCurrentCurrencyCode(),
        ];

        $baseUrl = $this->getEndpointUrl();
        if (substr($baseUrl, -1) !== '/') {
            $baseUrl .= '/';
        }
        $url = $baseUrl . 'sdk/js?' . http_build_query($params);
        return $url;
    }

    /**
     * Get order amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->getMethod()->getQuote()->getGrandTotal();
    }

    /**
     * Get currency code
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        return Mage::app()->getStore()->getCurrentCurrencyCode();
    }
}
