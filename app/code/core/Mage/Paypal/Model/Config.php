<?php

class Mage_Paypal_Model_Config extends Varien_Object
{
    const BUTTON_SHAPE_RECT = 'rect';
    const BUTTON_SHAPE_PILL = 'pill';
    const BUTTON_SHAPE_SHARP = 'sharp';

    const BUTTON_COLOR_GOLD = 'gold';
    const BUTTON_COLOR_BLUE = 'blue';
    const BUTTON_COLOR_SILVER = 'silver';
    const BUTTON_COLOR_WHITE = 'white';
    const BUTTON_COLOR_BLACK = 'black';

    const BUTTON_LAYOUT_VERTICAL = 'vertical';
    const BUTTON_LAYOUT_HORIZONTAL = 'horizontal';

    const BUTTON_LABEL_PAYPAL = 'paypal';
    const BUTTON_LABEL_CHECKOUT = 'checkout';
    const BUTTON_LABEL_BUYNOW = 'buynow';
    const BUTTON_LABEL_PAY = 'pay';
    const BUTTON_LABEL_INSTALLMENT = 'installment';

    /**
     * Supported currencies for PayPal transactions
     * https://developer.paypal.com/docs/reports/reference/paypal-supported-currencies/
     *
     * @var array
     */
    protected $_supportedCurrencies = [
        'AUD',
        'BRL',
        'CAD',
        'CNY',
        'CZK',
        'DKK',
        'EUR',
        'HKD',
        'HUF',
        'ILS',
        'JPY',
        'MYR',
        'MXN',
        'TWD',
        'NZD',
        'NOK',
        'PHP',
        'PLN',
        'GBP',
        'SGD',
        'SEK',
        'CHF',
        'THB',
        'USD'
    ];

    public function getApiCredentials()
    {
        return [
            'client_id' => $this->getConfigData('client_id'),
            'client_secret' => Mage::helper('core')->decrypt(
                $this->getConfigData('client_secret')
            ),
        ];
    }

    public function isDebugEnabled()
    {
        return (bool)$this->getConfigData('debug');
    }

    public function isSandbox()
    {
        return (bool)$this->getConfigData('sandbox_mode');
    }

    public function getPaymentAction()
    {
        return $this->getConfigData('payment_action');
    }

    public function getEndpoint()
    {
        return $this->isSandbox() ? 'https://www.sandbox.paypal.com' : 'https://www.paypal.com';
    }

    public function getButtonConfiguration()
    {
        return [
            'shape' => $this->getConfigData('button_shape'),
            'color' => $this->getConfigData('button_color'),
            'layout' => $this->getConfigData('button_layout'),
            'label' => $this->getConfigData('button_label'),
            'message' => (bool)$this->getConfigData('button_message')
        ];
    }

    public function getMerchantCountry()
    {
        return $this->getConfigData('merchant_country')
            ?: Mage::getStoreConfig('general/country/default');
    }

    public function isActive($store = null)
    {
        return (bool)$this->getConfigData('active', $store);
    }

    public function getAllowedCurrencyCodes()
    {
        return $this->_supportedCurrencies;
    }

    protected function getConfigData($field, $store = null)
    {
        return Mage::getStoreConfig('payment/paypal/' . $field, $store);
    }
}
