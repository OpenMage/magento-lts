<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/**
 * USPS Service Standards (Delivery Estimates) Service
 *
 * Queries USPS Service Standards API to get estimated delivery
 * timeframes for different mail classes. Used to display
 * "Arrives by [date]" information with shipping rates.
 *
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_Usps_Service_Standards
{
    /**
     * Cache key prefix for service standards
     */
    public const CACHE_KEY_PREFIX = 'usps_service_standards_';

    /**
     * Cache TTL for service standards (24 hours default - these don't change often)
     */
    public const CACHE_TTL = 86400;

    /**
     * @var Mage_Usa_Model_Shipping_Carrier_Usps_Rest_Client
     */
    protected ?Mage_Usa_Model_Shipping_Carrier_Usps_Rest_Client $_client = null;

    /**
     * @var bool
     */
    protected bool $_debug = false;

    /**
     * Mail class to API service type mapping
     *
     * @var array
     */
    protected array $_mailClassMapping = [
        'USPS_GROUND_ADVANTAGE' => 'USPS_GROUND_ADVANTAGE',
        'PRIORITY_MAIL' => 'PRIORITY',
        'PRIORITY_MAIL_EXPRESS' => 'PRIORITY_MAIL_EXPRESS',
        'FIRST_CLASS_PACKAGE' => 'FIRST_CLASS',
        'PARCEL_SELECT' => 'PARCEL_SELECT',
        'MEDIA_MAIL' => 'MEDIA',
        'LIBRARY_MAIL' => 'LIBRARY',
        'BOUND_PRINTED_MATTER' => 'BPM',
    ];

    /**
     * Constructor
     *
     * @param null|Mage_Usa_Model_Shipping_Carrier_Usps_Rest_Client $client
     */
    public function __construct(?Mage_Usa_Model_Shipping_Carrier_Usps_Rest_Client $client = null)
    {
        $this->_client = $client;
        $this->_debug = (bool) Mage::getStoreConfig('carriers/usps/debug');
    }

    /**
     * Get REST client instance
     *
     * @return Mage_Usa_Model_Shipping_Carrier_Usps_Rest_Client
     */
    protected function _getClient(): Mage_Usa_Model_Shipping_Carrier_Usps_Rest_Client
    {
        if ($this->_client === null) {
            $this->_client = Mage::getModel('usa/shipping_carrier_usps_rest_client');

            $baseUrl = Mage::getStoreConfig('carriers/usps/gateway_url');
            if ($baseUrl) {
                $this->_client->setBaseUrl($baseUrl);
            }

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
     * Check if delivery estimates feature is enabled
     *
     * @param  null|int $storeId Store ID
     * @return bool
     */
    public function isEnabled(?int $storeId = null): bool
    {
        return (bool) Mage::getStoreConfig('carriers/usps/show_delivery_estimates', $storeId)
            && Mage::getStoreConfig('carriers/usps/active', $storeId);
    }

    /**
     * Get delivery estimate for a single mail class
     *
     * @param  string      $originZip  5-digit origin ZIP code
     * @param  string      $destZip    5-digit destination ZIP code
     * @param  string      $mailClass  Mail class code
     * @param  null|string $acceptDate Acceptance date (Y-m-d format)
     * @return null|array
     */
    public function getEstimate(string $originZip, string $destZip, string $mailClass, ?string $acceptDate = null): ?array
    {
        $estimates = $this->getEstimates($originZip, $destZip, [$mailClass], $acceptDate);
        return $estimates[$mailClass] ?? null;
    }

    /**
     * Get delivery estimates for multiple mail classes
     *
     * @param  string      $originZip   5-digit origin ZIP code
     * @param  string      $destZip     5-digit destination ZIP code
     * @param  array       $mailClasses Array of mail class codes
     * @param  null|string $acceptDate  Acceptance date (Y-m-d format)
     * @return array       Keyed by mail class code
     */
    public function getEstimates(string $originZip, string $destZip, array $mailClasses, ?string $acceptDate = null): array
    {
        if ($mailClasses === []) {
            return [];
        }

        $originZip = $this->_cleanZip($originZip);
        $destZip = $this->_cleanZip($destZip);
        $acceptDate = $acceptDate ?: \Carbon\Carbon::now()->format('Y-m-d');

        $estimates = [];
        $uncached = [];

        // Check cache first
        foreach ($mailClasses as $mailClass) {
            $cacheKey = $this->_getCacheKey($originZip, $destZip, $mailClass, $acceptDate);
            $cached = Mage::app()->getCache()->load($cacheKey);

            if ($cached !== false) {
                $decoded = json_decode($cached, true);
                if (is_array($decoded)) {
                    $estimates[$mailClass] = $decoded;
                } else {
                    $uncached[] = $mailClass;
                }
            } else {
                $uncached[] = $mailClass;
            }
        }

        // Fetch uncached estimates from API
        if ($uncached !== []) {
            $fetched = $this->_fetchEstimatesFromApi($originZip, $destZip, $uncached, $acceptDate);

            foreach ($fetched as $mailClass => $estimate) {
                $estimates[$mailClass] = $estimate;

                // Cache the result
                $cacheKey = $this->_getCacheKey($originZip, $destZip, $mailClass, $acceptDate);
                Mage::app()->getCache()->save(
                    json_encode($estimate),
                    $cacheKey,
                    ['usps_service_standards'],
                    self::CACHE_TTL,
                );
            }
        }

        return $estimates;
    }

    /**
     * Fetch estimates from USPS Service Standards API
     *
     * @param  string $originZip
     * @param  string $destZip
     * @param  string $acceptDate
     * @return array
     */
    protected function _fetchEstimatesFromApi(string $originZip, string $destZip, array $mailClasses, string $acceptDate): array
    {
        $estimates = [];
        $client = $this->_getClient();

        foreach ($mailClasses as $mailClass) {
            $apiMailClass = $this->_mapToApiMailClass($mailClass);
            if (!$apiMailClass) {
                $this->_debug([
                    'action' => 'service_standards_skip',
                    'mail_class' => $mailClass,
                    'reason' => 'No API mapping',
                ]);
                continue;
            }

            $endpoint = sprintf(
                'service-standards/v3/estimates?originZIPCode=%s&destinationZIPCode=%s&mailClass=%s&acceptanceDate=%s',
                urlencode($originZip),
                urlencode($destZip),
                urlencode($apiMailClass),
                urlencode($acceptDate),
            );

            $response = $client->getWithRetry($endpoint, true, 2);

            $this->_debug([
                'action' => 'service_standards_request',
                'endpoint' => $endpoint,
                'response' => $response,
            ]);

            if ($response['success'] && isset($response['data']) && $response['data'] !== []) {
                $estimate = $this->_parseEstimateResponse($response['data']);
                if ($estimate) {
                    $estimates[$mailClass] = $estimate;
                }
            }
        }

        return $estimates;
    }

    /**
     * Parse USPS Service Standards API response
     *
     * @param  array      $data API response data
     * @return null|array Parsed estimate or null
     */
    protected function _parseEstimateResponse(array $data): ?array
    {
        $standard = $data[0] ?? $data;

        $deliveryDays = $standard['serviceStandardMessage'] ?? null;
        $scheduledDate = $standard['scheduledDeliveryDate'] ?? null;

        if ($scheduledDate) {
            $today = \Carbon\Carbon::now();
            $delivery = new DateTime($scheduledDate);
            $diff = $today->diff($delivery);
            $days = $diff->days;

            return [
                'min_days' => $days,
                'max_days' => $days,
                'scheduled_date' => $scheduledDate,
                'display' => $this->_formatDeliveryDisplay($days, $days, $scheduledDate),
            ];
        }

        if ($deliveryDays && preg_match('/(\d+)(?:-(\d+))?\s*(?:day|business day)/i', $deliveryDays, $matches)) {
            $minDays = (int) $matches[1];
            $maxDays = isset($matches[2]) ? (int) $matches[2] : $minDays;

            return [
                'min_days' => $minDays,
                'max_days' => $maxDays,
                'scheduled_date' => null,
                'display' => $this->_formatDeliveryDisplay($minDays, $maxDays, null),
            ];
        }

        if (isset($standard['deliveryDays']) && is_numeric($standard['deliveryDays'])) {
            $days = (int) $standard['deliveryDays'];
            return [
                'min_days' => $days,
                'max_days' => $days,
                'scheduled_date' => null,
                'display' => $this->_formatDeliveryDisplay($days, $days, null),
            ];
        }

        return null;
    }

    /**
     * Format delivery estimate for display
     *
     * @param  int         $minDays
     * @param  int         $maxDays
     * @param  null|string $scheduledDate
     * @return string
     */
    protected function _formatDeliveryDisplay(int $minDays, int $maxDays, ?string $scheduledDate): string
    {
        $helper = Mage::helper('usa');

        if ($scheduledDate) {
            try {
                $date = new DateTime($scheduledDate);
                return $helper->__('Arrives by %s', $date->format('M j'));
            } catch (Exception) {
                // Fall through to days display
            }
        }

        if ($minDays === $maxDays) {
            if ($minDays === 1) {
                return $helper->__('1 business day');
            }

            return $helper->__('%d business days', $minDays);
        }

        return $helper->__('%d-%d business days', $minDays, $maxDays);
    }

    /**
     * Map internal mail class to USPS API mail class
     *
     * @param  string      $mailClass
     * @return null|string
     */
    protected function _mapToApiMailClass(string $mailClass): ?string
    {
        $baseClass = preg_replace('/_(SP|FE|FB|PL|FS|FP|FA|LFR|LR)$/', '', $mailClass);

        if (isset($this->_mailClassMapping[$baseClass])) {
            return $this->_mailClassMapping[$baseClass];
        }

        foreach ($this->_mailClassMapping as $key => $value) {
            if (stripos($baseClass, (string) $key) !== false) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Generate cache key for service standards
     *
     * @param  string $originZip
     * @param  string $destZip
     * @param  string $mailClass
     * @param  string $acceptDate
     * @return string
     */
    protected function _getCacheKey(string $originZip, string $destZip, string $mailClass, string $acceptDate): string
    {
        return self::CACHE_KEY_PREFIX . hash('sha256', implode('_', [
            $originZip,
            $destZip,
            $mailClass,
            $acceptDate,
        ]));
    }

    /**
     * Clean ZIP code to 5 digits
     *
     * @param  string $zip
     * @return string
     */
    protected function _cleanZip(string $zip): string
    {
        return substr(preg_replace('/\D/', '', $zip), 0, 5);
    }

    /**
     * Debug logging
     *
     * @return void
     */
    protected function _debug(array $data): void
    {
        if (!$this->_debug) {
            return;
        }

        Mage::log(
            'USPS ServiceStandards: ' . json_encode($data),
            Zend_Log::DEBUG,
            'shipping_usps.log',
            true,
        );
    }
}
