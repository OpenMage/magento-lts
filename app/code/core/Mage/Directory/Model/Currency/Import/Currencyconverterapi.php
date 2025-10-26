<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Directory
 */

/**
 * Currency rate import model (From www.currencyconverterapi.com)
 *
 * @package    Mage_Directory
 */
class Mage_Directory_Model_Currency_Import_Currencyconverterapi extends Mage_Directory_Model_Currency_Import_Abstract
{
    /**
     * XML path to Currency Converter timeout setting
     */
    public const XML_PATH_CURRENCY_CONVERTER_TIMEOUT = 'currency/currencyconverterapi/timeout';

    /**
     * XML path to Currency Converter API key setting
     */
    public const XML_PATH_CURRENCY_CONVERTER_API_KEY = 'currency/currencyconverterapi/api_key';

    /**
     * URL template for currency rates import
     *
     * @var string
     */
    protected $_url = 'https://free.currconv.com/api/v7/convert?apiKey={{API_KEY}}&q={{CURRENCY_FROM}}_{{CURRENCY_TO}}&compact=ultra';

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
     * @inheritdoc
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
        $apiKey = Mage::getStoreConfig(self::XML_PATH_CURRENCY_CONVERTER_API_KEY);
        if (empty($apiKey)) {
            $this->_messages[] = Mage::helper('directory')
                ->__('No API Key was specified or an invalid API Key was specified.');
            $data[$currencyFrom] = $this->_makeEmptyResponse($currenciesTo);
            return $data;
        }

        foreach ($currenciesTo as $currencyTo) {
            $currenciesCombined = $currencyFrom . '_' . $currencyTo;
            $url = str_replace(
                ['{{API_KEY}}', '{{CURRENCY_FROM}}_{{CURRENCY_TO}}'],
                [$apiKey, $currenciesCombined],
                $this->_url,
            );

            $timeLimitCalculated = 2 * Mage::getStoreConfigAsInt(self::XML_PATH_CURRENCY_CONVERTER_TIMEOUT)
                + (int) ini_get('max_execution_time');

            @set_time_limit($timeLimitCalculated);
            try {
                $response = $this->_getServiceResponse($url);
            } catch (Exception) {
                ini_restore('max_execution_time');
            }

            if ($currencyFrom == $currencyTo) {
                $data[$currencyFrom][$currencyTo] = $this->_numberFormat(1);
            } elseif (empty($response)) {
                $this->_messages[] = Mage::helper('directory')
                    ->__('We can\'t retrieve a rate from %s for %s.', $url, $currencyTo);
                $data[$currencyFrom][$currencyTo] = null;
            } else {
                $data[$currencyFrom][$currencyTo] = $this->_numberFormat((float) $response[$currenciesCombined]);
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
                ->setConfig(['timeout' => Mage::getStoreConfig(self::XML_PATH_CURRENCY_CONVERTER_TIMEOUT)])
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
     * Fill simulated response with empty data
     *
     * @return array
     */
    protected function _makeEmptyResponse(array $currenciesTo)
    {
        return array_fill_keys($currenciesTo, null);
    }
}
