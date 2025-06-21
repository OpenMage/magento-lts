<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

class Mage_Paypal_Model_Api_Helper
{
    protected $_validator;
    protected $_helper;

    public function __construct()
    {
        $this->_validator = Mage::getSingleton('paypal/validator_payment');
        $this->_helper = Mage::helper('paypal');
    }

    public function validatePurchaseUnit($purchaseUnit)
    {
        if (empty($purchaseUnit['amount']) || empty($purchaseUnit['amount']['value'])) {
            throw new Mage_Paypal_Model_Exception('Invalid purchase unit: missing amount');
        }

        if (empty($purchaseUnit['reference_id'])) {
            throw new Mage_Paypal_Model_Exception('Invalid purchase unit: missing reference ID');
        }

        return true;
    }

    /**
     * Log debug information
     *
     * @param string $action Action being performed
     * @param Mage_Sales_Model_Order|Mage_Sales_Model_Quote $quote Quote or order object
     * @param mixed $request Request object or data
     * @param PaypalServerSdkLib\Http\ApiResponse|null $response API response (optional)
     * @return void
     */
    public function logDebug($action, $quote, $request, $response = null)
    {
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
            if ($response instanceof PaypalServerSdkLib\Http\ApiResponse) {
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
     * Log error information
     *
     * @param string $message Error message
     * @param Exception $exception Exception object
     * @return void
     */
    public function logError($message, $exception)
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
