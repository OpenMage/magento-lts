<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * PayPal payment information model
 *
 * Aware of all PayPal payment methods
 * Collects and provides access to PayPal-specific payment data
 * Provides business logic information about payment flow
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Model_Info
{
    /**
     * Cross-models public exchange keys
     *
     * @var string
     */
    public const PAYER_ID       = 'payer_id';

    public const PAYER_EMAIL    = 'email';

    public const PAYER_STATUS   = 'payer_status';

    public const ADDRESS_ID     = 'address_id';

    public const ADDRESS_STATUS = 'address_status';

    public const PROTECTION_EL  = 'protection_eligibility';

    public const FRAUD_FILTERS  = 'collected_fraud_filters';

    public const CORRELATION_ID = 'correlation_id';

    public const AVS_CODE       = 'avs_result';

    public const CVV2_MATCH     = 'cvv2_check_result';

    public const CENTINEL_VPAS  = 'centinel_vpas_result';

    public const CENTINEL_ECI   = 'centinel_eci_result';

    // Next two fields are required for Brazil
    public const BUYER_TAX_ID   = 'buyer_tax_id';

    public const BUYER_TAX_ID_TYPE = 'buyer_tax_id_type';

    public const PAYMENT_STATUS = 'payment_status';

    public const PENDING_REASON = 'pending_reason';

    public const IS_FRAUD       = 'is_fraud_detected';

    public const PAYMENT_STATUS_GLOBAL = 'paypal_payment_status';

    public const PENDING_REASON_GLOBAL = 'paypal_pending_reason';

    public const IS_FRAUD_GLOBAL       = 'paypal_is_fraud_detected';

    /**
     * Possible buyer's tax id types (Brazil only)
     */
    public const BUYER_TAX_ID_TYPE_CPF = 'BR_CPF';

    public const BUYER_TAX_ID_TYPE_CNPJ = 'BR_CNPJ';

    /**
     * All payment information map
     *
     * @var array
     */
    protected $_paymentMap = [
        self::PAYER_ID       => 'paypal_payer_id',
        self::PAYER_EMAIL    => 'paypal_payer_email',
        self::PAYER_STATUS   => 'paypal_payer_status',
        self::ADDRESS_ID     => 'paypal_address_id',
        self::ADDRESS_STATUS => 'paypal_address_status',
        self::PROTECTION_EL  => 'paypal_protection_eligibility',
        self::FRAUD_FILTERS  => 'paypal_fraud_filters',
        self::CORRELATION_ID => 'paypal_correlation_id',
        self::AVS_CODE       => 'paypal_avs_code',
        self::CVV2_MATCH     => 'paypal_cvv2_match',
        self::CENTINEL_VPAS  => self::CENTINEL_VPAS,
        self::CENTINEL_ECI   => self::CENTINEL_ECI,
        self::BUYER_TAX_ID   => self::BUYER_TAX_ID,
        self::BUYER_TAX_ID_TYPE => self::BUYER_TAX_ID_TYPE,
    ];

    /**
     * System information map
     *
     * @var array
     */
    protected $_systemMap = [
        self::PAYMENT_STATUS => self::PAYMENT_STATUS_GLOBAL,
        self::PENDING_REASON => self::PENDING_REASON_GLOBAL,
        self::IS_FRAUD       => self::IS_FRAUD_GLOBAL,
    ];

    /**
     * PayPal payment status possible values
     *
     * @var string
     */
    public const PAYMENTSTATUS_NONE         = 'none';

    public const PAYMENTSTATUS_COMPLETED    = 'completed';

    public const PAYMENTSTATUS_DENIED       = 'denied';

    public const PAYMENTSTATUS_EXPIRED      = 'expired';

    public const PAYMENTSTATUS_FAILED       = 'failed';

    public const PAYMENTSTATUS_INPROGRESS   = 'in_progress';

    public const PAYMENTSTATUS_PENDING      = 'pending';

    public const PAYMENTSTATUS_REFUNDED     = 'refunded';

    public const PAYMENTSTATUS_REFUNDEDPART = 'partially_refunded';

    public const PAYMENTSTATUS_REVERSED     = 'reversed';

    public const PAYMENTSTATUS_UNREVERSED   = 'canceled_reversal';

    public const PAYMENTSTATUS_PROCESSED    = 'processed';

    public const PAYMENTSTATUS_VOIDED       = 'voided';

    /**
     * PayPal payment transaction type
     */
    public const TXN_TYPE_ADJUSTMENT = 'adjustment';

    public const TXN_TYPE_NEW_CASE   = 'new_case';

    /**
     * PayPal payment reason code when payment_status is Reversed, Refunded, or Canceled_Reversal.
     */
    public const PAYMENT_REASON_CODE_REFUND  = 'refund';

    /**
     * PayPal order status for Reverse payment status
     */
    public const ORDER_STATUS_REVERSED = 'paypal_reversed';

    /**
     * PayPal order status for Canceled Reversal payment status
     */
    public const ORDER_STATUS_CANCELED_REVERSAL = 'paypal_canceled_reversal';

    /**
     * Map of payment information available to customer
     *
     * @var array
     */
    protected $_paymentPublicMap = [
        'paypal_payer_email',
        self::BUYER_TAX_ID,
        self::BUYER_TAX_ID_TYPE,
    ];

    /**
     * Rendered payment map cache
     *
     * @var array
     */
    protected $_paymentMapFull = [];

    /**
     * All available payment info getter
     *
     * @param bool $labelValuesOnly
     * @return array
     */
    public function getPaymentInfo(Mage_Payment_Model_Info $payment, $labelValuesOnly = false)
    {
        // collect paypal-specific info
        $result = $this->_getFullInfo(array_values($this->_paymentMap), $payment, $labelValuesOnly);

        // add last_trans_id
        $label = Mage::helper('paypal')->__('Last Transaction ID');
        $value = $payment->getLastTransId();
        if ($labelValuesOnly) {
            $result[$label] = $value;
        } else {
            $result['last_trans_id'] = ['label' => $label, 'value' => $value];
        }

        return $result;
    }

    /**
     * Public payment info getter
     *
     * @param bool $labelValuesOnly
     * @return array
     */
    public function getPublicPaymentInfo(Mage_Payment_Model_Info $payment, $labelValuesOnly = false)
    {
        return $this->_getFullInfo($this->_paymentPublicMap, $payment, $labelValuesOnly);
    }

    /**
     * Grab data from source and map it into payment
     *
     * @param array|callable|Varien_Object $from
     */
    public function importToPayment($from, Mage_Payment_Model_Info $payment)
    {
        $fullMap = array_merge($this->_paymentMap, $this->_systemMap);
        if (is_object($from)) {
            $from = [$from, 'getDataUsingMethod'];
        }

        Varien_Object_Mapper::accumulateByMap($from, [$payment, 'setAdditionalInformation'], $fullMap);
    }

    /**
     * Grab data from payment and map it into target
     *
     * @param array|callable|Varien_Object $to
     * @return array|Varien_Object
     */
    public function &exportFromPayment(Mage_Payment_Model_Info $payment, $to, ?array $map = null)
    {
        $fullMap = array_merge($this->_paymentMap, $this->_systemMap);
        Varien_Object_Mapper::accumulateByMap(
            [$payment, 'getAdditionalInformation'],
            $to,
            $map ? $map : array_flip($fullMap),
        );
        return $to;
    }

    /**
     * Check whether the payment is in review state
     *
     * @return bool
     */
    public static function isPaymentReviewRequired(Mage_Payment_Model_Info $payment)
    {
        $paymentStatus = $payment->getAdditionalInformation(self::PAYMENT_STATUS_GLOBAL);
        if (self::PAYMENTSTATUS_PENDING === $paymentStatus) {
            $pendingReason = $payment->getAdditionalInformation(self::PENDING_REASON_GLOBAL);
            return !in_array($pendingReason, ['authorization', 'order']);
        }

        return false;
    }

    /**
     * Check whether fraud order review detected and can be reviewed
     *
     * @return bool
     */
    public static function isFraudReviewAllowed(Mage_Payment_Model_Info $payment)
    {
        return self::isPaymentReviewRequired($payment)
            && $payment->getAdditionalInformation(self::IS_FRAUD_GLOBAL) == 1;
    }

    /**
     * Check whether the payment is completed
     *
     * @return bool
     */
    public static function isPaymentCompleted(Mage_Payment_Model_Info $payment)
    {
        $paymentStatus = $payment->getAdditionalInformation(self::PAYMENT_STATUS_GLOBAL);
        return self::PAYMENTSTATUS_COMPLETED === $paymentStatus;
    }

    /**
     * Check whether the payment was processed successfully
     *
     * @return bool
     */
    public static function isPaymentSuccessful(Mage_Payment_Model_Info $payment)
    {
        $paymentStatus = $payment->getAdditionalInformation(self::PAYMENT_STATUS_GLOBAL);
        if (in_array($paymentStatus, [
            self::PAYMENTSTATUS_COMPLETED, self::PAYMENTSTATUS_INPROGRESS, self::PAYMENTSTATUS_REFUNDED,
            self::PAYMENTSTATUS_REFUNDEDPART, self::PAYMENTSTATUS_UNREVERSED, self::PAYMENTSTATUS_PROCESSED,
        ])
        ) {
            return true;
        }

        $pendingReason = $payment->getAdditionalInformation(self::PENDING_REASON_GLOBAL);
        return self::PAYMENTSTATUS_PENDING === $paymentStatus
            && in_array($pendingReason, ['authorization', 'order']);
    }

    /**
     * Check whether the payment was processed unsuccessfully or failed
     *
     * @return bool
     */
    public static function isPaymentFailed(Mage_Payment_Model_Info $payment)
    {
        $paymentStatus = $payment->getAdditionalInformation(self::PAYMENT_STATUS_GLOBAL);
        return in_array($paymentStatus, [
            self::PAYMENTSTATUS_DENIED, self::PAYMENTSTATUS_EXPIRED, self::PAYMENTSTATUS_FAILED,
            self::PAYMENTSTATUS_REVERSED, self::PAYMENTSTATUS_VOIDED,
        ]);
    }

    /**
     * Explain pending payment reason code
     *
     * @param string $code
     * @return string
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_html_IPNandPDTVariables
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_GetTransactionDetails
     */
    public static function explainPendingReason($code)
    {
        return match ($code) {
            'address' => Mage::helper('paypal')->__('Customer did not include a confirmed address.'),
            'authorization', 'order' => Mage::helper('paypal')->__('The payment is authorized but not settled.'),
            'echeck' => Mage::helper('paypal')->__('The payment eCheck is not yet cleared.'),
            'intl' => Mage::helper('paypal')->__('Merchant holds a non-U.S. account and does not have a withdrawal mechanism.'),
            'multi-currency', 'multi_currency', 'multicurrency' => Mage::helper('paypal')->__("The payment curency does not match any of the merchant's balances currency."),
            'paymentreview' => Mage::helper('paypal')->__('The payment is pending while it is being reviewed by PayPal for risk.'),
            'unilateral' => Mage::helper('paypal')->__('The payment is pending because it was made to an email address that is not yet registered or confirmed.'),
            'verify' => Mage::helper('paypal')->__('The merchant account is not yet verified.'),
            'upgrade' => Mage::helper('paypal')->__('The payment was made via credit card. In order to receive funds merchant must upgrade account to Business or Premier status.'),
            default => Mage::helper('paypal')->__('Unknown reason. Please contact PayPal customer service.'),
        };
    }

    /**
     * Explain the refund or chargeback reason code
     *
     * @param string $code
     * @return string
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_html_IPNandPDTVariables
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_GetTransactionDetails
     */
    public static function explainReasonCode($code)
    {
        $comments = [
            'chargeback'               => Mage::helper('paypal')->__('A reversal has occurred on this transaction due to a chargeback by your customer.'),
            'guarantee'                => Mage::helper('paypal')->__('A reversal has occurred on this transaction due to your customer triggering a money-back guarantee.'),
            'buyer-complaint'          => Mage::helper('paypal')->__('A reversal has occurred on this transaction due to a complaint about the transaction from your customer.'),
            'buyer_complaint'          => Mage::helper('paypal')->__('A reversal has occurred on this transaction due to a complaint about the transaction from your customer.'),
            'refund'                   => Mage::helper('paypal')->__('A reversal has occurred on this transaction because you have given the customer a refund.'),
            'adjustment_reversal'      => Mage::helper('paypal')->__('Reversal of an adjustment.'),
            'admin_fraud_reversal'     => Mage::helper('paypal')->__('Transaction reversal due to fraud detected by PayPal administrators.'),
            'admin_reversal'           => Mage::helper('paypal')->__('Transaction reversal by PayPal administrators.'),
            'chargeback_reimbursement' => Mage::helper('paypal')->__('Reimbursement for a chargeback.'),
            'chargeback_settlement'    => Mage::helper('paypal')->__('Settlement of a chargeback.'),
            'unauthorized_spoof'       => Mage::helper('paypal')->__('A reversal has occurred on this transaction because of a customer dispute suspecting unauthorized spoof.'),
            'non_receipt'              => Mage::helper('paypal')->__('Buyer claims that he did not receive goods or service.'),
            'not_as_described'         => Mage::helper('paypal')->__('Buyer claims that the goods or service received differ from merchant’s description of the goods or service.'),
            'unauthorized'             => Mage::helper('paypal')->__('Buyer claims that he/she did not authorize transaction.'),
            'adjustment_reimburse'     => Mage::helper('paypal')->__('A case that has been resolved and close requires a reimbursement.'),
            'duplicate'                => Mage::helper('paypal')->__('Buyer claims that a possible duplicate payment was made to the merchant.'),
            'merchandise'              => Mage::helper('paypal')->__('Buyer claims that the received merchandise is unsatisfactory, defective, or damaged.'),
        ];
        return (array_key_exists($code, $comments) && !empty($comments[$code]))
            ? $comments[$code]
            : Mage::helper('paypal')->__('Unknown reason. Please contact PayPal customer service.');
    }

    /**
     * Whether a reversal/refund can be disputed with PayPal
     *
     * @param string $code
     * @return bool
     */
    public static function isReversalDisputable($code)
    {
        return match ($code) {
            'none', 'other', 'chargeback', 'buyer-complaint', 'buyer_complaint', 'adjustment_reversal' => true,
            default => false,
        };
    }

    /**
     * Render info item
     *
     * @param bool $labelValuesOnly
     * @return array
     */
    protected function _getFullInfo(array $keys, Mage_Payment_Model_Info $payment, $labelValuesOnly)
    {
        $result = [];
        foreach ($keys as $key) {
            if (!isset($this->_paymentMapFull[$key])) {
                $this->_paymentMapFull[$key] = [];
            }

            if (!isset($this->_paymentMapFull[$key]['label'])) {
                if (!$payment->hasAdditionalInformation($key)) {
                    $this->_paymentMapFull[$key]['label'] = false;
                    $this->_paymentMapFull[$key]['value'] = false;
                } else {
                    $value = $payment->getAdditionalInformation($key);
                    $this->_paymentMapFull[$key]['label'] = $this->_getLabel($key);
                    $this->_paymentMapFull[$key]['value'] = $this->_getValue($value, $key);
                }
            }

            if (!empty($this->_paymentMapFull[$key]['value'])) {
                if ($labelValuesOnly) {
                    $result[$this->_paymentMapFull[$key]['label']] = $this->_paymentMapFull[$key]['value'];
                } else {
                    $result[$key] = $this->_paymentMapFull[$key];
                }
            }
        }

        return $result;
    }

    /**
     * Render info item labels
     *
     * @param string $key
     * @return string
     */
    protected function _getLabel($key)
    {
        return match ($key) {
            'paypal_payer_id' => Mage::helper('paypal')->__('Payer ID'),
            'paypal_payer_email' => Mage::helper('paypal')->__('Payer Email'),
            'paypal_payer_status' => Mage::helper('paypal')->__('Payer Status'),
            'paypal_address_id' => Mage::helper('paypal')->__('Payer Address ID'),
            'paypal_address_status' => Mage::helper('paypal')->__('Payer Address Status'),
            'paypal_protection_eligibility' => Mage::helper('paypal')->__('Merchant Protection Eligibility'),
            'paypal_fraud_filters' => Mage::helper('paypal')->__('Triggered Fraud Filters'),
            'paypal_correlation_id' => Mage::helper('paypal')->__('Last Correlation ID'),
            'paypal_avs_code' => Mage::helper('paypal')->__('Address Verification System Response'),
            'paypal_cvv2_match' => Mage::helper('paypal')->__('CVV2 Check Result by PayPal'),
            self::BUYER_TAX_ID => Mage::helper('paypal')->__("Buyer's Tax ID"),
            self::BUYER_TAX_ID_TYPE => Mage::helper('paypal')->__("Buyer's Tax ID Type"),
            self::CENTINEL_VPAS => Mage::helper('paypal')->__('PayPal/Centinel Visa Payer Authentication Service Result'),
            self::CENTINEL_ECI => Mage::helper('paypal')->__('PayPal/Centinel Electronic Commerce Indicator'),
            default => '',
        };
    }

    /**
     * Get case type label
     *
     * @param string $key
     * @return string
     */
    public static function getCaseTypeLabel($key)
    {
        $labels = [
            'chargeback' => Mage::helper('paypal')->__('Chargeback'),
            'complaint'  => Mage::helper('paypal')->__('Complaint'),
            'dispute'    => Mage::helper('paypal')->__('Dispute'),
        ];
        return (array_key_exists($key, $labels) && !empty($labels[$key])) ? $labels[$key] : '';
    }

    /**
     * Apply a filter upon value getting
     *
     * @param string $value
     * @param string $key
     * @return string
     */
    protected function _getValue($value, $key)
    {
        $label = '';
        switch ($key) {
            case 'paypal_avs_code':
                $label = $this->_getAvsLabel($value);
                break;
            case 'paypal_cvv2_match':
                $label = $this->_getCvv2Label($value);
                break;
            case self::CENTINEL_VPAS:
                $label = $this->_getCentinelVpasLabel($value);
                break;
            case self::CENTINEL_ECI:
                $label = $this->_getCentinelEciLabel($value);
                break;
            case self::BUYER_TAX_ID_TYPE:
                $value = $this->_getBuyerIdTypeValue($value);
                // no break
            default:
                return $value;
        }

        return sprintf('#%s%s', $value, $value == $label ? '' : ': ' . $label);
    }

    /**
     * Attempt to convert AVS check result code into label
     *
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_AVSResponseCodes
     * @param string $value
     * @return string
     */
    protected function _getAvsLabel($value)
    {
        return match ($value) {
            'A', 'YN' => Mage::helper('paypal')->__('Matched Address only (no ZIP)'),
            // international "A"
            'B' => Mage::helper('paypal')->__('Matched Address only (no ZIP). International'),
            'N' => Mage::helper('paypal')->__('No Details matched'),
            // international "N"
            'C' => Mage::helper('paypal')->__('No Details matched. International'),
            'X' => Mage::helper('paypal')->__('Exact Match. Address and nine-digit ZIP code'),
            // international "X"
            'D' => Mage::helper('paypal')->__('Exact Match. Address and Postal Code. International'),
            // UK-specific "X"
            'F' => Mage::helper('paypal')->__('Exact Match. Address and Postal Code. UK-specific'),
            'E' => Mage::helper('paypal')->__('N/A. Not allowed for MOTO (Internet/Phone) transactions'),
            'G' => Mage::helper('paypal')->__('N/A. Global Unavailable'),
            'I' => Mage::helper('paypal')->__('N/A. International Unavailable'),
            'Z', 'NY' => Mage::helper('paypal')->__('Matched five-digit ZIP only (no Address)'),
            'P', 'NY' => Mage::helper('paypal')->__('Matched Postal Code only (no Address)'),
            'R' => Mage::helper('paypal')->__('N/A. Retry'),
            'S' => Mage::helper('paypal')->__('N/A. Service not Supported'),
            'U' => Mage::helper('paypal')->__('N/A. Unavailable'),
            'W' => Mage::helper('paypal')->__('Matched whole nine-didgit ZIP (no Address)'),
            'Y' => Mage::helper('paypal')->__('Yes. Matched Address and five-didgit ZIP'),
            '0' => Mage::helper('paypal')->__('All the address information matched'),
            '1' => Mage::helper('paypal')->__('None of the address information matched'),
            '2' => Mage::helper('paypal')->__('Part of the address information matched'),
            '3' => Mage::helper('paypal')->__('N/A. The merchant did not provide AVS information'),
            '4' => Mage::helper('paypal')->__('N/A. Address not checked, or acquirer had no response. Service not available'),
            default => $value,
        };
    }

    /**
     * Attempt to convert CVV2 check result code into label
     *
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_AVSResponseCodes
     * @param string $value
     * @return string
     */
    protected function _getCvv2Label($value)
    {
        return match ($value) {
            'M' => Mage::helper('paypal')->__('Matched (CVV2CSC)'),
            'N' => Mage::helper('paypal')->__('No match'),
            'P' => Mage::helper('paypal')->__('N/A. Not processed'),
            'S' => Mage::helper('paypal')->__('N/A. Service not supported'),
            'U' => Mage::helper('paypal')->__('N/A. Service not available'),
            'X' => Mage::helper('paypal')->__('N/A. No response'),
            '0' => Mage::helper('paypal')->__('Matched (CVV2)'),
            '1' => Mage::helper('paypal')->__('No match'),
            '2' => Mage::helper('paypal')->__('N/A. The merchant has not implemented CVV2 code handling'),
            '3' => Mage::helper('paypal')->__('N/A. Merchant has indicated that CVV2 is not present on card'),
            '4' => Mage::helper('paypal')->__('N/A. Service not available'),
            default => $value,
        };
    }

    /**
     * Attempt to convert centinel VPAS result into label
     *
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_DoDirectPayment
     * @param string $value
     * @return string
     */
    private function _getCentinelVpasLabel($value)
    {
        return match ($value) {
            '2', 'D' => Mage::helper('paypal')->__('Authenticated, Good Result'),
            '1' => Mage::helper('paypal')->__('Authenticated, Bad Result'),
            '3', '6', '8', 'A', 'C' => Mage::helper('paypal')->__('Attempted Authentication, Good Result'),
            '4', '7', '9' => Mage::helper('paypal')->__('Attempted Authentication, Bad Result'),
            '', '0', 'B' => Mage::helper('paypal')->__('No Liability Shift'),
            default => $value,
        };
    }

    /**
     * Attempt to convert centinel ECI result into label
     *
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_DoDirectPayment
     * @param string $value
     * @return string
     */
    private function _getCentinelEciLabel($value)
    {
        return match ($value) {
            '01', '07' => Mage::helper('paypal')->__('Merchant Liability'),
            '02', '05', '06' => Mage::helper('paypal')->__('Issuer Liability'),
            default => $value,
        };
    }

    /**
     * Retrieve buyer id type value based on code received from PayPal (Brazil only)
     *
     * @param string $code
     * @return string
     */
    protected function _getBuyerIdTypeValue($code)
    {
        $value = '';
        return match ($code) {
            self::BUYER_TAX_ID_TYPE_CNPJ => Mage::helper('paypal')->__('CNPJ'),
            self::BUYER_TAX_ID_TYPE_CPF => Mage::helper('paypal')->__('CPF'),
            default => $value,
        };
    }
}
