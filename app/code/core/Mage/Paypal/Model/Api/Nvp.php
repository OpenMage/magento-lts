<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * NVP API wrappers model
 * @TODO: move some parts to abstract, don't hesitate to throw exceptions on api calls
 */
class Mage_Paypal_Model_Api_Nvp extends Mage_Paypal_Model_Api_Abstract
{
    /**
     * Paypal methods definition
     */
    const DO_DIRECT_PAYMENT = 'DoDirectPayment';
    const DO_CAPTURE = 'DoCapture';
    const DO_AUTHORIZATION = 'DoAuthorization';
    const DO_VOID = 'DoVoid';
    const REFUND_TRANSACTION = 'RefundTransaction';
    const SET_EXPRESS_CHECKOUT = 'SetExpressCheckout';
    const GET_EXPRESS_CHECKOUT_DETAILS = 'GetExpressCheckoutDetails';
    const DO_EXPRESS_CHECKOUT_PAYMENT = 'DoExpressCheckoutPayment';
    const CALLBACK_RESPONSE = 'CallbackResponse';

    /**
     * Paypal ManagePendingTransactionStatus actions
     */
    const PENDING_TRANSACTION_ACCEPT = 'Accept';
    const PENDING_TRANSACTION_DENY = 'Deny';

    /**
     * Capture types (make authorization close or remain open)
     * @var string
     */
    protected $_captureTypeComplete = 'Complete';
    protected $_captureTypeNotcomplete = 'NotComplete';

    /**
     * Global public interface map
     * @var array
     */
    protected $_globalMap = array(
        // each call
        'VERSION'      => 'version',
        'USER'         => 'api_username',
        'PWD'          => 'api_password',
        'SIGNATURE'    => 'api_signature',
        'BUTTONSOURCE' => 'build_notation_code',

        // for Unilateral payments
        'SUBJECT'      => 'business_account',

        // commands
        'PAYMENTACTION' => 'payment_action',
        'RETURNURL'     => 'return_url',
        'CANCELURL'     => 'cancel_url',
        'INVNUM'        => 'inv_num',
        'TOKEN'         => 'token',
        'CORRELATIONID' => 'correlation_id',
        'SOLUTIONTYPE'  => 'solution_type',
        'GIROPAYCANCELURL'  => 'giropay_cancel_url',
        'GIROPAYSUCCESSURL' => 'giropay_success_url',
        'BANKTXNPENDINGURL' => 'giropay_bank_txn_pending_url',
        'IPADDRESS'         => 'ip_address',
        'NOTIFYURL'         => 'notify_url',
        'RETURNFMFDETAILS'  => 'fraud_management_filters_enabled',
        'NOTE'              => 'note',
        'REFUNDTYPE'        => 'refund_type',
        'ACTION'            => 'action',
        'REDIRECTREQUIRED'  => 'redirect_required',
        'SUCCESSPAGEREDIRECTREQUESTED'  => 'redirect_requested',
        'REQBILLINGADDRESS' => 'require_billing_address',
        // style settings
        'PAGESTYLE'      => 'page_style',
        'HDRIMG'         => 'hdrimg',
        'HDRBORDERCOLOR' => 'hdrbordercolor',
        'HDRBACKCOLOR'   => 'hdrbackcolor',
        'PAYFLOWCOLOR'   => 'payflowcolor',
        'LOCALECODE'     => 'locale_code',
        'PAL'            => 'pal',
        'USERSELECTEDFUNDINGSOURCE' => 'funding_source',

        // transaction info
        'TRANSACTIONID'   => 'transaction_id',
        'AUTHORIZATIONID' => 'authorization_id',
        'REFUNDTRANSACTIONID' => 'refund_transaction_id',
        'COMPLETETYPE'    => 'complete_type',
        'AMT' => 'amount',
        'ITEMAMT' => 'subtotal_amount',
        'GROSSREFUNDAMT' => 'refunded_amount', // possible mistake, check with API reference

        // payment/billing info
        'CURRENCYCODE'  => 'currency_code',
        'PAYMENTSTATUS' => 'payment_status',
        'PENDINGREASON' => 'pending_reason',
        'PROTECTIONELIGIBILITY' => 'protection_eligibility',
        'PAYERID' => 'payer_id',
        'PAYERSTATUS' => 'payer_status',
        'ADDRESSID' => 'address_id',
        'ADDRESSSTATUS' => 'address_status',
        'EMAIL'         => 'email',
            // backwards compatibility
            'FIRSTNAME'     => 'firstname',
            'LASTNAME'      => 'lastname',

        // shipping rate
        'SHIPPINGOPTIONNAME' => 'shipping_rate_code',
        'NOSHIPPING'         => 'suppress_shipping',

        // paypal direct credit card information
        'CREDITCARDTYPE' => 'credit_card_type',
        'ACCT'           => 'credit_card_number',
        'EXPDATE'        => 'credit_card_expiration_date',
        'CVV2'           => 'credit_card_cvv2',
        'STARTDATE'      => 'maestro_solo_issue_date', // MMYYYY, always six chars, including leading zero
        'ISSUENUMBER'    => 'maestro_solo_issue_number',
        'CVV2MATCH'      => 'cvv2_check_result',
        'AVSCODE'        => 'avs_result',
        // cardinal centinel
        'AUTHSTATUS3DS' => 'centinel_authstatus',
        'MPIVENDOR3DS'  => 'centinel_mpivendor',
        'CAVV'         => 'centinel_cavv',
        'ECI3DS'       => 'centinel_eci',
        'XID'          => 'centinel_xid',
        'VPAS'         => 'centinel_vpas_result',
        'ECISUBMITTED3DS' => 'centinel_eci_result',

        // recurring payment profiles
//'TOKEN' => 'token',
        'SUBSCRIBERNAME'    =>'subscriber_name',
        'PROFILESTARTDATE'  => 'start_datetime',
        'PROFILEREFERENCE'  => 'internal_reference_id',
        'DESC'              => 'schedule_description',
        'MAXFAILEDPAYMENTS' => 'suspension_threshold',
        'AUTOBILLAMT'       => 'bill_failed_later',
        'BILLINGPERIOD'     => 'period_unit',
        'BILLINGFREQUENCY'    => 'period_frequency',
        'TOTALBILLINGCYCLES'  => 'period_max_cycles',
//'AMT' => 'billing_amount', // have to use 'amount', see above
        'TRIALBILLINGPERIOD'      => 'trial_period_unit',
        'TRIALBILLINGFREQUENCY'   => 'trial_period_frequency',
        'TRIALTOTALBILLINGCYCLES' => 'trial_period_max_cycles',
        'TRIALAMT'            => 'trial_billing_amount',
// 'CURRENCYCODE' => 'currency_code',
        'SHIPPINGAMT'         => 'shipping_amount',
        'TAXAMT'              => 'tax_amount',
        'INITAMT'             => 'init_amount',
        'FAILEDINITAMTACTION' => 'init_may_fail',
        'PROFILEID'           => 'recurring_profile_id',
        'PROFILESTATUS'       => 'recurring_profile_status',
        'STATUS'              => 'status',

        //Next two fields are used for Brazil only
        'TAXID'               => 'buyer_tax_id',
        'TAXIDTYPE'           => 'buyer_tax_id_type',

        'BILLINGAGREEMENTID' => 'billing_agreement_id',
        'REFERENCEID' => 'reference_id',
        'BILLINGAGREEMENTSTATUS' => 'billing_agreement_status',
        'BILLINGTYPE' => 'billing_type',
        'SREET' => 'street',
        'CITY' => 'city',
        'STATE' => 'state',
        'COUNTRYCODE' => 'countrycode',
        'ZIP' => 'zip',
        'PAYERBUSINESS' => 'payer_business',
    );

    /**
     * Filter callbacks for preparing internal amounts to NVP request
     *
     * @var array
     */
    protected $_exportToRequestFilters = array(
        'AMT'         => '_filterAmount',
        'ITEMAMT'     => '_filterAmount',
        'TRIALAMT'    => '_filterAmount',
        'SHIPPINGAMT' => '_filterAmount',
        'TAXAMT'      => '_filterAmount',
        'INITAMT'     => '_filterAmount',
        'CREDITCARDTYPE' => '_filterCcType',
//        'PROFILESTARTDATE' => '_filterToPaypalDate',
        'AUTOBILLAMT' => '_filterBillFailedLater',
        'BILLINGPERIOD' => '_filterPeriodUnit',
        'TRIALBILLINGPERIOD' => '_filterPeriodUnit',
        'FAILEDINITAMTACTION' => '_filterInitialAmountMayFail',
        'BILLINGAGREEMENTSTATUS' => '_filterBillingAgreementStatus',
        'NOSHIPPING' => '_filterInt',
    );

    protected $_importFromRequestFilters = array(
        'REDIRECTREQUIRED'  => '_filterToBool',
        'SUCCESSPAGEREDIRECTREQUESTED'  => '_filterToBool',
        'PAYMENTSTATUS' => '_filterPaymentStatusFromNvpToInfo',
    );

    /**
     * Request map for each API call
     * @var array
     */
    protected $_eachCallRequest = array('VERSION', 'USER', 'PWD', 'SIGNATURE', 'BUTTONSOURCE',);

    /**
     * SetExpressCheckout request/response map
     * @var array
     */
    protected $_setExpressCheckoutRequest = array(
        'PAYMENTACTION', 'AMT', 'CURRENCYCODE', 'RETURNURL', 'CANCELURL', 'INVNUM', 'SOLUTIONTYPE', 'NOSHIPPING',
        'GIROPAYCANCELURL', 'GIROPAYSUCCESSURL', 'BANKTXNPENDINGURL',
        'PAGESTYLE', 'HDRIMG', 'HDRBORDERCOLOR', 'HDRBACKCOLOR', 'PAYFLOWCOLOR', 'LOCALECODE',
        'BILLINGTYPE', 'SUBJECT', 'ITEMAMT', 'SHIPPINGAMT', 'TAXAMT', 'REQBILLINGADDRESS',
        'USERSELECTEDFUNDINGSOURCE'
    );
    protected $_setExpressCheckoutResponse = array('TOKEN');

    /**
     * GetExpressCheckoutDetails request/response map
     * @var array
     */
    protected $_getExpressCheckoutDetailsRequest = array('TOKEN', 'SUBJECT',);

    /**
     * DoExpressCheckoutPayment request/response map
     * @var array
     */
    protected $_doExpressCheckoutPaymentRequest = array(
        'TOKEN', 'PAYERID', 'PAYMENTACTION', 'AMT', 'CURRENCYCODE', 'IPADDRESS', 'BUTTONSOURCE', 'NOTIFYURL',
        'RETURNFMFDETAILS', 'SUBJECT', 'ITEMAMT', 'SHIPPINGAMT', 'TAXAMT',
    );
    protected $_doExpressCheckoutPaymentResponse = array(
        'TRANSACTIONID', 'AMT', 'PAYMENTSTATUS', 'PENDINGREASON', 'REDIRECTREQUIRED'
    );

    /**
     * DoDirectPayment request/response map
     * @var array
     */
    protected $_doDirectPaymentRequest = array(
        'PAYMENTACTION', 'IPADDRESS', 'RETURNFMFDETAILS',
        'AMT', 'CURRENCYCODE', 'INVNUM', 'NOTIFYURL', 'EMAIL', 'ITEMAMT', 'SHIPPINGAMT', 'TAXAMT',
        'CREDITCARDTYPE', 'ACCT', 'EXPDATE', 'CVV2', 'STARTDATE', 'ISSUENUMBER',
        'AUTHSTATUS3DS', 'MPIVENDOR3DS', 'CAVV', 'ECI3DS', 'XID',
    );
    protected $_doDirectPaymentResponse = array(
        'TRANSACTIONID', 'AMT', 'AVSCODE', 'CVV2MATCH', 'VPAS', 'ECISUBMITTED3DS'
    );

    /**
     * DoReauthorization request/response map
     * @var array
     */
    protected $_doReauthorizationRequest = array('AUTHORIZATIONID', 'AMT', 'CURRENCYCODE');
    protected $_doReauthorizationResponse = array(
        'AUTHORIZATIONID', 'PAYMENTSTATUS', 'PENDINGREASON', 'PROTECTIONELIGIBILITY'
    );

    /**
     * DoCapture request/response map
     * @var array
     */
    protected $_doCaptureRequest = array('AUTHORIZATIONID', 'COMPLETETYPE', 'AMT', 'CURRENCYCODE', 'NOTE', 'INVNUM',);
    protected $_doCaptureResponse = array('TRANSACTIONID', 'CURRENCYCODE', 'AMT', 'PAYMENTSTATUS', 'PENDINGREASON',);


    /**
     * DoAuthorization request/response map
     * @var array
     */
    protected $_doAuthorizationRequest = array('TRANSACTIONID', 'AMT', 'CURRENCYCODE');
    protected $_doAuthorizationResponse = array('TRANSACTIONID', 'AMT');

    /**
     * DoVoid request map
     * @var array
     */
    protected $_doVoidRequest = array('AUTHORIZATIONID', 'NOTE',);

    /**
     * GetTransactionDetailsRequest
     * @var array
     */
    protected $_getTransactionDetailsRequest = array('TRANSACTIONID');
    protected $_getTransactionDetailsResponse = array(
        'PAYERID', 'FIRSTNAME', 'LASTNAME', 'TRANSACTIONID', 'PARENTTRANSACTIONID', 'CURRENCYCODE', 'AMT',
        'PAYMENTSTATUS', 'PENDINGREASON',
    );

    /**
     * RefundTransaction request/response map
     * @var array
     */
    protected $_refundTransactionRequest = array('TRANSACTIONID', 'REFUNDTYPE', 'CURRENCYCODE', 'NOTE',);
    protected $_refundTransactionResponse = array('REFUNDTRANSACTIONID', 'GROSSREFUNDAMT',);

    /**
     * ManagePendingTransactionStatus request/response map
     */
    protected $_managePendingTransactionStatusRequest = array('TRANSACTIONID', 'ACTION');
    protected $_managePendingTransactionStatusResponse = array('TRANSACTIONID', 'STATUS');

    /**
     * GetPalDetails response map
     * @var array
     */
    protected $_getPalDetailsResponse = array('PAL');

    /**
     * CreateRecurringPaymentsProfile request/response map
     *
     * @var array
     */
    protected $_createRecurringPaymentsProfileRequest = array(
        'TOKEN', 'SUBSCRIBERNAME', 'PROFILESTARTDATE', 'PROFILEREFERENCE', 'DESC', 'MAXFAILEDPAYMENTS', 'AUTOBILLAMT',
        'BILLINGPERIOD', 'BILLINGFREQUENCY', 'TOTALBILLINGCYCLES', 'AMT', 'TRIALBILLINGPERIOD', 'TRIALBILLINGFREQUENCY',
        'TRIALTOTALBILLINGCYCLES', 'TRIALAMT', 'CURRENCYCODE', 'SHIPPINGAMT', 'TAXAMT', 'INITAMT', 'FAILEDINITAMTACTION'
    );
    protected $_createRecurringPaymentsProfileResponse = array(
        'PROFILEID', 'PROFILESTATUS'
    );

    /**
     * Request/response for ManageRecurringPaymentsProfileStatus map
     *
     * @var array
     */
    protected $_manageRecurringPaymentsProfileStatusRequest = array('PROFILEID', 'ACTION');
//    protected $_manageRecurringPaymentsProfileStatusResponse = array('PROFILEID');

    /**
     * Request/response for GetRecurringPaymentsProfileDetails
     *
     * @var array
     */
    protected $_getRecurringPaymentsProfileDetailsRequest = array('PROFILEID');
    protected $_getRecurringPaymentsProfileDetailsResponse = array('STATUS', /* TODO: lot of other stuff */);

    /**
     * Map for billing address import/export
     * @var array
     */
    protected $_billingAddressMap = array (
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
    );

    /**
     * Map for billing address to do request (not response)
     * Merging with $_billingAddressMap
     *
     * @var array
     */
    protected $_billingAddressMapRequest = array ();

    /**
     * Map for shipping address import/export (extends billing address mapper)
     * @var array
     */
    protected $_shippingAddressMap = array(
        'SHIPTOCOUNTRYCODE' => 'country_id',
        'SHIPTOSTATE' => 'region',
        'SHIPTOCITY'    => 'city',
        'SHIPTOSTREET'  => 'street',
        'SHIPTOSTREET2' => 'street2',
        'SHIPTOZIP' => 'postcode',
        'SHIPTOPHONENUM' => 'telephone',
        // 'SHIPTONAME' will be treated manually in address import/export methods
    );

    /**
     * Map for callback request
     * @var array
     */
    protected $_callbackRequestMap = array(
        'SHIPTOCOUNTRY' => 'country_id',
        'SHIPTOSTATE' => 'region',
        'SHIPTOCITY'    => 'city',
        'SHIPTOSTREET'  => 'street',
        'SHIPTOSTREET2' => 'street2',
        'SHIPTOZIP' => 'postcode'
    );

    /**
     * Payment information response specifically to be collected after some requests
     * @var array
     */
    protected $_paymentInformationResponse = array(
        'PAYERID', 'PAYERSTATUS', 'CORRELATIONID', 'ADDRESSID', 'ADDRESSSTATUS',
        'PAYMENTSTATUS', 'PENDINGREASON', 'PROTECTIONELIGIBILITY', 'EMAIL', 'SHIPPINGOPTIONNAME', 'TAXID', 'TAXIDTYPE'
    );

    /**
     * Line items export mapping settings
     * @var array
     */
    protected $_lineItemTotalExportMap = array(
        Mage_Paypal_Model_Cart::TOTAL_SUBTOTAL => 'ITEMAMT',
        Mage_Paypal_Model_Cart::TOTAL_TAX      => 'TAXAMT',
        Mage_Paypal_Model_Cart::TOTAL_SHIPPING => 'SHIPPINGAMT',
    );
    protected $_lineItemExportItemsFormat = array(
        'id'     => 'L_NUMBER%d',
        'name'   => 'L_NAME%d',
        'qty'    => 'L_QTY%d',
        'amount' => 'L_AMT%d',
    );

    /**
     * Shipping options export to request mapping settings
     * @var array
     */
    protected $_shippingOptionsExportItemsFormat = array(
        'is_default' => 'L_SHIPPINGOPTIONISDEFAULT%d',
        'amount'     => 'L_SHIPPINGOPTIONAMOUNT%d',
        'code'       => 'L_SHIPPINGOPTIONNAME%d',
        'name'       => 'L_SHIPPINGOPTIONLABEL%d',
        'tax_amount' => 'L_TAXAMT%d',
    );

    /**
     * init Billing Agreement request/response map
     * @var array
     */
    protected $_customerBillingAgreementRequest = array('RETURNURL', 'CANCELURL', 'BILLINGTYPE');
    protected $_customerBillingAgreementResponse = array('TOKEN');

    /**
     * Billing Agreement details request/response map
     * @var array
     */
    protected $_billingAgreementCustomerDetailsRequest = array('TOKEN');
    protected $_billingAgreementCustomerDetailsResponse = array('EMAIL', 'PAYERID', 'PAYERSTATUS', 'SHIPTOCOUNTRYCODE',
        'PAYERBUSINESS'
    );

    /**
     * Create Billing Agreement request/response map
     * @var array
     */
    protected $_createBillingAgreementRequest = array('TOKEN');
    protected $_createBillingAgreementResponse = array('BILLINGAGREEMENTID');

    /**
     * Update Billing Agreement request/response map
     * @var array
     */
    protected $_updateBillingAgreementRequest = array(
        'REFERENCEID', 'BILLINGAGREEMENTDESCRIPTION', 'BILLINGAGREEMENTSTATUS', 'BILLINGAGREEMENTCUSTOM'
    );
    protected $_updateBillingAgreementResponse = array(
        'REFERENCEID', 'BILLINGAGREEMENTDESCRIPTION', 'BILLINGAGREEMENTSTATUS', 'BILLINGAGREEMENTCUSTOM'
    );

    /**
     * Do Reference Transaction request/response map
     *
     * @var array
     */
    protected $_doReferenceTransactionRequest = array('REFERENCEID', 'PAYMENTACTION', 'AMT', 'ITEMAMT', 'SHIPPINGAMT',
        'TAXAMT', 'INVNUM', 'NOTIFYURL', 'CURRENCYCODE',
    );

    protected $_doReferenceTransactionResponse = array('BILLINGAGREEMENTID', 'TRANSACTIONID');

    /**
     * Fields that should be replaced in debug with '***'
     *
     * @var array
     */
    protected $_debugReplacePrivateDataKeys = array(

        'ACCT', 'EXPDATE', 'CVV2', 'CARDISSUE', 'CARDSTART', 'CREDITCARDTYPE', 'USER', 'PWD', 'SIGNATURE'

    );

    /**
     * Map of credit card types supported by this API
     * @var array
     */
    protected $_supportedCcTypes = array(
        'VI' => 'Visa', 'MC' => 'MasterCard', 'DI' => 'Discover', 'AE' => 'Amex', 'SM' => 'Maestro', 'SO' => 'Solo');

    /**
     * Required fields in the response
     *
     * @var array
     */
    protected $_requiredResponseParams = array(
        self::DO_DIRECT_PAYMENT             => array('ACK', 'CORRELATIONID', 'AMT'),
        self::DO_EXPRESS_CHECKOUT_PAYMENT   => array('ACK', 'CORRELATIONID', 'AMT'),
    );

    /**
     * Warning codes recollected after each API call
     *
     * @var array
     */
    protected $_callWarnings = array();

    /**
     * Error codes recollected after each API call
     *
     * @var array
     */
    protected $_callErrors = array();

    /**
     * Whether to return raw response information after each call
     *
     * @var bool
     */
    protected $_rawResponseNeeded = false;

    /**
     * API call HTTP headers
     *
     * @var array
     */
    protected $_headers = array();

    /**
     * API endpoint getter
     *
     * @return string
     */
    public function getApiEndpoint()
    {
        $url = $this->getUseCertAuthentication() ? 'https://api%s.paypal.com/nvp' : 'https://api-3t%s.paypal.com/nvp';
        return sprintf($url, $this->_config->sandboxFlag ? '.sandbox' : '');
    }

    /**
     * Return Paypal Api version
     *
     * @return string
     */
    public function getVersion()
    {
        return '72.0';
    }

    /**
     * Retrieve billing agreement type
     *
     * @return string
     */
    public function getBillingAgreementType()
    {
        return 'MerchantInitiatedBilling';
    }

    /**
     * SetExpressCheckout call
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_SetExpressCheckout
     * TODO: put together style and giropay settings
     */
    public function callSetExpressCheckout()
    {
        $this->_prepareExpressCheckoutCallRequest($this->_setExpressCheckoutRequest);
        $request = $this->_exportToRequest($this->_setExpressCheckoutRequest);
        $this->_exportLineItems($request);

        // import/suppress shipping address, if any
        $options = $this->getShippingOptions();
        if ($this->getAddress()) {
            $request = $this->_importAddresses($request);
            $request['ADDROVERRIDE'] = 1;
        } elseif ($options && (count($options) <= 10)) { // doesn't support more than 10 shipping options
            $request['CALLBACK'] = $this->getShippingOptionsCallbackUrl();
            $request['CALLBACKTIMEOUT'] = 6; // max value
            $request['MAXAMT'] = $request['AMT'] + 999.00; // it is impossible to calculate max amount
            $this->_exportShippingOptions($request);
        }

        // add recurring profiles information
        $i = 0;
        foreach ($this->_recurringPaymentProfiles as $profile) {
            $request["L_BILLINGTYPE{$i}"] = 'RecurringPayments';
            $request["L_BILLINGAGREEMENTDESCRIPTION{$i}"] = $profile->getScheduleDescription();
            $i++;
        }

        $response = $this->call(self::SET_EXPRESS_CHECKOUT, $request);
        $this->_importFromResponse($this->_setExpressCheckoutResponse, $response);
    }

    /**
     * GetExpressCheckoutDetails call
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_GetExpressCheckoutDetails
     */
    function callGetExpressCheckoutDetails()
    {
        $this->_prepareExpressCheckoutCallRequest($this->_getExpressCheckoutDetailsRequest);
        $request = $this->_exportToRequest($this->_getExpressCheckoutDetailsRequest);
        $response = $this->call(self::GET_EXPRESS_CHECKOUT_DETAILS, $request);
        $this->_importFromResponse($this->_paymentInformationResponse, $response);
        $this->_exportAddressses($response);
    }

    /**
     * DoExpressCheckout call
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_DoExpressCheckoutPayment
     */
    public function callDoExpressCheckoutPayment()
    {
        $this->_prepareExpressCheckoutCallRequest($this->_doExpressCheckoutPaymentRequest);
        $request = $this->_exportToRequest($this->_doExpressCheckoutPaymentRequest);
        $this->_exportLineItems($request);

        $response = $this->call(self::DO_EXPRESS_CHECKOUT_PAYMENT, $request);
        $this->_importFromResponse($this->_paymentInformationResponse, $response);
        $this->_importFromResponse($this->_doExpressCheckoutPaymentResponse, $response);
        $this->_importFromResponse($this->_createBillingAgreementResponse, $response);
    }

    /**
     * Process a credit card payment
     */
    public function callDoDirectPayment()
    {
        $request = $this->_exportToRequest($this->_doDirectPaymentRequest);
        $this->_exportLineItems($request);
        if ($this->getAddress()) {
            $request = $this->_importAddresses($request);
        }
        $response = $this->call(self::DO_DIRECT_PAYMENT, $request);
        $this->_importFromResponse($this->_doDirectPaymentResponse, $response);
    }

    /**
     * Do Reference Transaction call
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_DoReferenceTransaction
     */
    public function callDoReferenceTransaction()
    {
        $request = $this->_exportToRequest($this->_doReferenceTransactionRequest);
        $this->_exportLineItems($request);
        $response = $this->call('DoReferenceTransaction', $request);
        $this->_importFromResponse($this->_doReferenceTransactionResponse, $response);
    }

    /**
     * Check whether the last call was returned with fraud warning
     *
     * @return bool
     */
    public function getIsFraudDetected()
    {
        return in_array(11610, $this->_callWarnings);
    }

    /**
     * Made additional request to paypal to get autharization id
     */
    public function callDoReauthorization()
    {
        $request = $this->_export($this->_doReauthorizationRequest);
        $response = $this->call('DoReauthorization', $request);
        $this->_import($response, $this->_doReauthorizationResponse);
    }

    /**
     * DoCapture call
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_DoCapture
     */
    public function callDoCapture()
    {
        $this->setCompleteType($this->_getCaptureCompleteType());
        $request = $this->_exportToRequest($this->_doCaptureRequest);
        $response = $this->call(self::DO_CAPTURE, $request);
        $this->_importFromResponse($this->_paymentInformationResponse, $response);
        $this->_importFromResponse($this->_doCaptureResponse, $response);
    }

    /**
     * DoAuthorization call
     *
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_DoAuthorization
     * @return Mage_Paypal_Model_Api_Nvp
     */
    public function callDoAuthorization()
    {
        $request = $this->_exportToRequest($this->_doAuthorizationRequest);
        $response = $this->call(self::DO_AUTHORIZATION, $request);
        $this->_importFromResponse($this->_paymentInformationResponse, $response);
        $this->_importFromResponse($this->_doAuthorizationResponse, $response);

        return $this;
    }


    /**
     * DoVoid call
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_DoVoid
     */
    public function callDoVoid()
    {
        $request = $this->_exportToRequest($this->_doVoidRequest);
        $this->call(self::DO_VOID, $request);
    }

    /**
     * GetTransactionDetails
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_GetTransactionDetails
     */
    public function callGetTransactionDetails()
    {
        $request = $this->_exportToRequest($this->_getTransactionDetailsRequest);
        $response = $this->call('GetTransactionDetails', $request);
        $this->_importFromResponse($this->_getTransactionDetailsResponse, $response);
    }

    /**
     * RefundTransaction call
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_RefundTransaction
     */
    public function callRefundTransaction()
    {
        $request = $this->_exportToRequest($this->_refundTransactionRequest);
        if ($this->getRefundType() === Mage_Paypal_Model_Config::REFUND_TYPE_PARTIAL) {
            $request['AMT'] = $this->getAmount();
        }
        $response = $this->call(self::REFUND_TRANSACTION, $request);
        $this->_importFromResponse($this->_refundTransactionResponse, $response);
    }

    /**
     * ManagePendingTransactionStatus
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_ManagePendingTransactionStatus
     */
    public function callManagePendingTransactionStatus()
    {
        $request = $this->_exportToRequest($this->_managePendingTransactionStatusRequest);
        if (isset($request['ACTION'])) {
            $request['ACTION'] = $this->_filterPaymentReviewAction($request['ACTION']);
        }
        $response = $this->call('ManagePendingTransactionStatus', $request);
        $this->_importFromResponse($this->_managePendingTransactionStatusResponse, $response);
    }

    /**
     * getPalDetails call
     * @link https://www.x.com/docs/DOC-1300
     * @link https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_ECButtonIntegration
     */
    public function callGetPalDetails()
    {
        $response = $this->call('getPalDetails', array());
        $this->_importFromResponse($this->_getPalDetailsResponse, $response);
    }

    /**
     * Set Customer BillingA greement call
     *
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_SetCustomerBillingAgreement
     */
    public function callSetCustomerBillingAgreement()
    {
        $request = $this->_exportToRequest($this->_customerBillingAgreementRequest);
        $response = $this->call('SetCustomerBillingAgreement', $request);
        $this->_importFromResponse($this->_customerBillingAgreementResponse, $response);
    }

    /**
     * Get Billing Agreement Customer Details call
     *
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_GetBillingAgreementCustomerDetails
     */
    public function callGetBillingAgreementCustomerDetails()
    {
        $request = $this->_exportToRequest($this->_billingAgreementCustomerDetailsRequest);
        $response = $this->call('GetBillingAgreementCustomerDetails', $request);
        $this->_importFromResponse($this->_billingAgreementCustomerDetailsResponse, $response);
    }

    /**
     * Create Billing Agreement call
     *
     */
    public function callCreateBillingAgreement()
    {
        $request = $this->_exportToRequest($this->_createBillingAgreementRequest);
        $response = $this->call('CreateBillingAgreement', $request);
        $this->_importFromResponse($this->_createBillingAgreementResponse, $response);
    }

    /**
     * Billing Agreement Update call
     *
     */
    public function callUpdateBillingAgreement()
    {
        $request = $this->_exportToRequest($this->_updateBillingAgreementRequest);
        try {
        $response = $this->call('BillAgreementUpdate', $request);
        } catch (Mage_Core_Exception $e) {
            if (in_array(10201, $this->_callErrors)) {
                $this->setIsBillingAgreementAlreadyCancelled(true);
            }
            throw $e;
        }
        $this->_importFromResponse($this->_updateBillingAgreementResponse, $response);
    }

    /**
     * CreateRecurringPaymentsProfile call
     */
    public function callCreateRecurringPaymentsProfile()
    {
        $request = $this->_exportToRequest($this->_createRecurringPaymentsProfileRequest);
        $response = $this->call('CreateRecurringPaymentsProfile', $request);
        $this->_importFromResponse($this->_createRecurringPaymentsProfileResponse, $response);
        $this->_analyzeRecurringProfileStatus($this->getRecurringProfileStatus(), $this);
    }

    /**
     * ManageRecurringPaymentsProfileStatus call
     */
    public function callManageRecurringPaymentsProfileStatus()
    {
        $request = $this->_exportToRequest($this->_manageRecurringPaymentsProfileStatusRequest);
        if (isset($request['ACTION'])) {
            $request['ACTION'] = $this->_filterRecurringProfileActionToNvp($request['ACTION']);
        }
        try {
            $response = $this->call('ManageRecurringPaymentsProfileStatus', $request);
        } catch (Mage_Core_Exception $e) {
            if ((in_array(11556, $this->_callErrors) && 'Cancel' === $request['ACTION'])
                || (in_array(11557, $this->_callErrors) && 'Suspend' === $request['ACTION'])
                || (in_array(11558, $this->_callErrors) && 'Reactivate' === $request['ACTION'])
            ) {
                Mage::throwException(Mage::helper('paypal')->__('Unable to change status. Current status is not correspond to real status.'));
            }
            throw $e;
        }
    }

    /**
     * GetRecurringPaymentsProfileDetails call
     */
    public function callGetRecurringPaymentsProfileDetails(Varien_Object $result)
    {
        $request = $this->_exportToRequest($this->_getRecurringPaymentsProfileDetailsRequest);
        $response = $this->call('GetRecurringPaymentsProfileDetails', $request);
        $this->_importFromResponse($this->_getRecurringPaymentsProfileDetailsResponse, $response);
        $this->_analyzeRecurringProfileStatus($this->getStatus(), $result);
    }

    /**
     * Import callback request array into $this public data
     *
     * @param array $request
     * @return Varien_Object
     */
    public function prepareShippingOptionsCallbackAddress(array $request)
    {
        $address = new Varien_Object();
        Varien_Object_Mapper::accumulateByMap($request, $address, $this->_callbackRequestMap);
        $address->setExportedKeys(array_values($this->_callbackRequestMap));
        $this->_applyStreetAndRegionWorkarounds($address);
        return $address;
    }

    /**
     * Prepare response for shipping options callback
     *
     * @return string
     */
    public function formatShippingOptionsCallback()
    {
        $response = array();
        if (!$this->_exportShippingOptions($response)) {
            $response['NO_SHIPPING_OPTION_DETAILS'] = '1';
        }
        $response = $this->_addMethodToRequest(self::CALLBACK_RESPONSE, $response);
        return $this->_buildQuery($response);
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
        $request['METHOD'] = $methodName;
        return $request;
    }

    /**
     * Do the API call
     *
     * @param string $methodName
     * @param array $request
     * @return array
     * @throws Mage_Core_Exception
     */
    public function call($methodName, array $request)
    {
        $request = $this->_addMethodToRequest($methodName, $request);
        $eachCallRequest = $this->_prepareEachCallRequest($methodName);
        if ($this->getUseCertAuthentication()) {
            if ($key = array_search('SIGNATURE', $eachCallRequest)) {
                unset($eachCallRequest[$key]);
            }
        }
        $request = $this->_exportToRequest($eachCallRequest, $request);
        $debugData = array('url' => $this->getApiEndpoint(), $methodName => $request);

        try {
            $http = new Varien_Http_Adapter_Curl();
            $config = array(
                'timeout'    => 60,
                'verifypeer' => $this->_config->verifyPeer
            );

            if ($this->getUseProxy()) {
                $config['proxy'] = $this->getProxyHost(). ':' . $this->getProxyPort();
            }
            if ($this->getUseCertAuthentication()) {
                $config['ssl_cert'] = $this->getApiCertificate();
            }
            $http->setConfig($config);
            $http->write(
                Zend_Http_Client::POST,
                $this->getApiEndpoint(),
                '1.1',
                $this->_headers,
                $this->_buildQuery($request)
            );
            $response = $http->read();
        } catch (Exception $e) {
            $debugData['http_error'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
            $this->_debug($debugData);
            throw $e;
        }

        $response = preg_split('/^\r?$/m', $response, 2);
        $response = trim($response[1]);
        $response = $this->_deformatNVP($response);

        $debugData['response'] = $response;
        $this->_debug($debugData);
        $response = $this->_postProcessResponse($response);

        // handle transport error
        if ($http->getErrno()) {
            Mage::logException(new Exception(
                sprintf('PayPal NVP CURL connection error #%s: %s', $http->getErrno(), $http->getError())
            ));
            $http->close();

            Mage::throwException(Mage::helper('paypal')->__('Unable to communicate with the PayPal gateway.'));
        }

        // cUrl resource must be closed after checking it for errors
        $http->close();

        if (!$this->_validateResponse($methodName, $response)) {
            Mage::logException(new Exception(
                Mage::helper('paypal')->__("PayPal response hasn't required fields.")
            ));
            Mage::throwException(Mage::helper('paypal')->__('There was an error processing your order. Please contact us or try again later.'));
        }

        $this->_callErrors = array();
        if ($this->_isCallSuccessful($response)) {
            if ($this->_rawResponseNeeded) {
                $this->setRawSuccessResponseData($response);
            }
            return $response;
        }
        $this->_handleCallErrors($response);
        return $response;
    }

    /**
     * Setter for 'raw response needed' flag
     *
     * @param bool $flag
     * @return Mage_Paypal_Model_Api_Nvp
     */
    public function setRawResponseNeeded($flag)
    {
        $this->_rawResponseNeeded = $flag;
        return $this;
    }

    /**
     * Handle logical errors
     *
     * @param array $response
     * @throws Mage_Core_Exception
     */
    protected function _handleCallErrors($response)
    {
        $errors = $this->_extractErrorsFromResponse($response);
        $errorsCount = count($errors);

        if (!$errorsCount) {
            return;
        }

        $exceptionCode = 0;

        if ($errorsCount == 1 && $this->_isProcessableError($errors[0]['code'])) {
            $exceptionCode = $errors[0]['code'];
            $exceptionClass = 'Mage_Paypal_Model_Api_ProcessableException';
        } else {
            $exceptionClass = 'Mage_Core_Exception';
        }

        $errorMessages = array();

        foreach ($errors as $error) {
            $errorMessages[] = $error['message'];
            $this->_callErrors[] = $error['code'];
        }

        $errorMessages = implode(' ', array_values($errorMessages));
        $exceptionLogMessage = sprintf('PayPal NVP gateway errors: %s Correlation ID: %s. Version: %s.',
            $errorMessages,
            isset($response['CORRELATIONID']) ? $response['CORRELATIONID'] : '',
            isset($response['VERSION']) ? $response['VERSION'] : ''
        );

        $exception = new $exceptionClass($exceptionLogMessage, $exceptionCode);
        Mage::logException($exception);

        $exception->setMessage(Mage::helper('paypal')->__('PayPal gateway has rejected request. %s', $errorMessages));

        throw $exception;
    }

    /**
     * Format error message from error code, short error message and long error message
     *
     * @param string $errorCode
     * @param string $shortErrorMessage
     * @param string $longErrorMessage
     * @return string
     */
    protected function _formatErrorMessage($errorCode, $shortErrorMessage, $longErrorMessage)
    {
        $longErrorMessage  = preg_replace('/\.$/', '', $longErrorMessage);
        $shortErrorMessage = preg_replace('/\.$/', '', $shortErrorMessage);

        return $longErrorMessage ? sprintf('%s (#%s: %s).', $longErrorMessage, $errorCode, $shortErrorMessage)
            : sprintf('#%s: %s.', $errorCode, $shortErrorMessage);
    }


    /**
     * Check whether PayPal error can be processed
     *
     * @param int $errorCode
     * @return bool
     */
    protected function _isProcessableError($errorCode)
    {
        $processableErrorsList = $this->getProcessableErrors();

        if (!$processableErrorsList || !is_array($processableErrorsList)) {
            return false;
        }

        return in_array($errorCode, $processableErrorsList);
    }

    /**
     * Extract errors from PayPal's response and return them in array
     *
     * @param array $response
     * @return array
     */
    protected function _extractErrorsFromResponse($response)
    {
        $errors = array();

        for ($i = 0; isset($response["L_ERRORCODE{$i}"]); $i++) {
            $errorCode = $response["L_ERRORCODE{$i}"];
            $errorMessage = $this->_formatErrorMessage($errorCode, $response["L_SHORTMESSAGE{$i}"],
                $response["L_LONGMESSAGE{$i}"]);
            $errors[] = array (
                'code'    => $errorCode,
                'message' => $errorMessage
            );
        }

        return $errors;
    }

    /**
     * Catch success calls and collect warnings
     *
     * @param array
     * @return bool| success flag
     */
    protected function _isCallSuccessful($response)
    {
        if (!isset($response['ACK'])) {
            return false;
        }

        $ack = strtoupper($response['ACK']);
        $this->_callWarnings = array();
        if ($ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING') {
            // collect warnings
            if ($ack == 'SUCCESSWITHWARNING') {
                for ($i = 0; isset($response["L_ERRORCODE{$i}"]); $i++) {
                    $this->_callWarnings[] = $response["L_ERRORCODE{$i}"];
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Validate response array.
     *
     * @param string $method
     * @param array $response
     * @return bool
     */
    protected function _validateResponse($method, $response)
    {
        if (isset($this->_requiredResponseParams[$method])) {
            foreach ($this->_requiredResponseParams[$method] as $param) {
                if (!isset($response[$param])) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Parse an NVP response string into an associative array
     * @param string $nvpstr
     * @return array
     */
    protected function _deformatNVP($nvpstr)
    {
        $intial=0;
        $nvpArray = array();

        $nvpstr = strpos($nvpstr, "\r\n\r\n")!==false ? substr($nvpstr, strpos($nvpstr, "\r\n\r\n")+4) : $nvpstr;

        while(strlen($nvpstr)) {
            //postion of Key
            $keypos= strpos($nvpstr,'=');
            //position of value
            $valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);

            /*getting the Key and Value values and storing in a Associative Array*/
            $keyval=substr($nvpstr,$intial,$keypos);
            $valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
            //decoding the respose
            $nvpArray[urldecode($keyval)] =urldecode( $valval);
            $nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
         }
        return $nvpArray;
    }

    /**
     * NVP doesn't support passing discount total as a separate amount - add it as a line item
     *
     * @param array $request
     * @param int $i
     * @return true|null
     */
    protected function _exportLineItems(array &$request, $i = 0)
    {
        if (!$this->_cart) {
            return;
        }
        $this->_cart->isDiscountAsItem(true);
        return parent::_exportLineItems($request, $i);
    }

    /**
     * Create billing and shipping addresses basing on response data
     * @param array $data
     */
    protected function _exportAddressses($data)
    {
        $address = new Varien_Object();
        Varien_Object_Mapper::accumulateByMap($data, $address, $this->_billingAddressMap);
        $address->setExportedKeys(array_values($this->_billingAddressMap));
        $this->_applyStreetAndRegionWorkarounds($address);
        $this->setExportedBillingAddress($address);
        // assume there is shipping address if there is at least one field specific to shipping
        if (isset($data['SHIPTONAME'])) {
            $shippingAddress = clone $address;
            Varien_Object_Mapper::accumulateByMap($data, $shippingAddress, $this->_shippingAddressMap);
            $this->_applyStreetAndRegionWorkarounds($shippingAddress);
            // PayPal doesn't provide detailed shipping name fields, so the name will be overwritten
            $shippingAddress->addData(array(
                'firstname'  => $data['SHIPTONAME'],
            ));
            $this->setExportedShippingAddress($shippingAddress);
        }
    }

    /**
     * Adopt specified address object to be compatible with Magento
     *
     * @param Varien_Object $address
     */
    protected function _applyStreetAndRegionWorkarounds(Varien_Object $address)
    {
        // merge street addresses into 1
        if ($address->hasStreet2()) {
             $address->setStreet(implode("\n", array($address->getStreet(), $address->getStreet2())));
             $address->unsStreet2();
        }
        // attempt to fetch region_id from directory
        if ($address->getCountryId() && $address->getRegion()) {
            $regions = Mage::getModel('directory/country')->loadByCode($address->getCountryId())->getRegionCollection()
                ->addRegionCodeOrNameFilter($address->getRegion())
                ->setPageSize(1);
            foreach ($regions as $region) {
                $address->setRegionId($region->getId());
                $address->setExportedKeys(array_merge($address->getExportedKeys(), array('region_id')));
                break;
            }
        }
    }

    /**
     * Adopt specified request array to be compatible with Paypal
     * Puerto Rico should be as state of USA and not as a country
     *
     * @param array $request
     */
    protected function _applyCountryWorkarounds(&$request)
    {
        if (isset($request['SHIPTOCOUNTRYCODE']) && $request['SHIPTOCOUNTRYCODE'] == 'PR') {
            $request['SHIPTOCOUNTRYCODE'] = 'US';
            $request['SHIPTOSTATE']       = 'PR';
        }
    }

    /**
     * Prepare request data basing on provided address
     *
     * @deprecated after 1.4.2.0-beta1, use _importAddresses() instead
     *
     * @param Varien_Object $address
     * @param array $to
     * @return array
     */
    protected function _importAddress(Varien_Object $address, array $to)
    {
        $this->setAddress($address);
        return $this->_importAddresses($to);
    }

    /**
     * Prepare request data basing on provided addresses
     *
     * @param array $to
     * @return array
     */
    protected function _importAddresses(array $to)
    {
        $billingAddress  = ($this->getBillingAddress()) ? $this->getBillingAddress() : $this->getAddress();
        $shippingAddress = $this->getAddress();

        $to = Varien_Object_Mapper::accumulateByMap(
            $billingAddress,
            $to,
            array_merge(array_flip($this->_billingAddressMap), $this->_billingAddressMapRequest)
        );
        if ($regionCode = $this->_lookupRegionCodeFromAddress($billingAddress)) {
            $to['STATE'] = $regionCode;
        }
        if (!$this->getSuppressShipping()) {
            $to = Varien_Object_Mapper::accumulateByMap($shippingAddress, $to, array_flip($this->_shippingAddressMap));
            if ($regionCode = $this->_lookupRegionCodeFromAddress($shippingAddress)) {
                $to['SHIPTOSTATE'] = $regionCode;
            }
            $this->_importStreetFromAddress($shippingAddress, $to, 'SHIPTOSTREET', 'SHIPTOSTREET2');
            $this->_importStreetFromAddress($billingAddress, $to, 'STREET', 'STREET2');
            $to['SHIPTONAME'] = $shippingAddress->getName();
        }
        $this->_applyCountryWorkarounds($to);
        return $to;
    }

    /**
     * Filter for credit card type
     *
     * @param string $value
     * @return string
     */
    protected function _filterCcType($value)
    {
        if (isset($this->_supportedCcTypes[$value])) {
            return $this->_supportedCcTypes[$value];
        }
        return '';
    }

    /**
     * Filter for true/false values (converts to boolean)
     *
     * @param mixed $value
     * @return mixed
     */
    protected function _filterToBool($value)
    {
        if ('false' === $value || '0' === $value) {
            return false;
        } elseif ('true' === $value || '1' === $value) {
            return true;
        }
        return $value;
    }

    /**
     * Filter for 'AUTOBILLAMT'
     *
     * @param string $value
     * @return string
     */
    protected function _filterBillFailedLater($value)
    {
        return $value ? 'AddToNextBilling' : 'NoAutoBill';
    }

    /**
     * Filter for 'BILLINGPERIOD' and 'TRIALBILLINGPERIOD'
     *
     * @param string $value
     * @return string
     */
    protected function _filterPeriodUnit($value)
    {
        switch ($value) {
            case 'day':        return 'Day';
            case 'week':       return 'Week';
            case 'semi_month': return 'SemiMonth';
            case 'month':      return 'Month';
            case 'year':       return 'Year';
        }
    }

    /**
     * Filter for 'FAILEDINITAMTACTION'
     *
     * @param string $value
     * @return string
     */
    protected function _filterInitialAmountMayFail($value)
    {
        return $value ? 'ContinueOnFailure' : 'CancelOnFailure';
    }

    /**
     * Filter for billing agreement status
     *
     * @param string $value
     * @return string
     */
    protected function _filterBillingAgreementStatus($value)
    {
        switch ($value) {
            case 'canceled':    return 'Canceled';
            case 'active':      return 'Active';
        }
    }

    /**
     * Convert payment status from NVP format to paypal/info model format
     *
     * @param string $value
     * @return string|null
     */
    protected function _filterPaymentStatusFromNvpToInfo($value)
    {
        switch ($value) {
            case 'None': return Mage_Paypal_Model_Info::PAYMENTSTATUS_NONE;
            case 'Completed': return Mage_Paypal_Model_Info::PAYMENTSTATUS_COMPLETED;
            case 'Denied': return Mage_Paypal_Model_Info::PAYMENTSTATUS_DENIED;
            case 'Expired': return Mage_Paypal_Model_Info::PAYMENTSTATUS_EXPIRED;
            case 'Failed': return Mage_Paypal_Model_Info::PAYMENTSTATUS_FAILED;
            case 'In-Progress': return Mage_Paypal_Model_Info::PAYMENTSTATUS_INPROGRESS;
            case 'Pending': return Mage_Paypal_Model_Info::PAYMENTSTATUS_PENDING;
            case 'Refunded': return Mage_Paypal_Model_Info::PAYMENTSTATUS_REFUNDED;
            case 'Partially-Refunded': return Mage_Paypal_Model_Info::PAYMENTSTATUS_REFUNDEDPART;
            case 'Reversed': return Mage_Paypal_Model_Info::PAYMENTSTATUS_REVERSED;
            case 'Canceled-Reversal': return Mage_Paypal_Model_Info::PAYMENTSTATUS_UNREVERSED;
            case 'Processed': return Mage_Paypal_Model_Info::PAYMENTSTATUS_PROCESSED;
            case 'Voided': return Mage_Paypal_Model_Info::PAYMENTSTATUS_VOIDED;
        }
    }

    /**
     * Convert payment review action to NVP-compatible value
     *
     * @param string $value
     * @return string|null
     */
    protected function _filterPaymentReviewAction($value)
    {
        switch ($value) {
            case Mage_Paypal_Model_Pro::PAYMENT_REVIEW_ACCEPT:
                return 'Accept';
            case Mage_Paypal_Model_Pro::PAYMENT_REVIEW_DENY:
                return 'Deny';
        }
    }

    /**
     * Convert RP management action to NVP format
     *
     * @param string $value
     * @return string|null
     */
    protected function _filterRecurringProfileActionToNvp($value)
    {
        switch ($value) {
            case 'cancel': return 'Cancel';
            case 'suspend':  return 'Suspend';
            case 'activate': return 'Reactivate';
        }
    }

    /**
     * Check the obtained RP status in NVP format and specify the profile state
     *
     * @param string $value
     * @param Varien_Object $result
     */
    protected function _analyzeRecurringProfileStatus($value, Varien_Object $result)
    {
        switch ($value) {
            case 'ActiveProfile':
            case 'Active':
                $result->setIsProfileActive(true);
                break;
            case 'PendingProfile':
                $result->setIsProfilePending(true);
                break;
            case 'CancelledProfile':
            case 'Cancelled':
                $result->setIsProfileCanceled(true);
                break;
            case 'SuspendedProfile':
            case 'Suspended':
                $result->setIsProfileSuspended(true);
                break;
            case 'ExpiredProfile':
            case 'Expired': // ??
                $result->setIsProfileExpired(true);
                break;
        }
    }

    /**
     * Return capture type
     *
     * @param Varien_Object $payment
     * @return string
     */
    protected function _getCaptureCompleteType()
    {
        return ($this->getIsCaptureComplete())
                ? $this->_captureTypeComplete
                : $this->_captureTypeNotcomplete;
    }

    /**
     * Return each call request without unused fields in case of Express Checkout Unilateral payments
     *
     * @param string $methodName Current method name
     * @return array
     */
    protected function _prepareEachCallRequest($methodName)
    {
        $expressCheckooutMetods = array(
            self::SET_EXPRESS_CHECKOUT, self::GET_EXPRESS_CHECKOUT_DETAILS, self::DO_EXPRESS_CHECKOUT_PAYMENT
        );
        if (!in_array($methodName, $expressCheckooutMetods) || !$this->_config->shouldUseUnilateralPayments()) {
            return $this->_eachCallRequest;
        }
        return array_diff($this->_eachCallRequest, array('USER', 'PWD', 'SIGNATURE'));
    }

    /**
     * Check the EC request against unilateral payments mode and remove the SUBJECT if needed
     *
     * @param &array $requestFields
     */
    protected function _prepareExpressCheckoutCallRequest(&$requestFields)
    {
        if (!$this->_config->shouldUseUnilateralPayments()) {
            if ($key = array_search('SUBJECT', $requestFields)) {
                unset($requestFields[$key]);
            }
        }
    }

    /**
     * Additional response processing.
     * Hack to cut off length from API type response params.
     *
     * @param  array $response
     * @return array
     */
    protected function _postProcessResponse($response)
    {
        foreach ($response as $key => $value) {
            $pos = strpos($key, '[');

            if ($pos === false) {
                continue;
            }

            unset($response[$key]);

            if ($pos !== 0) {
                $modifiedKey = substr($key, 0, $pos);
                $response[$modifiedKey] = $value;
            }
        }

        return $response;
    }
}
