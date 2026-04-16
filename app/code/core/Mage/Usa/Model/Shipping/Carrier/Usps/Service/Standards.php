<?php

declare(strict_types=1);

use Carbon\Carbon;

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
class Mage_Usa_Model_Shipping_Carrier_Usps_Service_Standards extends Mage_Usa_Model_Shipping_Carrier_Usps_AbstractService
{
    /**
     * Cache key prefix for service standards
     */
    public const CACHE_KEY_PREFIX = 'usps_service_standards_';

    /**
     * Cache TTL for service standards (24 hours default - these don't change often)
     */
    public const CACHE_TTL = 86400;

    protected string $_debugPrefix = 'USPS ServiceStandards';

    /**
     * Mail class to API service type mapping
     *
     * @var array<string, string>
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
     * Check if delivery estimates feature is enabled
     *
     * @param null|int $storeId Store ID
     */
    public function isEnabled(?int $storeId = null): bool
    {
        return Mage::getStoreConfigFlag('carriers/usps/show_delivery_estimates', $storeId)
            && Mage::getStoreConfigFlag('carriers/usps/active', $storeId);
    }

    /**
     * Get delivery estimate for a single mail class
     *
     * @param  string                    $originZip  5-digit origin ZIP code
     * @param  string                    $destZip    5-digit destination ZIP code
     * @param  string                    $mailClass  Mail class code
     * @param  null|string               $acceptDate Acceptance date (Y-m-d format)
     * @return null|array<string, mixed>
     */
    public function getEstimate(string $originZip, string $destZip, string $mailClass, ?string $acceptDate = null): ?array
    {
        $estimates = $this->getEstimates($originZip, $destZip, [$mailClass], $acceptDate);
        return $estimates[$mailClass] ?? null;
    }

    /**
     * Get delivery estimates for multiple mail classes
     *
     * @param  string                              $originZip   5-digit origin ZIP code
     * @param  string                              $destZip     5-digit destination ZIP code
     * @param  array<int, string>                  $mailClasses Array of mail class codes
     * @param  null|string                         $acceptDate  Acceptance date (Y-m-d format)
     * @return array<string, array<string, mixed>> Keyed by mail class code
     */
    public function getEstimates(string $originZip, string $destZip, array $mailClasses, ?string $acceptDate = null): array
    {
        if ($mailClasses === []) {
            return [];
        }

        $originZip = $this->_cleanZip($originZip);
        $destZip = $this->_cleanZip($destZip);
        $acceptDate = $acceptDate ?? Mage::helper('core/clock')->now()->format('Y-m-d');

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
     * @param  array<int, string>                  $mailClasses
     * @return array<string, array<string, mixed>>
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
     * @param  array<string, mixed>      $data API response data
     * @return null|array<string, mixed> Parsed estimate or null
     */
    protected function _parseEstimateResponse(array $data): ?array
    {
        $standard = $data[0] ?? $data;

        $deliveryDays = $standard['serviceStandardMessage'] ?? null;
        $scheduledDate = $standard['scheduledDeliveryDate'] ?? null;

        if ($scheduledDate) {
            $today = Carbon::now();
            $delivery = new DateTime($scheduledDate);
            $diff = $today->diff($delivery);
            $days = (int) $diff->days;

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
     */
    protected function _mapToApiMailClass(string $mailClass): ?string
    {
        $baseClass = preg_replace('/_(SP|FE|FB|PL|FS|FP|FA|LFR|LR)$/', '', $mailClass) ?? $mailClass;

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
     */
    protected function _cleanZip(string $zip): string
    {
        return substr(preg_replace('/\D/', '', $zip) ?? '', 0, 5);
    }

}
