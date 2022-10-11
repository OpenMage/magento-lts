<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order model
 *
 * Supported events:
 *  sales_order_load_after
 *  sales_order_save_before
 *  sales_order_save_after
 *  sales_order_delete_before
 *  sales_order_delete_after
 *
 * @method Mage_Sales_Model_Resource_Order _getResource()
 * @method Mage_Sales_Model_Resource_Order getResource()
 * @method Mage_Sales_Model_Resource_Order_Collection getCollection()
 *
 * @method float getAdjustmentNegative()
 * @method $this setAdjustmentNegative(float $value)
 * @method float getAdjustmentPositive()
 * @method $this setAdjustmentPositive(float $value)
 * @method string getAppliedRuleIds()
 * @method $this setAppliedRuleIds(string $value)
 * @method array getAppliedTaxes()
 * @method $this setAppliedTaxes(array $value)
 * @method bool getAppliedTaxIsSaved()
 * @method $this setAppliedTaxIsSaved(bool $value)
 *
 * @method string getBackUrl()
 * @method float getBaseAdjustmentNegative()
 * @method $this setBaseAdjustmentNegative(float $value)
 * @method float getBaseAdjustmentPositive()
 * @method $this setBaseAdjustmentPositive(float $value)
 * @method string getBaseCurrencyCode()
 * @method $this setBaseCurrencyCode(string $value)
 * @method float getBaseDiscountAmount()
 * @method $this setBaseDiscountAmount(float $value)
 * @method float getBaseDiscountCanceled()
 * @method $this setBaseDiscountCanceled(float $value)
 * @method float getBaseDiscountInvoiced()
 * @method $this setBaseDiscountInvoiced(float $value)
 * @method float getBaseDiscountRefunded()
 * @method $this setBaseDiscountRefunded(float $value)
 * @method float getBaseGrandTotal()
 * @method $this setBaseGrandTotal(float $value)
 * @method float getBaseHiddenTaxAmount()
 * @method $this setBaseHiddenTaxAmount(float $value)
 * @method float getBaseHiddenTaxInvoiced()
 * @method $this setBaseHiddenTaxInvoiced(float $value)
 * @method float getBaseHiddenTaxRefunded()
 * @method $this setBaseHiddenTaxRefunded(float $value)
 * @method float getBaseShippingAmount()
 * @method $this setBaseShippingAmount(float $value)
 * @method float getBaseShippingCanceled()
 * @method $this setBaseShippingCanceled(float $value)
 * @method float getBaseShippingDiscountAmount()
 * @method $this setBaseShippingDiscountAmount(float $value)
 * @method float getBaseShippingHiddenTaxAmount()
 * @method $this setBaseShippingHiddenTaxAmount(float $value)
 * @method float getBaseShippingHiddenTaxInvoiced()
 * @method float getBaseShippingHiddenTaxRefunded()
 * @method float getBaseShippingInclTax()
 * @method $this setBaseShippingInclTax(float $value)
 * @method float getBaseShippingInvoiced()
 * @method $this setBaseShippingInvoiced(float $value)
 * @method float getBaseShippingRefunded()
 * @method $this setBaseShippingRefunded(float $value)
 * @method float getBaseShippingTaxAmount()
 * @method $this setBaseShippingTaxAmount(float $value)
 * @method float getBaseShippingTaxInvoiced()
 * @method $this setBaseShippingTaxInvoiced(float $value)
 * @method float getBaseShippingTaxRefunded()
 * @method $this setBaseShippingTaxRefunded(float $value)
 * @method float getBaseSubtotal()
 * @method $this setBaseSubtotal(float $value)
 * @method float getBaseSubtotalCanceled()
 * @method $this setBaseSubtotalCanceled(float $value)
 * @method float getBaseSubtotalInclTax()
 * @method $this setBaseSubtotalInclTax(float $value)
 * @method float getBaseSubtotalInvoiced()
 * @method $this setBaseSubtotalInvoiced(float $value)
 * @method float getBaseSubtotalRefunded()
 * @method $this setBaseSubtotalRefunded(float $value)
 * @method float getBaseTaxAmount()
 * @method $this setBaseTaxAmount(float $value)
 * @method float getBaseTaxCanceled()
 * @method $this setBaseTaxCanceled(float $value)
 * @method float getBaseTaxInvoiced()
 * @method $this setBaseTaxInvoiced(float $value)
 * @method float getBaseTaxRefunded()
 * @method $this setBaseTaxRefunded(float $value)
 * @method float getBaseToGlobalRate()
 * @method $this setBaseToGlobalRate(float $value)
 * @method float getBaseToOrderRate()
 * @method $this setBaseToOrderRate(float $value)
 * @method float getBaseTotalCanceled()
 * @method $this setBaseTotalCanceled(float $value)
 * @method $this setBaseTotalDue(float $value)
 * @method float getBaseTotalInvoiced()
 * @method $this setBaseTotalInvoiced(float $value)
 * @method float getBaseTotalInvoicedCost()
 * @method $this setBaseTotalInvoicedCost(float $value)
 * @method float getBaseTotalOfflineRefunded()
 * @method $this setBaseTotalOfflineRefunded(float $value)
 * @method float getBaseTotalOnlineRefunded()
 * @method $this setBaseTotalOnlineRefunded(float $value)
 * @method float getBaseTotalPaid()
 * @method $this setBaseTotalPaid(float $value)
 * @method float getBaseTotalQtyOrdered()
 * @method $this setBaseTotalQtyOrdered(float $value)
 * @method float getBaseTotalRefunded()
 * @method $this setBaseTotalRefunded(float $value)
 * @method bool hasBillingAddressId()
 * @method int getBillingAddressId()
 * @method $this setBillingAddressId(int $value)
 * @method $this unsBillingAddressId()
 * @method int getBillingFirstname()
 * @method int getBillingLastname()
 *
 * @method bool hasCanReturnToStock()
 * @method bool getCanReturnToStock()
 * @method $this setCanReturnToStock()
 * @method int getCanShipPartially()
 * @method $this setCanShipPartially(int $value)
 * @method int getCanShipPartiallyItem()
 * @method $this setCanShipPartiallyItem(int $value)
 * @method bool getConvertingFromQuote()
 * @method $this setConvertingFromQuote(bool $value)
 * @method string getCouponCode()
 * @method $this setCouponCode(string $value)
 * @method $this setCouponRuleName(string $value)
 * @method string getCreatedAt()
 * @method $this setCreatedAt(string $value)
 * @method Mage_Customer_Model_Customer getCustomer()
 * @method $this setCustomer(Mage_Customer_Model_Customer $value)
 * @method string getCustomerDob()
 * @method $this setCustomerDob(string $value)
 * @method string getCustomerEmail()
 * @method $this setCustomerEmail(string $value)
 * @method string getCustomerFirstname()
 * @method $this setCustomerFirstname(string $value)
 * @method int getCustomerGender()
 * @method $this setCustomerGender(int $value)
 * @method int getCustomerGroupId()
 * @method $this setCustomerGroupId(int $value)
 * @method int getCustomerId()
 * @method $this setCustomerId(int $value)
 * @method int getCustomerIsGuest()
 * @method $this setCustomerIsGuest(int $value)
 * @method string getCustomerLastname()
 * @method $this setCustomerLastname(string $value)
 * @method string getCustomerMiddlename()
 * @method $this setCustomerMiddlename(string $value)
 * @method string getCustomerNote()
 * @method $this setCustomerNote(string $value)
 * @method bool hasCustomerNoteNotify()
 * @method int getCustomerNoteNotify()
 * @method $this setCustomerNoteNotify(int $value)
 * @method string getCustomerPrefix()
 * @method $this setCustomerPrefix(string $value)
 * @method string getCustomerSuffix()
 * @method $this setCustomerSuffix(string $value)
 * @method string getCustomerTaxvat()
 * @method $this setCustomerTaxvat(string $value)
 *
 * @method float getDiscountAmount()
 * @method $this setDiscountAmount(float $value)
 * @method float getDiscountCanceled()
 * @method $this setDiscountCanceled(float $value)
 * @method string getDiscountDescription()
 * @method $this setDiscountDescription(string $value)
 * @method float getDiscountInvoiced()
 * @method $this setDiscountInvoiced(float $value)
 * @method float getDiscountRefunded()
 * @method $this setDiscountRefunded(float $value)
 *
 * @method int getEditIncrement()
 * @method $this setEditIncrement(int $value)
 * @method int getEmailSent()
 * @method $this setEmailSent(int $value)
 * @method string getExtCustomerId()
 * @method $this setExtCustomerId(string $value)
 * @method string getExtOrderId()
 * @method $this setExtOrderId(string $value)
 *
 * @method bool hasForcedCanCreditmemo()
 * @method bool getForcedCanCreditmemo()
 * @method $this setForcedCanCreditmemo(bool $value)
 * @method int getForcedDoShipmentWithInvoice()
 * @method $this setForcedDoShipmentWithInvoice(int $value)
 *
 * @method $this setGiftMessage(string $value)
 * @method int getGiftMessageId()
 * @method $this setGiftMessageId(int $value)
 * @method string getGlobalCurrencyCode()
 * @method $this setGlobalCurrencyCode(string $value)
 * @method float getGrandTotal()
 * @method $this setGrandTotal(float $value)
 *
 * @method float getHiddenTaxAmount()
 * @method $this setHiddenTaxAmount(float $value)
 * @method float getHiddenTaxInvoiced()
 * @method $this setHiddenTaxInvoiced(float $value)
 * @method float getHiddenTaxRefunded()
 * @method $this setHiddenTaxRefunded(float $value)
 * @method string getHoldBeforeState()
 * @method $this setHoldBeforeState(string $value)
 * @method string getHoldBeforeStatus()
 * @method $this setHoldBeforeStatus(string $value)
 *
 * @method string getIncrementId()
 * @method $this setIncrementId(string $value)
 * @method bool getIsInProcess()
 * @method $this setIsInProcess(bool $value)
 * @method bool getIsMultiPayment()
 *
 * @method string getOrderCurrencyCode()
 * @method $this setOrderCurrencyCode(string $value)
 * @method string getOriginalIncrementId()
 * @method $this setOriginalIncrementId(string $value)
 *
 * @method float getPaymentAuthorizationAmount()
 * @method $this setPaymentAuthorizationAmount(float $value)
 * @method int getPaymentAuthorizationExpiration()
 * @method $this setPaymentAuthorizationExpiration(int $value)
 * @method int getPaypalIpnCustomerNotified()
 * @method $this setPaypalIpnCustomerNotified(int $value)
 * @method string getProtectCode()
 * @method $this setProtectCode(string $value)
 *
 * @method float getQuantity()
 * @method Mage_Sales_Model_Quote getQuote()
 * @method int getQuoteAddressId()
 * @method $this setQuoteAddressId(int $value)
 * @method float getQuoteBaseGrandTotal()
 * @method int getQuoteId()
 * @method $this setQuoteId(int $value)
 * @method $this setQuote(Mage_Sales_Model_Quote $value)
 *
 * @method string getRelationChildId()
 * @method $this setRelationChildId(string $value)
 * @method string getRelationChildRealId()
 * @method $this setRelationChildRealId(string $value)
 * @method string getRelationParentId()
 * @method $this setRelationParentId(string $value)
 * @method string getRelationParentRealId()
 * @method $this setRelationParentRealId(string $value)
 * @method string getRemoteIp()
 * @method $this setRemoteIp(string $value)
 * @method bool getReordered()
 * @method float getRevenue()
 * @method int getRowTaxDisplayPrecision()
 *
 * @method float getShipping()
 * @method bool hasShippingAddressId()
 * @method int getShippingAddressId()
 * @method $this setShippingAddressId(int $value)
 * @method $this unsShippingAddressId()
 * @method float getShippingAmount()
 * @method $this setShippingAmount(float $value)
 * @method float getShippingCanceled()
 * @method $this setShippingCanceled(float $value)
 * @method string getShippingDescription()
 * @method $this setShippingDescription(string $value)
 * @method float getShippingDiscountAmount()
 * @method $this setShippingDiscountAmount(float $value)
 * @method float getShippingHiddenTaxAmount()
 * @method $this setShippingHiddenTaxAmount(float $value)
 * @method float getShippingHiddenTaxInvoiced()
 * @method float getShippingHiddenTaxRefunded()
 * @method float getShippingInclTax()
 * @method $this setShippingInclTax(float $value)
 * @method float getShippingInvoiced()
 * @method $this setShippingInvoiced(float $value)
 * @method $this setShippingMethod(string $value)
 * @method float getShippingRefunded()
 * @method $this setShippingRefunded(float $value)
 * @method float getShippingTaxAmount()
 * @method $this setShippingTaxAmount(float $value)
 * @method float getShippingTaxInvoiced()
 * @method $this setShippingTaxInvoiced(float $value)
 * @method float getShippingTaxRefunded()
 * @method $this setShippingTaxRefunded(float $value)
 * @method string getState()
 * @method string getStatus()
 * @method $this setStatus(string $value)
 * @method string getStoreCurrencyCode()
 * @method $this setStoreCurrencyCode(string $value)
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method string getStoreName()
 * @method $this setStoreName(string $value)
 * @method float getStoreToBaseRate()
 * @method $this setStoreToBaseRate(float $value)
 * @method float getStoreToOrderRate()
 * @method $this setStoreToOrderRate(float $value)
 * @method float getSubtotal()
 * @method $this setSubtotal(float $value)
 * @method float getSubtotalCanceled()
 * @method $this setSubtotalCanceled(float $value)
 * @method float getSubtotalInclTax()
 * @method $this setSubtotalInclTax(float $value)
 * @method float getSubtotalInvoiced()
 * @method $this setSubtotalInvoiced(float $value)
 * @method float getSubtotalRefunded()
 * @method $this setSubtotalRefunded(float $value)
 *
 * @method float getTax()
 * @method float getTaxAmount()
 * @method $this setTaxAmount(float $value)
 * @method float getTaxCanceled()
 * @method $this setTaxCanceled(float $value)
 * @method float getTaxInvoiced()
 * @method $this setTaxInvoiced(float $value)
 * @method float getTaxRefunded()
 * @method $this setTaxRefunded(float $value)
 * @method float getTotalCanceled()
 * @method $this setTotalCanceled(float $value)
 * @method $this setTotalDue(float $value)
 * @method float getTotalInvoiced()
 * @method $this setTotalInvoiced(float $value)
 * @method int getTotalItemCount()
 * @method $this setTotalItemCount(int $value)
 * @method float getTotalOfflineRefunded()
 * @method $this setTotalOfflineRefunded(float $value)
 * @method float getTotalOnlineRefunded()
 * @method $this setTotalOnlineRefunded(float $value)
 * @method float getTotalPaid()
 * @method $this setTotalPaid(float $value)
 * @method float getTotalQtyOrdered()
 * @method $this setTotalQtyOrdered(float $value)
 * @method float getTotalRefunded()
 * @method $this setTotalRefunded(float $value)
 *
 * @method string getUpdatedAt()
 * @method $this setUpdatedAt(string $value)
 *
 * @method int getIsVirtual()
 * @method $this setIsVirtual(int $value)
 *
 * @method float getWeight()
 * @method $this setWeight(float $value)
 *
 * @method string getXForwardedFor()
 * @method $this setXForwardedFor(string $value)
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order extends Mage_Sales_Model_Abstract
{
    /**
     * Identifier for history item
     */
    const ENTITY                                = 'order';

    /**
     * Event type names for order emails
     */
    const EMAIL_EVENT_NAME_NEW_ORDER    = 'new_order';
    const EMAIL_EVENT_NAME_UPDATE_ORDER = 'update_order';

    /**
     * XML configuration paths
     */
    const XML_PATH_EMAIL_TEMPLATE               = 'sales_email/order/template';
    const XML_PATH_EMAIL_GUEST_TEMPLATE         = 'sales_email/order/guest_template';
    const XML_PATH_EMAIL_IDENTITY               = 'sales_email/order/identity';
    const XML_PATH_EMAIL_COPY_TO                = 'sales_email/order/copy_to';
    const XML_PATH_EMAIL_COPY_METHOD            = 'sales_email/order/copy_method';
    const XML_PATH_EMAIL_ENABLED                = 'sales_email/order/enabled';

    const XML_PATH_UPDATE_EMAIL_TEMPLATE        = 'sales_email/order_comment/template';
    const XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE  = 'sales_email/order_comment/guest_template';
    const XML_PATH_UPDATE_EMAIL_IDENTITY        = 'sales_email/order_comment/identity';
    const XML_PATH_UPDATE_EMAIL_COPY_TO         = 'sales_email/order_comment/copy_to';
    const XML_PATH_UPDATE_EMAIL_COPY_METHOD     = 'sales_email/order_comment/copy_method';
    const XML_PATH_UPDATE_EMAIL_ENABLED         = 'sales_email/order_comment/enabled';

    /**
     * Order states
     */
    const STATE_NEW             = 'new';
    const STATE_PENDING_PAYMENT = 'pending_payment';
    const STATE_PROCESSING      = 'processing';
    const STATE_COMPLETE        = 'complete';
    const STATE_CLOSED          = 'closed';
    const STATE_CANCELED        = 'canceled';
    const STATE_HOLDED          = 'holded';
    const STATE_PAYMENT_REVIEW  = 'payment_review';

    /**
     * Order statuses
     */
    const STATUS_FRAUD  = 'fraud';

    /**
     * Order flags
     */
    const ACTION_FLAG_CANCEL                    = 'cancel';
    const ACTION_FLAG_HOLD                      = 'hold';
    const ACTION_FLAG_UNHOLD                    = 'unhold';
    const ACTION_FLAG_EDIT                      = 'edit';
    const ACTION_FLAG_CREDITMEMO                = 'creditmemo';
    const ACTION_FLAG_INVOICE                   = 'invoice';
    const ACTION_FLAG_REORDER                   = 'reorder';
    const ACTION_FLAG_SHIP                      = 'ship';
    const ACTION_FLAG_COMMENT                   = 'comment';
    const ACTION_FLAG_PRODUCTS_PERMISSION_DENIED= 'product_permission_denied';

    /**
     * Report date types
     */
    const REPORT_DATE_TYPE_CREATED = 'created';
    const REPORT_DATE_TYPE_UPDATED = 'updated';
    /**
     * Identifier for history item
     */
    const HISTORY_ENTITY_NAME = 'order';

    protected $_eventPrefix = 'sales_order';
    protected $_eventObject = 'order';

    /**
     * @var Mage_Sales_Model_Resource_Order_Address_Collection|Mage_Sales_Model_Order_Address[]
     */
    protected $_addresses       = null;

    /**
     * @var Mage_Sales_Model_Resource_Order_Item_Collection|Mage_Sales_Model_Order_Item[]
     */
    protected $_items           = null;

    /**
     * @var Mage_Sales_Model_Resource_Order_Payment_Collection|Mage_Sales_Model_Order_Payment[]
     */
    protected $_payments        = null;

    /**
     * @var Mage_Sales_Model_Resource_Order_Status_History_Collection|Mage_Sales_Model_Order_Status_History[]
     */
    protected $_statusHistory   = null;

    /**
     * @var Mage_Sales_Model_Resource_Order_Invoice_Collection
     */
    protected $_invoices;

    /**
     * @var Mage_Sales_Model_Resource_Order_Shipment_Track_Collection
     */
    protected $_tracks;

    /**
     * @var Mage_Sales_Model_Resource_Order_Shipment_Collection|false
     */
    protected $_shipments;

    /**
     * @var Mage_Sales_Model_Resource_Order_Creditmemo_Collection|Mage_Sales_Model_Order_Creditmemo[]|false
     */
    protected $_creditmemos;

    protected $_relatedObjects  = [];
    protected $_orderCurrency   = null;
    protected $_baseCurrency    = null;

    /**
     * Array of action flags for canUnhold, canEdit, etc.
     *
     * @var array
     */
    protected $_actionFlag = [];

    /**
     * Flag: if after order placing we can send new email to the customer.
     *
     * @var bool
     */
    protected $_canSendNewEmailFlag = true;

    /**
     * Identifier for history item
     *
     * @var string
     */
    protected $_historyEntityName = self::HISTORY_ENTITY_NAME;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('sales/order');
    }

     /**
     * Init mapping array of short fields to
     * its full names
     *
     * @return Varien_Object
     */
    protected function _initOldFieldsMap()
    {
        // pre 1.6 fields names, old => new
        $this->_oldFieldsMap = [
            'payment_authorization_expiration' => 'payment_auth_expiration',
            'forced_do_shipment_with_invoice' => 'forced_shipment_with_invoice',
            'base_shipping_hidden_tax_amount' => 'base_shipping_hidden_tax_amnt',
        ];
        return $this;
    }

    /**
     * Clear order object data
     *
     * @param string $key data key
     * @return $this
     */
    public function unsetData($key = null)
    {
        parent::unsetData($key);
        if (is_null($key)) {
            $this->_items = null;
        }
        return $this;
    }

    /**
     * Retrieve can flag for action (edit, unhold, etc..)
     *
     * @param string $action
     * @return boolean|null
     */
    public function getActionFlag($action)
    {
        if (isset($this->_actionFlag[$action])) {
            return $this->_actionFlag[$action];
        }
        return null;
    }

    /**
     * Set can flag value for action (edit, unhold, etc...)
     *
     * @param string $action
     * @param boolean $flag
     * @return $this
     */
    public function setActionFlag($action, $flag)
    {
        $this->_actionFlag[$action] = (boolean) $flag;
        return $this;
    }

    /**
     * Return flag for order if it can sends new email to customer.
     *
     * @return bool
     */
    public function getCanSendNewEmailFlag()
    {
        return $this->_canSendNewEmailFlag;
    }

    /**
     * Set flag for order if it can sends new email to customer.
     *
     * @param bool $flag
     * @return $this
     */
    public function setCanSendNewEmailFlag($flag)
    {
        $this->_canSendNewEmailFlag = (boolean) $flag;
        return $this;
    }

    /**
     * Load order by system increment identifier
     *
     * @param string $incrementId
     * @return $this
     */
    public function loadByIncrementId($incrementId)
    {
        return $this->loadByAttribute('increment_id', $incrementId);
    }

    /**
     * Load order by custom attribute value. Attribute value should be unique
     *
     * @param string $attribute
     * @param string $value
     * @return $this
     */
    public function loadByAttribute($attribute, $value)
    {
        $this->load($value, $attribute);
        return $this;
    }

    /**
     * Retrieve store model instance
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        $storeId = $this->getStoreId();
        if ($storeId) {
            return Mage::app()->getStore($storeId);
        }
        return Mage::app()->getStore();
    }

    /**
     * Retrieve order cancel availability
     *
     * @return bool
     */
    public function canCancel()
    {
        if (!$this->_canVoidOrder()) {
            return false;
        }
        if ($this->canUnhold()) {  // $this->isPaymentReview()
            return false;
        }

        $allInvoiced = true;
        foreach ($this->getAllItems() as $item) {
            if ($item->getQtyToInvoice()) {
                $allInvoiced = false;
                break;
            }
        }
        if ($allInvoiced) {
            return false;
        }

        $state = $this->getState();
        if ($this->isCanceled() || $state === self::STATE_COMPLETE || $state === self::STATE_CLOSED) {
            return false;
        }

        if ($this->getActionFlag(self::ACTION_FLAG_CANCEL) === false) {
            return false;
        }
        /**
         * Use only state for availability detect
         */
        /*foreach ($this->getAllItems() as $item) {
            if ($item->getQtyToCancel()>0) {
                return true;
            }
        }
        return false;*/
        return true;
    }

    /**
     * Getter whether the payment can be voided
     *
     * @return bool
     */
    public function canVoidPayment()
    {
        return $this->_canVoidOrder() ? $this->getPayment()->canVoid($this->getPayment()) : false;
    }

    /**
     * Check whether order could be canceled by states and flags
     *
     * @return bool
     */
    protected function _canVoidOrder()
    {
        if ($this->canUnhold() || $this->isPaymentReview()) {
            return false;
        }
        return true;
    }

    /**
     * Retrieve order invoice availability
     *
     * @return bool
     */
    public function canInvoice()
    {
        if ($this->canUnhold() || $this->isPaymentReview()) {
            return false;
        }
        $state = $this->getState();
        if ($this->isCanceled() || $state === self::STATE_COMPLETE || $state === self::STATE_CLOSED) {
            return false;
        }

        if ($this->getActionFlag(self::ACTION_FLAG_INVOICE) === false) {
            return false;
        }

        foreach ($this->getAllItems() as $item) {
            if ($item->getQtyToInvoice()>0 && !$item->getLockedDoInvoice()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieve order credit memo (refund) availability
     *
     * @return bool
     */
    public function canCreditmemo()
    {
        if ($this->hasForcedCanCreditmemo()) {
            return $this->getForcedCanCreditmemo();
        }

        if ($this->canUnhold() || $this->isPaymentReview()) {
            return false;
        }

        if ($this->isCanceled() || $this->getState() === self::STATE_CLOSED) {
            return false;
        }

        /**
         * We can have problem with float in php (on some server $a=762.73;$b=762.73; $a-$b!=0)
         * for this we have additional diapason for 0
         * TotalPaid - contains amount, that were not rounded.
         */
        if (abs($this->getStore()->roundPrice($this->getTotalPaid()) - $this->getTotalRefunded()) < .0001) {
            return false;
        }

        if ($this->getActionFlag(self::ACTION_FLAG_EDIT) === false) {
            return false;
        }
        return true;
    }

    /**
     * Retrieve order hold availability
     *
     * @return bool
     */
    public function canHold()
    {
        $state = $this->getState();
        if ($this->isCanceled() || $this->isPaymentReview()
            || $state === self::STATE_COMPLETE || $state === self::STATE_CLOSED || $state === self::STATE_HOLDED) {
            return false;
        }

        if ($this->getActionFlag(self::ACTION_FLAG_HOLD) === false) {
            return false;
        }
        return true;
    }

    /**
     * Retrieve order unhold availability
     *
     * @return bool
     */
    public function canUnhold()
    {
        if ($this->getActionFlag(self::ACTION_FLAG_UNHOLD) === false || $this->isPaymentReview()) {
            return false;
        }
        return $this->getState() === self::STATE_HOLDED;
    }

    /**
     * Check if comment can be added to order history
     *
     * @return bool
     */
    public function canComment()
    {
        if ($this->getActionFlag(self::ACTION_FLAG_COMMENT) === false) {
            return false;
        }
        return true;
    }

    /**
     * Retrieve order shipment availability
     *
     * @return bool
     */
    public function canShip()
    {
        if ($this->canUnhold() || $this->isPaymentReview()) {
            return false;
        }

        if ($this->getIsVirtual() || $this->isCanceled()) {
            return false;
        }

        if ($this->getActionFlag(self::ACTION_FLAG_SHIP) === false) {
            return false;
        }

        foreach ($this->getAllItems() as $item) {
            if ($item->getQtyToShip()>0 && !$item->getIsVirtual()
                && !$item->getLockedDoShip()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieve order edit availability
     *
     * @return bool
     */
    public function canEdit()
    {
        if ($this->canUnhold()) {
            return false;
        }

        $state = $this->getState();
        if ($this->isCanceled() || $this->isPaymentReview()
            || $state === self::STATE_COMPLETE || $state === self::STATE_CLOSED) {
            return false;
        }

        if (!$this->getPayment()->getMethodInstance()->canEdit()) {
            return false;
        }

        if ($this->getActionFlag(self::ACTION_FLAG_EDIT) === false) {
            return false;
        }

        return true;
    }

    /**
     * Retrieve order reorder availability
     *
     * @return bool
     */
    public function canReorder()
    {
        return $this->_canReorder(true);
    }

    /**
     * Check the ability to reorder ignoring the availability in stock or status of the ordered products
     *
     * @return bool
     */
    public function canReorderIgnoreSalable()
    {
        return $this->_canReorder(true);
    }

    /**
     * Retrieve order reorder availability
     *
     * @param bool $ignoreSalable
     * @return bool
     */
    protected function _canReorder($ignoreSalable = false)
    {
        if ($this->canUnhold() || $this->isPaymentReview()) {
            return false;
        }

        if ($this->getActionFlag(self::ACTION_FLAG_REORDER) === false) {
            return false;
        }

        $products = [];
        foreach ($this->getItemsCollection() as $item) {
            $products[] = $item->getProductId();
        }

        if (!empty($products)) {
            /*
             * @TODO ACPAOC: Use product collection here, but ensure that product
             * is loaded with order store id, otherwise there'll be problems with isSalable()
             * for configurables, bundles and other composites
             *
             */
            /*
            $productsCollection = Mage::getModel('catalog/product')->getCollection()
                ->setStoreId($this->getStoreId())
                ->addIdFilter($products)
                ->addAttributeToSelect('status')
                ->load();

            foreach ($productsCollection as $product) {
                if (!$product->isSalable()) {
                    return false;
                }
            }
            */

            foreach ($products as $productId) {
                $product = Mage::getModel('catalog/product')
                    ->setStoreId($this->getStoreId())
                    ->load($productId);
                if (!$product->getId() || (!$ignoreSalable && !$product->isSalable())) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check whether the payment is in payment review state
     * In this state order cannot be normally processed. Possible actions can be:
     * - accept or deny payment
     * - fetch transaction information
     *
     * @return bool
     */
    public function isPaymentReview()
    {
        return $this->getState() === self::STATE_PAYMENT_REVIEW;
    }

    /**
     * Check whether payment can be accepted or denied
     *
     * @return bool
     */
    public function canReviewPayment()
    {
        return $this->isPaymentReview() && $this->getPayment()->canReviewPayment();
    }

    /**
     * Check whether there can be a transaction update fetched for payment in review state
     *
     * @return bool
     */
    public function canFetchPaymentReviewUpdate()
    {
        return $this->isPaymentReview() && $this->getPayment()->canFetchTransactionInfo();
    }

    /**
     * Retrieve order configuration model
     *
     * @return Mage_Sales_Model_Order_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('sales/order_config');
    }

    /**
     * Place order payments
     *
     * @return $this
     */
    protected function _placePayment()
    {
        $this->getPayment()->place();
        return $this;
    }

    /**
     * Retrieve order payment model object
     *
     * @return Mage_Sales_Model_Order_Payment|false
     */
    public function getPayment()
    {
        foreach ($this->getPaymentsCollection() as $payment) {
            if (!$payment->isDeleted()) {
                return $payment;
            }
        }
        return false;
    }

    /**
     * Declare order billing address
     *
     * @param   Mage_Sales_Model_Order_Address $address
     * @return  $this
     */
    public function setBillingAddress(Mage_Sales_Model_Order_Address $address)
    {
        $old = $this->getBillingAddress();
        if (!empty($old)) {
            $address->setId($old->getId());
        }
        $this->addAddress($address->setAddressType('billing'));
        return $this;
    }

    /**
     * Declare order shipping address
     *
     * @param   Mage_Sales_Model_Order_Address $address
     * @return  $this
     */
    public function setShippingAddress(Mage_Sales_Model_Order_Address $address)
    {
        $old = $this->getShippingAddress();
        if (!empty($old)) {
            $address->setId($old->getId());
        }
        $this->addAddress($address->setAddressType('shipping'));
        return $this;
    }

    /**
     * Retrieve order billing address
     *
     * @return Mage_Sales_Model_Order_Address|false
     */
    public function getBillingAddress()
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getAddressType()=='billing' && !$address->isDeleted()) {
                return $address;
            }
        }
        return false;
    }

    /**
     * Retrieve order shipping address
     *
     * @return Mage_Sales_Model_Order_Address|false
     */
    public function getShippingAddress()
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getAddressType()=='shipping' && !$address->isDeleted()) {
                return $address;
            }
        }
        return false;
    }

    /**
     * Order state setter.
     * If status is specified, will add order status history with specified comment
     * the setData() cannot be overriden because of compatibility issues with resource model
     *
     * @param string $state
     * @param string|bool $status
     * @param string $comment
     * @param bool $isCustomerNotified
     * @return $this
     */
    public function setState($state, $status = false, $comment = '', $isCustomerNotified = null)
    {
        return $this->_setState($state, $status, $comment, $isCustomerNotified, true);
    }

    /**
     * Order state protected setter.
     * By default allows to set any state. Can also update status to default or specified value
     * Сomplete and closed states are encapsulated intentionally, see the _checkState()
     *
     * @param string $state
     * @param string|bool $status
     * @param string $comment
     * @param bool $isCustomerNotified
     * @param bool $shouldProtectState
     * @return $this
     */
    protected function _setState(
        $state,
        $status = false,
        $comment = '',
        $isCustomerNotified = null,
        $shouldProtectState = false
    ) {
        // attempt to set the specified state
        if ($shouldProtectState) {
            if ($this->isStateProtected($state)) {
                Mage::throwException(
                    Mage::helper('sales')->__('The Order State "%s" must not be set manually.', $state)
                );
            }
        }
        $this->setData('state', $state);

        // add status history
        if ($status) {
            if ($status === true) {
                $status = $this->getConfig()->getStateDefaultStatus($state);
            }
            $this->setStatus($status);
            $history = $this->addStatusHistoryComment($comment, false); // no sense to set $status again
            $history->setIsCustomerNotified($isCustomerNotified); // for backwards compatibility
        }
        return $this;
    }

    /**
     * Whether specified state can be set from outside
     * @param string $state
     * @return bool
     */
    public function isStateProtected($state)
    {
        if (empty($state)) {
            return false;
        }
        return self::STATE_COMPLETE == $state || self::STATE_CLOSED == $state;
    }

    /**
     * Retrieve label of order status
     *
     * @return string
     */
    public function getStatusLabel()
    {
        return $this->getConfig()->getStatusLabel($this->getStatus());
    }

    /**
     * Add status change information to history
     * @deprecated after 1.4.0.0-alpha3
     *
     * @param  string $status
     * @param  string $comment
     * @param  bool $isCustomerNotified
     * @return $this
     */
    public function addStatusToHistory($status, $comment = '', $isCustomerNotified = false)
    {
        $history = $this->addStatusHistoryComment($comment, $status)
            ->setIsCustomerNotified($isCustomerNotified);
        return $this;
    }

    /**
     * Add a comment to order
     * Different or default status may be specified
     *
     * @param string $comment
     * @param bool|string $status
     * @return Mage_Sales_Model_Order_Status_History
     */
    public function addStatusHistoryComment($comment, $status = false)
    {
        if ($status === false) {
            $status = $this->getStatus();
        } elseif ($status === true) {
            $status = $this->getConfig()->getStateDefaultStatus($this->getState());
        } else {
            $this->setStatus($status);
        }
        $history = Mage::getModel('sales/order_status_history')
            ->setStatus($status)
            ->setComment($comment)
            ->setEntityName($this->_historyEntityName);
        $this->addStatusHistory($history);
        return $history;
    }

    /**
     * Overrides entity id, which will be saved to comments history status
     *
     * @param string $entityName
     * @return $this
     */
    public function setHistoryEntityName($entityName)
    {
        $this->_historyEntityName = $entityName;
        return $this;
    }

    /**
     * Place order
     *
     * @return $this
     */
    public function place()
    {
        Mage::dispatchEvent('sales_order_place_before', ['order'=>$this]);
        $this->_placePayment();
        Mage::dispatchEvent('sales_order_place_after', ['order'=>$this]);
        return $this;
    }

    /**
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function hold()
    {
        if (!$this->canHold()) {
            Mage::throwException(Mage::helper('sales')->__('Hold action is not available.'));
        }
        $this->setHoldBeforeState($this->getState());
        $this->setHoldBeforeStatus($this->getStatus());
        $this->setState(self::STATE_HOLDED, true);
        return $this;
    }

    /**
     * Attempt to unhold the order
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function unhold()
    {
        if (!$this->canUnhold()) {
            Mage::throwException(Mage::helper('sales')->__('Unhold action is not available.'));
        }
        $this->setState($this->getHoldBeforeState(), $this->getHoldBeforeStatus());
        $this->setHoldBeforeState(null);
        $this->setHoldBeforeStatus(null);
        return $this;
    }

    /**
     * Cancel order
     * @param string $comment
     * @return $this
     */
    public function cancel($comment = '')
    {
        if ($this->canCancel()) {
            $this->getPayment()->cancel();
            $this->registerCancellation($comment);
            Mage::dispatchEvent('order_cancel_after', ['order' => $this]);
        }

        return $this;
    }

    /**
     * Prepare order totals to cancellation
     * @param string $comment
     * @param bool $graceful
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function registerCancellation($comment = '', $graceful = true)
    {
        if ($this->canCancel() || $this->isPaymentReview()) {
            $cancelState = self::STATE_CANCELED;
            foreach ($this->getAllItems() as $item) {
                if ($cancelState != self::STATE_PROCESSING && $item->getQtyToRefund()) {
                    if ($item->getQtyToShip() > $item->getQtyToCancel()) {
                        $cancelState = self::STATE_PROCESSING;
                    } else {
                        $cancelState = self::STATE_COMPLETE;
                    }
                }
                $item->cancel();
            }

            $this->setSubtotalCanceled($this->getSubtotal() - $this->getSubtotalInvoiced());
            $this->setBaseSubtotalCanceled($this->getBaseSubtotal() - $this->getBaseSubtotalInvoiced());

            $this->setTaxCanceled($this->getTaxAmount() - $this->getTaxInvoiced());
            $this->setBaseTaxCanceled($this->getBaseTaxAmount() - $this->getBaseTaxInvoiced());

            $this->setShippingCanceled($this->getShippingAmount() - $this->getShippingInvoiced());
            $this->setBaseShippingCanceled($this->getBaseShippingAmount() - $this->getBaseShippingInvoiced());

            $this->setDiscountCanceled(abs($this->getDiscountAmount()) - $this->getDiscountInvoiced());
            $this->setBaseDiscountCanceled(abs($this->getBaseDiscountAmount()) - $this->getBaseDiscountInvoiced());

            $this->setTotalCanceled($this->getGrandTotal() - $this->getTotalPaid());
            $this->setBaseTotalCanceled($this->getBaseGrandTotal() - $this->getBaseTotalPaid());

            $this->_setState($cancelState, true, $comment);
        } elseif (!$graceful) {
            Mage::throwException(Mage::helper('sales')->__('Order does not allow to be canceled.'));
        }
        return $this;
    }

    /**
     * Retrieve tracking numbers
     *
     * @return array
     */
    public function getTrackingNumbers()
    {
        if ($this->getData('tracking_numbers')) {
            return explode(',', $this->getData('tracking_numbers'));
        }
        return [];
    }

    /**
     * Return model of shipping carrier
     *
     * @return Mage_Shipping_Model_Carrier_Abstract
     */
    public function getShippingCarrier()
    {
        $carrierModel = $this->getData('shipping_carrier');
        if (is_null($carrierModel)) {
            $carrierModel = false;
            /**
             * $method - carrier_method
             */
            $method = $this->getShippingMethod(true);
            if ($method instanceof Varien_Object) {
                $className = Mage::getStoreConfig('carriers/' . $method->getCarrierCode() . '/model');
                if ($className) {
                    $carrierModel = Mage::getModel($className);
                }
            }
            $this->setData('shipping_carrier', $carrierModel);
        }
        return $carrierModel;
    }

    /**
     * Retrieve shipping method
     *
     * @param bool $asObject return carrier code and shipping method data as object
     * @return string|Varien_Object
     */
    public function getShippingMethod($asObject = false)
    {
        $shippingMethod = parent::getShippingMethod();
        if (!$asObject) {
            return $shippingMethod;
        } else {
            $segments = explode('_', $shippingMethod, 2);
            if (!isset($segments[1])) {
                $segments[1] = $segments[0];
            }
            list($carrierCode, $method) = $segments;
            return new Varien_Object([
                'carrier_code' => $carrierCode,
                'method'       => $method
            ]);
        }
    }

    /**
     * Queue email with new order data
     *
     * @param bool $forceMode if true then email will be sent regardless of the fact that it was already sent previously
     *
     * @return $this
     * @throws Exception
     */
    public function queueNewOrderEmail($forceMode = false)
    {
        $storeId = $this->getStore()->getId();

        if (!Mage::helper('sales')->canSendNewOrderEmail($storeId)) {
            return $this;
        }

        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $storeId);

        // Start store emulation process
        if ($storeId != Mage::app()->getStore()->getId()) {
            /** @var Mage_Core_Model_App_Emulation $appEmulation */
            $appEmulation = Mage::getSingleton('core/app_emulation');
            $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);
        }

        try {
            // Retrieve specified view block from appropriate design package (depends on emulated store)
            $paymentBlock = Mage::helper('payment')->getInfoBlock($this->getPayment())
                ->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($storeId);
            $paymentBlockHtml = $paymentBlock->toHtml();
        } catch (Exception $e) {
            // Stop store emulation process
            if (isset($appEmulation, $initialEnvironmentInfo)) {
                $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            }
            throw $e;
        }

        // Stop store emulation process
        if (isset($appEmulation, $initialEnvironmentInfo)) {
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
        }

        // Retrieve corresponding email template id and customer name
        if ($this->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $this->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $storeId);
            $customerName = $this->getCustomerName();
        }

        /** @var Mage_Core_Model_Email_Template_Mailer $mailer */
        $mailer = Mage::getModel('core/email_template_mailer');
        /** @var Mage_Core_Model_Email_Info $emailInfo */
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($this->getCustomerEmail(), $customerName);
        if ($copyTo && $copyMethod == 'bcc') {
            // Add bcc to customer email
            foreach ($copyTo as $email) {
                $emailInfo->addBcc($email);
            }
        }
        $mailer->addEmailInfo($emailInfo);

        // Email copies are sent as separated emails if their copy method is 'copy'
        if ($copyTo && $copyMethod == 'copy') {
            foreach ($copyTo as $email) {
                $emailInfo = Mage::getModel('core/email_info');
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams([
            'order'        => $this,
            'billing'      => $this->getBillingAddress(),
            'payment_html' => $paymentBlockHtml
        ]);

        /** @var Mage_Core_Model_Email_Queue $emailQueue */
        $emailQueue = Mage::getModel('core/email_queue');
        $emailQueue->setEntityId($this->getId())
            ->setEntityType(self::ENTITY)
            ->setEventType(self::EMAIL_EVENT_NAME_NEW_ORDER)
            ->setIsForceCheck(!$forceMode);

        $mailer->setQueue($emailQueue)->send();

        $this->setEmailSent(true);
        $this->_getResource()->saveAttribute($this, 'email_sent');

        return $this;
    }

    /**
     * Send email with order data
     *
     * @return $this
     */
    public function sendNewOrderEmail()
    {
        $this->queueNewOrderEmail(true);
        return $this;
    }

    /**
     * Queue email with order update information
     *
     * @param boolean $notifyCustomer
     * @param string $comment
     * @param bool $forceMode if true then email will be sent regardless of the fact that it was already sent previously
     *
     * @return $this
     */
    public function queueOrderUpdateEmail($notifyCustomer = true, $comment = '', $forceMode = false)
    {
        $storeId = $this->getStore()->getId();

        if (!Mage::helper('sales')->canSendOrderCommentEmail($storeId)) {
            return $this;
        }
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_UPDATE_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_COPY_METHOD, $storeId);
        // Check if at least one recipient is found
        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }

        // Retrieve corresponding email template id and customer name
        if ($this->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $this->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_TEMPLATE, $storeId);
            $customerName = $this->getCustomerName();
        }

        /** @var Mage_Core_Model_Email_Template_Mailer $mailer */
        $mailer = Mage::getModel('core/email_template_mailer');
        if ($notifyCustomer) {
            /** @var Mage_Core_Model_Email_Info $emailInfo */
            $emailInfo = Mage::getModel('core/email_info');
            $emailInfo->addTo($this->getCustomerEmail(), $customerName);
            if ($copyTo && $copyMethod == 'bcc') {
                // Add bcc to customer email
                foreach ($copyTo as $email) {
                    $emailInfo->addBcc($email);
                }
            }
            $mailer->addEmailInfo($emailInfo);
        }

        // Email copies are sent as separated emails if their copy method is
        // 'copy' or a customer should not be notified
        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {
            foreach ($copyTo as $email) {
                $emailInfo = Mage::getModel('core/email_info');
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams([
                'order'   => $this,
                'comment' => $comment,
                'billing' => $this->getBillingAddress()
        ]);

        /** @var Mage_Core_Model_Email_Queue $emailQueue */
        $emailQueue = Mage::getModel('core/email_queue');
        $emailQueue->setEntityId($this->getId())
            ->setEntityType(self::ENTITY)
            ->setEventType(self::EMAIL_EVENT_NAME_UPDATE_ORDER)
            ->setIsForceCheck(!$forceMode);
        $mailer->setQueue($emailQueue)->send();

        return $this;
    }

    /**
     * Send email with order update information
     *
     * @param bool $notifyCustomer
     * @param string $comment
     *
     * @return $this
     */
    public function sendOrderUpdateEmail($notifyCustomer = true, $comment = '')
    {
        $this->queueOrderUpdateEmail($notifyCustomer, $comment, true);
         return $this;
    }

    /**
     * @param string $configPath
     * @return array|false
     */
    protected function _getEmails($configPath)
    {
        $data = Mage::getStoreConfig($configPath, $this->getStoreId());
        if (!empty($data)) {
            return explode(',', $data);
        }
        return false;
    }

/*********************** ADDRESSES ***************************/

    /**
     * @return Mage_Sales_Model_Resource_Order_Address_Collection
     */
    public function getAddressesCollection()
    {
        if (is_null($this->_addresses)) {
            $this->_addresses = Mage::getResourceModel('sales/order_address_collection')
                ->setOrderFilter($this);

            if ($this->getId()) {
                foreach ($this->_addresses as $address) {
                    $address->setOrder($this);
                }
            }
        }

        return $this->_addresses;
    }

    /**
     * @param int|string $addressId
     * @return false|Mage_Sales_Model_Order_Address
     */
    public function getAddressById($addressId)
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getId()==$addressId) {
                return $address;
            }
        }
        return false;
    }

    /**
     * @param Mage_Sales_Model_Order_Address $address
     * @return $this
     * @throws Exception
     */
    public function addAddress(Mage_Sales_Model_Order_Address $address)
    {
        $address->setOrder($this)->setParentId($this->getId());
        if (!$address->getId()) {
            $this->getAddressesCollection()->addItem($address);
        }
        return $this;
    }

    /**
     * @param array $filterByTypes
     * @param bool $nonChildrenOnly
     * @return Mage_Sales_Model_Resource_Order_Item_Collection
     */
    public function getItemsCollection($filterByTypes = [], $nonChildrenOnly = false)
    {
        if (is_null($this->_items)) {
            $this->_items = Mage::getResourceModel('sales/order_item_collection')
                ->setOrderFilter($this);

            if ($filterByTypes) {
                $this->_items->filterByTypes($filterByTypes);
            }
            if ($nonChildrenOnly) {
                $this->_items->filterByParent();
            }

            if ($this->getId()) {
                foreach ($this->_items as $item) {
                    $item->setOrder($this);
                }
            }
        }
        return $this->_items;
    }

    /**
     * Get random items collection with related children
     *
     * @param int $limit
     * @return Mage_Sales_Model_Resource_Order_Item_Collection
     */
    public function getItemsRandomCollection($limit = 1)
    {
        return $this->_getItemsRandomCollection($limit);
    }

    /**
     * Get random items collection without related children
     *
     * @param int $limit
     * @return Mage_Sales_Model_Resource_Order_Item_Collection
     */
    public function getParentItemsRandomCollection($limit = 1)
    {
        return $this->_getItemsRandomCollection($limit, true);
    }

    /**
     * Get random items collection with or without related children
     *
     * @param int $limit
     * @param bool $nonChildrenOnly
     * @return Mage_Sales_Model_Resource_Order_Item_Collection
     */
    protected function _getItemsRandomCollection($limit, $nonChildrenOnly = false)
    {
        $collection = Mage::getModel('sales/order_item')->getCollection()
            ->setOrderFilter($this)
            ->setRandomOrder();

        if ($nonChildrenOnly) {
            $collection->filterByParent();
        }
        $products = [];
        /** @var Mage_Sales_Model_Order_Item $item */
        foreach ($collection as $item) {
            $products[] = $item->getProductId();
        }

        $productsCollection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addIdFilter($products)
            ->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInSiteIds())
            /* Price data is added to consider item stock status using price index */
            ->addPriceData()
            ->setPageSize($limit)
            ->load();

        foreach ($collection as $item) {
            $product = $productsCollection->getItemById($item->getProductId());
            if ($product) {
                $item->setProduct($product);
            }
        }

        return $collection;
    }

    /**
     * @return Mage_Sales_Model_Order_Item[]
     */
    public function getAllItems()
    {
        $items = [];
        foreach ($this->getItemsCollection() as $item) {
            if (!$item->isDeleted()) {
                $items[] =  $item;
            }
        }
        return $items;
    }

    /**
     * @return array
     */
    public function getAllVisibleItems()
    {
        $items = [];
        foreach ($this->getItemsCollection() as $item) {
            if (!$item->isDeleted() && !$item->getParentItemId()) {
                $items[] =  $item;
            }
        }
        return $items;
    }

    /**
     * @param int $itemId
     * @return Varien_Object|null
     */
    public function getItemById($itemId)
    {
        return $this->getItemsCollection()->getItemById($itemId);
    }

    /**
     * @param int $quoteItemId
     * @return Mage_Sales_Model_Order_Item|null
     */
    public function getItemByQuoteItemId($quoteItemId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getQuoteItemId()==$quoteItemId) {
                return $item;
            }
        }
        return null;
    }

    /**
     * @param Mage_Sales_Model_Order_Item $item
     * @return $this
     * @throws Exception
     */
    public function addItem(Mage_Sales_Model_Order_Item $item)
    {
        $item->setOrder($this);
        if (!$item->getId()) {
            $this->getItemsCollection()->addItem($item);
        }
        return $this;
    }

    /**
     * Whether the order has nominal items only
     *
     * @return bool
     */
    public function isNominal()
    {
        foreach ($this->getAllVisibleItems() as $item) {
            if ($item->getIsNominal() == '0') {
                return false;
            }
        }
        return true;
    }

/*********************** PAYMENTS ***************************/

    /**
     * @return Mage_Sales_Model_Resource_Order_Payment_Collection
     */
    public function getPaymentsCollection()
    {
        if (is_null($this->_payments)) {
            $this->_payments = Mage::getResourceModel('sales/order_payment_collection')
                ->setOrderFilter($this);

            if ($this->getId()) {
                foreach ($this->_payments as $payment) {
                    $payment->setOrder($this);
                }
            }
        }
        return $this->_payments;
    }

    /**
     * @return Mage_Sales_Model_Order_Payment[]
     */
    public function getAllPayments()
    {
        $payments = [];
        foreach ($this->getPaymentsCollection() as $payment) {
            if (!$payment->isDeleted()) {
                $payments[] =  $payment;
            }
        }
        return $payments;
    }

    /**
     * @param int $paymentId
     * @return bool|Mage_Sales_Model_Order_Payment
     */
    public function getPaymentById($paymentId)
    {
        foreach ($this->getPaymentsCollection() as $payment) {
            if ($payment->getId()==$paymentId) {
                return $payment;
            }
        }
        return false;
    }

    /**
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return $this
     * @throws Exception
     */
    public function addPayment(Mage_Sales_Model_Order_Payment $payment)
    {
        $payment->setOrder($this)
            ->setParentId($this->getId());
        if (!$payment->getId()) {
            $this->getPaymentsCollection()->addItem($payment);
        }
        return $this;
    }

    /**
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Mage_Sales_Model_Order_Payment
     */
    public function setPayment(Mage_Sales_Model_Order_Payment $payment)
    {
        if (!$this->getIsMultiPayment() && ($old = $this->getPayment())) {
            $payment->setId($old->getId());
        }
        $this->addPayment($payment);
        return $payment;
    }

/*********************** STATUSES ***************************/

    /**
     * @param bool $reload
     * @return Mage_Sales_Model_Resource_Order_Status_History_Collection
     */
    public function getStatusHistoryCollection($reload = false)
    {
        if (is_null($this->_statusHistory) || $reload) {
            $this->_statusHistory = Mage::getResourceModel('sales/order_status_history_collection')
                ->setOrderFilter($this)
                ->setOrder('created_at', 'desc')
                ->setOrder('entity_id', 'desc');

            if ($this->getId()) {
                foreach ($this->_statusHistory as $status) {
                    $status->setOrder($this);
                }
            }
        }
        return $this->_statusHistory;
    }

    /**
     * Return collection of order status history items.
     *
     * @return Mage_Sales_Model_Order_Status_History[]
     */
    public function getAllStatusHistory()
    {
        $history = [];
        foreach ($this->getStatusHistoryCollection() as $status) {
            if (!$status->isDeleted()) {
                $history[] =  $status;
            }
        }
        return $history;
    }

    /**
     * Return collection of visible on frontend order status history items.
     *
     * @return array
     */
    public function getVisibleStatusHistory()
    {
        $history = [];
        foreach ($this->getStatusHistoryCollection() as $status) {
            if (!$status->isDeleted() && $status->getComment() && $status->getIsVisibleOnFront()) {
                $history[] =  $status;
            }
        }
        return $history;
    }

    /**
     * @param int $statusId
     * @return false|Mage_Sales_Model_Order_Status_History
     */
    public function getStatusHistoryById($statusId)
    {
        foreach ($this->getStatusHistoryCollection() as $status) {
            if ($status->getId()==$statusId) {
                return $status;
            }
        }
        return false;
    }

    /**
     * Set the order status history object and the order object to each other
     * Adds the object to the status history collection, which is automatically saved when the order is saved.
     * See the entity_id attribute backend model.
     * Or the history record can be saved standalone after this.
     *
     * @param Mage_Sales_Model_Order_Status_History $history
     * @return $this
     */
    public function addStatusHistory(Mage_Sales_Model_Order_Status_History $history)
    {
        $history->setOrder($this);
        $this->setStatus($history->getStatus());
        if (!$history->getId()) {
            $this->getStatusHistoryCollection()->addItem($history);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getRealOrderId()
    {
        $id = $this->getData('real_order_id');
        if (is_null($id)) {
            $id = $this->getIncrementId();
        }
        return $id;
    }

    /**
     * Get currency model instance. Will be used currency with which order placed
     *
     * @return Mage_Directory_Model_Currency
     */
    public function getOrderCurrency()
    {
        if (is_null($this->_orderCurrency)) {
            $this->_orderCurrency = Mage::getModel('directory/currency')->load($this->getOrderCurrencyCode());
        }
        return $this->_orderCurrency;
    }

    /**
     * Get formated price value including order currency rate to order website currency
     *
     * @param   float $price
     * @param   bool  $addBrackets
     * @return  string
     */
    public function formatPrice($price, $addBrackets = false)
    {
        return $this->formatPricePrecision($price, 2, $addBrackets);
    }

    /**
     * @param float $price
     * @param int $precision
     * @param bool $addBrackets
     * @return string
     */
    public function formatPricePrecision($price, $precision, $addBrackets = false)
    {
        return $this->getOrderCurrency()->formatPrecision($price, $precision, [], true, $addBrackets);
    }

    /**
     * Retrieve text formated price value includeing order rate
     *
     * @param   float $price
     * @return  string
     */
    public function formatPriceTxt($price)
    {
        return $this->getOrderCurrency()->formatTxt($price);
    }

    /**
     * Retrieve order website currency for working with base prices
     *
     * @return Mage_Directory_Model_Currency
     */
    public function getBaseCurrency()
    {
        if (is_null($this->_baseCurrency)) {
            $this->_baseCurrency = Mage::getModel('directory/currency')->load($this->getBaseCurrencyCode());
        }
        return $this->_baseCurrency;
    }

    /**
     * Retrieve order website currency for working with base prices
     * @deprecated  please use getBaseCurrency instead.
     *
     * @return Mage_Directory_Model_Currency
     */
    public function getStoreCurrency()
    {
        return $this->getData('store_currency');
    }

    /**
     * @param float $price
     * @return string
     */
    public function formatBasePrice($price)
    {
        return $this->formatBasePricePrecision($price, 2);
    }

    /**
     * @param float $price
     * @param int $precision
     * @return string
     */
    public function formatBasePricePrecision($price, $precision)
    {
        return $this->getBaseCurrency()->formatPrecision($price, $precision);
    }

    /**
     * @return bool
     */
    public function isCurrencyDifferent()
    {
        return $this->getOrderCurrencyCode() != $this->getBaseCurrencyCode();
    }

    /**
     * Retrieve order total due value
     *
     * @return float
     */
    public function getTotalDue()
    {
        $total = $this->getGrandTotal()-$this->getTotalPaid();
        $total = Mage::app()->getStore($this->getStoreId())->roundPrice($total);
        return max($total, 0);
    }

    /**
     * Retrieve order total due value
     *
     * @return float
     */
    public function getBaseTotalDue()
    {
        $total = $this->getBaseGrandTotal()-$this->getBaseTotalPaid();
        $total = Mage::app()->getStore($this->getStoreId())->roundPrice($total);
        return max($total, 0);
    }

    /**
     * @param string $key
     * @param int|string|null $index
     * @return float|mixed
     */
    public function getData($key = '', $index = null)
    {
        if ($key == 'total_due') {
            return $this->getTotalDue();
        }
        if ($key == 'base_total_due') {
            return $this->getBaseTotalDue();
        }
        return parent::getData($key, $index);
    }

    /**
     * Retrieve order invoices collection
     *
     * @return Mage_Sales_Model_Resource_Order_Invoice_Collection
     */
    public function getInvoiceCollection()
    {
        if (is_null($this->_invoices)) {
            $this->_invoices = Mage::getResourceModel('sales/order_invoice_collection')
                ->setOrderFilter($this);

            if ($this->getId()) {
                foreach ($this->_invoices as $invoice) {
                    $invoice->setOrder($this);
                }
            }
        }
        return $this->_invoices;
    }

     /**
     * Retrieve order shipments collection
     *
     * @return Mage_Sales_Model_Resource_Order_Shipment_Collection|false
     */
    public function getShipmentsCollection()
    {
        if (empty($this->_shipments)) {
            if ($this->getId()) {
                $this->_shipments = Mage::getResourceModel('sales/order_shipment_collection')
                    ->setOrderFilter($this)
                    ->load();
            } else {
                return false;
            }
        }
        return $this->_shipments;
    }

    /**
     * Retrieve order creditmemos collection
     *
     * @return  Mage_Sales_Model_Resource_Order_Creditmemo_Collection|Mage_Sales_Model_Order_Creditmemo[]|false
     */
    public function getCreditmemosCollection()
    {
        if (empty($this->_creditmemos)) {
            if ($this->getId()) {
                $this->_creditmemos = Mage::getResourceModel('sales/order_creditmemo_collection')
                    ->setOrderFilter($this)
                    ->load();
            } else {
                return false;
            }
        }
        return $this->_creditmemos;
    }

    /**
     * Retrieve order tracking numbers collection
     *
     * @return Mage_Sales_Model_Resource_Order_Shipment_Track_Collection
     */
    public function getTracksCollection()
    {
        if (empty($this->_tracks)) {
            $this->_tracks = Mage::getResourceModel('sales/order_shipment_track_collection')
                ->setOrderFilter($this);

            if ($this->getId()) {
                $this->_tracks->load();
            }
        }
        return $this->_tracks;
    }

    /**
     * Check order invoices availability
     *
     * @return bool
     */
    public function hasInvoices()
    {
        return $this->getInvoiceCollection()->count();
    }

    /**
     * Check order shipments availability
     *
     * @return bool
     */
    public function hasShipments()
    {
        $result = false;
        $shipmentsCollection = $this->getShipmentsCollection();
        if ($shipmentsCollection) {
            $result = (bool)$shipmentsCollection->count();
        }
        return $result;
    }

    /**
     * Check order creditmemos availability
     *
     * @return bool
     */
    public function hasCreditmemos()
    {
        $result = false;
        $creditmemosCollection = $this->getCreditmemosCollection();
        if ($creditmemosCollection) {
            $result = (bool)$creditmemosCollection->count();
        }
        return $result;
    }

    /**
     * Retrieve array of related objects
     *
     * Used for order saving
     *
     * @return array
     */
    public function getRelatedObjects()
    {
        return $this->_relatedObjects;
    }

    /**
     * Retrieve customer name
     *
     * @return string
     */
    public function getCustomerName()
    {
        if ($this->getCustomerFirstname()) {
            $customerName = Mage::helper('customer')->getFullCustomerName($this);
        } else {
            $customerName = Mage::helper('sales')->__('Guest');
        }
        return $customerName;
    }

    /**
     * Add New object to related array
     *
     * @param   Mage_Core_Model_Abstract $object
     * @return  $this
     */
    public function addRelatedObject(Mage_Core_Model_Abstract $object)
    {
        $this->_relatedObjects[] = $object;
        return $this;
    }

    /**
     * Get formated order created date in store timezone
     *
     * @param   string $format date format type (short|medium|long|full)
     * @return  string
     */
    public function getCreatedAtFormated($format)
    {
        return Mage::helper('core')->formatDate($this->getCreatedAtStoreDate(), $format, true);
    }

    /**
     * @return string
     */
    public function getEmailCustomerNote()
    {
        if ($this->getCustomerNoteNotify()) {
            return $this->getCustomerNote();
        }
        return '';
    }

    /**
     * Processing object before save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $this->_checkState();
        if (!$this->getId()) {
            $store = $this->getStore();
            $name = [$store->getWebsite()->getName(),$store->getGroup()->getName(),$store->getName()];
            $this->setStoreName(implode("\n", $name));
        }

        if (!$this->getIncrementId()) {
            $incrementId = Mage::getSingleton('eav/config')
                ->getEntityType('order')
                ->fetchNewIncrementId($this->getStoreId());
            $this->setIncrementId($incrementId);
        }

        /**
         * Process items dependency for new order
         */
        if (!$this->getId()) {
            $itemsCount = 0;
            foreach ($this->getAllItems() as $item) {
                $parent = $item->getQuoteParentItemId();
                if ($parent && !$item->getParentItem()) {
                    $item->setParentItem($this->getItemByQuoteItemId($parent));
                } elseif (!$parent) {
                    $itemsCount++;
                }
            }
            // Set items count
            $this->setTotalItemCount($itemsCount);
        }
        if ($this->getCustomer()) {
            $this->setCustomerId($this->getCustomer()->getId());
        }

        if ($this->hasBillingAddressId() && $this->getBillingAddressId() === null) {
            $this->unsBillingAddressId();
        }

        if ($this->hasShippingAddressId() && $this->getShippingAddressId() === null) {
            $this->unsShippingAddressId();
        }

        if (!$this->getId()) {
            $this->setData('protect_code', substr(md5(uniqid(mt_rand(), true) . ':' . microtime(true)), 5, 6));
        }
        return $this;
    }

    /**
     * Check order state before saving
     */
    protected function _checkState()
    {
        if (!$this->getId()) {
            return $this;
        }

        $userNotification = $this->hasCustomerNoteNotify() ? $this->getCustomerNoteNotify() : null;

        if (!$this->isCanceled()
            && !$this->canUnhold()
            && !$this->canInvoice()
            && !$this->canShip()) {
            if ($this->getBaseGrandTotal() == 0 || $this->canCreditmemo()) {
                if ($this->getState() !== self::STATE_COMPLETE) {
                    $this->_setState(self::STATE_COMPLETE, true, '', $userNotification);
                }
            } /**
             * Order can be closed just in case when we have refunded amount.
             * In case of "0" grand total order checking ForcedCanCreditmemo flag
             */
            elseif (floatval($this->getTotalRefunded()) || (!$this->getTotalRefunded()
                && $this->hasForcedCanCreditmemo())
            ) {
                if ($this->getState() !== self::STATE_CLOSED) {
                    $this->_setState(self::STATE_CLOSED, true, '', $userNotification);
                }
            }
        }

        if ($this->getState() == self::STATE_NEW && $this->getIsInProcess()) {
            $this->setState(self::STATE_PROCESSING, true, '', $userNotification);
        }
        return $this;
    }

    /**
     * Save order related objects
     *
     * @inheritDoc
     */
    protected function _afterSave()
    {
        if ($this->_addresses !== null) {
            $this->_addresses->save();
            $billingAddress = $this->getBillingAddress();
            $attributesForSave = [];
            if ($billingAddress && $this->getBillingAddressId() != $billingAddress->getId()) {
                $this->setBillingAddressId($billingAddress->getId());
                $attributesForSave[] = 'billing_address_id';
            }

            $shippingAddress = $this->getShippingAddress();
            if ($shippingAddress && $this->getShippingAddressId() != $shippingAddress->getId()) {
                $this->setShippingAddressId($shippingAddress->getId());
                $attributesForSave[] = 'shipping_address_id';
            }

            if (!empty($attributesForSave)) {
                $this->_getResource()->saveAttribute($this, $attributesForSave);
            }
        }
        if ($this->_items !== null) {
            $this->_items->save();
        }
        if ($this->_payments !== null) {
            $this->_payments->save();
        }
        if ($this->_statusHistory !== null) {
            $this->_statusHistory->save();
        }
        foreach ($this->getRelatedObjects() as $object) {
            $object->save();
        }
        return parent::_afterSave();
    }

    /**
     * @return string
     */
    public function getStoreGroupName()
    {
        $storeId = $this->getStoreId();
        if (is_null($storeId)) {
            return $this->getStoreName(1); // 0 - website name, 1 - store group name, 2 - store name
        }
        return $this->getStore()->getGroup()->getName();
    }

    /**
     * Resets all data in object
     * so after another load it will be complete new object
     *
     * @return $this
     */
    public function reset()
    {
        $this->unsetData();
        $this->_actionFlag = [];
        $this->_addresses = null;
        $this->_items = null;
        $this->_payments = null;
        $this->_statusHistory = null;
        $this->_invoices = null;
        $this->_tracks = null;
        $this->_shipments = null;
        $this->_creditmemos = null;
        $this->_relatedObjects = [];
        $this->_orderCurrency = null;
        $this->_baseCurrency = null;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsNotVirtual()
    {
        return !$this->getIsVirtual();
    }

    /**
     * @return mixed
     */
    public function getFullTaxInfo()
    {
        $rates = Mage::getModel('tax/sales_order_tax')->getCollection()->loadByOrder($this)->toArray();
        return Mage::getSingleton('tax/calculation')->reproduceProcess($rates['items']);
    }

    /**
     * Create new invoice with maximum qty for invoice for each item
     *
     * @param array $qtys
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function prepareInvoice($qtys = [])
    {
        return Mage::getModel('sales/service_order', $this)->prepareInvoice($qtys);
    }

    /**
     * Create new shipment with maximum qty for shipping for each item
     *
     * @param array $qtys
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function prepareShipment($qtys = [])
    {
        return Mage::getModel('sales/service_order', $this)->prepareShipment($qtys);
    }

    /**
     * Check whether order is canceled
     *
     * @return bool
     */
    public function isCanceled()
    {
        return ($this->getState() === self::STATE_CANCELED);
    }

    /**
     * Protect order delete from not admin scope
     * @inheritDoc
     */
    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        return parent::_beforeDelete();
    }
}
