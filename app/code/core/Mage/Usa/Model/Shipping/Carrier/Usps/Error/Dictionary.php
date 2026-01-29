<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/**
 * USPS REST API Error Dictionary
 *
 * Translates USPS REST API error codes and messages to user-friendly messages.
 * Based on USPS REST API v3 error specifications.
 *
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_Usps_Error_Dictionary
{
    /**
     * HTTP status code to message mapping
     *
     * @var array
     */
    protected $_httpStatusMessages = array(
        400 => 'Invalid request. Please verify package details and try again.',
        401 => 'USPS authentication failed. Please check your API credentials in admin settings.',
        403 => 'Access denied. Your USPS account may not have permission for this operation.',
        404 => 'USPS service endpoint not found. Please contact support.',
        405 => 'Invalid API request method. Please contact support.',
        409 => 'Conflicting request. The shipment may already exist.',
        415 => 'Unsupported content type. Please contact support.',
        422 => 'Unable to process request. Please check package dimensions and weight.',
        429 => 'USPS rate limit exceeded. Please try again in a few minutes.',
        500 => 'USPS service temporarily unavailable. Please try again later.',
        502 => 'USPS gateway error. Please try again later.',
        503 => 'USPS service is temporarily down for maintenance. Please try again later.',
        504 => 'USPS gateway timeout. Please try again later.',
    );

    /**
     * USPS API error code to message mapping
     *
     * Error codes from USPS REST API responses (error.code field)
     *
     * @var array
     */
    protected $_apiErrorCodes = array(
        // Authentication & Authorization
        'INVALID_TOKEN' => 'USPS authentication token is invalid or expired. Please check your credentials.',
        'TOKEN_EXPIRED' => 'USPS authentication token has expired. Please try again.',
        'UNAUTHORIZED' => 'Your USPS account is not authorized for this operation.',
        'INVALID_CREDENTIALS' => 'Invalid USPS API credentials. Please verify your Client ID and Client Secret.',

        // Rate/Pricing Errors
        'INVALID_MAIL_CLASS' => 'The selected shipping method is not available for this shipment.',
        'INVALID_MAIL_DIMENSION' => 'Package dimensions exceed USPS limits or are invalid.',
        'INVALID_WEIGHT' => 'Package weight exceeds USPS limits or is invalid.',
        'INVALID_DESTINATION' => 'The destination address is not serviceable by the selected method.',
        'INVALID_ORIGIN' => 'The origin ZIP code is invalid or not recognized.',
        'NO_RATES_AVAILABLE' => 'No shipping rates available for this destination and package configuration.',
        'RATE_NOT_FOUND' => 'The requested shipping rate was not found.',

        // Address Errors
        'INVALID_ADDRESS' => 'The provided address could not be verified by USPS.',
        'INVALID_ZIP_CODE' => 'The ZIP code is invalid or not recognized.',
        'INVALID_STATE' => 'The state code is invalid or not recognized.',
        'INVALID_CITY' => 'The city name is invalid or not recognized.',
        'ADDRESS_NOT_FOUND' => 'The address could not be found in the USPS database.',

        // Label/Shipment Errors
        'INVALID_LABEL_SIZE' => 'The requested label size is not supported.',
        'INVALID_LABEL_FORMAT' => 'The requested label format is not supported.',
        'DUPLICATE_TRACKING' => 'This tracking number has already been used.',
        'SHIPMENT_NOT_FOUND' => 'The requested shipment was not found.',
        'LABEL_ALREADY_CANCELLED' => 'This shipping label has already been cancelled.',
        'LABEL_EXPIRED' => 'This shipping label has expired and cannot be used.',

        // Payment/Account Errors
        'INSUFFICIENT_FUNDS' => 'Insufficient funds in your USPS account. Please add funds and try again.',
        'INVALID_ACCOUNT' => 'The USPS account number is invalid or not recognized.',
        'ACCOUNT_SUSPENDED' => 'Your USPS account has been suspended. Please contact USPS.',
        'INVALID_PERMIT' => 'The permit number is invalid or not recognized.',
        'INVALID_CRID' => 'The Customer Registration ID (CRID) is invalid.',
        'INVALID_MID' => 'The Mailer ID (MID) is invalid.',

        // Package Errors
        'EXCEEDS_MAX_WEIGHT' => 'Package weight exceeds the maximum allowed for this service.',
        'EXCEEDS_MAX_DIMENSIONS' => 'Package dimensions exceed the maximum allowed for this service.',
        'HAZMAT_NOT_ALLOWED' => 'Hazardous materials are not allowed with the selected shipping method.',
        'INVALID_CONTENTS' => 'The package contents description is invalid or prohibited.',

        // International Errors
        'INVALID_CUSTOMS_FORM' => 'The customs form data is incomplete or invalid.',
        'INVALID_COUNTRY' => 'The destination country is not recognized or not serviceable.',
        'EXPORT_LICENSE_REQUIRED' => 'An export license is required for this shipment. Please provide AES/ITN.',
        'PROHIBITED_DESTINATION' => 'Shipping to this destination is currently prohibited.',
    );

    /**
     * Common error message patterns to user-friendly translations
     *
     * @var array
     */
    protected $_messagePatterns = array(
        '/mailClass.*invalid/i' => 'The selected shipping method is not available. Please choose a different method.',
        '/weight.*exceed/i' => 'Package weight exceeds USPS limits for this shipping method.',
        '/dimension.*exceed/i' => 'Package dimensions exceed USPS limits for this shipping method.',
        '/not.*serviceable/i' => 'This destination is not serviceable by the selected USPS method.',
        '/zip.*invalid/i' => 'Please check the destination ZIP code and try again.',
        '/authentication.*fail/i' => 'USPS authentication failed. Please contact support.',
        '/rate.*not.*found/i' => 'No shipping rates available for this configuration.',
        '/token.*expired/i' => 'USPS session expired. Please refresh and try again.',
        '/account.*not.*found/i' => 'USPS account configuration error. Please contact support.',
    );

    /**
     * Get user-friendly message for HTTP status code
     *
     * @param int $statusCode HTTP status code
     * @return string|null User-friendly message or null if not found
     */
    public function getHttpStatusMessage($statusCode)
    {
        $statusCode = (int) $statusCode;
        return isset($this->_httpStatusMessages[$statusCode]) ? $this->_httpStatusMessages[$statusCode] : null;
    }

    /**
     * Get user-friendly message for USPS API error code
     *
     * @param string $errorCode USPS API error code
     * @return string|null User-friendly message or null if not found
     */
    public function getApiErrorMessage($errorCode)
    {
        $errorCode = strtoupper(trim((string) $errorCode));
        return isset($this->_apiErrorCodes[$errorCode]) ? $this->_apiErrorCodes[$errorCode] : null;
    }

    /**
     * Translate API error message using pattern matching
     *
     * @param string $apiMessage Raw API error message
     * @return string|null User-friendly message
     */
    public function translateMessage($apiMessage)
    {
        $apiMessage = (string) $apiMessage;

        foreach ($this->_messagePatterns as $pattern => $translation) {
            if (preg_match($pattern, $apiMessage)) {
                return Mage::helper('usa')->__($translation);
            }
        }

        return null;
    }

    /**
     * Get user-friendly error message from USPS API response
     *
     * Attempts to translate the error using (in order):
     * 1. HTTP status code
     * 2. API error code (if present in response)
     * 3. Message pattern matching
     * 4. Falls back to generic carrier error message
     *
     * @param int $httpCode HTTP response code
     * @param array|null $responseData Decoded JSON response
     * @param string $fallbackMessage Default message if no translation found
     * @return string User-friendly error message
     */
    public function getErrorMessage($httpCode, $responseData = null, $fallbackMessage = null)
    {
        // Try HTTP status code first for common errors
        if ($httpCode >= 500) {
            $message = $this->getHttpStatusMessage($httpCode);
            if ($message) {
                return Mage::helper('usa')->__($message);
            }
        }

        // Try to extract error code from response
        if (is_array($responseData)) {
            // Check for error.code format
            if (isset($responseData['error']['code'])) {
                $message = $this->getApiErrorMessage($responseData['error']['code']);
                if ($message) {
                    return Mage::helper('usa')->__($message);
                }
            }

            // Check for errors[0].code format
            if (isset($responseData['errors'][0]['code'])) {
                $message = $this->getApiErrorMessage($responseData['errors'][0]['code']);
                if ($message) {
                    return Mage::helper('usa')->__($message);
                }
            }

            // Try pattern matching on error message
            $apiMessage = null;
            if (isset($responseData['error']['message'])) {
                $apiMessage = $responseData['error']['message'];
            } elseif (isset($responseData['errors'][0]['message'])) {
                $apiMessage = $responseData['errors'][0]['message'];
            } elseif (isset($responseData['message'])) {
                $apiMessage = $responseData['message'];
            }

            if ($apiMessage) {
                $message = $this->translateMessage($apiMessage);
                if ($message) {
                    return $message;
                }
            }
        }

        // Try HTTP status for 4xx errors
        if ($httpCode >= 400 && $httpCode < 500) {
            $message = $this->getHttpStatusMessage($httpCode);
            if ($message) {
                return Mage::helper('usa')->__($message);
            }
        }

        // Return fallback or generic message
        return $fallbackMessage ?: Mage::helper('usa')->__(
            'Unable to retrieve shipping rates from USPS. Please try again or contact support.'
        );
    }

    /**
     * Check if error is transient and should be retried
     *
     * @param int $httpCode HTTP response code
     * @param array|null $responseData Decoded JSON response
     * @return bool True if error is transient and operation can be retried
     */
    public function isTransientError($httpCode, $responseData = null)
    {
        // Server errors are typically transient
        if ($httpCode >= 500) {
            return true;
        }

        // Rate limiting is transient
        if ($httpCode === 429) {
            return true;
        }

        // Token expired is transient (can refresh)
        if (is_array($responseData)) {
            $errorCode = '';
            if (isset($responseData['error']['code'])) {
                $errorCode = $responseData['error']['code'];
            } elseif (isset($responseData['errors'][0]['code'])) {
                $errorCode = $responseData['errors'][0]['code'];
            }
            if (in_array(strtoupper($errorCode), array('TOKEN_EXPIRED', 'INVALID_TOKEN'))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all HTTP status messages for reference
     *
     * @return array
     */
    public function getAllHttpStatusMessages()
    {
        return $this->_httpStatusMessages;
    }

    /**
     * Get all API error codes for reference
     *
     * @return array
     */
    public function getAllApiErrorCodes()
    {
        return $this->_apiErrorCodes;
    }
}
