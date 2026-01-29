<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

class Mage_Usa_Adminhtml_UspsController extends Mage_Adminhtml_Controller_Action
{
    public function testconnectionAction()
    {
        $request = $this->getRequest();
        $environment = $request->getParam('environment');
        $clientId = $request->getParam('client_id');
        $clientSecret = $request->getParam('client_secret');
        $websiteCode = $request->getParam('website');
        $storeCode = $request->getParam('store');

        if ($clientId === '******') {
            $clientId = $this->_getConfig('carriers/usps/client_id', $websiteCode, $storeCode);
        }
        if ($clientSecret === '******') {
            $clientSecret = $this->_getConfig('carriers/usps/client_secret', $websiteCode, $storeCode);
        }
        
        try {
            if ($clientId === '' || $clientId === null || $clientSecret === '' || $clientSecret === null || $environment === '' || $environment === null) {
                throw new Exception('Client ID, Client Secret, and Environment are required.');
            }
            
            $gatewayUrl = ($environment === 'production') 
                ? 'https://apis.usps.com/' 
                : 'https://apis-tem.usps.com/';
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $gatewayUrl . 'oauth2/v3/token');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'grant_type' => 'client_credentials',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ]));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200) {
                $result = json_decode($response, true);
                if (isset($result['access_token'])) {
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode([
                        'success' => true,
                        'message' => 'Connection successful! Environment: ' . ucfirst($environment)
                    ]));
                } else {
                    throw new Exception('No access token in response');
                }
            } else {
                $errorData = json_decode($response, true);
                $errorMsg = isset($errorData['error_description']) 
                    ? $errorData['error_description'] 
                    : 'HTTP ' . $httpCode;
                throw new Exception('Authentication failed: ' . $errorMsg);
            }
            
        } catch (Exception $e) {
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode([
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage()
            ]));
        }
    }
    
    protected function _getConfig($path, $websiteCode, $storeCode)
    {
        $scope = 'default';
        $scopeId = 0;
        
        if ($storeCode) {
            $scope = 'stores';
            $scopeId = Mage::app()->getStore($storeCode)->getId();
        } elseif ($websiteCode) {
            $scope = 'websites';
            $scopeId = Mage::app()->getWebsite($websiteCode)->getId();
        }
        
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $table = Mage::getSingleton('core/resource')->getTableName('core_config_data');
        
        $value = $read->fetchOne(
            "SELECT value FROM $table WHERE path = ? AND scope = ? AND scope_id = ?",
            [$path, $scope, $scopeId]
        );
        
        if ($value === false) {
            if ($scope === 'stores') {
                $websiteId = Mage::app()->getStore($storeCode)->getWebsiteId();
                $value = $read->fetchOne(
                    "SELECT value FROM $table WHERE path = ? AND scope = 'websites' AND scope_id = ?",
                    [$path, $websiteId]
                );
            }
            
            if ($value === false) {
                $value = $read->fetchOne(
                    "SELECT value FROM $table WHERE path = ? AND scope = 'default' AND scope_id = 0",
                    [$path]
                );
            }
        }
        
        if ($value && strpos($path, 'client_id') !== false || strpos($path, 'client_secret') !== false) {
            $value = Mage::helper('core')->decrypt($value);
        }
        
        return $value;
    }

    public function createdimensionsAction()
    {
        try {
            $attributes = [
                'package_length' => 'Package Length (inches)',
                'package_width' => 'Package Width (inches)',
                'package_height' => 'Package Height (inches)'
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
                        'note' => ''
                    ]);
                    $attribute->save();
                    $created[] = $code;
                } else {
                    $existing[] = $code;
                }
            }
            
            if (count($created) > 0) {
                $message = 'Created: ' . implode(', ', $created);
                if (count($existing) > 0) {
                    $message .= '. Existing: ' . implode(', ', $existing);
                }
            } else {
                $message = 'All attributes exist: ' . implode(', ', $existing);
            }
            
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode([
                'success' => true,
                'message' => $message
            ]));
            
        } catch (Exception $e) {
            Mage::logException($e);
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]));
        }
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/config');
    }

    public function testRateQuoteAction()
    {
        $request = $this->getRequest();
        $environment = $request->getParam('environment');
        $clientId = $request->getParam('client_id');
        $clientSecret = $request->getParam('client_secret');
        $websiteCode = $request->getParam('website');
        $storeCode = $request->getParam('store');

        if ($clientId === '******') {
            $clientId = $this->_getConfig('carriers/usps/client_id', $websiteCode, $storeCode);
        }
        if ($clientSecret === '******') {
            $clientSecret = $this->_getConfig('carriers/usps/client_secret', $websiteCode, $storeCode);
        }
        
        try {
            if ($clientId === '' || $clientId === null || $clientSecret === '' || $clientSecret === null || $environment === '' || $environment === null) {
                throw new Exception('Client ID, Client Secret, and Environment are required.');
            }
            
            $gatewayUrl = ($environment === 'production') 
                ? 'https://apis.usps.com/' 
                : 'https://apis-tem.usps.com/';
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $gatewayUrl . 'oauth2/v3/token');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'grant_type' => 'client_credentials',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ]));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $tokenResponse = curl_exec($ch);
            $tokenHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($tokenHttpCode !== 200) {
                $errorData = json_decode($tokenResponse, true);
                throw new Exception('Authentication failed: ' . ($errorData['error_description'] ?? 'HTTP ' . $tokenHttpCode));
            }
            
            $tokenData = json_decode($tokenResponse, true);
            $accessToken = $tokenData['access_token'] ?? null;
            
            if (!$accessToken) {
                throw new Exception('No access token received');
            }
            
            $rateRequest = [
                'originZIPCode' => '10001',
                'destinationZIPCode' => '90210',
                'weight' => 1.0,
                'length' => 6.0,
                'width' => 4.0,
                'height' => 2.0,
                'mailClasses' => ['USPS_GROUND_ADVANTAGE', 'PRIORITY_MAIL', 'PRIORITY_MAIL_EXPRESS'],
                'priceType' => 'COMMERCIAL',
                'mailingDate' => date('Y-m-d')
            ];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $gatewayUrl . 'prices/v3/total-rates/search');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($rateRequest));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $rateResponse = curl_exec($ch);
            $rateHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($rateHttpCode !== 200) {
                $errorData = json_decode($rateResponse, true);
                $errorMsg = $errorData['error']['message'] ?? $errorData['message'] ?? 'HTTP ' . $rateHttpCode;
                throw new Exception('Rate request failed: ' . $errorMsg);
            }
            
            $rateData = json_decode($rateResponse, true);
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
                        'price' => number_format((float) $price, 2)
                    ];
                }
            }
            
            usort($rates, function($a, $b) {
                return (float) $a['price'] <=> (float) $b['price'];
            });
            
            if (count($rates) > 0) {
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode([
                    'success' => true,
                    'message' => 'Found ' . count($rates) . ' rate(s)',
                    'rates' => $rates
                ]));
            } else {
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode([
                    'success' => false,
                    'message' => 'No rates returned.',
                    'debug' => json_encode($rateData, JSON_PRETTY_PRINT)
                ]));
            }
            
        } catch (Exception $e) {
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode([
                'success' => false,
                'message' => $e->getMessage()
            ]));
        }
    }
}
