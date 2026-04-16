<?php

declare(strict_types=1);

use Carbon\Carbon;

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

class Mage_Usa_Adminhtml_UspsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Environment-to-URL allowlist
     */
    private const ALLOWED_ENVIRONMENTS = [
        'production' => 'https://apis.usps.com/',
        'sandbox' => 'https://apis-tem.usps.com/',
    ];

    public function testconnectionAction(): void
    {
        if (!$this->_validateFormKey()) {
            $this->_sendJson(['success' => false, 'message' => 'Invalid form key. Please refresh the page and try again.']);
            return;
        }

        try {
            [$clientId, $clientSecret, $gatewayUrl, $environment] = $this->_getCredentials();
            $this->_getOAuthToken($gatewayUrl, $clientId, $clientSecret);

            $this->_sendJson([
                'success' => true,
                'message' => 'Connection successful! Environment: ' . ucfirst($environment),
            ]);
        } catch (Exception $exception) {
            $this->_sendJson(['success' => false, 'message' => 'Connection failed: ' . $exception->getMessage()]);
        }
    }

    protected function _getConfig(string $path, string $websiteCode, string $storeCode): ?string
    {
        if ($storeCode) {
            return Mage::getStoreConfig($path, $storeCode);
        }

        if ($websiteCode) {
            return Mage::app()->getWebsite($websiteCode)->getConfig($path);
        }

        return Mage::getStoreConfig($path);
    }

    public function createdimensionsAction(): void
    {
        if (!$this->_validateFormKey()) {
            $this->_sendJson(['success' => false, 'message' => 'Invalid form key. Please refresh the page and try again.']);
            return;
        }

        try {
            $attributes = [
                'package_length' => 'Package Length (inches)',
                'package_width' => 'Package Width (inches)',
                'package_height' => 'Package Height (inches)',
            ];

            $created = [];
            $existing = [];

            foreach ($attributes as $code => $label) {
                $attributeId = Mage::getResourceModel('catalog/eav_attribute')
                    ->getIdByCode('catalog_product', $code);

                if (!$attributeId) {
                    $attribute = Mage::getModel('catalog/resource_eav_attribute');
                    $attribute->setData([
                        'attribute_code' => $code,
                        'entity_type_id' => Mage::getModel('catalog/product')->getResource()->getTypeId(),
                        'frontend_input' => 'text',
                        'frontend_label' => $label,
                        'backend_type' => 'decimal',
                        'is_required' => 0,
                        'is_user_defined' => 1,
                        'is_unique' => 0,
                        'is_global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                        'is_visible' => 1,
                        'is_searchable' => 0,
                        'is_filterable' => 0,
                        'is_comparable' => 0,
                        'is_visible_on_front' => 0,
                        'is_html_allowed_on_front' => 0,
                        'is_used_for_price_rules' => 0,
                        'is_filterable_in_search' => 0,
                        'used_in_product_listing' => 0,
                        'used_for_sort_by' => 0,
                        'is_configurable' => 0,
                        'apply_to' => '',
                        'position' => 0,
                        'note' => '',
                    ]);
                    $attribute->save();

                    // Add to Default attribute set
                    $entityTypeId = Mage::getModel('catalog/product')->getResource()->getTypeId();
                    $defaultSetId = Mage::getModel('eav/entity_type')->load($entityTypeId)->getDefaultAttributeSetId();
                    $generalGroupId = Mage::getResourceModel('eav/entity_attribute_group_collection')
                        ->setAttributeSetFilter((int) $defaultSetId)
                        ->addFieldToFilter('attribute_group_name', 'General')
                        ->getFirstItem()
                        ->getId();
                    if ($generalGroupId) {
                        Mage::getModel('eav/entity_attribute')
                            ->setEntityTypeId($entityTypeId)
                            ->setAttributeSetId($defaultSetId)
                            ->setAttributeGroupId($generalGroupId)
                            ->setAttributeId($attribute->getId())
                            ->setSortOrder(100)
                            ->save();
                    }

                    $created[] = $code;
                } else {
                    $existing[] = $code;
                }
            }

            if ($created !== []) {
                $message = 'Created: ' . implode(', ', $created);
                if ($existing !== []) {
                    $message .= '. Existing: ' . implode(', ', $existing);
                }
            } else {
                $message = 'All attributes exist: ' . implode(', ', $existing);
            }

            $this->_sendJson(['success' => true, 'message' => $message]);

        } catch (Exception $exception) {
            Mage::logException($exception);
            $this->_sendJson(['success' => false, 'message' => 'Error: ' . $exception->getMessage()]);
        }
    }

    protected function _isAllowed(): bool
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/config');
    }

    public function testRateQuoteAction(): void
    {
        if (!$this->_validateFormKey()) {
            $this->_sendJson(['success' => false, 'message' => 'Invalid form key. Please refresh the page and try again.']);
            return;
        }

        try {
            [$clientId, $clientSecret, $gatewayUrl] = $this->_getCredentials();
            $accessToken = $this->_getOAuthToken($gatewayUrl, $clientId, $clientSecret);

            $rateRequest = [
                'originZIPCode' => '10001',
                'destinationZIPCode' => '90210',
                'weight' => 1.0,
                'length' => 6.0,
                'width' => 4.0,
                'height' => 2.0,
                'mailClasses' => ['USPS_GROUND_ADVANTAGE', 'PRIORITY_MAIL', 'PRIORITY_MAIL_EXPRESS'],
                'priceType' => 'COMMERCIAL',
                'mailingDate' => Mage::helper('core/clock')->now()->format('Y-m-d'),
            ];

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $gatewayUrl . 'prices/v3/total-rates/search');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken,
            ]);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($rateRequest) ? json_encode($rateRequest) : '');
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

            $rateResponse = curl_exec($curl);
            $rateHttpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $rateCurlError = ($rateResponse === false) ? curl_error($curl) : '';
            curl_close($curl);

            if ($rateResponse === false) {
                throw new Mage_Core_Exception('Rate request failed: cURL error — ' . $rateCurlError);
            }

            if ($rateHttpCode !== 200) {
                $errorData = json_decode((string) $rateResponse, true);
                $errorMsg = $errorData['error']['message'] ?? $errorData['message'] ?? 'HTTP ' . $rateHttpCode;
                throw new Mage_Core_Exception('Rate request failed: ' . $errorMsg);
            }

            $rateData = json_decode((string) $rateResponse, true);
            $rates = [];
            $rateOptions = $rateData['rateOptions'] ?? [];

            foreach ($rateOptions as $option) {
                foreach ($option['rates'] ?? [] as $rate) {
                    $mailClass = $rate['mailClass'] ?? '';
                    $rateIndicator = $rate['rateIndicator'] ?? '';
                    $price = $option['totalBasePrice'] ?? $rate['price'] ?? 0;

                    $methodName = str_replace('_', ' ', $mailClass);
                    if ($rateIndicator && $rateIndicator !== 'SP') {
                        $methodName .= ' (' . $rateIndicator . ')';
                    }

                    $rates[] = [
                        'method' => $methodName,
                        'price' => number_format((float) $price, 2),
                    ];
                }
            }

            usort($rates, function ($a, $b) {
                return (float) $a['price'] <=> (float) $b['price'];
            });

            if ($rates !== []) {
                $this->_sendJson([
                    'success' => true,
                    'message' => 'Found ' . count($rates) . ' rate(s)',
                    'rates' => $rates,
                ]);
            } else {
                $this->_sendJson([
                    'success' => false,
                    'message' => 'No rates returned.',
                    'debug' => json_encode($rateData, JSON_PRETTY_PRINT),
                ]);
            }

        } catch (Exception $exception) {
            $this->_sendJson(['success' => false, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * Send JSON response
     *
     * @param array<string, mixed> $data
     */
    protected function _sendJson(array $data): void
    {
        $this->getResponse()
            ->setHeader('Content-Type', 'application/json', true)
            ->setBody(Mage::helper('core')->jsonEncode($data));
    }

    /**
     * Read credentials from request, resolve masked values, validate, and return gateway URL
     *
     * @return array{0: string, 1: string, 2: string, 3: string} [clientId, clientSecret, gatewayUrl, environment]
     * @throws Mage_Core_Exception
     */
    protected function _getCredentials(): array
    {
        $request = $this->getRequest();
        $environment = $request->getParam('environment');
        $clientId = $request->getParam('client_id');
        $clientSecret = $request->getParam('client_secret');
        $websiteCode = (string) $request->getParam('website', '');
        $storeCode = (string) $request->getParam('store', '');

        if ($clientId === '******') {
            $clientId = $this->_getConfig('carriers/usps/client_id', $websiteCode, $storeCode);
        }

        if ($clientSecret === '******') {
            $clientSecret = $this->_getConfig('carriers/usps/client_secret', $websiteCode, $storeCode);
        }

        if ($clientId === '' || $clientId === null || $clientSecret === '' || $clientSecret === null || $environment === '' || $environment === null) {
            throw new Mage_Core_Exception('Client ID, Client Secret, and Environment are required.');
        }

        if (!isset(self::ALLOWED_ENVIRONMENTS[$environment])) {
            throw new Mage_Core_Exception('Invalid environment: ' . $environment . '. Allowed: ' . implode(', ', array_keys(self::ALLOWED_ENVIRONMENTS)));
        }

        return [$clientId, $clientSecret, self::ALLOWED_ENVIRONMENTS[$environment], $environment];
    }

    /**
     * Acquire OAuth access token from USPS
     *
     * @throws Mage_Core_Exception
     */
    protected function _getOAuthToken(string $gatewayUrl, string $clientId, string $clientSecret): string
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $gatewayUrl . 'oauth2/v3/token');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ]));
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = ($response === false) ? curl_error($curl) : '';
        curl_close($curl);

        if ($response === false) {
            throw new Mage_Core_Exception('Authentication failed: cURL error — ' . $curlError);
        }

        if ($httpCode !== 200) {
            $errorData = json_decode((string) $response, true);
            throw new Mage_Core_Exception('Authentication failed: ' . ($errorData['error_description'] ?? 'HTTP ' . $httpCode));
        }

        $tokenData = json_decode((string) $response, true);
        $accessToken = $tokenData['access_token'] ?? null;

        if (!$accessToken) {
            throw new Mage_Core_Exception('No access token in response');
        }

        return $accessToken;
    }
}
