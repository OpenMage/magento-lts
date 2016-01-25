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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/*
 * Model for report rows
 */
/**
 * Enter description here ...
 *
 * @method Mage_Paypal_Model_Resource_Report_Settlement_Row _getResource()
 * @method Mage_Paypal_Model_Resource_Report_Settlement_Row getResource()
 * @method int getReportId()
 * @method Mage_Paypal_Model_Report_Settlement_Row setReportId(int $value)
 * @method string getTransactionId()
 * @method Mage_Paypal_Model_Report_Settlement_Row setTransactionId(string $value)
 * @method string getInvoiceId()
 * @method Mage_Paypal_Model_Report_Settlement_Row setInvoiceId(string $value)
 * @method string getPaypalReferenceId()
 * @method Mage_Paypal_Model_Report_Settlement_Row setPaypalReferenceId(string $value)
 * @method string getPaypalReferenceIdType()
 * @method Mage_Paypal_Model_Report_Settlement_Row setPaypalReferenceIdType(string $value)
 * @method string getTransactionEventCode()
 * @method Mage_Paypal_Model_Report_Settlement_Row setTransactionEventCode(string $value)
 * @method string getTransactionInitiationDate()
 * @method Mage_Paypal_Model_Report_Settlement_Row setTransactionInitiationDate(string $value)
 * @method string getTransactionCompletionDate()
 * @method Mage_Paypal_Model_Report_Settlement_Row setTransactionCompletionDate(string $value)
 * @method string getTransactionDebitOrCredit()
 * @method Mage_Paypal_Model_Report_Settlement_Row setTransactionDebitOrCredit(string $value)
 * @method float getGrossTransactionAmount()
 * @method Mage_Paypal_Model_Report_Settlement_Row setGrossTransactionAmount(float $value)
 * @method string getGrossTransactionCurrency()
 * @method Mage_Paypal_Model_Report_Settlement_Row setGrossTransactionCurrency(string $value)
 * @method string getFeeDebitOrCredit()
 * @method Mage_Paypal_Model_Report_Settlement_Row setFeeDebitOrCredit(string $value)
 * @method float getFeeAmount()
 * @method Mage_Paypal_Model_Report_Settlement_Row setFeeAmount(float $value)
 * @method string getFeeCurrency()
 * @method Mage_Paypal_Model_Report_Settlement_Row setFeeCurrency(string $value)
 * @method string getCustomField()
 * @method Mage_Paypal_Model_Report_Settlement_Row setCustomField(string $value)
 * @method string getConsumerId()
 * @method Mage_Paypal_Model_Report_Settlement_Row setConsumerId(string $value)
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Model_Report_Settlement_Row extends Mage_Core_Model_Abstract
{
    /**
     * Assoc array event code => label
     *
     * @var array
     */
    protected static $_eventList = array();

    /**
     * Casted amount keys registry
     *
     * @var array
     */
    protected $_castedAmounts = array();

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('paypal/report_settlement_row');
    }

    /**
     * Return description of Reference ID Type
     * If no code specified, return full list of codes with their description
     *
     * @param string code
     * @return string|array
     */
    public function getReferenceType($code = null)
    {
        $types = array(
            'TXN' => Mage::helper('paypal')->__('Transaction ID'),
            'ODR' => Mage::helper('paypal')->__('Order ID'),
            'SUB' => Mage::helper('paypal')->__('Subscription ID'),
            'PAP' => Mage::helper('paypal')->__('Preapproved Payment ID')
        );
        if($code === null) {
            asort($types);
            return $types;
        }
        if (isset($types[$code])) {
            return $types[$code];
        }
        return $code;
    }

    /**
     * Get native description for transaction code
     *
     * @param string code
     * @return string
     */
    public function getTransactionEvent($code)
    {
        $this->_generateEventLabels();
        if (isset(self::$_eventList[$code])) {
            return self::$_eventList[$code];
        }
        return $code;
    }

    /**
     * Get full list of codes with their description
     *
     * @return &array
     */
    public function &getTransactionEvents()
    {
        $this->_generateEventLabels();
        return self::$_eventList;
    }

    /**
     * Return description of "Debit or Credit" value
     * If no code specified, return full list of codes with their description
     *
     * @param string code
     * @return string|array
     */
    public function getDebitCreditText($code = null)
    {
        $options = array(
            'CR' => Mage::helper('paypal')->__('Credit'),
            'DR' => Mage::helper('paypal')->__('Debit'),
        );
        if($code === null) {
            return $options;
        }
        if (isset($options[$code])) {
            return $options[$code];
        }
        return $code;
    }

    /**
     * Invoke casting some amounts
     *
     * @param mixed $key
     * @param mixed $index
     * @return mixed
     */
    public function getData($key = '', $index = null)
    {
        $this->_castAmount('fee_amount', 'fee_debit_or_credit');
        $this->_castAmount('gross_transaction_amount', 'transaction_debit_or_credit');
        return parent::getData($key, $index);
    }

    /**
     * Cast amounts of the specified keys
     *
     * PayPal settlement reports contain amounts in cents, hence the values need to be divided by 100
     * Also if the "credit" value is detected, it will be casted to negative amount
     *
     * @param string $key
     * @param string $creditKey
     */
    public function _castAmount($key, $creditKey)
    {
        if (isset($this->_castedAmounts[$key]) || !isset($this->_data[$key]) || !isset($this->_data[$creditKey])) {
            return;
        }
        if (empty($this->_data[$key])) {
            return;
        }
        $amount = $this->_data[$key] / 100;
        if ('CR' === $this->_data[$creditKey]) {
            $amount = -1 * $amount;
        }
        $this->_data[$key] = $amount;
        $this->_castedAmounts[$key] = true;
    }

    /**
     * Fill/translate and sort all event codes/labels
     */
    protected function _generateEventLabels()
    {
        if (!self::$_eventList) {
            self::$_eventList = array(
            'T0000' => Mage::helper('paypal')->__('General: received payment of a type not belonging to the other T00xx categories'),
            'T0001' => Mage::helper('paypal')->__('Mass Pay Payment'),
            'T0002' => Mage::helper('paypal')->__('Subscription Payment, either payment sent or payment received'),
            'T0003' => Mage::helper('paypal')->__('Preapproved Payment (BillUser API), either sent or received'),
            'T0004' => Mage::helper('paypal')->__('eBay Auction Payment'),
            'T0005' => Mage::helper('paypal')->__('Direct Payment API'),
            'T0006' => Mage::helper('paypal')->__('Express Checkout APIs'),
            'T0007' => Mage::helper('paypal')->__('Website Payments Standard Payment'),
            'T0008' => Mage::helper('paypal')->__('Postage Payment to either USPS or UPS'),
            'T0009' => Mage::helper('paypal')->__('Gift Certificate Payment: purchase of Gift Certificate'),
            'T0010' => Mage::helper('paypal')->__('Auction Payment other than through eBay'),
            'T0011' => Mage::helper('paypal')->__('Mobile Payment (made via a mobile phone)'),
            'T0012' => Mage::helper('paypal')->__('Virtual Terminal Payment'),
            'T0100' => Mage::helper('paypal')->__('General: non-payment fee of a type not belonging to the other T01xx categories'),
            'T0101' => Mage::helper('paypal')->__('Fee: Web Site Payments Pro Account Monthly'),
            'T0102' => Mage::helper('paypal')->__('Fee: Foreign ACH Withdrawal'),
            'T0103' => Mage::helper('paypal')->__('Fee: WorldLink Check Withdrawal'),
            'T0104' => Mage::helper('paypal')->__('Fee: Mass Pay Request'),
            'T0200' => Mage::helper('paypal')->__('General Currency Conversion'),
            'T0201' => Mage::helper('paypal')->__('User-initiated Currency Conversion'),
            'T0202' => Mage::helper('paypal')->__('Currency Conversion required to cover negative balance'),
            'T0300' => Mage::helper('paypal')->__('General Funding of PayPal Account '),
            'T0301' => Mage::helper('paypal')->__('PayPal Balance Manager function of PayPal account'),
            'T0302' => Mage::helper('paypal')->__('ACH Funding for Funds Recovery from Account Balance'),
            'T0303' => Mage::helper('paypal')->__('EFT Funding (German banking)'),
            'T0400' => Mage::helper('paypal')->__('General Withdrawal from PayPal Account'),
            'T0401' => Mage::helper('paypal')->__('AutoSweep'),
            'T0500' => Mage::helper('paypal')->__('General: Use of PayPal account for purchasing as well as receiving payments'),
            'T0501' => Mage::helper('paypal')->__('Virtual PayPal Debit Card Transaction'),
            'T0502' => Mage::helper('paypal')->__('PayPal Debit Card Withdrawal from ATM'),
            'T0503' => Mage::helper('paypal')->__('Hidden Virtual PayPal Debit Card Transaction'),
            'T0504' => Mage::helper('paypal')->__('PayPal Debit Card Cash Advance'),
            'T0600' => Mage::helper('paypal')->__('General: Withdrawal from PayPal Account'),
            'T0700' => Mage::helper('paypal')->__('General (Purchase with a credit card)'),
            'T0701' => Mage::helper('paypal')->__('Negative Balance'),
            'T0800' => Mage::helper('paypal')->__('General: bonus of a type not belonging to the other T08xx categories'),
            'T0801' => Mage::helper('paypal')->__('Debit Card Cash Back'),
            'T0802' => Mage::helper('paypal')->__('Merchant Referral Bonus'),
            'T0803' => Mage::helper('paypal')->__('Balance Manager Account Bonus'),
            'T0804' => Mage::helper('paypal')->__('PayPal Buyer Warranty Bonus'),
            'T0805' => Mage::helper('paypal')->__('PayPal Protection Bonus'),
            'T0806' => Mage::helper('paypal')->__('Bonus for first ACH Use'),
            'T0900' => Mage::helper('paypal')->__('General Redemption'),
            'T0901' => Mage::helper('paypal')->__('Gift Certificate Redemption'),
            'T0902' => Mage::helper('paypal')->__('Points Incentive Redemption'),
            'T0903' => Mage::helper('paypal')->__('Coupon Redemption'),
            'T0904' => Mage::helper('paypal')->__('Reward Voucher Redemption'),
            'T1000' => Mage::helper('paypal')->__('General. Product no longer supported'),
            'T1100' => Mage::helper('paypal')->__('General: reversal of a type not belonging to the other T11xx categories'),
            'T1101' => Mage::helper('paypal')->__('ACH Withdrawal'),
            'T1102' => Mage::helper('paypal')->__('Debit Card Transaction'),
            'T1103' => Mage::helper('paypal')->__('Reversal of Points Usage'),
            'T1104' => Mage::helper('paypal')->__('ACH Deposit (Reversal)'),
            'T1105' => Mage::helper('paypal')->__('Reversal of General Account Hold'),
            'T1106' => Mage::helper('paypal')->__('Account-to-Account Payment, initiated by PayPal'),
            'T1107' => Mage::helper('paypal')->__('Payment Refund initiated by merchant'),
            'T1108' => Mage::helper('paypal')->__('Fee Reversal'),
            'T1110' => Mage::helper('paypal')->__('Hold for Dispute Investigation'),
            'T1111' => Mage::helper('paypal')->__('Reversal of hold for Dispute Investigation'),
            'T1200' => Mage::helper('paypal')->__('General: adjustment of a type not belonging to the other T12xx categories'),
            'T1201' => Mage::helper('paypal')->__('Chargeback'),
            'T1202' => Mage::helper('paypal')->__('Reversal'),
            'T1203' => Mage::helper('paypal')->__('Charge-off'),
            'T1204' => Mage::helper('paypal')->__('Incentive'),
            'T1205' => Mage::helper('paypal')->__('Reimbursement of Chargeback'),
            'T1300' => Mage::helper('paypal')->__('General (Authorization)'),
            'T1301' => Mage::helper('paypal')->__('Reauthorization'),
            'T1302' => Mage::helper('paypal')->__('Void'),
            'T1400' => Mage::helper('paypal')->__('General (Dividend)'),
            'T1500' => Mage::helper('paypal')->__('General: temporary hold of a type not belonging to the other T15xx categories'),
            'T1501' => Mage::helper('paypal')->__('Open Authorization'),
            'T1502' => Mage::helper('paypal')->__('ACH Deposit (Hold for Dispute or Other Investigation)'),
            'T1503' => Mage::helper('paypal')->__('Available Balance'),
            'T1600' => Mage::helper('paypal')->__('Funding'),
            'T1700' => Mage::helper('paypal')->__('General: Withdrawal to Non-Bank Entity'),
            'T1701' => Mage::helper('paypal')->__('WorldLink Withdrawal'),
            'T1800' => Mage::helper('paypal')->__('Buyer Credit Payment'),
            'T1900' => Mage::helper('paypal')->__('General Adjustment without businessrelated event'),
            'T2000' => Mage::helper('paypal')->__('General (Funds Transfer from PayPal Account to Another)'),
            'T2001' => Mage::helper('paypal')->__('Settlement Consolidation'),
            'T9900' => Mage::helper('paypal')->__('General: event not yet categorized'),
            );
            asort(self::$_eventList);
        }
    }
}
