<?php

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
    const CONTENT_TYPE_JSON = 'application/json';

    /**
     * Authorization header prefix
     */
    const AUTHORIZATION_BEARER = 'Bearer ';

    /**
     * REST API tracking endpoint
     */
    const TRACKING_ENDPOINT = 'tracking/v3/tracking/';

    /**
     * Default error message
     */
    const ERROR_TITLE_DEFAULT = 'Unable to retrieve tracking info right now.';

    /**
     * Rate result object
     *
     * @var Mage_Shipping_Model_Tracking_Result|null
     */
    protected $_result = null;

    /**
     * Carrier model instance
     *
     * @var Mage_Usa_Model_Shipping_Carrier_Usps|null
     */
    protected $_carrierModel = null;

    /**
     * Set carrier model for configuration access
     *
     * @param Mage_Usa_Model_Shipping_Carrier_Usps $carrierModel
     * @return Mage_Usa_Model_Shipping_Carrier_Usps_Tracking_Service
     */
    public function setCarrierModel(Mage_Usa_Model_Shipping_Carrier_Usps $carrierModel)
    {
        $this->_carrierModel = $carrierModel;
        return $this;
    }

    /**
     * Get tracking information via REST API
     *
     * @param array $trackingNumbers Array of tracking numbers to look up
     * @param string $accessToken OAuth access token
     * @param string $baseUrl USPS REST API base URL
     * @return Mage_Shipping_Model_Tracking_Result|null
     */
    public function getRestTracking(array $trackingNumbers, $accessToken, $baseUrl)
    {
        if ($trackingNumbers === [] || $trackingNumbers === null || $accessToken === '' || $accessToken === null) {
            return null;
        }

        $this->_result = Mage::getModel('shipping/tracking_result');

        foreach ($trackingNumbers as $tracking) {
            $url = rtrim($baseUrl, '/') . '/' . self::TRACKING_ENDPOINT . urlencode($tracking);
            $queryString = '?' . http_build_query(array('expand' => 'DETAIL'));

            $debugData = array(
                'request' => array(
                    'tracking_number' => $tracking,
                    'endpoint' => self::TRACKING_ENDPOINT . $tracking,
                    'expand' => 'DETAIL',
                ),
                '__pid' => getmypid(),
            );
            $this->_debug($debugData);

            try {
                $headers = array(
                    'Content-Type: ' . self::CONTENT_TYPE_JSON,
                    'Authorization: ' . self::AUTHORIZATION_BEARER . $accessToken,
                );

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url . $queryString);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

                $jsonResponse = curl_exec($ch);

                if ($jsonResponse === false) {
                    $error = curl_error($ch);
                    curl_close($ch);
                    $this->_debug(array(
                        'error' => $error,
                        '__pid' => getmypid(),
                    ));
                    $this->_setTrackingError($tracking, 'Network error: ' . $error);
                    continue;
                }

                curl_close($ch);

                // Log response
                $this->_debug(array(
                    'result' => $jsonResponse,
                    '__pid' => getmypid(),
                ));

                $this->_parseRestTrackingResponse((string) $tracking, $jsonResponse);
            } catch (Exception $e) {
                $this->_debug(array(
                    'error' => $e->getMessage(),
                    '__pid' => getmypid(),
                ));
                $this->_setTrackingError($tracking, self::ERROR_TITLE_DEFAULT);
            }
        }

        return $this->_result;
    }

    /**
     * Parse REST tracking response
     *
     * @param string $trackingValue Tracking number
     * @param string $jsonResponse JSON response from REST API
     * @return void
     */
    protected function _parseRestTrackingResponse($trackingValue, $jsonResponse)
    {
        $errorTitle = self::ERROR_TITLE_DEFAULT;
        $resultArr = array();
        $packageProgress = array();

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
            $errorMessage = isset($responseData['error']['message']) ? $responseData['error']['message'] : $errorTitle;
            $this->_setTrackingError($trackingValue, $errorMessage);
            return;
        }

        // Check for valid tracking response
        if (!isset($responseData['trackingNumber'])) {
            $this->_setTrackingError($trackingValue, $errorTitle);
            return;
        }

        // Parse tracking events
        $trackingEvents = isset($responseData['trackingEvents']) ? $responseData['trackingEvents'] : array();

        if (is_array($trackingEvents)) {
            foreach ($trackingEvents as $activityTag) {
                $this->_processActivityRestTagInfo($activityTag, $packageProgress);
            }

            $resultArr['track_summary'] = isset($responseData['statusSummary']) ? $responseData['statusSummary'] : '';
            $resultArr['progressdetail'] = $packageProgress;
        }

        // Set successful tracking data
        if ($resultArr !== [] && $resultArr !== null) {
            $tracking = Mage::getModel('shipping/tracking_result_status');
            $tracking->setCarrier('usps');
            $tracking->setCarrierTitle($this->_getCarrierTitle());
            $tracking->setTracking($trackingValue);
            $tracking->addData($resultArr);
            $this->_result->append($tracking);
        } else {
            $this->_setTrackingError($trackingValue, $errorTitle);
        }
    }

    /**
     * Process activity tag from REST API response
     *
     * @param array $activityTag Event data from response
     * @param array $packageProgress Reference to progress array
     * @return void
     */
    protected function _processActivityRestTagInfo(array $activityTag, array &$packageProgress)
    {
        $eventTimestamp = null;

        if (isset($activityTag['eventTimestamp'])) {
            try {
                $eventTimestamp = new DateTime($activityTag['eventTimestamp']);
            } catch (Exception $e) {
                $eventTimestamp = new DateTime();
            }
        } else {
            $eventTimestamp = new DateTime();
        }

        $date = $eventTimestamp->format('Y-m-d');
        $time = $eventTimestamp->format('H:i:s');

        // Build location string
        $locationParts = array();
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

        $packageProgress[] = array(
            'activity' => (string) (isset($activityTag['eventType']) ? $activityTag['eventType'] : ''),
            'deliverydate' => $date,
            'deliverytime' => $time,
            'deliverylocation' => implode(', ', $locationParts),
        );
    }

    /**
     * Set tracking error result
     *
     * @param string $trackingValue Tracking number
     * @param string $errorMessage Error message
     * @return void
     */
    protected function _setTrackingError($trackingValue, $errorMessage)
    {
        $error = Mage::getModel('shipping/tracking_result_error');
        $error->setCarrier('usps');
        $error->setCarrierTitle($this->_getCarrierTitle());
        $error->setTracking($trackingValue);
        $error->setErrorMessage($errorMessage);
        $this->_result->append($error);
    }

    /**
     * Get carrier title from config
     *
     * @return string
     */
    protected function _getCarrierTitle()
    {
        if ($this->_carrierModel) {
            return $this->_carrierModel->getConfigData('title') ?: 'USPS';
        }
        return 'USPS';
    }

    /**
     * Debug logging
     *
     * @param array $debugData
     * @return void
     */
    protected function _debug(array $debugData)
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
                    if (isset($data['track_summary']) && $data['track_summary'] !== '' && $data['track_summary'] !== null) {
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
