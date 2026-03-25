<?php

declare(strict_types=1);

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
class Mage_Usa_Model_Shipping_Carrier_Usps_Address_Service extends Mage_Usa_Model_Shipping_Carrier_Usps_AbstractService
{
    /**
     * Address match status constants
     */
    public const MATCH_EXACT = 'exact';

    public const MATCH_CORRECTED = 'corrected';

    public const MATCH_INVALID = 'invalid';

    public const MATCH_MULTIPLE = 'multiple';

    protected string $_debugPrefix = 'USPS AddressService';

    /**
     * Check if address verification is enabled
     *
     * @param null|int $storeId Store ID
     */
    public function isEnabled(?int $storeId = null): bool
    {
        return (bool) Mage::getStoreConfig('carriers/usps/verify_addresses', $storeId)
            && Mage::getStoreConfig('carriers/usps/active', $storeId);
    }

    /**
     * Verify an address using USPS Address API
     *
     * @param  Mage_Customer_Model_Address_Abstract $address Address to verify
     * @return array                                Verification result with keys:
     *                                              - status: string (exact|corrected|invalid|multiple)
     *                                              - original: array Original address data
     *                                              - corrected: array|null Corrected address if available
     *                                              - corrections: array List of corrections made (field => [original, corrected])
     *                                              - warnings: array List of warning messages
     *                                              - deliveryPoint: string|null Delivery point barcode
     *                                              - dpvConfirmation: string|null DPV confirmation indicator
     */
    public function verify(Mage_Customer_Model_Address_Abstract $address): array
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
     * @param  array $data     USPS API response data
     * @param  array $original Original address array
     * @return array Verification result
     */
    protected function _parseVerificationResponse(array $data, array $original): array
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
            $origValue = strtoupper(trim((string) $original[$field]));
            $corrValue = strtoupper(trim((string) $corrected[$field]));

            if ($origValue !== $corrValue) {
                $corrections[$field] = [
                    'original' => $original[$field],
                    'corrected' => $corrected[$field],
                ];
            }
        }

        // Check for warnings/footnotes
        if (isset($data['warnings']) && $data['warnings'] !== []) {
            $warnings = is_array($data['warnings']) ? $data['warnings'] : [$data['warnings']];
        }

        // Check for address errors
        $addressErrors = $data['addressErrors'] ?? [];
        if ($addressErrors !== []) {
            foreach ($addressErrors as $error) {
                $warnings[] = $error['message'] ?? 'Unknown address error';
            }
        }

        // Determine status
        $dpvConfirmation = $address['DPVConfirmation'] ?? null;
        if ($dpvConfirmation === 'Y') {
            // Full delivery point validation
            $status = ($corrections === []) ? self::MATCH_EXACT : self::MATCH_CORRECTED;
        } elseif ($dpvConfirmation === 'D' || $dpvConfirmation === 'S') {
            // Partial match
            $status = self::MATCH_CORRECTED;
            $warnings[] = 'Secondary address (apartment/suite) may be incomplete or invalid.';
        } elseif ($dpvConfirmation === 'N') {
            $status = self::MATCH_INVALID;
            $warnings[] = 'Address not found in USPS database.';
        } else {
            // No DPV data - fallback to corrections check
            $status = ($corrections === []) ? self::MATCH_EXACT : self::MATCH_CORRECTED;
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
     * @param  array $addressData Address data with keys: street1, street2, city, region, postcode
     * @return array Verification result
     */
    public function verifyFromArray(array $addressData): array
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
     * @param  array $corrected Corrected address data array
     * @return array Result with success flag and message
     */
    public function applyCorrectionToQuote(array $corrected): array
    {
        try {
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            $shippingAddress = $quote ? $quote->getShippingAddress() : null;

            if (!$quote || !$quote->getId() || !$shippingAddress) {
                return ['success' => false, 'message' => 'No active quote or shipping address found'];
            }

            // Apply corrections to quote shipping address
            $this->applyCorrection($shippingAddress, $corrected);

            // Save the quote to persist changes
            $quote->collectTotals()->save();

            return ['success' => true, 'message' => 'Address updated successfully'];

        } catch (Exception $exception) {
            Mage::logException($exception);
            return ['success' => false, 'message' => 'Failed to update address'];
        }
    }

    /**
     * Apply corrected address to a Magento address object
     *
     * Automatically updates the address with USPS-corrected values.
     *
     * @param  Mage_Customer_Model_Address_Abstract $address   Address to update
     * @param  array                                $corrected Corrected address array
     * @return bool                                 True on success
     */
    public function applyCorrection(Mage_Customer_Model_Address_Abstract $address, array $corrected): bool
    {
        if ($corrected === []) {
            return false;
        }

        // Handle street specially (combine street1 + street2)
        $street = [$corrected['street1'] ?? ''];
        if (isset($corrected['street2']) && $corrected['street2'] !== '') {
            $street[] = $corrected['street2'];
        }

        $address->setStreet($street);

        // Set other fields
        if (isset($corrected['city']) && $corrected['city'] !== '') {
            $address->setCity($corrected['city']);
        }

        if (isset($corrected['region']) && $corrected['region'] !== '') {
            $address->setRegion($corrected['region']);
            // Also set region_id if we can find it
            $regionId = $this->_getRegionId($corrected['region'], $corrected['country_id'] ?? 'US');
            if ($regionId) {
                $address->setRegionId($regionId);
            }
        }

        if (isset($corrected['postcode']) && $corrected['postcode'] !== '') {
            $address->setPostcode($corrected['postcode']);
        }

        // Mark as verified
        $address->setData('usps_address_verified', true);
        $address->setData('usps_address_verified_at', \Carbon\Carbon::now()->format('Y-m-d H:i:s'));

        $this->_debug([
            'action' => 'apply_correction',
            'corrected' => $corrected,
        ]);

        return true;
    }

    /**
     * Convert Magento address to array format
     */
    protected function _addressToArray(Mage_Customer_Model_Address_Abstract $address): array
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
     */
    protected function _extractZip5(string $postcode): string
    {
        $postcode = preg_replace('/\D/', '', $postcode);
        return substr($postcode, 0, 5);
    }

    /**
     * Extract ZIP+4 from postcode
     */
    protected function _extractZip4(string $postcode): string
    {
        $postcode = preg_replace('/\D/', '', $postcode);
        if (strlen($postcode) >= 9) {
            return substr($postcode, 5, 4);
        }

        return '';
    }

    /**
     * Build formatted ZIP code
     */
    protected function _buildZip(string $zip5, string $zip4): string
    {
        if ($zip4 !== '') {
            return $zip5 . '-' . $zip4;
        }

        return $zip5;
    }

    /**
     * Get region ID from region code
     */
    protected function _getRegionId(string $regionCode, string $countryId): ?int
    {
        $region = Mage::getModel('directory/region')->loadByCode($regionCode, $countryId);
        return $region->getId() ?: null;
    }

}
