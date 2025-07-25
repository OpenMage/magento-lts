<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_PaypalUk
 */

/**
 * NVP API wrappers model
 *
 * @package    Mage_PaypalUk
 */
class Mage_PaypalUk_Model_Api_Nvp extends Mage_Paypal_Model_Api_Nvp
{
    /**
     * Transaction types declaration
     *
     * @var mixed
     */
    public const TRXTYPE_AUTH_ONLY         = 'A';
    public const TRXTYPE_SALE              = 'S';
    public const TRXTYPE_CREDIT            = 'C';
    public const TRXTYPE_DELAYED_CAPTURE   = 'D';
    public const TRXTYPE_DELAYED_VOID      = 'V';

    /**
     * Tender definition
     *
     * @var mixed
     */
    public const TENDER_CC                 = 'C';
    public const TENDER_PAYPAL             = 'P';

    /**
     * Express Checkout Actions
     *
     * @var string
     */
    public const EXPRESS_SET               = 'S';
    public const EXPRESS_GET               = 'G';
    public const EXPRESS_DO_PAYMENT        = 'D';

    /**
     * Response codes definition
     *
     * @var mixed
     */
    public const RESPONSE_CODE_APPROVED = 0;
    public const RESPONSE_CODE_FRAUD = 126;

    /**
     * Capture types (make authorization close or remain open)
     *
     * @var string
     */
    protected $_captureTypeComplete = 'Y';
    protected $_captureTypeNotcomplete = 'N';

    /**
     * Global public interface map
     *
     * @var array
     */
    protected $_globalMap = [
        // each call
        'PARTNER' => 'partner',
        'VENDOR' => 'vendor',
        'USER' => 'user',
        'PWD' => 'password',
        'BUTTONSOURCE' => 'build_notation_code',
        'TENDER' => 'tender',
        // commands
        'RETURNURL' => 'return_url',
        'CANCELURL' => 'cancel_url',
        'INVNUM' => 'inv_num',
        'TOKEN' => 'token',
        'CORRELATIONID' => 'correlation_id',
        'CUSTIP' => 'ip_address',
        'NOTIFYURL' => 'notify_url',
        'NOTE' => 'note',
        // style settings
        'PAGESTYLE' => 'page_style',
        'HDRIMG' => 'hdrimg',
        'HDRBORDERCOLOR' => 'hdrbordercolor',
        'HDRBACKCOLOR' => 'hdrbackcolor',
        'PAYFLOWCOLOR' => 'payflowcolor',
        'LOCALECODE' => 'locale_code',

        // transaction info
        'PPREF' => 'paypal_transaction_id', //We need to store paypal trx id for correct IPN working
        'PAYMENTINFO_0_TRANSACTIONID' => 'paypal_transaction_id',
        'TRANSACTIONID' => 'paypal_transaction_id',
        'REFUNDTRANSACTIONID' => 'paypal_transaction_id',

        'PNREF' => 'transaction_id',
        'ORIGID' => 'authorization_id',
        'CAPTURECOMPLETE' => 'complete_type',
        'AMT' => 'amount',
        'AVSADDR' => 'address_verification',
        'AVSZIP' => 'postcode_verification',

        // payment/billing info
        'CURRENCY' => 'currency_code',
        'PAYMENTSTATUS' => 'payment_status',
        'PENDINGREASON' => 'pending_reason',
        'PAYERID' => 'payer_id',
        'PAYERSTATUS' => 'payer_status',
        'EMAIL' => 'email',
        // backwards compatibility
        'FIRSTNAME' => 'firstname',
        'LASTNAME' => 'lastname',
        // paypal direct credit card information
        'ACCT' => 'credit_card_number',
        'EXPDATE' => 'credit_card_expiration_date',
        'CVV2' => 'credit_card_cvv2',
        'CARDSTART' => 'maestro_solo_issue_date', // MMYY, including leading zero
        'CARDISSUE' => 'maestro_solo_issue_number',
        'CVV2MATCH' => 'cvv2_check_result',
        // cardinal centinel
        'AUTHSTATUS3DS' => 'centinel_authstatus',
        'MPIVENDOR3DS' => 'centinel_mpivendor',
        'CAVV' => 'centinel_cavv',
        'ECI' => 'centinel_eci',
        'XID' => 'centinel_xid',
        'VPAS' => 'centinel_vpas_result',
        'ECISUBMITTED3DS' => 'centinel_eci_result',
        'USERSELECTEDFUNDINGSOURCE' => 'funding_source',
    ];

    /**
     * Fields that should be replaced in debug with '***'
     *
     * @var array
     */
    protected $_debugReplacePrivateDataKeys = [
        'ACCT', 'EXPDATE', 'CVV2',
        'PARTNER', 'USER', 'VENDOR', 'PWD',
    ];

    /**
     * DoDirectPayment request/response map
     *
     * @var array
     */
    protected $_doDirectPaymentRequest = [
        'ACCT', 'EXPDATE', 'CVV2', 'CURRENCY', 'EMAIL', 'TENDER', 'NOTIFYURL',
        'AMT', 'CUSTIP', 'INVNUM',
        'CARDISSUE', 'CARDSTART',
        'AUTHSTATUS3DS', 'MPIVENDOR3DS', 'CAVV', 'ECI', 'XID',//cardinal centinel params
        'TAXAMT', 'FREIGHTAMT',
    ];
    protected $_doDirectPaymentResponse = [
        'PNREF', 'PPREF', 'CORRELATIONID', 'CVV2MATCH', 'AVSADDR', 'AVSZIP', 'PENDINGREASON',
    ];

    /**
     * DoCapture request/response map
     *
     * @var array
     */
    protected $_doCaptureRequest = ['ORIGID', 'CAPTURECOMPLETE', 'AMT', 'TENDER', 'NOTE', 'INVNUM'];
    protected $_doCaptureResponse = ['PNREF', 'PPREF'];

    /**
     * DoVoid request map
     *
     * @var array
     */
    protected $_doVoidRequest = ['ORIGID', 'NOTE', 'TENDER'];

    /**
     * Request map for each API call
     *
     * @var array
     */
    protected $_eachCallRequest = ['PARTNER', 'USER', 'VENDOR', 'PWD', 'BUTTONSOURCE'];

    /**
     * RefundTransaction request/response map
     *
     * @var array
     */
    protected $_refundTransactionRequest = ['ORIGID', 'TENDER'];
    protected $_refundTransactionResponse = ['PNREF', 'PPREF'];

    /**
     * SetExpressCheckout request/response map
     *
     * @var array
     */
    protected $_setExpressCheckoutRequest = [
        'TENDER', 'AMT', 'CURRENCY', 'RETURNURL', 'CANCELURL', 'INVNUM',
        'PAGESTYLE', 'HDRIMG', 'HDRBORDERCOLOR', 'HDRBACKCOLOR', 'PAYFLOWCOLOR', 'LOCALECODE',
        'USERSELECTEDFUNDINGSOURCE',
    ];
    protected $_setExpressCheckoutResponse = ['REPMSG', 'TOKEN'];

    /**
     * GetExpressCheckoutDetails request/response map
     *
     * @var array
     */
    protected $_getExpressCheckoutDetailsRequest = ['TENDER', 'TOKEN'];

    /**
     * DoExpressCheckoutPayment request/response map
     *
     * @var array
     */
    protected $_doExpressCheckoutPaymentRequest = [
        'TENDER', 'TOKEN', 'PAYERID', 'AMT', 'CURRENCY', 'CUSTIP', 'BUTTONSOURCE', 'NOTIFYURL',
    ];
    protected $_doExpressCheckoutPaymentResponse = [
        'PNREF', 'PPREF', 'REPMSG', 'AMT', 'PENDINGREASON',
        'CVV2MATCH', 'AVSADDR', 'AVSZIP', 'CORRELATIONID',
    ];

    /**
     * GetTransactionDetailsRequest
     *
     * @var array
     */
    protected $_getTransactionDetailsRequest = ['ORIGID', 'TENDER'];
    protected $_getTransactionDetailsResponse = [
        'PAYERID', 'FIRSTNAME', 'LASTNAME', 'TRANSACTIONID',
        'PARENTTRANSACTIONID', 'CURRENCYCODE', 'AMT', 'PAYMENTSTATUS',
    ];

    /**
     * Map for shipping address import/export (extends billing address mapper)
     *
     * @var array
     */
    protected $_shippingAddressMap = [
        'SHIPTOCOUNTRY' => 'country_id',
        'SHIPTOSTATE' => 'region',
        'SHIPTOCITY'    => 'city',
        'SHIPTOSTREET'  => 'street',
        'SHIPTOSTREET2' => 'street2',
        'SHIPTOZIP' => 'postcode',
        'SHIPTOPHONENUM' => 'telephone', // does not supported by PaypalUk
    ];

    /**
     * Map for billing address import/export
     *
     * @var array
     */
    protected $_billingAddressMap = [
        'BUSINESS' => 'company',
        'NOTETEXT' => 'customer_notes',
        'EMAIL' => 'email',
        'FIRSTNAME' => 'firstname',
        'LASTNAME' => 'lastname',
        'MIDDLENAME' => 'middlename',
        'SALUTATION' => 'prefix',
        'SUFFIX' => 'suffix',

        'COUNTRYCODE' => 'country_id', // iso-3166 two-character code
        'STATE'    => 'region',
        'CITY'     => 'city',
        'STREET'   => 'street',
        'STREET2'  => 'street2',
        'ZIP'      => 'postcode',
        'PHONENUM' => 'telephone',
    ];

    /**
     * Map for billing address to do request to PayPalUk
     *
     * @var array
     */
    protected $_billingAddressMapRequest = [
        'country_id' => 'COUNTRY',
    ];

    /**
     * Line items export mapping settings
     *
     * @var array
     */
    protected $_lineItemTotalExportMap = [
        Mage_Paypal_Model_Cart::TOTAL_TAX      => 'TAXAMT',
        Mage_Paypal_Model_Cart::TOTAL_SHIPPING => 'FREIGHTAMT',
    ];

    protected $_lineItemExportItemsFormat = [
        'name'   => 'L_NAME%d',
        'qty'    => 'L_QTY%d',
        'amount' => 'L_COST%d',
    ];

    /**
     * Payment information response specifically to be collected after some requests
     *
     * @var array
     */
    protected $_paymentInformationResponse = [
        'PAYERID', 'CORRELATIONID', 'ADDRESSID', 'ADDRESSSTATUS',
        'PAYMENTSTATUS', 'PENDINGREASON', 'PROTECTIONELIGIBILITY', 'EMAIL',
    ];

    /**
     * Required fields in the response
     *
     * @var array
     */
    protected $_requiredResponseParams = [
        self::DO_DIRECT_PAYMENT => ['RESULT', 'PNREF'],
    ];

    /**
     * API endpoint getter
     *
     * @return string
     */
    public function getApiEndpoint()
    {
        return sprintf('https://%spayflowpro.paypal.com/transaction', $this->_config->sandboxFlag ? 'pilot-' : '');
    }

    /**
     * Return PaypalUk partner based on config data
     *
     * @return string
     */
    public function getPartner()
    {
        return $this->_getDataOrConfig('partner');
    }

    /**
     * Return PaypalUk user based on config data
     *
     * @return string
     */
    public function getUser()
    {
        return $this->_getDataOrConfig('user');
    }

    /**
     * Return PaypalUk password based on config data
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->_getDataOrConfig('pwd');
    }

    /**
     * Return PaypalUk vendor based on config data
     *
     * @return string
     */
    public function getVendor()
    {
        return $this->_getDataOrConfig('vendor');
    }

    /**
     * Return PaypalUk tender based on config data
     *
     * @return string
     */
    public function getTender()
    {
        if ($this->_config->getMethodCode() == Mage_Paypal_Model_Config::METHOD_WPP_PE_EXPRESS) {
            return self::TENDER_PAYPAL;
        }
        return self::TENDER_CC;
    }

    /**
     * Override transaction id getting to process payflow accounts not assigned to paypal side
     *
     * @return string
     */
    public function getPaypalTransactionId()
    {
        if ($this->getData('paypal_transaction_id')) {
            return $this->getData('paypal_transaction_id');
        }
        return $this->getTransactionId();
    }

    /**
     * Add method to request array
     *
     * @param string $methodName
     * @param array $request
     * @return array
     */
    protected function _addMethodToRequest($methodName, $request)
    {
        $request['TRXTYPE'] = $this->_mapPaypalMethodName($methodName);
        if (!is_null($this->_getPaypalUkActionName($methodName))) {
            $request['ACTION'] = $this->_getPaypalUkActionName($methodName);
        }
        return $request;
    }

    /**
     * Return Payflow Edition
     *
     * @param string $methodName
     * @return string|null
     */
    protected function _getPaypalUkActionName($methodName)
    {
        return match ($methodName) {
            Mage_Paypal_Model_Api_Nvp::SET_EXPRESS_CHECKOUT => self::EXPRESS_SET,
            Mage_Paypal_Model_Api_Nvp::GET_EXPRESS_CHECKOUT_DETAILS => self::EXPRESS_GET,
            Mage_Paypal_Model_Api_Nvp::DO_EXPRESS_CHECKOUT_PAYMENT => self::EXPRESS_DO_PAYMENT,
            default => null,
        };
    }

    /**
     * Map paypal method names
     *
     * @param string $methodName
     * @return string|void
     */
    protected function _mapPaypalMethodName($methodName)
    {
        switch ($methodName) {
            case Mage_Paypal_Model_Api_Nvp::DO_EXPRESS_CHECKOUT_PAYMENT:
            case Mage_Paypal_Model_Api_Nvp::GET_EXPRESS_CHECKOUT_DETAILS:
            case Mage_Paypal_Model_Api_Nvp::SET_EXPRESS_CHECKOUT:
            case Mage_Paypal_Model_Api_Nvp::DO_DIRECT_PAYMENT:
                return ($this->_config->payment_action == Mage_Paypal_Model_Config::PAYMENT_ACTION_AUTH)
                    ? self::TRXTYPE_AUTH_ONLY
                    : self::TRXTYPE_SALE;
            case Mage_Paypal_Model_Api_Nvp::DO_CAPTURE:
                return self::TRXTYPE_DELAYED_CAPTURE;
            case Mage_Paypal_Model_Api_Nvp::DO_VOID:
                return self::TRXTYPE_DELAYED_VOID;
            case Mage_Paypal_Model_Api_Nvp::REFUND_TRANSACTION:
                return self::TRXTYPE_CREDIT;
        }
    }

    /**
     * Catch success calls and collect warnings
     *
     * @param array $response
     * @return bool success flag
     */
    protected function _isCallSuccessful($response)
    {
        $this->_callWarnings = [];
        if ($response['RESULT'] == self::RESPONSE_CODE_APPROVED) {
            // collect warnings
            if (!empty($response['RESPMSG']) && strtoupper($response['RESPMSG']) != 'APPROVED') {
                $this->_callWarnings[] = $response['RESPMSG'];
            }
            return true;
        }
        return false;
    }

    /**
     * Handle logical errors
     *
     * @param array $response
     */
    protected function _handleCallErrors($response)
    {
        if ($response['RESULT'] != self::RESPONSE_CODE_APPROVED) {
            $message = $response['RESPMSG'];
            $e = new Exception(sprintf('PayPal gateway errors: %s.', $message));
            Mage::logException($e);
            Mage::throwException(
                Mage::helper('paypal')->__('PayPal gateway rejected the request. %s', $message),
            );
        }
    }

    /**
     * Build query string without urlencoding from request
     *
     * @param array $request
     * @return string
     */
    protected function _buildQuery($request)
    {
        $result = '';
        foreach ($request as $k => $v) {
            $result .= '&' . $k . '=' . $v;
        }
        return trim($result, '&');
    }

    /**
     * Generate Request ID
     *
     * @return string
     */
    protected function getRequestId()
    {
        return Mage::helper('core')->uniqHash();
    }

    /**
     * "GetTransactionDetails" method does not exists in PaypalUK
     */
    public function callGetTransactionDetails() {}

    /**
     * Get FMF results from response, if any
     */
    protected function _importFraudFiltersResult(array $from, array $collectedWarnings)
    {
        if ($from['RESULT'] != self::RESPONSE_CODE_FRAUD) {
            return;
        }
        $this->setIsPaymentPending(true);
    }

    /**
     * Return each call request fields
     * (PayFlow edition doesn't support Unilateral payments)
     *
     * @param string $methodName Current method name
     * @return array
     */
    protected function _prepareEachCallRequest($methodName)
    {
        return $this->_eachCallRequest;
    }

    /**
     * Overwrite parent logic, simply return input data
     * (PayFlow edition doesn't support Unilateral payments)
     *
     * @param array $requestFields Standard set of values
     * @return array
     */
    protected function _prepareExpressCheckoutCallRequest(&$requestFields)
    {
        return $requestFields;
    }

    /**
     * Adopt specified request array to be compatible with Paypal
     * Puerto Rico should be as state of USA and not as a country
     *
     * @param array $request
     */
    protected function _applyCountryWorkarounds(&$request)
    {
        if (isset($request['SHIPTOCOUNTRY']) && $request['SHIPTOCOUNTRY'] == 'PR') {
            $request['SHIPTOCOUNTRY'] = 'US';
            $request['SHIPTOSTATE']   = 'PR';
        }
    }

    /**
     * Checking negative line items
     *
     * @param int $i
     * @return bool|void
     */
    protected function _exportLineItems(array &$request, $i = 0)
    {
        $requestBefore = $request;
        $result = parent::_exportLineItems($request, $i);
        if ($this->getIsLineItemsEnabled() && $this->_cart->hasNegativeItemAmount()) {
            $this->_lineItemTotalExportMap = [
                Mage_Paypal_Model_Cart::TOTAL_TAX       => 'TAXAMT',
                Mage_Paypal_Model_Cart::TOTAL_SHIPPING  => 'FREIGHTAMT',
                'amount'                                => 'PAYMENTREQUEST_0_ITEMAMT',
            ];
            $this->_lineItemExportItemsFormat = [
                'name'   => 'L_PAYMENTREQUEST_0_NAME%d',
                'qty'    => 'L_PAYMENTREQUEST_0_QTY%d',
                'amount' => 'L_PAYMENTREQUEST_0_AMT%d',
            ];
            $request = $requestBefore;
            $result = parent::_exportLineItems($request, $i);
            $paypalNvp = new Mage_Paypal_Model_Api_Nvp();
            $this->_doCaptureResponse = $paypalNvp->_doCaptureResponse;
            $this->_refundTransactionResponse = $paypalNvp->_refundTransactionResponse;
            $this->_getTransactionDetailsResponse = $paypalNvp->_getTransactionDetailsResponse;
            $this->_paymentInformationResponse = $paypalNvp->_paymentInformationResponse;
            $this->_headers[] = 'PAYPAL-NVP: Y';
            $this->_setSpecificForNegativeLineItems();
        }
        return $result;
    }

    /**
     * Set specific data when negative line item case
     */
    protected function _setSpecificForNegativeLineItems()
    {
        $index = array_search('PPREF', $this->_doDirectPaymentResponse);
        if ($index !== false) {
            unset($this->_doDirectPaymentResponse[$index]);
        }
        $this->_doDirectPaymentResponse[] = 'TRANSACTIONID';
    }
}
