<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Directory
 */

/**
 * Currency rate import model (from Fixer API / Apilayer.com formerly fixer.io)
 *
 * @package    Mage_Directory
 */
class Mage_Directory_Model_Currency_Import_Fixerio extends Mage_Directory_Model_Currency_Import_Abstract
{
    /**
     * XML path to Fixer.IO timeout setting
     */
    public const XML_PATH_FIXERIO_TIMEOUT = 'currency/fixerio/timeout';

    /**
     * XML path to Fixer.IO API key setting
     */
    public const XML_PATH_FIXERIO_API_KEY = 'currency/fixerio/api_key';

    /**
     * URL template for currency rates import
     *
     * @var string
     */
    protected $_url = 'https://api.apilayer.com/fixer/latest?apikey={{ACCESS_KEY}}&base={{CURRENCY_FROM}}&symbols={{CURRENCY_TO}}';

    /**
     * Information messages stack
     *
     * @var array
     */
    protected $_messages = [];

    /**
     * HTTP client
     *
     * @var Varien_Http_Client
     */
    protected $_httpClient;

    /**
     * Create and set HTTP Client
     */
    public function __construct()
    {
        $this->_httpClient = new Varien_Http_Client();
    }

    /**
     * @inheritDoc
     */
    protected function _convert($currencyFrom, $currencyTo)
    {
        return 1;
    }

    /**
     * Fetching of the currency rates data
     *
     * @return array
     */
    public function fetchRates()
    {
        $data = [];
        $currencies = $this->_getCurrencyCodes();
        $defaultCurrencies = $this->_getDefaultCurrencyCodes();

        foreach ($defaultCurrencies as $currencyFrom) {
            if (!isset($data[$currencyFrom])) {
                $data[$currencyFrom] = [];
            }

            $data = $this->_convertBatch($data, $currencyFrom, $currencies);
            ksort($data[$currencyFrom]);
        }

        return $data;
    }

    /**
     * Batch import of currency rates
     *
     * @param string $currencyFrom
     * @return array
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    protected function _convertBatch(array $data, $currencyFrom, array $currenciesTo)
    {
        $accessKey = Mage::getStoreConfig(self::XML_PATH_FIXERIO_API_KEY);
        if (empty($accessKey)) {
            $this->_messages[] = Mage::helper('directory')
                ->__('No API Key was specified or an invalid API Key was specified.');
            $data[$currencyFrom] = $this->_makeEmptyResponse($currenciesTo);
            return $data;
        }

        $currenciesImploded = implode(',', $currenciesTo);
        $url = str_replace(
            ['{{ACCESS_KEY}}', '{{CURRENCY_FROM}}', '{{CURRENCY_TO}}'],
            [$accessKey, $currencyFrom, $currenciesImploded],
            $this->_url,
        );

        $timeLimitCalculated = 2 * Mage::getStoreConfigAsInt(self::XML_PATH_FIXERIO_TIMEOUT)
            + (int) ini_get('max_execution_time');

        @set_time_limit($timeLimitCalculated);
        try {
            $response = $this->_getServiceResponse($url);
        } catch (Exception) {
            ini_restore('max_execution_time');
        }

        if (isset($response) && !$this->_validateResponse($response, $currencyFrom)) {
            $data[$currencyFrom] = $this->_makeEmptyResponse($currenciesTo);
            return $data;
        }

        foreach ($currenciesTo as $currencyTo) {
            if ($currencyFrom == $currencyTo) {
                $data[$currencyFrom][$currencyTo] = $this->_numberFormat(1);
            } elseif (empty($response['rates'][$currencyTo])) {
                $this->_messages[] = Mage::helper('directory')
                    ->__("We can't retrieve a rate from %s for %s.", $url, $currencyTo);
                $data[$currencyFrom][$currencyTo] = null;
            } else {
                $data[$currencyFrom][$currencyTo] = $this->_numberFormat((float) $response['rates'][$currencyTo]);
            }
        }

        return $data;
    }

    /**
     * Get response from external service
     *
     * @param string $url
     * @param int $retry
     * @return array
     */
    protected function _getServiceResponse($url, $retry = 0)
    {
        $response = [];
        try {
            $jsonResponse = $this->_httpClient
                ->setUri($url)
                ->setConfig(['timeout' => Mage::getStoreConfig(self::XML_PATH_FIXERIO_TIMEOUT)])
                ->request('GET')
                ->getBody();

            $response = json_decode($jsonResponse, true);
        } catch (Exception) {
            if ($retry === 0) {
                $response = $this->_getServiceResponse($url, 1);
            }
        }

        return $response;
    }

    /**
     * Validate response from external service
     *
     * @param string $baseCurrency
     * @return bool
     */
    protected function _validateResponse(array $response, $baseCurrency)
    {
        if (!$response['success']) {
            $errorCodes = [
                101 => Mage::helper('directory')
                    ->__('No API Key was specified or an invalid API Key was specified.'),
                102 => Mage::helper('directory')
                    ->__('The account this API request is coming from is inactive.'),
                104 => Mage::helper('directory')
                    ->__('The maximum allowed API amount of monthly API requests has been reached.'),
                105 => Mage::helper('directory')
                    ->__('The "%s" is not allowed as base currency for your subscription plan.', $baseCurrency),
                106 => Mage::helper('directory')
                    ->__('The current request did not return any results.'),
                201 => Mage::helper('directory')
                    ->__('An invalid base currency has been entered.'),
                202 => Mage::helper('directory')
                    ->__('One or more invalid symbols have been specified.'),
            ];

            $this->_messages[] = $errorCodes[$response['error']['code']] ?? Mage::helper('directory')->__("Currency rates can't be retrieved.");

            return false;
        }

        return true;
    }

    /**
     * Fill simulated response with empty data
     *
     * @return array
     */
    protected function _makeEmptyResponse(array $currenciesTo)
    {
        return array_fill_keys($currenciesTo, null);
    }
}
