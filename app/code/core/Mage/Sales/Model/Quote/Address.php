<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Sales Quote address model
 *
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Resource_Quote_Address            _getResource()
 * @method string                                             getAddressType()
 * @method array                                              getAppliedRuleIds()
 * @method bool                                               getAppliedTaxesReset()
 * @method float                                              getBaseCustbalanceAmount()
 * @method float                                              getBaseDiscountAmount()
 * @method float                                              getBaseExtraTaxAmount()
 * @method float                                              getBaseGrandTotal()
 * @method float                                              getBaseHiddenTaxAmount()
 * @method float                                              getBaseRowTotal()
 * @method float                                              getBaseShippingAmount()
 * @method float                                              getBaseShippingAmountForDiscount()
 * @method float                                              getBaseShippingDiscountAmount()
 * @method float                                              getBaseShippingHiddenTaxAmount()
 * @method float                                              getBaseShippingInclTax()
 * @method float                                              getBaseShippingTaxable()
 * @method float                                              getBaseShippingTaxAmount()
 * @method float                                              getBaseSubtotal()
 * @method float                                              getBaseSubtotalInclTax()
 * @method float                                              getBaseSubtotalTotalInclTax()
 * @method float                                              getBaseTaxAmount()
 * @method float                                              getBaseVirtualAmount()
 * @method float                                              getBaseWeeeDiscount()
 * @method array                                              getCartFixedRules()
 * @method Mage_Sales_Model_Quote_Address[]                   getChildren()
 * @method string                                             getCity()
 * @method Mage_Sales_Model_Resource_Quote_Address_Collection getCollection()()
 * @method int                                                getCollectShippingRates()
 * @method string                                             getCompany()
 * @method string                                             getCountryId()
 * @method float                                              getCustbalanceAmount()
 * @method Mage_Customer_Model_Address                        getCustomerAddress()
 * @method int                                                getCustomerAddressId()
 * @method int                                                getCustomerId()
 * @method string                                             getCustomerNotes()
 * @method string                                             getCustomerPassword()
 * @method float                                              getDiscountAmount()
 * @method string                                             getDiscountDescription()
 * @method null|array                                         getDiscountDescriptionArray()
 * @method float                                              getDiscountTaxCompensation()
 * @method string                                             getDob()
 * @method string                                             getEmail()
 * @method float                                              getExtraTaxAmount()
 * @method string                                             getFax()
 * @method string                                             getFirstname()
 * @method float                                              getFreeMethodWeight()
 * @method int                                                getFreeShipping()
 * @method string                                             getGender()
 * @method int                                                getGiftMessageId()
 * @method float                                              getGrandTotal()
 * @method bool                                               getHasChildren()
 * @method float                                              getHiddenTaxAmount()
 * @method bool                                               getIsShippingInclTax()
 * @method string                                             getLastname()
 * @method string                                             getLimitCarrier()
 * @method string                                             getMiddlename()
 * @method Mage_Sales_Model_Quote_Address                     getParentItem()
 * @method int                                                getParentItemId()
 * @method string                                             getPostcode()
 * @method string                                             getPrefix()
 * @method Mage_Catalog_Model_Product                         getProduct()
 * @method float                                              getQty()
 * @method int                                                getQuoteId()
 * @method Mage_Sales_Model_Resource_Quote_Address            getResource()
 * @method array                                              getRoundingDeltas()
 * @method float                                              getRowTotal()
 * @method int                                                getSameAsBilling()
 * @method int                                                getSaveInAddressBook()
 * @method float                                              getShippingAmount()
 * @method float                                              getShippingAmountForDiscount()
 * @method string                                             getShippingDescription()
 * @method float                                              getShippingDiscountAmount()
 * @method float                                              getShippingDiscountPercent()
 * @method float                                              getShippingHiddenTaxAmount()
 * @method float                                              getShippingInclTax()
 * @method string                                             getShippingMethod()
 * @method float                                              getShippingTaxable()
 * @method float                                              getShippingTaxAmount()
 * @method int                                                getStoreId()
 * @method float                                              getSubtotal()
 * @method float                                              getSubtotalInclTax()
 * @method string                                             getSuffix()
 * @method float                                              getTaxAmount()
 * @method string                                             getTaxvat()
 * @method string                                             getTelephone()
 * @method float                                              getTotalQty()
 * @method float                                              getWeeeDiscount()
 * @method float                                              getWeight()
 * @method bool                                               hasCouponCode()
 * @method bool                                               hasPaymentMethod()
 * @method $this                                              setAddressType(string $value)
 * @method $this                                              setAppliedRuleIds(string $value)
 * @method $this                                              setAppliedTaxesReset(bool $value)
 * @method $this                                              setBaseCustbalanceAmount(float $float)
 * @method $this                                              setBaseDiscountAmount(float $float)
 * @method $this                                              setBaseExtraTaxAmount(float $float)
 * @method $this                                              setBaseGrandTotal(float $float)
 * @method $this                                              setBaseHiddenTaxAmount(float $float)
 * @method $this                                              setBaseShippingAmountForDiscount(float $float)
 * @method $this                                              setBaseShippingDiscountAmount(float $float)
 * @method $this                                              setBaseShippingHiddenTaxAmount(float $float)
 * @method $this                                              setBaseShippingInclTax(float $float)
 * @method $this                                              setBaseShippingTaxable(float $float)
 * @method $this                                              setBaseShippingTaxAmount(float $float)
 * @method $this                                              setBaseSubtotal(float $float)
 * @method $this                                              setBaseSubtotalInclTax(float $float)
 * @method $this                                              setBaseSubtotalTotalInclTax(float $float)
 * @method $this                                              setBaseSubtotalWithDiscount(float $float)
 * @method $this                                              setBaseTaxAmount(float $value)
 * @method $this                                              setBaseVirtualAmount(float $value)
 * @method $this                                              setBaseWeeeDiscount(float $value)
 * @method $this                                              setCartFixedRules(array $value)
 * @method $this                                              setCity(string $value)
 * @method $this                                              setCollectShippingRates(int $value)
 * @method $this                                              setCompany(string $value)
 * @method $this                                              setCountryId(string $value)
 * @method $this                                              setCouponCode(string $value)
 * @method $this                                              setCustbalanceAmount(float $int)
 * @method $this                                              setCustomerAddress(Mage_Customer_Model_Address $value)
 * @method $this                                              setCustomerAddressId(int $value)
 * @method $this                                              setCustomerId(int $value)
 * @method $this                                              setCustomerNotes(string $value)
 * @method $this                                              setDeleteImmediately(bool $value)
 * @method $this                                              setDiscountAmount(float $value)
 * @method $this                                              setDiscountDescription(string $value)
 * @method $this                                              setDiscountDescriptionArray(array $value)
 * @method $this                                              setEmail(string $value)
 * @method $this                                              setExtraTaxAmount(float $value)
 * @method $this                                              setFax(string $value)
 * @method $this                                              setFirstname(string $value)
 * @method $this                                              setFreeMethodWeight(int $value)
 * @method $this                                              setFreeShipping(int $value)
 * @method $this                                              setGiftMessageId(int $value)
 * @method $this                                              setGrandTotal(float $value)
 * @method $this                                              setHiddenTaxAmount(float $value)
 * @method $this                                              setIsShippingInclTax(bool $value)
 * @method $this                                              setItemQty(float $value)
 * @method $this                                              setLastname(string $string)
 * @method $this                                              setMiddlename(string $string)
 * @method $this                                              setPaymentMethod(null|string $value)
 * @method $this                                              setPostcode(string $string)
 * @method $this                                              setPrefix(string $string)
 * @method $this                                              setPrevQuoteCustomerGroupId(int $groupId)
 * @method $this                                              setQuoteId(int $value)
 * @method $this                                              setRegion(string $value)
 * @method $this                                              setRegionId(int $value)
 * @method $this                                              setRoundingDeltas(array $value)
 * @method $this                                              setRowWeight(float $value)
 * @method $this                                              setSameAsBilling(int $value)
 * @method $this                                              setSaveInAddressBook(int $value)
 * @method $this                                              setShippingAmountForDiscount(float|int $value)
 * @method $this                                              setShippingDescription(string $value)
 * @method $this                                              setShippingDiscountAmount(float $value)
 * @method $this                                              setShippingDiscountPercent(float $value)
 * @method $this                                              setShippingHiddenTaxAmount(float $value)
 * @method $this                                              setShippingInclTax(float $value)
 * @method $this                                              setShippingMethod(string $value)
 * @method $this                                              setShippingTaxable(float $value)
 * @method $this                                              setShippingTaxAmount(float $value)
 * @method $this                                              setSubtotal(float $value)
 * @method $this                                              setSubtotalInclTax(float $value)
 * @method $this                                              setSubtotalWithDiscount(float $value)
 * @method $this                                              setSuffix(string $value)
 * @method $this                                              setTaxAmount(float $value)
 * @method $this                                              setTelephone(string $value)
 * @method $this                                              setTotalQty(float $int)
 * @method $this                                              setVirtualAmount(float $value)
 * @method $this                                              setWeeeDiscount(float $value)
 * @method $this                                              setWeight(float $value)
 * @method $this                                              unsAddressId()
 * @method $this                                              unsAddressType()
 *
 * @method $this unsBaseSubtotalInclTax()
 * @method $this unsSubtotalInclTax()
 */
class Mage_Sales_Model_Quote_Address extends Mage_Customer_Model_Address_Abstract
{
    /**
     * Default value for Destination street
     */
    public const DEFAULT_DEST_STREET = -1;

    /**
     * Prefix of model events
     *
     * @var string
     */
    protected $_eventPrefix = 'sales_quote_address';

    /**
     * Name of event object
     *
     * @var string
     */
    protected $_eventObject = 'quote_address';

    /**
     * Quote object
     *
     * @var null|Mage_Sales_Model_Quote_Address_Item[]|Mage_Sales_Model_Resource_Quote_Address_Item_Collection
     */
    protected $_items = null;

    /**
     * Quote object
     *
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote = null;

    /**
     * Sales Quote address rates
     *
     * @var null|Mage_Sales_Model_Quote_Address_Rate[]|Mage_Sales_Model_Resource_Quote_Address_Rate_Collection
     */
    protected $_rates = null;

    /**
     * Total models collector
     *
     * @var Mage_Sales_Model_Quote_Address_Total_Collector
     */
    protected $_totalCollector = null;

    /**
     * Total data as array
     *
     * @var array
     */
    protected $_totals = [];

    /**
     * Total amounts
     *
     * @var array
     */
    protected $_totalAmounts = [];

    /**
     * Total base amounts
     *
     * @var array
     */
    protected $_baseTotalAmounts = [];

    /**
     * Whether to segregate by nominal items only
     *
     * @var null|bool
     */
    protected $_nominalOnly = null;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sales/quote_address');
    }

    /**
     * Init mapping array of short fields to its full names
     *
     * @return $this
     */
    protected function _initOldFieldsMap()
    {
        return $this;
    }

    /**
     * Initialize Quote identifier before save
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $this->_populateBeforeSaveData();
        return $this;
    }

    /**
     * Set the required fields
     *
     * @throws Mage_Core_Exception
     */
    protected function _populateBeforeSaveData()
    {
        if ($this->getQuote()) {
            $this->_dataSaveAllowed = (bool) $this->getQuote()->getId();

            if ($this->getQuote()->getId()) {
                $this->setQuoteId($this->getQuote()->getId());
            }

            $this->setCustomerId($this->getQuote()->getCustomerId());

            /**
             * Init customer address id if customer address is assigned
             */
            if ($this->getCustomerAddress()) {
                $this->setCustomerAddressId($this->getCustomerAddress()->getId());
            }

            /**
             * Set same_as_billing to "1" when default shipping address is set as default
             * and it is not equal billing address
             */
            if (!$this->getId() && !$this->hasSameAsBilling()) {
                $this->setSameAsBilling((int) $this->_isSameAsBilling());
            }
        }
    }

    /**
     * Returns true if the billing address is same as the shipping
     *
     * @return bool
     * @throws Mage_Core_Exception
     */
    protected function _isSameAsBilling()
    {
        return ($this->getAddressType() === self::TYPE_SHIPPING
            && ($this->_isNotRegisteredCustomer() || $this->_isDefaultShippingNullOrSameAsBillingAddress()));
    }

    /**
     * Checks if the user is a registered customer
     *
     * @return bool
     * @throws Mage_Core_Exception
     */
    protected function _isNotRegisteredCustomer()
    {
        return !$this->getQuote()->getCustomerId() || $this->getCustomerAddressId() === null;
    }

    /**
     * Returns true if the def billing address is same as customer address
     *
     * @return bool
     * @throws Mage_Core_Exception
     */
    protected function _isDefaultShippingNullOrSameAsBillingAddress()
    {
        $customer = $this->getQuote()->getCustomer();
        return !$customer->getDefaultShippingAddress()
            || $customer->getDefaultBillingAddress() && $customer->getDefaultShippingAddress()
                && $customer->getDefaultBillingAddress()->getId() == $customer->getDefaultShippingAddress()->getId();
    }

    /**
     * Save child collections
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    protected function _afterSave()
    {
        parent::_afterSave();
        if ($this->_items !== null) {
            $this->getItemsCollection()->save();
        }

        if ($this->_rates !== null) {
            $this->getShippingRatesCollection()->save();
        }

        return $this;
    }

    /**
     * Declare address quote model object
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function setQuote(Mage_Sales_Model_Quote $quote)
    {
        $this->_quote = $quote;
        if ($this->getQuoteId() != $quote->getId()) {
            $this->setQuoteId($quote->getId());
        }

        return $this;
    }

    /**
     * Retrieve quote object
     *
     * @return Mage_Sales_Model_Quote
     * @throws Mage_Core_Exception
     */
    public function getQuote()
    {
        if (is_null($this->_quote)) {
            $this->_quote = Mage::getModel('sales/quote')->load($this->getQuoteId());
        }

        return $this->_quote;
    }

    /**
     * Import quote address data from customer address object
     *
     * @return $this
     */
    public function importCustomerAddress(Mage_Customer_Model_Address $address)
    {
        Mage::helper('core')->copyFieldset('customer_address', 'to_quote_address', $address, $this);
        $email = null;
        if ($address->hasEmail()) {
            $email = $address->getEmail();
        } elseif ($address->getCustomer()) {
            $email = $address->getCustomer()->getEmail();
        }

        if ($email) {
            $this->setEmail($email);
        }

        return $this;
    }

    /**
     * Export data to customer address object
     *
     * @return Mage_Customer_Model_Address
     */
    public function exportCustomerAddress()
    {
        $address = Mage::getModel('customer/address');
        Mage::helper('core')->copyFieldset('sales_convert_quote_address', 'to_customer_address', $this, $address);
        return $address;
    }

    /**
     * Import address data from order address
     *
     * @return $this
     */
    public function importOrderAddress(Mage_Sales_Model_Order_Address $address)
    {
        $this->setAddressType($address->getAddressType())
            ->setCustomerId($address->getCustomerId())
            ->setCustomerAddressId($address->getCustomerAddressId())
            ->setEmail($address->getEmail());

        Mage::helper('core')->copyFieldset('sales_convert_order_address', 'to_quote_address', $address, $this);

        return $this;
    }

    /**
     * Convert object to array
     *
     * @return array
     * @throws Mage_Core_Exception
     */
    public function toArray(array $arrAttributes = [])
    {
        $arr = parent::toArray($arrAttributes);
        $arr['rates'] = $this->getShippingRatesCollection()->toArray($arrAttributes);
        $arr['items'] = $this->getItemsCollection()->toArray($arrAttributes);
        foreach ($this->getTotals() as $key => $total) {
            $arr['totals'][$key] = $total->toArray();
        }

        return $arr;
    }

    /**
     * Retrieve address items collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     * @throws Mage_Core_Exception
     */
    public function getItemsCollection()
    {
        if (is_null($this->_items)) {
            $this->_items = Mage::getModel('sales/quote_address_item')->getCollection()
                ->setAddressFilter($this->getId());

            if ($this->getId()) {
                foreach ($this->_items as $item) {
                    $item->setAddress($this);
                }
            }
        }

        return $this->_items;
    }

    /**
     * Get all available address items
     *
     * @return Mage_Sales_Model_Quote_Address_Item[]
     * @throws Mage_Core_Exception
     */
    public function getAllItems()
    {
        // We calculate item list once and cache it in three arrays - all items, nominal, non-nominal
        $cachedItems = $this->_nominalOnly ? 'nominal' : ($this->_nominalOnly === false ? 'nonnominal' : 'all');
        $key = 'cached_items_' . $cachedItems;
        if (!$this->hasData($key)) {
            // For compatibility  we will use $this->_filterNominal to divide nominal items from non-nominal
            // (because it can be overloaded)
            // So keep current flag $this->_nominalOnly and restore it after cycle
            $wasNominal = $this->_nominalOnly;
            $this->_nominalOnly = true; // Now $this->_filterNominal() will return positive values for nominal items

            $quoteItems = $this->getQuote()->getItemsCollection();
            $addressItems = $this->getItemsCollection();

            $items = [];
            $nominalItems = [];
            $nonNominalItems = [];
            if ($this->getQuote()->getIsMultiShipping() && $addressItems->count() > 0) {
                foreach ($addressItems as $aItem) {
                    if ($aItem->isDeleted()) {
                        continue;
                    }

                    if (!$aItem->getQuoteItemImported()) {
                        $qItem = $this->getQuote()->getItemById($aItem->getQuoteItemId());
                        if ($qItem) {
                            $aItem->importQuoteItem($qItem);
                        }
                    }

                    $items[] = $aItem;
                    if ($this->_filterNominal($aItem)) {
                        $nominalItems[] = $aItem;
                    } else {
                        $nonNominalItems[] = $aItem;
                    }
                }
            } else {
                /*
                * For virtual quote we assign items only to billing address, otherwise - only to shipping address
                */
                $addressType = $this->getAddressType();
                $canAddItems = $this->getQuote()->isVirtual()
                    ? ($addressType == self::TYPE_BILLING)
                    : ($addressType == self::TYPE_SHIPPING);

                if ($canAddItems) {
                    foreach ($quoteItems as $qItem) {
                        if ($qItem->isDeleted()) {
                            continue;
                        }

                        $items[] = $qItem;
                        if ($this->_filterNominal($qItem)) {
                            $nominalItems[] = $qItem;
                        } else {
                            $nonNominalItems[] = $qItem;
                        }
                    }
                }
            }

            // Cache calculated lists
            $this->setData('cached_items_all', $items);
            $this->setData('cached_items_nominal', $nominalItems);
            $this->setData('cached_items_nonnominal', $nonNominalItems);

            $this->_nominalOnly = $wasNominal; // Restore original value before we changed it
        }

        return $this->getData($key);
    }

    /**
     * Getter for all non-nominal items
     *
     * @return array
     * @throws Mage_Core_Exception
     */
    public function getAllNonNominalItems()
    {
        $this->_nominalOnly = false;
        $result = $this->getAllItems();
        $this->_nominalOnly = null;
        return $result;
    }

    /**
     * Getter for all nominal items
     *
     * @return Mage_Sales_Model_Quote_Address_Item[]
     * @throws Mage_Core_Exception
     */
    public function getAllNominalItems()
    {
        $this->_nominalOnly = true;
        $result = $this->getAllItems();
        $this->_nominalOnly = null;
        return $result;
    }

    /**
     * Segregate by nominal criteria
     *
     * true: get nominals only
     * false: get non-nominals only
     * null: get all
     *
     * @param  Mage_Sales_Model_Quote_Item_Abstract       $item
     * @return false|Mage_Sales_Model_Quote_Item_Abstract
     */
    protected function _filterNominal($item)
    {
        return ($this->_nominalOnly === null)
            || (($this->_nominalOnly === false) && !$item->isNominal())
            || (($this->_nominalOnly === true) && $item->isNominal())
            ? $item : false;
    }

    /**
     * Retrieve all visible items
     *
     * @return Mage_Sales_Model_Quote_Address_Item[]
     * @throws Mage_Core_Exception
     */
    public function getAllVisibleItems()
    {
        $items = [];
        foreach ($this->getAllItems() as $item) {
            if (!$item->getParentItemId()) {
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * Retrieve item quantity by id
     *
     * @param  int                 $itemId
     * @return float|int
     * @throws Mage_Core_Exception
     */
    public function getItemQty($itemId = 0)
    {
        if ($this->hasData('item_qty')) {
            return $this->getData('item_qty');
        }

        $qty = 0;
        if ($itemId == 0) {
            foreach ($this->getAllItems() as $item) {
                $qty += $item->getQty();
            }
        } else {
            $item = $this->getItemById($itemId);
            if ($item) {
                $qty = $item->getQty();
            }
        }

        return $qty;
    }

    /**
     * Check Quote address has Items
     *
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function hasItems()
    {
        return count($this->getAllItems()) > 0;
    }

    /**
     * Get address item object by id without
     *
     * @param  int                                       $itemId
     * @return false|Mage_Sales_Model_Quote_Address_Item
     * @throws Mage_Core_Exception
     */
    public function getItemById($itemId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getId() == $itemId) {
                return $item;
            }
        }

        return false;
    }

    /**
     * Get prepared not deleted item
     *
     * @param  int                                       $itemId
     * @return false|Mage_Sales_Model_Quote_Address_Item
     * @throws Mage_Core_Exception
     */
    public function getValidItemById($itemId)
    {
        foreach ($this->getAllItems() as $item) {
            if ($item->getId() == $itemId) {
                return $item;
            }
        }

        return false;
    }

    /**
     * Retrieve item object by quote item Id
     *
     * @param  int                                       $itemId
     * @return false|Mage_Sales_Model_Quote_Address_Item
     * @throws Mage_Core_Exception
     */
    public function getItemByQuoteItemId($itemId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getQuoteItemId() == $itemId) {
                return $item;
            }
        }

        return false;
    }

    /**
     * Remove item from collection
     *
     * @param  int                 $itemId
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function removeItem($itemId)
    {
        $item = $this->getItemById($itemId);
        if ($item) {
            $item->isDeleted(true);
        }

        return $this;
    }

    /**
     * Add item to address
     *
     * @param  int                 $qty
     * @return $this
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    public function addItem(Mage_Sales_Model_Quote_Item_Abstract $item, $qty = null)
    {
        if ($item instanceof Mage_Sales_Model_Quote_Item) {
            if ($item->getParentItemId()) {
                return $this;
            }

            $addressItem = Mage::getModel('sales/quote_address_item')
                ->setAddress($this)
                ->importQuoteItem($item);
            $this->getItemsCollection()->addItem($addressItem);

            if ($item->getHasChildren()) {
                foreach ($item->getChildren() as $child) {
                    $addressChildItem = Mage::getModel('sales/quote_address_item')
                        ->setAddress($this)
                        ->importQuoteItem($child)
                        ->setParentItem($addressItem);
                    $this->getItemsCollection()->addItem($addressChildItem);
                }
            }
        } else {
            $addressItem = $item;
            $addressItem->setAddress($this);
            if (!$addressItem->getId()) {
                $this->getItemsCollection()->addItem($addressItem);
            }
        }

        if ($qty) {
            $addressItem->setQty($qty);
        }

        return $this;
    }

    /**
     * Retrieve collection of quote shipping rates
     *
     * @return Mage_Sales_Model_Resource_Quote_Address_Rate_Collection
     * @throws Mage_Core_Exception
     */
    public function getShippingRatesCollection()
    {
        if (is_null($this->_rates)) {
            $this->_rates = Mage::getModel('sales/quote_address_rate')->getCollection()
                ->setAddressFilter($this->getId());
            if ($this->getQuote()->hasNominalItems(false)) {
                $this->_rates->setFixedOnlyFilter(true);
            }

            if ($this->getId()) {
                foreach ($this->_rates as $rate) {
                    $rate->setAddress($this);
                }
            }
        }

        return $this->_rates;
    }

    /**
     * Retrieve all address shipping rates
     *
     * @return Mage_Sales_Model_Quote_Address_Rate[]
     * @throws Mage_Core_Exception
     */
    public function getAllShippingRates()
    {
        $rates = [];
        foreach ($this->getShippingRatesCollection() as $rate) {
            if (!$rate->isDeleted()) {
                $rates[] = $rate;
            }
        }

        return $rates;
    }

    /**
     * Retrieve all grouped shipping rates
     *
     * @return array
     * @throws Mage_Core_Exception
     */
    public function getGroupedAllShippingRates()
    {
        $rates = [];
        foreach ($this->getShippingRatesCollection() as $rate) {
            if (!$rate->isDeleted() && $rate->getCarrierInstance()) {
                if (!isset($rates[$rate->getCarrier()])) {
                    $rates[$rate->getCarrier()] = [];
                }

                $rates[$rate->getCarrier()][] = $rate;
                $rates[$rate->getCarrier()][0]->carrier_sort_order = $rate->getCarrierInstance()->getSortOrder();
            }
        }

        uasort($rates, [$this, '_sortRates']);
        return $rates;
    }

    /**
     * Sort rates recursive callback
     *
     * @param  array $a
     * @param  array $b
     * @return int
     */
    protected function _sortRates($a, $b)
    {
        if ((int) $a[0]->carrier_sort_order < (int) $b[0]->carrier_sort_order) {
            return -1;
        }

        if ((int) $a[0]->carrier_sort_order > (int) $b[0]->carrier_sort_order) {
            return 1;
        }

        return 0;
    }

    /**
     * Retrieve shipping rate by identifier
     *
     * @param  int                                       $rateId
     * @return false|Mage_Sales_Model_Quote_Address_Rate
     * @throws Mage_Core_Exception
     */
    public function getShippingRateById($rateId)
    {
        foreach ($this->getShippingRatesCollection() as $rate) {
            if ($rate->getId() == $rateId) {
                return $rate;
            }
        }

        return false;
    }

    /**
     * Retrieve shipping rate by code
     *
     * @param  string                                    $code
     * @return false|Mage_Sales_Model_Quote_Address_Rate
     * @throws Mage_Core_Exception
     */
    public function getShippingRateByCode($code)
    {
        foreach ($this->getShippingRatesCollection() as $rate) {
            if ($rate->getCode() == $code) {
                return $rate;
            }
        }

        return false;
    }

    /**
     * Mark all shipping rates as deleted
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function removeAllShippingRates()
    {
        foreach ($this->getShippingRatesCollection() as $rate) {
            $rate->isDeleted(true);
        }

        return $this;
    }

    /**
     * Add shipping rate
     *
     * @return $this
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    public function addShippingRate(Mage_Sales_Model_Quote_Address_Rate $rate)
    {
        $rate->setAddress($this);
        $this->getShippingRatesCollection()->addItem($rate);
        return $this;
    }

    /**
     * Collecting shipping rates by address
     *
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Zend_Controller_Response_Exception
     */
    public function collectShippingRates()
    {
        if (!$this->getCollectShippingRates()) {
            return $this;
        }

        $this->setCollectShippingRates(false);

        $this->removeAllShippingRates();

        if (!$this->getCountryId()) {
            return $this;
        }

        $found = $this->requestShippingRates();
        if (!$found) {
            $this->setShippingAmount(0)
                ->setBaseShippingAmount(0)
                ->setShippingMethod('')
                ->setShippingDescription('');
        }

        return $this;
    }

    /**
     * Request shipping rates for entire address or specified address item
     * Returns true if current selected shipping method code corresponds to one of the found rates
     *
     * @return bool
     * @throws Mage_Core_Exception
     * @throws Zend_Controller_Response_Exception
     */
    public function requestShippingRates(?Mage_Sales_Model_Quote_Item_Abstract $item = null)
    {
        /** @var Mage_Shipping_Model_Rate_Request $request */
        $request = Mage::getModel('shipping/rate_request');
        $request->setAllItems($item ? [$item] : $this->getAllItems());
        $request->setDestCountryId($this->getCountryId());
        $request->setDestRegionId($this->getRegionId());
        $request->setDestRegionCode($this->getRegionCode());
        /**
         * need to call getStreet with -1
         * to get data in string instead of array
         */
        $request->setDestStreet($this->getStreet(self::DEFAULT_DEST_STREET));
        $request->setDestCity($this->getCity());
        $request->setDestPostcode($this->getPostcode());
        $request->setPackageValue($item ? $item->getBaseRowTotal() : $this->getBaseSubtotal());

        $packageValueWithDiscount = $item
            ? $item->getBaseRowTotal() - $item->getBaseDiscountAmount()
            : $this->getBaseSubtotalWithDiscount();
        $request->setPackageValueWithDiscount($packageValueWithDiscount);
        $request->setPackageWeight($item ? $item->getRowWeight() : $this->getWeight());
        $request->setPackageQty($item ? $item->getQty() : $this->getItemQty());

        /**
         * Need for shipping methods that use insurance based on price of physical products
         */
        $packagePhysicalValue = $item
            ? $item->getBaseRowTotal()
            : $this->getBaseSubtotal() - $this->getBaseVirtualAmount();
        $request->setPackagePhysicalValue($packagePhysicalValue);

        $request->setFreeMethodWeight($item ? 0 : $this->getFreeMethodWeight());

        /**
         * Store and website identifiers need specify from quote
         */
        /*$request->setStoreId(Mage::app()->getStore()->getId());
        $request->setWebsiteId(Mage::app()->getStore()->getWebsiteId());*/

        $request->setStoreId($this->getQuote()->getStore()->getId());
        $request->setWebsiteId($this->getQuote()->getStore()->getWebsiteId());
        $request->setFreeShipping($this->getFreeShipping());
        /**
         * Currencies need to convert in free shipping
         */
        $request->setBaseCurrency($this->getQuote()->getStore()->getBaseCurrency());
        $request->setPackageCurrency($this->getQuote()->getStore()->getCurrentCurrency());
        $request->setLimitCarrier($this->getLimitCarrier());

        $request->setBaseSubtotalInclTax($this->getBaseSubtotalInclTax() + $this->getBaseExtraTaxAmount());

        $result = Mage::getModel('shipping/shipping')->collectRates($request)->getResult();

        $found = false;
        if ($result) {
            $shippingRates = $result->getAllRates();

            foreach ($shippingRates as $shippingRate) {
                $rate = Mage::getModel('sales/quote_address_rate')
                    ->importShippingRate($shippingRate);
                if (!$item) {
                    $this->addShippingRate($rate);
                }

                if ($this->getShippingMethod() == $rate->getCode()) {
                    if ($item) {
                        $item->setBaseShippingAmount($rate->getPrice());
                    } else {
                        /**
                         * possible bug: this should be setBaseShippingAmount(),
                         * see Mage_Sales_Model_Quote_Address_Total_Shipping::collect()
                         * where this value is set again from the current specified rate price
                         * (looks like a workaround for this bug)
                         */
                        $this->setShippingAmount($rate->getPrice());
                    }

                    $found = true;
                }
            }
        }

        return $found;
    }

    /**
     * Get totals collector model
     *
     * @return Mage_Sales_Model_Quote_Address_Total_Collector
     * @throws Mage_Core_Exception
     */
    public function getTotalCollector()
    {
        if ($this->_totalCollector === null) {
            $this->_totalCollector = Mage::getSingleton(
                'sales/quote_address_total_collector',
                ['store' => $this->getQuote()->getStore()],
            );
        }

        return $this->_totalCollector;
    }

    /**
     * Retrieve total models
     *
     * @return array
     * @throws Mage_Core_Exception
     * @deprecated
     */
    public function getTotalModels()
    {
        return $this->getTotalCollector()->getRetrievers();
    }

    /**
     * Collect address totals
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function collectTotals()
    {
        Mage::dispatchEvent($this->_eventPrefix . '_collect_totals_before', [$this->_eventObject => $this]);
        foreach ($this->getTotalCollector()->getCollectors() as $model) {
            $model->collect($this);
        }

        Mage::dispatchEvent($this->_eventPrefix . '_collect_totals_after', [$this->_eventObject => $this]);
        return $this;
    }

    /**
     * Get address totals as array
     *
     * @return Mage_Sales_Model_Quote_Address_Total[]
     * @throws Mage_Core_Exception
     */
    public function getTotals()
    {
        foreach ($this->getTotalCollector()->getRetrievers() as $model) {
            $model->fetch($this);
        }

        return $this->_totals;
    }

    /**
     * Add total data or model
     *
     * @param  array|Mage_Sales_Model_Quote_Address_Total $total
     * @return $this
     */
    public function addTotal($total)
    {
        if (is_array($total)) {
            $totalInstance = Mage::getModel('sales/quote_address_total')
                ->setData($total);
        } elseif ($total instanceof Mage_Sales_Model_Quote_Address_Total) {
            $totalInstance = $total;
        }

        if (isset($totalInstance)) {
            $totalInstance->setAddress($this);
            $this->_totals[$totalInstance->getCode()] = $totalInstance;
        }

        return $this;
    }

    /**
     * Rewrite clone method
     *
     * @throws Mage_Core_Exception
     */
    public function __clone()
    {
        $this->setId(null);
    }

    /**
     * Validate minimum amount
     *
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function validateMinimumAmount()
    {
        $storeId = $this->getQuote()->getStoreId();
        if (!Mage::getStoreConfigFlag('sales/minimum_order/active', $storeId)) {
            return true;
        }

        if ($this->getQuote()->getIsVirtual() && $this->getAddressType() == self::TYPE_SHIPPING) {
            return true;
        }

        if (!$this->getQuote()->getIsVirtual() && $this->getAddressType() != self::TYPE_SHIPPING) {
            return true;
        }

        $amount = Mage::getStoreConfig('sales/minimum_order/amount', $storeId);
        if ($this->getBaseSubtotalWithDiscount() < $amount) {
            return false;
        }

        return true;
    }

    /**
     * Retrieve applied taxes
     *
     * @return array
     */
    public function getAppliedTaxes()
    {
        $tax = $this->getData('applied_taxes');
        if (empty($tax)) {
            return [];
        }

        try {
            $return = Mage::helper('core/unserializeArray')->unserialize($tax);
        } catch (Exception) {
            $return = [];
        }

        return $return;
    }

    /**
     * Set applied taxes
     *
     * @param  array $data
     * @return $this
     */
    public function setAppliedTaxes($data)
    {
        return $this->setData('applied_taxes', serialize($data));
    }

    /**
     * Set shipping amount
     *
     * @param  float $value
     * @param  bool  $alreadyExclTax
     * @return $this
     */
    public function setShippingAmount($value, $alreadyExclTax = false)
    {
        return $this->setData('shipping_amount', $value);
    }

    /**
     * Set base shipping amount
     *
     * @param  float $value
     * @param  bool  $alreadyExclTax
     * @return $this
     */
    public function setBaseShippingAmount($value, $alreadyExclTax = false)
    {
        return $this->setData('base_shipping_amount', $value);
    }

    /**
     * Set total amount value
     *
     * @param  string $code
     * @param  float  $amount
     * @return $this
     */
    public function setTotalAmount($code, $amount)
    {
        $this->_totalAmounts[$code] = $amount;
        if ($code != 'subtotal') {
            $code .= '_amount';
        }

        $this->setData($code, $amount);
        return $this;
    }

    /**
     * Set total amount value in base store currency
     *
     * @param  string $code
     * @param  float  $amount
     * @return $this
     */
    public function setBaseTotalAmount($code, $amount)
    {
        $this->_baseTotalAmounts[$code] = $amount;
        if ($code != 'subtotal') {
            $code .= '_amount';
        }

        $this->setData('base_' . $code, $amount);
        return $this;
    }

    /**
     * Add amount total amount value
     *
     * @param  string $code
     * @param  float  $amount
     * @return $this
     */
    public function addTotalAmount($code, $amount)
    {
        $amount = $this->getTotalAmount($code) + $amount;
        $this->setTotalAmount($code, $amount);
        return $this;
    }

    /**
     * Add amount total amount value in base store currency
     *
     * @param  string $code
     * @param  float  $amount
     * @return $this
     */
    public function addBaseTotalAmount($code, $amount)
    {
        $amount = $this->getBaseTotalAmount($code) + $amount;
        $this->setBaseTotalAmount($code, $amount);
        return $this;
    }

    /**
     * Get total amount value by code
     *
     * @param  string $code
     * @return float
     */
    public function getTotalAmount($code)
    {
        return $this->_totalAmounts[$code] ?? 0;
    }

    /**
     * Get total amount value by code in base store curncy
     *
     * @param  string $code
     * @return float
     */
    public function getBaseTotalAmount($code)
    {
        return $this->_baseTotalAmounts[$code] ?? 0;
    }

    /**
     * Get all total amount values
     *
     * @return array
     */
    public function getAllTotalAmounts()
    {
        return $this->_totalAmounts;
    }

    /**
     * Get all total amount values in base currency
     *
     * @return array
     */
    public function getAllBaseTotalAmounts()
    {
        return $this->_baseTotalAmounts;
    }

    /**
     * Get subtotal amount with applied discount in base currency
     *
     * @return float
     */
    public function getBaseSubtotalWithDiscount()
    {
        return $this->getBaseSubtotal() + $this->getBaseDiscountAmount();
    }

    /**
     * Get subtotal amount with applied discount
     *
     * @return float
     */
    public function getSubtotalWithDiscount()
    {
        return $this->getSubtotal() + $this->getDiscountAmount();
    }

    public function getCouponCode(): string
    {
        return (string) $this->_getData('coupon_code');
    }
}
