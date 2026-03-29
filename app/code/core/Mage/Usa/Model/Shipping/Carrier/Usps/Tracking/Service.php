<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/**
 * USPS REST API Tracking Service
 *
 * Handles package tracking via USPS REST API endpoints.
 *
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_Usps_Tracking_Service
{
    /**
     * JSON content type for REST API
     */
    public const CONTENT_TYPE_JSON = 'application/json';

    /**
     * Authorization header prefix
     */
    public const AUTHORIZATION_BEARER = 'Bearer ';

    /**
     * REST API tracking endpoint
     */
    public const TRACKING_ENDPOINT = 'tracking/v3/tracking/';

    /**
     * Default error message
     */
    public const ERROR_TITLE_DEFAULT = 'Unable to retrieve tracking info right now.';

    /**
     * Rate result object
     */
    protected ?Mage_Shipping_Model_Tracking_Result $_result = null;

    /**
     * Carrier model instance
     */
    protected ?Mage_Usa_Model_Shipping_Carrier_Usps $_carrierModel = null;

    /**
     * Set carrier model for configuration access
     */
    public function setCarrierModel(Mage_Usa_Model_Shipping_Carrier_Usps $carrierModel): self
    {
        $this->_carrierModel = $carrierModel;
        return $this;
    }

    /**
     * Get tracking information via REST API
     *
     * @param array<int, string> $trackingNumbers Array of tracking numbers to look up
     * @param string             $accessToken     OAuth access token
     * @param string             $baseUrl         USPS REST API base URL
     */
    public function getRestTracking(array $trackingNumbers, string $accessToken, string $baseUrl): ?Mage_Shipping_Model_Tracking_Result
    {
        if ($trackingNumbers === [] || $accessToken === '') {
            return null;
        }

        $this->_result = Mage::getModel('shipping/tracking_result');

        foreach ($trackingNumbers as $tracking) {
            $url = rtrim($baseUrl, '/') . '/' . self::TRACKING_ENDPOINT . urlencode($tracking);
            $queryString = '?' . http_build_query(['expand' => 'DETAIL']);

            $debugData = [
                'request' => [
                    'tracking_number' => $tracking,
                    'endpoint' => self::TRACKING_ENDPOINT . $tracking,
                    'expand' => 'DETAIL',
                ],
                '__pid' => getmypid(),
            ];
            $this->_debug($debugData);

            try {
                $headers = [
                    'Content-Type: ' . self::CONTENT_TYPE_JSON,
                    'Authorization: ' . self::AUTHORIZATION_BEARER . $accessToken,
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url . $queryString);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

                $jsonResponse = curl_exec($ch);

                if ($jsonResponse === false) {
                    $error = curl_error($ch);
                    curl_close($ch);
                    $this->_debug([
                        'error' => $error,
                        '__pid' => getmypid(),
                    ]);
                    $this->_setTrackingError($tracking, 'Network error: ' . $error);
                    continue;
                }

                curl_close($ch);

                // Log response
                $this->_debug([
                    'result' => $jsonResponse,
                    '__pid' => getmypid(),
                ]);

                $this->_parseRestTrackingResponse((string) $tracking, (string) $jsonResponse);
            } catch (Exception $e) {
                $this->_debug([
                    'error' => $e->getMessage(),
                    '__pid' => getmypid(),
                ]);
                $this->_setTrackingError($tracking, self::ERROR_TITLE_DEFAULT);
            }
        }

        return $this->_result;
    }

    /**
     * Parse REST tracking response
     *
     * @param string $trackingValue Tracking number
     * @param string $jsonResponse  JSON response from REST API
     */
    protected function _parseRestTrackingResponse(string $trackingValue, string $jsonResponse): void
    {
        $errorTitle = self::ERROR_TITLE_DEFAULT;
        $resultArr = [];
        $packageProgress = [];

        if (!$jsonResponse) {
            $this->_setTrackingError($trackingValue, $errorTitle);
            return;
        }

        $responseData = json_decode($jsonResponse, true);

        if (!is_array($responseData)) {
            $this->_setTrackingError($trackingValue, $errorTitle);
            return;
        }

        // Check for error response
        if (isset($responseData['error'])) {
            $errorMessage = $responseData['error']['message'] ?? $errorTitle;
            $this->_setTrackingError($trackingValue, $errorMessage);
            return;
        }

        // Check for valid tracking response
        if (!isset($responseData['trackingNumber'])) {
            $this->_setTrackingError($trackingValue, $errorTitle);
            return;
        }

        // Parse tracking events
        $trackingEvents = $responseData['trackingEvents'] ?? [];

        if (is_array($trackingEvents)) {
            foreach ($trackingEvents as $activityTag) {
                $this->_processActivityRestTagInfo($activityTag, $packageProgress);
            }

            $resultArr['track_summary'] = $responseData['statusSummary'] ?? '';
            $resultArr['progressdetail'] = $packageProgress;
        }

        // Set successful tracking data
        if ($resultArr !== []) {
            $tracking = Mage::getModel('shipping/tracking_result_status');
            $tracking->setCarrier('usps');
            $tracking->setCarrierTitle($this->_getCarrierTitle());
            $tracking->setTracking($trackingValue);
            $tracking->addData($resultArr);
            if ($this->_result) {
                $this->_result->append($tracking);
            }
        } else {
            $this->_setTrackingError($trackingValue, $errorTitle);
        }
    }

    /**
     * Process activity tag from REST API response
     *
     * @param array<string, mixed>              $activityTag     Event data from response
     * @param array<int, array<string, string>> $packageProgress Reference to progress array
     */
    protected function _processActivityRestTagInfo(array $activityTag, array &$packageProgress): void
    {
        $eventTimestamp = null;

        if (isset($activityTag['eventTimestamp'])) {
            try {
                $eventTimestamp = new DateTime($activityTag['eventTimestamp']);
            } catch (Exception) {
                $eventTimestamp = \Carbon\Carbon::now();
            }
        } else {
            $eventTimestamp = \Carbon\Carbon::now();
        }

        $date = $eventTimestamp->format('Y-m-d');
        $time = $eventTimestamp->format('H:i:s');

        // Build location string
        $locationParts = [];
        if (isset($activityTag['eventCity'])) {
            $locationParts[] = $activityTag['eventCity'];
        }

        if (isset($activityTag['eventState'])) {
            $locationParts[] = $activityTag['eventState'];
        }

        if (isset($activityTag['eventZIP'])) {
            $locationParts[] = $activityTag['eventZIP'];
        }

        if (isset($activityTag['eventCountry'])) {
            $locationParts[] = $activityTag['eventCountry'];
        }

        $packageProgress[] = [
            'activity' => (string) ($activityTag['eventType'] ?? ''),
            'deliverydate' => $date,
            'deliverytime' => $time,
            'deliverylocation' => implode(', ', $locationParts),
        ];
    }

    /**
     * Set tracking error result
     *
     * @param string $trackingValue Tracking number
     * @param string $errorMessage  Error message
     */
    protected function _setTrackingError(string $trackingValue, string $errorMessage): void
    {
        $error = Mage::getModel('shipping/tracking_result_error');
        $error->setCarrier('usps');
        $error->setCarrierTitle($this->_getCarrierTitle());
        $error->setTracking($trackingValue);
        $error->setErrorMessage($errorMessage);

        if ($this->_result) {
            $this->_result->append($error);
        }
    }

    /**
     * Get carrier title from config
     */
    protected function _getCarrierTitle(): string
    {
        if ($this->_carrierModel) {
            return $this->_carrierModel->getConfigData('title') ?: 'USPS';
        }

        return 'USPS';
    }

    /**
     * Debug logging
     *
     * @param array<string, mixed> $debugData
     */
    protected function _debug(array $debugData): void
    {
        if ($this->_carrierModel && $this->_carrierModel->getConfigFlag('debug')) {
            Mage::getModel('core/log_adapter', 'usps.log')->log($debugData);
        }
    }

    /**
     * Get tracking response summary for display
     *
     * @return string
     */
    public function getTrackingSummary()
    {
        $statuses = '';

        if ($this->_result instanceof Mage_Shipping_Model_Tracking_Result) {
            $trackingData = $this->_result->getAllTrackings();
            if ($trackingData) {
                foreach ($trackingData as $tracking) {
                    $data = $tracking->getAllData();
                    if (isset($data['track_summary']) && $data['track_summary'] !== '') {
                        $statuses .= Mage::helper('usa')->__($data['track_summary']);
                    } else {
                        $statuses .= Mage::helper('usa')->__('Empty response');
                    }
                }
            }
        }

        return $statuses ?: (string) Mage::helper('usa')->__('Empty response');
    }
}
