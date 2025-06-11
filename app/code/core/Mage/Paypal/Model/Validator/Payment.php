<?php
class Mage_Paypal_Model_Validator_Payment
{
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

    public function validateCurrency($currencyCode)
    {
        if (!in_array($currencyCode, $this->_supportedCurrencies)) {
            $message = sprintf('Currency "%s" is not supported by PayPal', $currencyCode);
            Mage::log($message, Zend_Log::ERR, 'paypal.log', true);
            throw new Mage_Paypal_Model_Exception($message);
        }
        return true;
    }

    public function validateAmount($amount)
    {
        if ($amount <= 0) {
            Mage::log('Invalid payment amount', Zend_Log::ERR, 'paypal.log', true);
            throw new Mage_Paypal_Model_Exception('Invalid payment amount');
        }
        return true;
    }

    public function validateTotal($calculatedTotal, $orderTotal)
    {
        $epsilon = 0.00001;
        if (abs($calculatedTotal - $orderTotal) > $epsilon) {
            $message = 'Total amount mismatch';
            Mage::log($message . sprintf(' (calculated: %s, order: %s)', 
                $calculatedTotal, 
                $orderTotal
            ), Zend_Log::ERR, 'paypal.log', true);
            throw new Mage_Paypal_Model_Exception($message);
        }
        return true;
    }
}
