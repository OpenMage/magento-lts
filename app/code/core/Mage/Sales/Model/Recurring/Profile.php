<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Sales implementation of recurring payment profiles
 * Implements saving and manageing profiles
 *
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Resource_Recurring_Profile _getResource()
 * @method string getAdditionalInfo()
 * @method int getBillFailedLater()
 * @method string getBillingAddressInfo()
 * @method float getBillingAmount()
 * @method Mage_Sales_Model_Resource_Recurring_Profile_Collection getCollection()
 * @method string getCreatedAt()
 * @method string getCurrencyCode()
 * @method int getCustomerId()
 * @method float getInitAmount()
 * @method int getInitMayFail()
 * @method string getInternalReferenceId()
 * @method string getMethodCode()
 * @method string getOrderInfo()
 * @method string getOrderItemInfo()
 * @method int getPeriodFrequency()
 * @method int getPeriodMaxCycles()
 * @method string getPeriodUnit()
 * @method string getProfileVendorInfo()
 * @method Mage_Sales_Model_Quote getQuote()
 * @method string getReferenceId()
 * @method Mage_Sales_Model_Resource_Recurring_Profile getResource()
 * @method Mage_Sales_Model_Resource_Recurring_Profile_Collection getResourceCollection()
 * @method string getScheduleDescription()
 * @method string getShippingAddressInfo()
 * @method float getShippingAmount()
 * @method string getStartDatetime()
 * @method string getState()
 * @method int getStoreId()
 * @method string getSubscriberName()
 * @method int getSuspensionThreshold()
 * @method float getTaxAmount()
 * @method float getTrialBillingAmount()
 * @method int getTrialPeriodFrequency()
 * @method int getTrialPeriodMaxCycles()
 * @method string getTrialPeriodUnit()
 * @method string getUpdatedAt()
 * @method $this setAdditionalInfo(string $value)
 * @method $this setBillFailedLater(int $value)
 * @method $this setBillingAddressInfo(string $value)
 * @method $this setBillingAmount(float $value)
 * @method $this setCreatedAt(string $value)
 * @method $this setCurrencyCode(string $value)
 * @method $this setCustomerId(int $value)
 * @method $this setInitAmount(float $value)
 * @method $this setInitMayFail(int $value)
 * @method $this setInternalReferenceId(string $value)
 * @method $this setMethodCode(string $value)
 * @method $this setNewState(string $value)
 * @method $this setOrderInfo(string $value)
 * @method $this setOrderItemInfo(string $value)
 * @method $this setPeriodFrequency(int $value)
 * @method $this setPeriodMaxCycles(int $value)
 * @method $this setPeriodUnit(string $value)
 * @method $this setProfileVendorInfo(string $value)
 * @method $this setReferenceId(string $value)
 * @method $this setScheduleDescription(string $value)
 * @method $this setShippingAddressInfo(string $value)
 * @method $this setShippingAmount(float $value)
 * @method $this setStartDatetime(string $value)
 * @method $this setState(string $value)
 * @method $this setStoreId(int $value)
 * @method $this setSubscriberName(string $value)
 * @method $this setSuspensionThreshold(int $value)
 * @method $this setTaxAmount(float $value)
 * @method $this setTrialBillingAmount(float $value)
 * @method $this setTrialPeriodFrequency(int $value)
 * @method $this setTrialPeriodMaxCycles(int $value)
 * @method $this setTrialPeriodUnit(string $value)
 * @method $this setUpdatedAt(string $value)
 */
class Mage_Sales_Model_Recurring_Profile extends Mage_Payment_Model_Recurring_Profile
{
    /**
     * Available states
     *
     * @var string
     */
    public const STATE_UNKNOWN   = 'unknown';

    public const STATE_PENDING   = 'pending';

    public const STATE_ACTIVE    = 'active';

    public const STATE_SUSPENDED = 'suspended';

    public const STATE_CANCELED  = 'canceled';

    public const STATE_EXPIRED   = 'expired';

    /**
     * Payment types
     *
     * @var string
     */
    public const PAYMENT_TYPE_REGULAR   = 'regular';

    public const PAYMENT_TYPE_TRIAL     = 'trial';

    public const PAYMENT_TYPE_INITIAL   = 'initial';

    /**
     * Allowed actions matrix
     *
     * @var array
     */
    protected $_workflow = null;

    /**
     * Load order by system increment identifier
     *
     * @param int $internalReferenceId
     * @return $this
     */
    public function loadByInternalReferenceId($internalReferenceId)
    {
        return $this->load($internalReferenceId, 'internal_reference_id');
    }

    /**
     * Submit a recurring profile right after an order is placed
     */
    public function submit()
    {
        $this->_getResource()->beginTransaction();
        try {
            $this->setInternalReferenceId(Mage::helper('core')->uniqHash('temporary-'));
            $this->save();
            $this->setInternalReferenceId(Mage::helper('core')->uniqHash($this->getId() . '-'));
            $this->getMethodInstance()->submitRecurringProfile($this, $this->getQuote()->getPayment());
            $this->save();
            $this->_getResource()->commit();
        } catch (Exception $exception) {
            $this->_getResource()->rollBack();
            throw $exception;
        }
    }

    /**
     * Activate the suspended profile
     */
    public function activate()
    {
        $this->_checkWorkflow(self::STATE_ACTIVE, false);
        $this->setNewState(self::STATE_ACTIVE);
        $this->getMethodInstance()->updateRecurringProfileStatus($this);
        $this->setState(self::STATE_ACTIVE)
            ->save();
    }

    /**
     * Check whether the workflow allows to activate the profile
     *
     * @return bool
     */
    public function canActivate()
    {
        return $this->_checkWorkflow(self::STATE_ACTIVE);
    }

    /**
     * Suspend active profile
     */
    public function suspend()
    {
        $this->_checkWorkflow(self::STATE_SUSPENDED, false);
        $this->setNewState(self::STATE_SUSPENDED);
        $this->getMethodInstance()->updateRecurringProfileStatus($this);
        $this->setState(self::STATE_SUSPENDED)
            ->save();
    }

    /**
     * Check whether the workflow allows to suspend the profile
     *
     * @return bool
     */
    public function canSuspend()
    {
        return $this->_checkWorkflow(self::STATE_SUSPENDED);
    }

    /**
     * Cancel active or suspended profile
     */
    public function cancel()
    {
        $this->_checkWorkflow(self::STATE_CANCELED, false);
        $this->setNewState(self::STATE_CANCELED);
        $this->getMethodInstance()->updateRecurringProfileStatus($this);
        $this->setState(self::STATE_CANCELED)
            ->save();
    }

    /**
     * Check whether the workflow allows to cancel the profile
     *
     * @return bool
     */
    public function canCancel()
    {
        return $this->_checkWorkflow(self::STATE_CANCELED);
    }

    public function fetchUpdate()
    {
        $result = new Varien_Object();
        $this->getMethodInstance()->getRecurringProfileDetails($this->getReferenceId(), $result);

        if ($result->getIsProfileActive()) {
            $this->setState(self::STATE_ACTIVE);
        } elseif ($result->getIsProfilePending()) {
            $this->setState(self::STATE_PENDING);
        } elseif ($result->getIsProfileCanceled()) {
            $this->setState(self::STATE_CANCELED);
        } elseif ($result->getIsProfileSuspended()) {
            $this->setState(self::STATE_SUSPENDED);
        } elseif ($result->getIsProfileExpired()) {
            $this->setState(self::STATE_EXPIRED);
        }
    }

    /**
     * @return mixed
     */
    public function canFetchUpdate()
    {
        return $this->getMethodInstance()->canGetRecurringProfileDetails();
    }

    /**
     * Initialize new order based on profile data
     *
     * Takes arbitrary number of Varien_Object instances to be treated as items for new order
     *
     * @return Mage_Sales_Model_Order
     */
    public function createOrder()
    {
        $items = [];
        $itemInfoObjects = func_get_args();

        $billingAmount = 0;
        $shippingAmount = 0;
        $taxAmount = 0;
        $isVirtual = 1;
        $weight = 0;
        foreach ($itemInfoObjects as $itemInfo) {
            $item = $this->_getItem($itemInfo);
            $billingAmount += $item->getPrice();
            $shippingAmount += $item->getShippingAmount();
            $taxAmount += $item->getTaxAmount();
            $weight += $item->getWeight();
            if (!$item->getIsVirtual()) {
                $isVirtual = 0;
            }

            $items[] = $item;
        }

        $grandTotal = $billingAmount + $shippingAmount + $taxAmount;

        $order = Mage::getModel('sales/order');

        $billingAddress = Mage::getModel('sales/order_address')
            ->setData($this->getBillingAddressInfo())
            ->setId(null);

        $shippingInfo = $this->getShippingAddressInfo();
        $shippingAddress = Mage::getModel('sales/order_address')
            ->setData($shippingInfo)
            ->setId(null);

        $payment = Mage::getModel('sales/order_payment')
            ->setMethod($this->getMethodCode());

        $transferDataKays = [
            'store_id',             'store_name',           'customer_id',          'customer_email',
            'customer_firstname',   'customer_lastname',    'customer_middlename',  'customer_prefix',
            'customer_suffix',      'customer_taxvat',      'customer_gender',      'customer_is_guest',
            'customer_note_notify', 'customer_group_id',    'customer_note',        'shipping_method',
            'shipping_description', 'base_currency_code',   'global_currency_code', 'order_currency_code',
            'store_currency_code',  'base_to_global_rate',  'base_to_order_rate',   'store_to_base_rate',
            'store_to_order_rate',
        ];

        $orderInfo = $this->getOrderInfo();
        foreach ($transferDataKays as $key) {
            if (isset($orderInfo[$key])) {
                $order->setData($key, $orderInfo[$key]);
            } elseif (isset($shippingInfo[$key])) {
                $order->setData($key, $shippingInfo[$key]);
            }
        }

        $order->setStoreId($this->getStoreId())
            ->setState(Mage_Sales_Model_Order::STATE_NEW)
            ->setBaseToOrderRate($this->getInfoValue('order_info', 'base_to_quote_rate'))
            ->setStoreToOrderRate($this->getInfoValue('order_info', 'store_to_quote_rate'))
            ->setOrderCurrencyCode($this->getInfoValue('order_info', 'quote_currency_code'))
            ->setBaseSubtotal($billingAmount)
            ->setSubtotal($billingAmount)
            ->setBaseShippingAmount($shippingAmount)
            ->setShippingAmount($shippingAmount)
            ->setBaseTaxAmount($taxAmount)
            ->setTaxAmount($taxAmount)
            ->setBaseGrandTotal($grandTotal)
            ->setGrandTotal($grandTotal)
            ->setIsVirtual($isVirtual)
            ->setWeight($weight)
            ->setTotalQtyOrdered($this->getInfoValue('order_info', 'items_qty'))
            ->setBillingAddress($billingAddress)
            ->setShippingAddress($shippingAddress)
            ->setPayment($payment);

        foreach ($items as $item) {
            $order->addItem($item);
        }

        return $order;
    }

    /**
     * Validate states
     *
     * @return bool
     */
    public function isValid()
    {
        parent::isValid();

        // state
        if (!in_array($this->getState(), $this->getAllStates(false), true)) {
            $this->_errors['state'][] = Mage::helper('sales')->__('Wrong state: "%s".', $this->getState());
        }

        return empty($this->_errors);
    }

    /**
     * Import quote information to the profile
     *
     * @return $this
     * @throws Exception
     */
    public function importQuote(Mage_Sales_Model_Quote $quote)
    {
        $this->setQuote($quote);

        if ($quote->getPayment() && $quote->getPayment()->getMethod()) {
            $this->setMethodInstance($quote->getPayment()->getMethodInstance());
        }

        $orderInfo = $quote->getData();
        $this->_cleanupArray($orderInfo);
        $this->setOrderInfo($orderInfo);

        $addressInfo = $quote->getBillingAddress()->getData();
        $this->_cleanupArray($addressInfo);
        $this->setBillingAddressInfo($addressInfo);
        if (!$quote->isVirtual()) {
            $addressInfo = $quote->getShippingAddress()->getData();
            $this->_cleanupArray($addressInfo);
            $this->setShippingAddressInfo($addressInfo);
        }

        $this->setCurrencyCode($quote->getBaseCurrencyCode());
        $this->setCustomerId($quote->getCustomerId());
        $this->setStoreId($quote->getStoreId());

        return $this;
    }

    /**
     * Import quote item information to the profile
     *
     * @return $this
     */
    public function importQuoteItem(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        $this->setQuoteItemInfo($item);

        // TODO: make it abstract from amounts
        $this->setBillingAmount($item->getBaseRowTotal())
            ->setTaxAmount($item->getBaseTaxAmount())
            ->setShippingAmount($item->getBaseShippingAmount())
        ;
        if (!$this->getScheduleDescription()) {
            $this->setScheduleDescription($item->getName());
        }

        $orderItemInfo = $item->getData();
        $this->_cleanupArray($orderItemInfo);

        $customOptions = $item->getOptionsByCode();
        if ($customOptions['info_buyRequest']) {
            $orderItemInfo['info_buyRequest'] = $customOptions['info_buyRequest']->getValue();
        }

        $this->setOrderItemInfo($orderItemInfo);

        return $this->_filterValues();
    }

    /**
     * Getter for sales-related field labels
     *
     * @param string $field
     * @return null|string
     */
    public function getFieldLabel($field)
    {
        return match ($field) {
            'order_item_id' => Mage::helper('sales')->__('Purchased Item'),
            'state' => Mage::helper('sales')->__('Profile State'),
            'created_at' => Mage::helper('adminhtml')->__('Created At'),
            'updated_at' => Mage::helper('adminhtml')->__('Updated At'),
            default => parent::getFieldLabel($field),
        };
    }

    /**
     * Getter for sales-related field comments
     *
     * @param string $field
     * @return null|string
     */
    public function getFieldComment($field)
    {
        return match ($field) {
            'order_item_id' => Mage::helper('sales')->__('Original order item that recurring payment profile correspondss to.'),
            default => parent::getFieldComment($field),
        };
    }

    /**
     * Getter for all available states
     *
     * @param bool $withLabels
     * @return array
     */
    public function getAllStates($withLabels = true)
    {
        $states = [self::STATE_UNKNOWN, self::STATE_PENDING, self::STATE_ACTIVE,
            self::STATE_SUSPENDED, self::STATE_CANCELED, self::STATE_EXPIRED,
        ];
        if ($withLabels) {
            $result = [];
            foreach ($states as $state) {
                $result[$state] = $this->getStateLabel($state);
            }

            return $result;
        }

        return $states;
    }

    /**
     * Get state label based on the code
     *
     * @param string $state
     * @return string
     */
    public function getStateLabel($state)
    {
        return match ($state) {
            self::STATE_UNKNOWN => Mage::helper('sales')->__('Not Initialized'),
            self::STATE_PENDING => Mage::helper('sales')->__('Pending'),
            self::STATE_ACTIVE => Mage::helper('sales')->__('Active'),
            self::STATE_SUSPENDED => Mage::helper('sales')->__('Suspended'),
            self::STATE_CANCELED => Mage::helper('sales')->__('Canceled'),
            self::STATE_EXPIRED => Mage::helper('sales')->__('Expired'),
            default => $state,
        };
    }

    /**
     * Render state as label
     *
     * @param string $key
     * @return mixed
     */
    public function renderData($key)
    {
        $value = $this->_getData($key);
        if ($key === 'state') {
            return $this->getStateLabel($value);
        }

        return parent::renderData($key);
    }

    /**
     * Getter for additional information value
     * It is assumed that the specified additional info is an object or associative array
     *
     * @param string $infoKey
     * @param string $infoValueKey
     * @return null|mixed
     */
    public function getInfoValue($infoKey, $infoValueKey)
    {
        $info = $this->getData($infoKey);
        if (!$info) {
            return;
        }

        if (!is_object($info)) {
            if (is_array($info) && isset($info[$infoValueKey])) {
                return $info[$infoValueKey];
            }
        } elseif ($info instanceof Varien_Object) {
            return $info->getDataUsingMethod($infoValueKey);
        } elseif (isset($info->$infoValueKey)) {
            return $info->$infoValueKey;
        }
    }

    protected function _construct()
    {
        $this->_init('sales/recurring_profile');
    }

    /**
     * Automatically set "unknown" state if not defined
     *
     * @inheritDoc
     */
    protected function _filterValues()
    {
        $result = parent::_filterValues();

        if (!$this->getState()) {
            $this->setState(self::STATE_UNKNOWN);
        }

        return $result;
    }

    /**
     * Initialize the workflow reference
     */
    protected function _initWorkflow()
    {
        if ($this->_workflow === null) {
            $this->_workflow = [
                'unknown'   => ['pending', 'active', 'suspended', 'canceled'],
                'pending'   => ['active', 'canceled'],
                'active'    => ['suspended', 'canceled'],
                'suspended' => ['active', 'canceled'],
                'canceled'  => [],
                'expired'   => [],
            ];
        }
    }

    /**
     * Check whether profile can be changed to specified state
     *
     * @param string $againstState
     * @param bool $soft
     * @return bool
     * @throws Mage_Core_Exception
     */
    protected function _checkWorkflow($againstState, $soft = true)
    {
        $this->_initWorkflow();
        $state = $this->getState();
        $result = (!empty($this->_workflow[$state])) && in_array($againstState, $this->_workflow[$state]);
        if (!$soft && !$result) {
            Mage::throwException(
                Mage::helper('sales')->__('This profile state cannot be changed to "%s".', $againstState),
            );
        }

        return $result;
    }

    /**
     * Return recurring profile child orders Ids
     *
     * @return array
     */
    public function getChildOrderIds()
    {
        $ids = $this->_getResource()->getChildOrderIds($this);
        if (empty($ids)) {
            $ids[] = '-1';
        }

        return $ids;
    }

    /**
     * Add order relation to recurring profile
     *
     * @param int $orderId
     * @return $this
     */
    public function addOrderRelation($orderId)
    {
        $this->getResource()->addOrderRelation($this->getId(), $orderId);
        return $this;
    }

    /**
     * Create and return new order item based on profile item data and $itemInfo
     *
     * @param Varien_Object $itemInfo
     * @return Mage_Sales_Model_Order_Item|void
     */
    protected function _getItem($itemInfo)
    {
        $paymentType = $itemInfo->getPaymentType();
        if (!$paymentType) {
            throw new Exception('Recurring profile payment type is not specified.');
        }

        switch ($paymentType) {
            case self::PAYMENT_TYPE_REGULAR:
                return $this->_getRegularItem($itemInfo);
            case self::PAYMENT_TYPE_TRIAL:
                return $this->_getTrialItem($itemInfo);
            case self::PAYMENT_TYPE_INITIAL:
                return $this->_getInitialItem($itemInfo);
            default:
                new Exception("Invalid recurring profile payment type '{$paymentType}'.");
        }
    }

    /**
     * Create and return new order item based on profile item data and $itemInfo
     * for regular payment
     *
     * @param Varien_Object $itemInfo
     * @return Mage_Sales_Model_Order_Item
     */
    protected function _getRegularItem($itemInfo)
    {
        $price = $itemInfo->getPrice() ? $itemInfo->getPrice() : $this->getBillingAmount();
        $shippingAmount = $itemInfo->getShippingAmount() ? $itemInfo->getShippingAmount() : $this->getShippingAmount();
        $taxAmount = $itemInfo->getTaxAmount() ? $itemInfo->getTaxAmount() : $this->getTaxAmount();

        return Mage::getModel('sales/order_item')
            ->setData($this->getOrderItemInfo())
            ->setQtyOrdered($this->getInfoValue('order_item_info', 'qty'))
            ->setBaseOriginalPrice($this->getInfoValue('order_item_info', 'price'))
            ->setPrice($price)
            ->setBasePrice($price)
            ->setRowTotal($price)
            ->setBaseRowTotal($price)
            ->setTaxAmount($taxAmount)
            ->setShippingAmount($shippingAmount)
            ->setId(null);
    }

    /**
     * Create and return new order item based on profile item data and $itemInfo
     * for trial payment
     *
     * @param Varien_Object $itemInfo
     * @return Mage_Sales_Model_Order_Item
     */
    protected function _getTrialItem($itemInfo)
    {
        $item = $this->_getRegularItem($itemInfo);

        $item->setName(
            Mage::helper('sales')->__('Trial ') . $item->getName(),
        );

        $option = [
            'label' => Mage::helper('sales')->__('Payment type'),
            'value' => Mage::helper('sales')->__('Trial period payment'),
        ];

        $this->_addAdditionalOptionToItem($item, $option);

        return $item;
    }

    /**
     * Create and return new order item based on profile item data and $itemInfo
     * for initial payment
     *
     * @param Varien_Object $itemInfo
     * @return Mage_Sales_Model_Order_Item
     */
    protected function _getInitialItem($itemInfo)
    {
        $price = $itemInfo->getPrice() ? $itemInfo->getPrice() : $this->getInitAmount();
        $shippingAmount = $itemInfo->getShippingAmount() ? $itemInfo->getShippingAmount() : 0;
        $taxAmount = $itemInfo->getTaxAmount() ? $itemInfo->getTaxAmount() : 0;
        $item = Mage::getModel('sales/order_item')
            ->setStoreId($this->getStoreId())
            ->setProductType(Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL)
            ->setIsVirtual(1)
            ->setSku('initial_fee')
            ->setName(Mage::helper('sales')->__('Recurring Profile Initial Fee'))
            ->setDescription('')
            ->setWeight(0)
            ->setQtyOrdered(1)
            ->setPrice($price)
            ->setOriginalPrice($price)
            ->setBasePrice($price)
            ->setBaseOriginalPrice($price)
            ->setRowTotal($price)
            ->setBaseRowTotal($price)
            ->setTaxAmount($taxAmount)
            ->setShippingAmount($shippingAmount);

        $option = [
            'label' => Mage::helper('sales')->__('Payment type'),
            'value' => Mage::helper('sales')->__('Initial period payment'),
        ];

        $this->_addAdditionalOptionToItem($item, $option);
        return $item;
    }

    /**
     * Add additional options suboption into itev
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @param array $option
     */
    protected function _addAdditionalOptionToItem($item, $option)
    {
        $options = $item->getProductOptions();
        $additionalOptions = $item->getProductOptionByCode('additional_options');
        if (is_array($additionalOptions)) {
            $additionalOptions[] = $option;
        } else {
            $additionalOptions = [$option];
        }

        $options['additional_options'] = $additionalOptions;
        $item->setProductOptions($options);
    }

    /**
     * Recursively cleanup array from objects
     *
     * @param array $array
     */
    private function _cleanupArray(&$array)
    {
        if (!$array) {
            return;
        }

        foreach ($array as $key => $value) {
            if (is_object($value)) {
                unset($array[$key]);
            } elseif (is_array($value)) {
                $this->_cleanupArray($array[$key]);
            }
        }
    }
}
