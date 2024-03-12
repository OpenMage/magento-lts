<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Usa
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2017-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * UPS Authentication and Access Token handling
 *
 * @category   Mage
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_UpsAuth extends Mage_Usa_Model_Shipping_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
    /**
     * Cache key prefix for UPS API token
     */
    public const CACHE_KEY_PREFIX = 'ups_api_token_';

    /**
     * @return bool|string
     */
    public function getAccessToken(string $clientId, string $clientSecret, string $clientUrl)
    {
        $cacheKey = self::CACHE_KEY_PREFIX;
        $cache = Mage::app()->getCache();
        $result = $cache->load($cacheKey);
        if ($result) {
            return $result;
        }

        $headers = [
            "Content-Type: application/x-www-form-urlencoded",
            "x-merchant-id: $clientId",
            "Authorization: Basic " . base64_encode("$clientId:$clientSecret"),
        ];
        $authPayload = http_build_query([
            'grant_type' => 'client_credentials',
        ]);
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $clientUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $authPayload);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->getConfigFlag('verify_peer'));
            $responseData = curl_exec($ch);
            curl_close($ch);
            $responseData = json_decode($responseData);

            if (isset($responseData->access_token)) {
                $result = $responseData->access_token;
                $cache->save($result, $cacheKey, [], $responseData->expires_in ?: 10000);
            } else {
                $error = Mage::getModel('shipping/rate_result_error');
                $error->setCarrier('ups');
                $error->setCarrierTitle($this->getConfigData('title'));
                if ($this->getConfigData('specificerrmsg') !== '') {
                    $errorTitle = $this->getConfigData('specificerrmsg');
                }
                if (!isset($errorTitle)) {
                    $errorTitle = Mage::helper('usa')->__('Cannot retrieve shipping rates');
                }
                $error->setErrorMessage($errorTitle);
                $result->append($error);
            }

            return $result;
        } catch (Exception $e) {
            Mage::throwException($e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    protected function _doShipmentRequest(Varien_Object $request)
    {
        return new Varien_Object();
    }

    /**
     * @inheritDoc
     */
    public function getAllowedMethods(): array
    {
        return [];
    }
}