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
 * @copyright  Copyright (c) 2024-2025 The OpenMage Contributors (https://www.openmage.org)
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
     * @return string
     * @throws Exception
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
            'Content-Type: application/x-www-form-urlencoded',
            "x-merchant-id: $clientId",
            'Authorization: Basic ' . base64_encode("$clientId:$clientSecret"),
        ];
        $authPayload = http_build_query([
            'grant_type' => 'client_credentials',
        ]);
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
        try {
            if ($responseData === false) {
                $code = curl_errno($ch);
                $description = curl_strerror($ch);
                $message = curl_error($ch);
                Mage::throwException("cURL Error: ($code) $description - \"$message\"");
            }
        } finally {
            curl_close($ch);
        }

        $responseData = json_decode($responseData);

        if (isset($responseData->errors)) {
            Mage::throwException('Failed to authenticate with UPS. Errors: ' . json_encode($responseData->errors));
        }

        if (!isset($responseData->access_token)) {
            Mage::throwException('Error decoding auth token from UPS');
        }

        $result = $responseData->access_token;
        $expiresIn = $responseData->expires_in ?? 10000;
        $cache->save($result, $cacheKey, [], $expiresIn);
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        return false;
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
