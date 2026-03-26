<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/**
 * USPS Label Generation Service
 *
 * Handles creation of outbound domestic and international shipping labels
 * via USPS REST API v3. Integrates with Magento's shipment workflow.
 *
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_Usps_Label_Service extends Mage_Usa_Model_Shipping_Carrier_Usps_AbstractService
{
    /**
     * Label format constants
     */
    public const FORMAT_PDF = 'LABEL_PDF';

    public const FORMAT_PDF_4X6 = 'LABEL_PDF_4X6';

    public const FORMAT_ZPL = 'LABEL_ZPL';

    protected string $_debugPrefix = 'USPS LabelService';

    /**
     * @var array<string, mixed> Configuration data
     */
    protected array $_config = [];

    /**
     * Constructor
     */
    public function __construct(?Mage_Usa_Model_Shipping_Carrier_Usps_Rest_Client $client = null)
    {
        parent::__construct($client);
        $this->_loadConfig();
    }

    /**
     * Load configuration from store config
     */
    protected function _loadConfig(): void
    {
        $this->_config = [
            'crid' => Mage::getStoreConfig('carriers/usps/crid'),
            'mid' => Mage::getStoreConfig('carriers/usps/mid'),
            'manifest_mid' => Mage::getStoreConfig('carriers/usps/mmid'),
            'account_type' => Mage::getStoreConfig('carriers/usps/account_type'),
            'eps_account_number' => Mage::helper('core')->decrypt(
                Mage::getStoreConfig('carriers/usps/eps_account_number'),
            ),
            'permit_zip' => Mage::getStoreConfig('carriers/usps/permit_zip'),
            'aesitn' => Mage::getStoreConfig('carriers/usps/aesitn'),
        ];
    }

    /**
     * Check if label generation is enabled and properly configured
     *
     * @param null|int $storeId Store ID
     */
    public function isEnabled(?int $storeId = null): bool
    {
        return Mage::getStoreConfigFlag('carriers/usps/enable_labels', $storeId)
            && Mage::getStoreConfigFlag('carriers/usps/active', $storeId)
            && Mage::getStoreConfig('carriers/usps/crid', $storeId)
            && Mage::getStoreConfig('carriers/usps/mid', $storeId)
            && Mage::getStoreConfig('carriers/usps/eps_account_number', $storeId);
    }

    /**
     * Create a domestic shipping label
     *
     * @param  Varien_Object $request Shipment request with package/address data
     * @return Varien_Object result with label_content, tracking_number, etc
     */
    public function createDomesticLabel(Varien_Object $request): Varien_Object
    {
        $result = new Varien_Object();

        try {
            $payload = $this->_buildDomesticLabelRequest($request);

            $this->_debug([
                'action' => 'create_domestic_label',
                'request' => $payload,
            ]);

            $client = $this->_getClient();
            $response = $client->postWithRetry('labels/v3/label', $payload, true, 2);

            $this->_debug([
                'action' => 'create_domestic_label_response',
                'response' => $response,
            ]);

            if (!$response['success']) {
                $errorMsg = $this->_extractErrorMessage($response);
                $result->setErrors($errorMsg);
                return $result;
            }

            return $this->_parseLabelResponse($response['data'], $result);

        } catch (Exception $exception) {
            Mage::logException($exception);
            $result->setErrors($exception->getMessage());
            return $result;
        }
    }

    /**
     * Create an international shipping label
     *
     * @param  Varien_Object $request Shipment request
     * @return Varien_Object Result with label_content, tracking_number, customs_form
     */
    public function createInternationalLabel(Varien_Object $request): Varien_Object
    {
        $result = new Varien_Object();

        try {
            $payload = $this->_buildInternationalLabelRequest($request);

            $this->_debug([
                'action' => 'create_international_label',
                'request' => $payload,
            ]);

            $client = $this->_getClient();
            $response = $client->postWithRetry('international-labels/v3/label', $payload, true, 2);

            $this->_debug([
                'action' => 'create_international_label_response',
                'response' => $response,
            ]);

            if (!$response['success']) {
                $errorMsg = $this->_extractErrorMessage($response);
                $result->setErrors($errorMsg);
                return $result;
            }

            return $this->_parseInternationalLabelResponse($response['data'], $result);

        } catch (Exception $exception) {
            Mage::logException($exception);
            $result->setErrors($exception->getMessage());
            return $result;
        }
    }

    /**
     * Build domestic label API request payload
     *
     * @return array<string, mixed>
     */
    protected function _buildDomesticLabelRequest(Varien_Object $request): array
    {
        $shipper = $request->getShipperAddressStreet() ?: $this->_getOriginAddress();
        $recipient = $request->getRecipientAddressStreet();

        $payload = [
            'imageInfo' => [
                'imageType' => self::FORMAT_PDF_4X6,
                'labelType' => 'SHIPPING_LABEL',
            ],
            'toAddress' => $this->_formatAddress($recipient, $request, 'recipient'),
            'fromAddress' => $this->_formatAddress($shipper, $request, 'shipper'),
            'senderInfo' => [
                'firstName' => $request->getShipperContactPersonFirstName(),
                'lastName' => $request->getShipperContactPersonLastName(),
                'phone' => $request->getShipperContactPhoneNumber(),
                'email' => $request->getShipperEmail(),
            ],
            'recipientInfo' => [
                'firstName' => $request->getRecipientContactPersonFirstName(),
                'lastName' => $request->getRecipientContactPersonLastName(),
                'phone' => $request->getRecipientContactPhoneNumber(),
                'email' => $request->getRecipientEmail(),
            ],
            'packageDescription' => $this->_buildPackageDescription($request),
            'mailClass' => $this->_mapServiceCode($request->getShippingMethod()),
            'paymentInfo' => $this->_buildPaymentInfo(),
            'CRID' => $this->_config['crid'],
            'MID' => $this->_config['mid'],
        ];

        if (isset($this->_config['manifest_mid']) && $this->_config['manifest_mid'] !== '') {
            $payload['manifestMID'] = $this->_config['manifest_mid'];
        }

        return $payload;
    }

    /**
     * Build international label API request payload
     *
     * @return array<string, mixed>
     */
    protected function _buildInternationalLabelRequest(Varien_Object $request): array
    {
        $payload = $this->_buildDomesticLabelRequest($request);
        $payload['customsDeclaration'] = $this->_buildCustomsDeclaration($request);

        if (isset($this->_config['aesitn']) && $this->_config['aesitn'] !== '') {
            $payload['AES'] = $this->_config['aesitn'];
        }

        return $payload;
    }

    /**
     * Build package description for API
     *
     * @return array<string, mixed>
     */
    protected function _buildPackageDescription(Varien_Object $request): array
    {
        $weight = $request->getPackageWeight();
        $params = $request->getPackageParams();
        $weightOz = $weight * 16;

        $package = [
            'weightOZ' => round($weightOz, 2),
            'mailClass' => $this->_mapServiceCode($request->getShippingMethod()),
        ];

        if ($params) {
            if ($params->getLength()) {
                $package['length'] = (float) $params->getLength();
            }

            if ($params->getWidth()) {
                $package['width'] = (float) $params->getWidth();
            }

            if ($params->getHeight()) {
                $package['height'] = (float) $params->getHeight();
            }
        }

        return $package;
    }

    /**
     * Build payment info for API
     *
     * @return array<string, mixed>
     */
    protected function _buildPaymentInfo(): array
    {
        $payment = [
            'paymentType' => $this->_config['account_type'],
        ];

        if (isset($this->_config['eps_account_number']) && $this->_config['eps_account_number'] !== '') {
            $payment['accountNumber'] = $this->_config['eps_account_number'];
        }

        if ($this->_config['account_type'] === 'PERMIT' && isset($this->_config['permit_zip']) && $this->_config['permit_zip'] !== '') {
            $payment['permitZIP'] = $this->_config['permit_zip'];
        }

        return $payment;
    }

    /**
     * Build customs declaration for international labels
     *
     * @return array<string, mixed>
     */
    protected function _buildCustomsDeclaration(Varien_Object $request): array
    {
        $items = $request->getPackageItems() ?: [];
        $customsItems = [];

        foreach ($items as $item) {
            $customsItems[] = [
                'description' => substr($item['name'] ?? 'Merchandise', 0, 60),
                'quantity' => (int) ($item['qty'] ?? 1),
                'value' => (float) ($item['customs_value'] ?? $item['price'] ?? 0),
                'weight' => (float) ($item['weight'] ?? 0),
                'HSCode' => $item['hs_code'] ?? '',
                'countryOfOrigin' => $item['country_of_manufacture'] ?? 'US',
            ];
        }

        return [
            'contents' => 'MERCHANDISE',
            'nonDeliveryOption' => 'RETURN',
            'customsItems' => $customsItems,
        ];
    }

    /**
     * Format address for API request
     *
     * @param  array<int, string>|string $street
     * @param  string                    $type   'shipper' or 'recipient'
     * @return array<string, mixed>
     */
    protected function _formatAddress(array|string $street, Varien_Object $request, string $type): array
    {
        $prefix = $type === 'shipper' ? 'shipper' : 'recipient';
        $streetLines = is_array($street) ? $street : [$street];
        $postalCode = preg_replace('/\D/', '', $request->getData($prefix . '_address_postal_code'));

        return [
            'streetAddress' => $streetLines[0] ?? '',
            'secondaryAddress' => $streetLines[1] ?? '',
            'city' => $request->getData($prefix . '_address_city'),
            'state' => $request->getData($prefix . '_address_state_or_province_code'),
            'ZIPCode' => substr($postalCode, 0, 5),
            'ZIPPlus4' => strlen($postalCode) > 5 ? substr($postalCode, 5, 4) : null,
            'urbanization' => null,
        ];
    }

    /**
     * Get origin (shipper) address from store configuration
     *
     * @return array<int, string>
     */
    protected function _getOriginAddress(): array
    {
        return [
            Mage::getStoreConfig('shipping/origin/street_line1'),
            Mage::getStoreConfig('shipping/origin/street_line2'),
        ];
    }

    /**
     * Map Magento shipping method code to USPS mail class
     */
    protected function _mapServiceCode(string $methodCode): string
    {
        $code = preg_replace('/^usps_/i', '', $methodCode) ?? $methodCode;
        return preg_replace('/_(SP|FE|FB|PL|FS|FP|FA|LFR|LR)$/', '', $code) ?? $code;
    }

    /**
     * Parse label creation API response
     *
     * @param array<string, mixed> $data
     */
    protected function _parseLabelResponse(array $data, Varien_Object $result): Varien_Object
    {
        $trackingNumber = $data['trackingNumber'] ?? null;
        if (!$trackingNumber) {
            $result->setErrors('No tracking number in response');
            return $result;
        }

        $labelImage = $data['labelImage'] ?? null;
        if (!$labelImage) {
            $result->setErrors('No label image in response');
            return $result;
        }

        if (is_string($labelImage) && preg_match('/^[A-Za-z0-9+\/=]+$/', $labelImage)) {
            $decoded = base64_decode($labelImage, true);
            if ($decoded === false) {
                $result->setErrors('Failed to decode label image');
                return $result;
            }

            $labelImage = $decoded;
        }

        $result->setTrackingNumber($trackingNumber);
        $result->setLabelContent($labelImage);
        $result->setShippingLabelContent($labelImage);

        if (isset($data['postage'])) {
            $result->setPostage((float) $data['postage']);
        }

        return $result;
    }

    /**
     * Parse international label creation API response
     *
     * @param array<string, mixed> $data
     */
    protected function _parseInternationalLabelResponse(array $data, Varien_Object $result): Varien_Object
    {
        $result = $this->_parseLabelResponse($data, $result);

        if (isset($data['customsForm'])) {
            $customsForm = $data['customsForm'];
            if (is_string($customsForm) && preg_match('/^[A-Za-z0-9+\/=]+$/', $customsForm)) {
                $decoded = base64_decode($customsForm, true);
                if ($decoded !== false) {
                    $customsForm = $decoded;
                }
            }

            $result->setCustomsForm($customsForm);
        }

        return $result;
    }

    /**
     * Cancel a shipping label
     */
    public function cancelLabel(string $trackingNumber): bool
    {
        try {
            $client = $this->_getClient();
            $response = $client->cancelLabel($trackingNumber);

            $this->_debug([
                'action' => 'cancel_label',
                'tracking_number' => $trackingNumber,
                'response' => $response,
            ]);

            return $response['success'];

        } catch (Exception $exception) {
            Mage::logException($exception);
            return false;
        }
    }

    /**
     * Extract error message from API response
     *
     * @param array<string, mixed> $response
     */
    protected function _extractErrorMessage(array $response): string
    {
        if (isset($response['error']) && $response['error'] !== '') {
            return $response['error'];
        }

        if (isset($response['data']['errors'][0]['message']) && $response['data']['errors'][0]['message'] !== '') {
            return $response['data']['errors'][0]['message'];
        }

        if (isset($response['data']['error']['message']) && $response['data']['error']['message'] !== '') {
            return $response['data']['error']['message'];
        }

        return 'Unknown error creating shipping label';
    }

    /**
     * Debug logging — redacts sensitive payment info before logging
     *
     * @param array<string, mixed> $data
     */
    protected function _debug(array $data): void
    {
        if (isset($data['request']['paymentInfo']['accountNumber'])) {
            $data['request']['paymentInfo']['accountNumber'] = '[REDACTED]';
        }

        parent::_debug($data);
    }
}
