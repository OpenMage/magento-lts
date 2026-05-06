<?php

declare(strict_types=1);

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
 * @method array                                              getAppliedRuleIds()
 * @method bool                                               getAppliedTaxesReset()
 * @method float                                              getBaseCustbalanceAmount()
 * @method float                                              getBaseWeeeDiscount()
 * @method array                                              getCartFixedRules()
 * @method Mage_Sales_Model_Quote_Address[]                   getChildren()
 * @method Mage_Sales_Model_Resource_Quote_Address_Collection getCollection()()
 * @method float                                              getCustbalanceAmount()
 * @method Mage_Customer_Model_Address                        getCustomerAddress()
 * @method string                                             getCustomerPassword()
 * @method null|array                                         getDiscountDescriptionArray()
 * @method bool                                               getHasChildren()
 * @method bool                                               getIsShippingInclTax()
 * @method Mage_Sales_Model_Quote_Address                     getParentItem()
 * @method Mage_Catalog_Model_Product                         getProduct()
 * @method Mage_Sales_Model_Resource_Quote_Address            getResource()
 * @method array                                              getRoundingDeltas()
 * @method float                                              getShippingDiscountPercent()
 * @method float                                              getWeeeDiscount()
 * @method bool                                               hasCouponCode()
 * @method bool                                               hasPaymentMethod()
 * @method $this                                              setAppliedRuleIds(string $value)
 * @method $this                                              setAppliedTaxesReset(bool $value)
 * @method $this                                              setBaseCustbalanceAmount(float $float)
 * @method $this                                              setBaseSubtotalWithDiscount(float $float)
 * @method $this                                              setBaseWeeeDiscount(float $value)
 * @method $this                                              setCartFixedRules(array $value)
 * @method $this                                              setCouponCode(string $value)
 * @method $this                                              setCustbalanceAmount(float $int)
 * @method $this                                              setCustomerAddress(Mage_Customer_Model_Address $value)
 * @method $this                                              setDeleteImmediately(bool $value)
 * @method $this                                              setDiscountDescriptionArray(array $value)
 * @method $this                                              setIsShippingInclTax(bool $value)
 * @method $this                                              setItemQty(float $value)
 * @method $this                                              setPaymentMethod(null|string $value)
 * @method $this                                              setPrevQuoteCustomerGroupId(int $groupId)
 * @method $this                                              setRoundingDeltas(array $value)
 * @method $this                                              setRowWeight(float $value)
 * @method $this                                              setSaveInAddressBook(int $value)
 * @method $this                                              setShippingDiscountPercent(float $value)
 * @method $this                                              setSubtotalWithDiscount(float $value)
 * @method $this                                              setWeeeDiscount(float $value)
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
     * @var array<string, float>
     */
    protected $_totalAmounts = [];

    /**
     * Total base amounts
     *
     * @var array<string, float>
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
    #[Override]
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
        if (!$this->getQuote()->getCustomerId()) {
            return true;
        }

        return $this->getCustomerAddressId() === null;
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
        if (!$customer->getDefaultShippingAddress()) {
            return true;
        }

        return $customer->getDefaultBillingAddress() && $customer->getDefaultShippingAddress()
            && $customer->getDefaultBillingAddress()->getId() == $customer->getDefaultShippingAddress()->getId();
    }

    /**
     * Save child collections
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    #[Override]
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
    #[Override]
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
     * @return Mage_Sales_Model_Resource_Quote_Address_Item_Collection
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
            return $this->getDataByKey('item_qty');
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
                /** @var Mage_Sales_Model_Quote_Item $child */
                foreach ($item->getChildren() as $child) {
                    $addressChildItem = Mage::getModel('sales/quote_address_item')
                        ->setAddress($this)
                        ->importQuoteItem($child)
                        ->setParentItem($addressItem);
                    $this->getItemsCollection()->addItem($addressChildItem);
                }
            }
        } elseif ($item instanceof Mage_Sales_Model_Quote_Address_Item) {
            $addressItem = $item;
            $addressItem->setAddress($this);
            if (!$addressItem->getId()) {
                $this->getItemsCollection()->addItem($addressItem);
            }
        }

        if ($qty && isset($addressItem)) {
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
                $rates[$rate->getCarrier()][0]->setCarrierSortOrder($rate->getCarrierInstance()->getSortOrder());
            }
        }

        uasort($rates, $this->_sortRates(...));
        return $rates;
    }

    /**
     * Sort rates recursive callback
     *
     * @param  Mage_Sales_Model_Quote_Address_Rate[] $a
     * @param  Mage_Sales_Model_Quote_Address_Rate[] $b
     * @return int
     */
    protected function _sortRates($a, $b)
    {
        return $a[0]->getCarrierSortOrder() <=> $b[0]->getCarrierSortOrder();
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
                if (!$item instanceof Mage_Sales_Model_Quote_Item_Abstract) {
                    $this->addShippingRate($rate);
                }

                if ($this->getShippingMethod() == $rate->getCode()) {
                    if ($item instanceof Mage_Sales_Model_Quote_Item_Abstract) {
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
        return $this->getBaseSubtotalWithDiscount() >= $amount;
    }

    /**
     * Retrieve applied taxes
     *
     * @return array
     */
    public function getAppliedTaxes()
    {
        $tax = $this->getDataByKey('applied_taxes');
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
     * Get total amount value by code in base store currency
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
     * @return array<string, float>
     */
    public function getAllTotalAmounts()
    {
        return $this->_totalAmounts;
    }

    /**
     * Get all total amount values in base currency
     *
     * @return array<string, float>
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

    public function getAddressType(): string
    {
        return (string) $this->_getData('address_type');
    }

    public function getBaseDiscountAmount(): float
    {
        return (float) $this->_getData('base_discount_amount');
    }

    public function getBaseExtraTaxAmount(): float
    {
        return (float) $this->_getData('base_extra_tax_amount');
    }

    public function getBaseGrandTotal(): float
    {
        return (float) $this->_getData('base_grand_total');
    }

    public function getBaseHiddenTaxAmount(): float
    {
        return (float) $this->_getData('base_hidden_tax_amount');
    }

    public function getBaseRowTotal(): float
    {
        return (float) $this->_getData('base_row_total');
    }

    public function getBaseShippingAmount(): float
    {
        return (float) $this->_getData('base_shipping_amount');
    }

    public function getBaseShippingAmountForDiscount(): float
    {
        return (float) $this->_getData('base_shipping_amount_for_discount');
    }

    public function getBaseShippingDiscountAmount(): float
    {
        return (float) $this->_getData('base_shipping_discount_amount');
    }

    public function getBaseShippingHiddenTaxAmount(): float
    {
        return (float) $this->_getData('base_shipping_hidden_tax_amount');
    }

    public function getBaseShippingInclTax(): float
    {
        return (float) $this->_getData('base_shipping_incl_tax');
    }

    public function getBaseShippingTaxable(): float
    {
        return (float) $this->_getData('base_shipping_taxable');
    }

    public function getBaseShippingTaxAmount(): float
    {
        return (float) $this->_getData('base_shipping_tax_amount');
    }

    public function getBaseSubtotal(): float
    {
        return (float) $this->_getData('base_subtotal');
    }

    public function getBaseSubtotalInclTax(): float
    {
        return (float) $this->_getData('base_subtotal_incl_tax');
    }

    public function getBaseSubtotalTotalInclTax(): float
    {
        return (float) $this->_getData('base_subtotal_total_incl_tax');
    }

    public function getBaseTaxAmount(): float
    {
        return (float) $this->_getData('base_tax_amount');
    }

    public function getBaseVirtualAmount(): float
    {
        return (float) $this->_getData('base_virtual_amount');
    }

    public function getCity(): string
    {
        return (string) $this->_getData('city');
    }

    public function getCollectShippingRates(): int
    {
        return (int) $this->_getData('collect_shipping_rates');
    }

    public function getCompany(): string
    {
        return (string) $this->_getData('company');
    }

    public function getCountryId(): string
    {
        return (string) $this->_getData('country_id');
    }

    public function getCustomerAddressId(): int
    {
        return (int) $this->_getData('customer_address_id');
    }

    public function getCustomerId(): int
    {
        return (int) $this->_getData('customer_id');
    }

    public function getCustomerNotes(): string
    {
        return (string) $this->_getData('customer_notes');
    }

    public function getDiscountAmount(): float
    {
        return (float) $this->_getData('discount_amount');
    }

    public function getDiscountDescription(): string
    {
        return (string) $this->_getData('discount_description');
    }

    public function getDiscountTaxCompensation(): float
    {
        return (float) $this->_getData('discount_tax_compensation');
    }

    public function getEmail(): string
    {
        return (string) $this->_getData('email');
    }

    public function getExtraTaxAmount(): float
    {
        return (float) $this->_getData('extra_tax_amount');
    }

    public function getFax(): string
    {
        return (string) $this->_getData('fax');
    }

    public function getFirstname(): string
    {
        return (string) $this->_getData('firstname');
    }

    public function getFreeMethodWeight(): float
    {
        return (float) $this->_getData('free_method_weight');
    }

    public function getFreeShipping(): int
    {
        return (int) $this->_getData('free_shipping');
    }

    public function getGender(): string
    {
        return (string) $this->_getData('gender');
    }

    public function getGiftMessageId(): int
    {
        return (int) $this->_getData('gift_message_id');
    }

    public function getGrandTotal(): float
    {
        return (float) $this->_getData('grand_total');
    }

    public function getHiddenTaxAmount(): float
    {
        return (float) $this->_getData('hidden_tax_amount');
    }

    public function getLastname(): string
    {
        return (string) $this->_getData('lastname');
    }

    public function getLimitCarrier(): string
    {
        return (string) $this->_getData('limit_carrier');
    }

    public function getMiddlename(): string
    {
        return (string) $this->_getData('middlename');
    }

    public function getParentItemId(): int
    {
        return (int) $this->_getData('parent_item_id');
    }

    public function getPostcode(): string
    {
        return (string) $this->_getData('postcode');
    }

    public function getPrefix(): string
    {
        return (string) $this->_getData('prefix');
    }

    public function getQuoteId(): int
    {
        return (int) $this->_getData('quote_id');
    }

    public function getRegion(): string
    {
        return (string) $this->_getData('region');
    }

    public function getRegionCode(): string
    {
        return (string) $this->_getData('region_code');
    }

    public function getRegionId(): int
    {
        return (int) $this->_getData('region_id');
    }

    public function getRewardPointsBalance(): int
    {
        return (int) $this->_getData('reward_points_balance');
    }

    public function getRowTotal(): float
    {
        return (float) $this->_getData('row_total');
    }

    public function getSameAsBilling(): int
    {
        return (int) $this->_getData('same_as_billing');
    }

    public function getShippingAmount(): float
    {
        return (float) $this->_getData('shipping_amount');
    }

    public function getShippingAmountForDiscount(): float
    {
        return (float) $this->_getData('shipping_amount_for_discount');
    }

    public function getShippingDescription(): string
    {
        return (string) $this->_getData('shipping_description');
    }

    public function getShippingDiscountAmount(): float
    {
        return (float) $this->_getData('shipping_discount_amount');
    }

    public function getShippingHiddenTaxAmount(): float
    {
        return (float) $this->_getData('shipping_hidden_tax_amount');
    }

    public function getShippingInclTax(): float
    {
        return (float) $this->_getData('shipping_incl_tax');
    }

    public function getShippingMethod(): string
    {
        return (string) $this->_getData('shipping_method');
    }

    public function getShippingTaxable(): float
    {
        return (float) $this->_getData('shipping_taxable');
    }

    public function getShippingTaxAmount(): float
    {
        return (float) $this->_getData('shipping_tax_amount');
    }

    public function getStreet1(): string
    {
        return (string) $this->_getData('street1');
    }

    public function getStreet2(): string
    {
        return (string) $this->_getData('street2');
    }

    public function getSubtotal(): float
    {
        return (float) $this->_getData('subtotal');
    }

    public function getSubtotalInclTax(): float
    {
        return (float) $this->_getData('subtotal_incl_tax');
    }

    public function getSubtotalTotalInclTax(): float
    {
        return (float) $this->_getData('subtotal_total_incl_tax');
    }

    public function getSuffix(): string
    {
        return (string) $this->_getData('suffix');
    }

    public function getTaxAmount(): float
    {
        return (float) $this->_getData('tax_amount');
    }

    public function getTelephone(): string
    {
        return (string) $this->_getData('telephone');
    }

    public function getTotalQty(): float
    {
        return (float) $this->_getData('total_qty');
    }

    public function getVirtualAmount(): float
    {
        return (float) $this->_getData('virtual_amount');
    }

    public function getWeight(): float
    {
        return (float) $this->_getData('weight');
    }

    public function getWeightee(): float
    {
        return (float) $this->_getData('weightee');
    }

    public function setAddressType(string $value): static
    {
        return $this->setData('address_type', $value);
    }

    public function setBaseDiscountAmount(float $value): static
    {
        return $this->setData('base_discount_amount', $value);
    }

    public function setBaseExtraTaxAmount(float $value): static
    {
        return $this->setData('base_extra_tax_amount', $value);
    }

    public function setBaseGrandTotal(float $value): static
    {
        return $this->setData('base_grand_total', $value);
    }

    public function setBaseHiddenTaxAmount(float $value): static
    {
        return $this->setData('base_hidden_tax_amount', $value);
    }

    public function setBaseRowTotal(float $value): static
    {
        return $this->setData('base_row_total', $value);
    }

    public function setBaseShippingAmountForDiscount(float $value): static
    {
        return $this->setData('base_shipping_amount_for_discount', $value);
    }

    public function setBaseShippingDiscountAmount(float $value): static
    {
        return $this->setData('base_shipping_discount_amount', $value);
    }

    public function setBaseShippingHiddenTaxAmount(float $value): static
    {
        return $this->setData('base_shipping_hidden_tax_amount', $value);
    }

    public function setBaseShippingInclTax(float $value): static
    {
        return $this->setData('base_shipping_incl_tax', $value);
    }

    public function setBaseShippingTaxable(float $value): static
    {
        return $this->setData('base_shipping_taxable', $value);
    }

    public function setBaseShippingTaxAmount(float $value): static
    {
        return $this->setData('base_shipping_tax_amount', $value);
    }

    public function setBaseSubtotal(float $value): static
    {
        return $this->setData('base_subtotal', $value);
    }

    public function setBaseSubtotalInclTax(float $value): static
    {
        return $this->setData('base_subtotal_incl_tax', $value);
    }

    public function setBaseSubtotalTotalInclTax(float $value): static
    {
        return $this->setData('base_subtotal_total_incl_tax', $value);
    }

    public function setBaseTaxAmount(float $value): static
    {
        return $this->setData('base_tax_amount', $value);
    }

    public function setBaseVirtualAmount(float $value): static
    {
        return $this->setData('base_virtual_amount', $value);
    }

    public function setCity(string $value): static
    {
        return $this->setData('city', $value);
    }

    public function setCollectShippingRates(int $value): static
    {
        return $this->setData('collect_shipping_rates', $value);
    }

    public function setCompany(string $value): static
    {
        return $this->setData('company', $value);
    }

    public function setCountryId(string $value): static
    {
        return $this->setData('country_id', $value);
    }

    public function setCustomerAddressId(int $value): static
    {
        return $this->setData('customer_address_id', $value);
    }

    public function setCustomerId(int $value): static
    {
        return $this->setData('customer_id', $value);
    }

    public function setCustomerNotes(string $value): static
    {
        return $this->setData('customer_notes', $value);
    }

    public function setDiscountAmount(float $value): static
    {
        return $this->setData('discount_amount', $value);
    }

    public function setDiscountDescription(string $value): static
    {
        return $this->setData('discount_description', $value);
    }

    public function setDiscountTaxCompensation(float $value): static
    {
        return $this->setData('discount_tax_compensation', $value);
    }

    public function setEmail(string $value): static
    {
        return $this->setData('email', $value);
    }

    public function setExtraTaxAmount(float $value): static
    {
        return $this->setData('extra_tax_amount', $value);
    }

    public function setFax(string $value): static
    {
        return $this->setData('fax', $value);
    }

    public function setFirstname(string $value): static
    {
        return $this->setData('firstname', $value);
    }

    public function setFreeMethodWeight(float $value): static
    {
        return $this->setData('free_method_weight', $value);
    }

    public function setFreeShipping(int $value): static
    {
        return $this->setData('free_shipping', $value);
    }

    public function setGender(string $value): static
    {
        return $this->setData('gender', $value);
    }

    public function setGiftMessageId(int $value): static
    {
        return $this->setData('gift_message_id', $value);
    }

    public function setGrandTotal(float $value): static
    {
        return $this->setData('grand_total', $value);
    }

    public function setHiddenTaxAmount(float $value): static
    {
        return $this->setData('hidden_tax_amount', $value);
    }

    public function setLastname(string $value): static
    {
        return $this->setData('lastname', $value);
    }

    public function setLimitCarrier(string $value): static
    {
        return $this->setData('limit_carrier', $value);
    }

    public function setMiddlename(string $value): static
    {
        return $this->setData('middlename', $value);
    }

    public function setParentItemId(int $value): static
    {
        return $this->setData('parent_item_id', $value);
    }

    public function setPostcode(string $value): static
    {
        return $this->setData('postcode', $value);
    }

    public function setPrefix(string $value): static
    {
        return $this->setData('prefix', $value);
    }

    public function setQuoteId(int $value): static
    {
        return $this->setData('quote_id', $value);
    }

    public function setRegion(string $value): static
    {
        return $this->setData('region', $value);
    }

    public function setRegionCode(string $value): static
    {
        return $this->setData('region_code', $value);
    }

    public function setRegionId(int $value): static
    {
        return $this->setData('region_id', $value);
    }

    public function setRewardPointsBalance(int $value): static
    {
        return $this->setData('reward_points_balance', $value);
    }

    public function setRowTotal(float $value): static
    {
        return $this->setData('row_total', $value);
    }

    public function setSameAsBilling(int $value): static
    {
        return $this->setData('same_as_billing', $value);
    }

    public function setShippingAmountForDiscount(float $value): static
    {
        return $this->setData('shipping_amount_for_discount', $value);
    }

    public function setShippingDescription(string $value): static
    {
        return $this->setData('shipping_description', $value);
    }

    public function setShippingDiscountAmount(float $value): static
    {
        return $this->setData('shipping_discount_amount', $value);
    }

    public function setShippingHiddenTaxAmount(float $value): static
    {
        return $this->setData('shipping_hidden_tax_amount', $value);
    }

    public function setShippingInclTax(float $value): static
    {
        return $this->setData('shipping_incl_tax', $value);
    }

    public function setShippingMethod(string $value): static
    {
        return $this->setData('shipping_method', $value);
    }

    public function setShippingTaxable(float $value): static
    {
        return $this->setData('shipping_taxable', $value);
    }

    public function setShippingTaxAmount(float $value): static
    {
        return $this->setData('shipping_tax_amount', $value);
    }

    public function setSubtotal(float $value): static
    {
        return $this->setData('subtotal', $value);
    }

    public function setSubtotalInclTax(float $value): static
    {
        return $this->setData('subtotal_incl_tax', $value);
    }

    public function setSubtotalTotalInclTax(float $value): static
    {
        return $this->setData('subtotal_total_incl_tax', $value);
    }

    public function setSuffix(string $value): static
    {
        return $this->setData('suffix', $value);
    }

    public function setTaxAmount(float $value): static
    {
        return $this->setData('tax_amount', $value);
    }

    public function setTelephone(string $value): static
    {
        return $this->setData('telephone', $value);
    }

    public function setTotalQty(float $value): static
    {
        return $this->setData('total_qty', $value);
    }

    public function setVirtualAmount(float $value): static
    {
        return $this->setData('virtual_amount', $value);
    }

    public function setWeight(float $value): static
    {
        return $this->setData('weight', $value);
    }

    public function setWeightee(float $value): static
    {
        return $this->setData('weightee', $value);
    }

    public function getSaveInAddressBook(): int
    {
        return (int) $this->_getData('save_in_address_book');
    }

    public function getStoreId(): int
    {
        return (int) $this->_getData('store_id');
    }

    public function getTaxvat(): ?string
    {
        $value = $this->_getData('taxvat');
        return $value !== null ? (string) $value : null;
    }

    public function getDob(): ?string
    {
        $value = $this->_getData('dob');
        return $value !== null ? (string) $value : null;
    }

    public function getQty(): float
    {
        return (float) $this->_getData('qty');
    }
}
