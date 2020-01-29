<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Directory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Currency rate import model (From www.currencyconverterapi.com)
 *
 * @category   Mage
 * @package    Mage_Directory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Directory_Model_Currency_Import_Currencyconverterapi extends Mage_Directory_Model_Currency_Import_Abstract
{
    /**
     * XML path to Currency Converter timeout setting
     */
    const XML_PATH_CURRENCY_CONVERTER_TIMEOUT = 'currency/currencyconverterapi/timeout';

    /**
     * XML path to Currency Converter API key setting
     */
    const XML_PATH_CURRENCY_CONVERTER_API_KEY = 'currency/currencyconverterapi/api_key';

    /**
     * URL template for currency rates import
     *
     * @var string
     */
    protected $_url = '';

    /**
     * Information messages stack
     *
     * @var array
     */
    protected $_messages = array();

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
        if (empty($this->_url)) {
            $this->_url = 'https://free.currconv.com/api/v7/convert'
                . '?apiKey={{API_KEY}}&q={{CURRENCY_FROM}}_{{CURRENCY_TO}}&compact=ultra';
        }
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
        $data = array();
        $currencies = $this->_getCurrencyCodes();
        $defaultCurrencies = $this->_getDefaultCurrencyCodes();

        foreach ($defaultCurrencies as $currencyFrom) {
            if (!isset($data[$currencyFrom])) {
                $data[$currencyFrom] = array();
            }

            $data = $this->_convertBatch($data, $currencyFrom, $currencies);
            ksort($data[$currencyFrom]);
        }

        return $data;
    }

    /**
     * Batch import of currency rates
     *
     * @param array $data
     * @param string $currencyFrom
     * @param array $currenciesTo
     * @return array
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
                array('{{API_KEY}}', '{{CURRENCY_FROM}}_{{CURRENCY_TO}}'),
                array($apiKey, $currenciesCombined),
                $this->_url
            );

            $timeLimitCalculated = 2 * (int) Mage::getStoreConfig(self::XML_PATH_CURRENCY_CONVERTER_TIMEOUT)
                + (int) ini_get('max_execution_time');

            @set_time_limit($timeLimitCalculated);
            try {
                $response = $this->_getServiceResponse($url);
            } catch (Exception $e) {
                ini_restore('max_execution_time');
            }

            if ($currencyFrom == $currencyTo) {
                $data[$currencyFrom][$currencyTo] = $this->_numberFormat(1);
            } else {
                if (empty($response)) {
                    $this->_messages[] = Mage::helper('directory')
                        ->__('We can\'t retrieve a rate from %s for %s.', $url, $currencyTo);
                    $data[$currencyFrom][$currencyTo] = null;
                } else {
                    $data[$currencyFrom][$currencyTo] = $this->_numberFormat((float) $response[$currenciesCombined]);
                }
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
        $response = array();
        try {
            $jsonResponse = $this->_httpClient
                ->setUri($url)
                ->setConfig(array('timeout' => Mage::getStoreConfig(self::XML_PATH_CURRENCY_CONVERTER_TIMEOUT)))
                ->request('GET')
                ->getBody();

            $response = json_decode($jsonResponse, true);
        } catch (Exception $e) {
            if ($retry === 0) {
                $response = $this->_getServiceResponse($url, 1);
            }
        }

        return $response;
    }

    /**
     * Fill simulated response with empty data
     *
     * @param array $currenciesTo
     * @return array
     */
    protected function _makeEmptyResponse(array $currenciesTo)
    {
        return array_fill_keys($currenciesTo, null);
    }
}
