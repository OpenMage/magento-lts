<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

/**
 * PayPal payment form block
 */
class Mage_Paypal_Block_Form extends Mage_Payment_Block_Form
{
    /**
     * Initializes the block by setting the payment template.
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->setTemplate('paypal/payment.phtml');
    }

    /**
     * Retrieves the configuration for the PayPal button.
     */
    public function getButtonConfig(): array
    {
        return Mage::helper('paypal')->getButtonConfig();
    }

    /**
     * Retrieves the PayPal API endpoint URL.
     */
    public function getEndpointUrl(): string
    {
        return Mage::helper('paypal')->getConfig()->getEndpoint();
    }

    /**
     * Retrieves the PayPal client ID from the API credentials.
     */
    public function getClientId(): string
    {
        return Mage::helper('paypal')->getConfig()->getApiCredentials()['client_id'];
    }

    /**
     * Constructs and retrieves the full URL for the PayPal JavaScript SDK.
     */
    public function getSdkUrl(): string
    {
        $intent = Mage::getSingleton('paypal/config')->getPaymentAction();

        $params = [
            'client-id' => $this->getClientId(),
            'components' => 'buttons',
            'intent' => (string) $intent,
            'currency' => Mage::app()->getStore()->getCurrentCurrencyCode(),
        ];

        $baseUrl = $this->getEndpointUrl();
        if (!str_ends_with($baseUrl, '/')) {
            $baseUrl .= '/';
        }
        return $baseUrl . 'sdk/js?' . http_build_query($params);
    }

    /**
     * Retrieves the grand total amount from the current quote.
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->getMethod()->getQuote()->getGrandTotal();
    }

    /**
     * Retrieves the currency code for the current store.
     */
    public function getCurrencyCode(): string
    {
        return Mage::app()->getStore()->getCurrentCurrencyCode();
    }
}
