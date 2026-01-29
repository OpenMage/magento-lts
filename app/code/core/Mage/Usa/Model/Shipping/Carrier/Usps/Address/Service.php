<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/**
 * USPS Address Verification Service
 *
 * Validates shipping addresses against USPS database and provides
 * standardized address corrections. When corrections are confirmed
 * by the customer, they are automatically applied to the order.
 *
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_Usps_Address_Service
{
    /**
     * Address match status constants
     */
    const MATCH_EXACT = 'exact';
    const MATCH_CORRECTED = 'corrected';
    const MATCH_INVALID = 'invalid';
    const MATCH_MULTIPLE = 'multiple';

    /**
     * @var Mage_Usa_Model_Shipping_Carrier_Usps_Rest_Client
     */
    protected $_client;

    /**
     * @var bool
     */
    protected $_debug = false;

    /**
     * Constructor
     *
     * @param Mage_Usa_Model_Shipping_Carrier_Usps_Rest_Client|null $client
     */
    public function __construct($client = null)
    {
        $this->_client = $client;
        $this->_debug = (bool) Mage::getStoreConfig('carriers/usps/debug');
    }

    /**
     * Get REST client instance
     *
     * @return Mage_Usa_Model_Shipping_Carrier_Usps_Rest_Client
     */
    protected function _getClient()
    {
        if ($this->_client === null) {
            $this->_client = Mage::getModel('usa/shipping_carrier_usps_rest_client');

            // Configure client
            $baseUrl = Mage::getStoreConfig('carriers/usps/gateway_url');
            if ($baseUrl) {
                $this->_client->setBaseUrl($baseUrl);
            }

            // Get auth token
            $auth = Mage::getModel('usa/shipping_carrier_uspsAuth');
            $clientId = Mage::helper('core')->decrypt(Mage::getStoreConfig('carriers/usps/client_id'));
            $clientSecret = Mage::helper('core')->decrypt(Mage::getStoreConfig('carriers/usps/client_secret'));
            $gatewayUrl = Mage::getStoreConfig('carriers/usps/gateway_url');

            $token = $auth->getAccessToken($clientId, $clientSecret, $gatewayUrl);
            if ($token) {
                $this->_client->setAccessToken($token);
            }
        }
        return $this->_client;
    }

    /**
     * Check if address verification is enabled
     *
     * @param int|null $storeId Store ID
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return (bool) Mage::getStoreConfig('carriers/usps/verify_addresses', $storeId)
            && Mage::getStoreConfig('carriers/usps/active', $storeId);
    }

    /**
     * Verify an address using USPS Address API
     *
     * @param Mage_Customer_Model_Address_Abstract $address Address to verify
     * @return array Verification result with keys:
     *   - status: string (exact|corrected|invalid|multiple)
     *   - original: array Original address data
     *   - corrected: array|null Corrected address if available
     *   - corrections: array List of corrections made (field => [original, corrected])
     *   - warnings: array List of warning messages
     *   - deliveryPoint: string|null Delivery point barcode
     *   - dpvConfirmation: string|null DPV confirmation indicator
     */
    public function verify(Mage_Customer_Model_Address_Abstract $address)
    {
        $original = $this->_addressToArray($address);

        // Build USPS API request format
        $apiAddress = [
            'streetAddress' => $original['street1'],
            'secondaryAddress' => $original['street2'],
            'city' => $original['city'],
            'state' => $original['region'],
            'ZIPCode' => $this->_extractZip5($original['postcode']),
            'ZIPPlus4' => $this->_extractZip4($original['postcode']),
        ];

        $client = $this->_getClient();
        $response = $client->verifyAddress($apiAddress);

        $this->_debug([
            'action' => 'verify_address',
            'request' => $apiAddress,
            'response' => $response,
        ]);

        if (!$response['success']) {
            return [
                'status' => self::MATCH_INVALID,
                'original' => $original,
                'corrected' => null,
                'corrections' => [],
                'warnings' => [$response['error'] ?? 'Address verification failed'],
                'deliveryPoint' => null,
                'dpvConfirmation' => null,
            ];
        }

        return $this->_parseVerificationResponse($response['data'], $original);
    }

    /**
     * Parse USPS verification response into standardized format
     *
     * @param array $data USPS API response data
     * @param array $original Original address array
     * @return array Verification result
     */
    protected function _parseVerificationResponse(array $data, array $original)
    {
        $address = $data['address'] ?? $data;
        $warnings = [];
        $corrections = [];

        // Extract corrected address fields
        $corrected = [
            'street1' => $address['streetAddress'] ?? '',
            'street2' => $address['secondaryAddress'] ?? '',
            'city' => $address['city'] ?? '',
            'region' => $address['state'] ?? '',
            'postcode' => $this->_buildZip($address['ZIPCode'] ?? '', $address['ZIPPlus4'] ?? ''),
            'country_id' => 'US',
        ];

        // Compare original vs corrected to find differences
        $fieldsToCompare = ['street1', 'street2', 'city', 'region', 'postcode'];
        foreach ($fieldsToCompare as $field) {
            $origValue = strtoupper(trim($original[$field] ?? ''));
            $corrValue = strtoupper(trim($corrected[$field] ?? ''));

            if ($origValue !== $corrValue) {
                $corrections[$field] = [
                    'original' => $original[$field] ?? '',
                    'corrected' => $corrected[$field] ?? '',
                ];
            }
        }

        // Check for warnings/footnotes
        if (!empty($data['warnings'])) {
            $warnings = is_array($data['warnings']) ? $data['warnings'] : [$data['warnings']];
        }

        // Check for address errors
        $addressErrors = $data['addressErrors'] ?? [];
        if (!empty($addressErrors)) {
            foreach ($addressErrors as $error) {
                $warnings[] = $error['message'] ?? 'Unknown address error';
            }
        }

        // Determine status
        $dpvConfirmation = $address['DPVConfirmation'] ?? null;
        if ($dpvConfirmation === 'Y') {
            // Full delivery point validation
            $status = empty($corrections) ? self::MATCH_EXACT : self::MATCH_CORRECTED;
        } elseif ($dpvConfirmation === 'D' || $dpvConfirmation === 'S') {
            // Partial match
            $status = self::MATCH_CORRECTED;
            $warnings[] = 'Secondary address (apartment/suite) may be incomplete or invalid.';
        } elseif ($dpvConfirmation === 'N') {
            $status = self::MATCH_INVALID;
            $warnings[] = 'Address not found in USPS database.';
        } else {
            // No DPV data - fallback to corrections check
            $status = empty($corrections) ? self::MATCH_EXACT : self::MATCH_CORRECTED;
        }

        return [
            'status' => $status,
            'original' => $original,
            'corrected' => $corrected,
            'corrections' => $corrections,
            'warnings' => $warnings,
            'deliveryPoint' => $address['deliveryPoint'] ?? null,
            'dpvConfirmation' => $dpvConfirmation,
            'success' => true,
        ];
    }

    /**
     * Verify an address from array format (for AJAX controller)
     *
     * @param array $addressData Address data with keys: street1, street2, city, region, postcode
     * @return array Verification result
     */
    public function verifyFromArray(array $addressData)
    {
        $original = [
            'street1' => $addressData['street1'] ?? '',
            'street2' => $addressData['street2'] ?? '',
            'city' => $addressData['city'] ?? '',
            'region' => $addressData['region'] ?? '',
            'postcode' => $addressData['postcode'] ?? '',
        ];

        // Build USPS API request format
        $apiAddress = [
            'streetAddress' => $original['street1'],
            'secondaryAddress' => $original['street2'],
            'city' => $original['city'],
            'state' => $original['region'],
            'ZIPCode' => $this->_extractZip5($original['postcode']),
            'ZIPPlus4' => $this->_extractZip4($original['postcode']),
        ];

        $client = $this->_getClient();
        $response = $client->verifyAddress($apiAddress);

        $this->_debug([
            'action' => 'verify_address_array',
            'request' => $apiAddress,
            'response' => $response,
        ]);

        if (!$response['success']) {
            return [
                'success' => false,
                'status' => self::MATCH_INVALID,
                'original' => $original,
                'corrected' => null,
                'corrections' => [],
                'warnings' => [$response['error'] ?? 'Address verification failed'],
                'error' => $response['error'] ?? 'Address verification failed',
            ];
        }

        return $this->_parseVerificationResponse($response['data'], $original);
    }

    /**
     * Apply corrected address to checkout session quote
     *
     * @param array $corrected Corrected address data array
     * @return array Result with success flag and message
     */
    public function applyCorrectionToQuote(array $corrected)
    {
        try {
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            if (!$quote || !$quote->getId()) {
                return ['success' => false, 'message' => 'No active quote found'];
            }

            $shippingAddress = $quote->getShippingAddress();
            if (!$shippingAddress) {
                return ['success' => false, 'message' => 'No shipping address on quote'];
            }

            // Apply corrections to quote shipping address
            $this->applyCorrection($shippingAddress, $corrected);

            // Save the quote to persist changes
            $quote->collectTotals()->save();

            return ['success' => true, 'message' => 'Address updated successfully'];

        } catch (Exception $e) {
            Mage::logException($e);
            return ['success' => false, 'message' => 'Failed to update address'];
        }
    }

    /**
     * Apply corrected address to a Magento address object
     *
     * Automatically updates the address with USPS-corrected values.
     *
     * @param Mage_Customer_Model_Address_Abstract $address Address to update
     * @param array $corrected Corrected address array
     * @return bool True on success
     */
    public function applyCorrection(Mage_Customer_Model_Address_Abstract $address, array $corrected)
    {
        if (empty($corrected)) {
            return false;
        }

        // Map our field names to Magento address methods
        $fieldMapping = [
            'street1' => 'street',
            'street2' => null, // Handled with street1
            'city' => 'city',
            'region' => 'region',
            'postcode' => 'postcode',
            'country_id' => 'country_id',
        ];

        // Handle street specially (combine street1 + street2)
        $street = [$corrected['street1'] ?? ''];
        if (!empty($corrected['street2'])) {
            $street[] = $corrected['street2'];
        }
        $address->setStreet($street);

        // Set other fields
        if (!empty($corrected['city'])) {
            $address->setCity($corrected['city']);
        }

        if (!empty($corrected['region'])) {
            $address->setRegion($corrected['region']);
            // Also set region_id if we can find it
            $regionId = $this->_getRegionId($corrected['region'], $corrected['country_id'] ?? 'US');
            if ($regionId) {
                $address->setRegionId($regionId);
            }
        }

        if (!empty($corrected['postcode'])) {
            $address->setPostcode($corrected['postcode']);
        }

        // Mark as verified
        $address->setData('usps_address_verified', true);
        $address->setData('usps_address_verified_at', date('Y-m-d H:i:s'));

        $this->_debug([
            'action' => 'apply_correction',
            'corrected' => $corrected,
        ]);

        return true;
    }

    /**
     * Convert Magento address to array format
     *
     * @param Mage_Customer_Model_Address_Abstract $address
     * @return array
     */
    protected function _addressToArray(Mage_Customer_Model_Address_Abstract $address)
    {
        $street = $address->getStreet();

        return [
            'street1' => is_array($street) ? ($street[0] ?? '') : $street,
            'street2' => is_array($street) ? ($street[1] ?? '') : '',
            'city' => $address->getCity(),
            'region' => $address->getRegionCode() ?: $address->getRegion(),
            'postcode' => $address->getPostcode(),
            'country_id' => $address->getCountryId(),
        ];
    }

    /**
     * Extract 5-digit ZIP from postcode
     *
     * @param string $postcode
     * @return string
     */
    protected function _extractZip5($postcode)
    {
        $postcode = preg_replace('/[^0-9]/', '', $postcode);
        return substr($postcode, 0, 5);
    }

    /**
     * Extract ZIP+4 from postcode
     *
     * @param string $postcode
     * @return string
     */
    protected function _extractZip4($postcode)
    {
        $postcode = preg_replace('/[^0-9]/', '', $postcode);
        if (strlen($postcode) >= 9) {
            return substr($postcode, 5, 4);
        }
        return '';
    }

    /**
     * Build formatted ZIP code
     *
     * @param string $zip5
     * @param string $zip4
     * @return string
     */
    protected function _buildZip($zip5, $zip4)
    {
        if (!empty($zip4)) {
            return $zip5 . '-' . $zip4;
        }
        return $zip5;
    }

    /**
     * Get region ID from region code
     *
     * @param string $regionCode
     * @param string $countryId
     * @return int|null
     */
    protected function _getRegionId($regionCode, $countryId)
    {
        $region = Mage::getModel('directory/region')->loadByCode($regionCode, $countryId);
        return $region->getId() ?: null;
    }

    /**
     * Debug logging
     *
     * @param array $data
     * @return void
     */
    protected function _debug(array $data)
    {
        if (!$this->_debug) {
            return;
        }

        Mage::log(
            'USPS AddressService: ' . print_r($data, true),
            Zend_Log::DEBUG,
            'shipping_usps.log',
            true
        );
    }
}
