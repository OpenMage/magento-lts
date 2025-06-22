<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

use PaypalServerSdkLib\Http\ApiResponse;

/**
 * PayPal API Helper
 *
 * Provides utility methods for logging and validation related to PayPal API interactions.
 */
class Mage_Paypal_Model_Api_Helper
{
    /**
     * @var Mage_Paypal_Helper_Data
     */
    protected $_helper;

    /**
     * Initializes the helper with its dependencies.
     */
    public function __construct()
    {
        $this->_helper = Mage::helper('paypal');
    }

    /**
     * Logs debug information for a PayPal API request if debugging is enabled.
     *
     * @param string $action Action being performed (e.g., 'Create Order').
     * @param Mage_Sales_Model_Order|Mage_Sales_Model_Quote $quote Quote or order object.
     * @param mixed $request Request object or data sent to the API.
     * @param ApiResponse|null $response API response, if available.
     * @return void
     */
    public function logDebug(
        string $action,
        Mage_Sales_Model_Order|Mage_Sales_Model_Quote $quote,
        mixed $request,
        ?ApiResponse $response = null
    ): void {
        if (Mage::getStoreConfigFlag('payment/paypal/debug')) {
            $requestData = '';

            if (is_object($request) && method_exists($request, 'jsonSerialize')) {
                $requestData = json_encode($request->jsonSerialize());
            } elseif (is_object($request)) {
                $requestData = json_encode($request);
            } elseif (is_string($request)) {
                $requestData = $request;
            } elseif (is_array($request)) {
                $requestData = json_encode($request);
            }
            $debug = Mage::getModel('paypal/debug');
            if ($quote instanceof Mage_Sales_Model_Quote) {
                $debug->setQuoteId($quote->getId());
            } elseif ($quote instanceof Mage_Sales_Model_Order) {
                $debug->setIncrementId($quote->getIncrementId());
            }

            $debug->setAction($action)
                ->setRequestBody($requestData);
            Mage::log($response, null, 'paypal.log');
            if ($response instanceof ApiResponse) {
                $result = $response->getResult();
                if ($response->isError()) {
                    $debug->setTransactionId($result['debug_id'])
                        ->setResponseBody(json_encode($result));
                } else {
                    $debug->setTransactionId($response->getResult()->getId() ?? null)
                        ->setResponseBody(json_encode($response->getResult()));
                }
            }
            $debug->save();
        }
    }

    /**
     * Logs an error message and exception details if debugging is enabled.
     *
     * @param string $message The error message.
     * @param Exception $exception The exception object.
     * @return void
     */
    public function logError(string $message, Exception $exception): void
    {
        if (Mage::getStoreConfigFlag('payment/paypal/debug')) {
            $errorData = [
                'message' => $message,
                'error' => $exception->getMessage(),
            ];

            if ($exception instanceof Mage_Paypal_Model_Exception) {
                $errorData['debug_data'] = $exception->getDebugData();
            }

            Mage::log($message, Zend_Log::ERR, 'paypal.log', true);
        }
    }
}
