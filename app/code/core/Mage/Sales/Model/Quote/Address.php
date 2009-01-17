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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Quote address  model
 */
class Mage_Sales_Model_Quote_Address extends Mage_Customer_Model_Address_Abstract
{
    const TYPE_BILLING  = 'billing';
    const TYPE_SHIPPING = 'shipping';
    const RATES_FETCH = 1;
    const RATES_RECALCULATE = 2;

    /**
     * Quote object
     *
     * @var Mage_Sales_Model_Quote
     */
    protected $_items = null;
    protected $_quote = null;
    protected $_rates = null;
    protected $_totalModels;
    protected $_totals = array();

    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('sales/quote_address');
    }

    /**
     * Initialize quote identifier before save
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if ($this->getQuote()) {
            $this->setQuoteId($this->getQuote()->getId());
        }
        return $this;
    }

    /**
     * Save child collections
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    protected function _afterSave()
    {
        parent::_afterSave();
        if (null !== $this->_items) {
            $this->getItemsCollection()->save();
        }
        if (null !== $this->_rates) {
            $this->getShippingRatesCollection()->save();
        }
        return $this;
    }

    /**
     * Declare adress quote model object
     *
     * @param   Mage_Sales_Model_Quote $quote
     * @return  Mage_Sales_Model_Quote_Address
     */
    public function setQuote(Mage_Sales_Model_Quote $quote)
    {
        $this->_quote = $quote;
        $this->setQuoteId($quote->getId());
        return $this;
    }

    /**
     * Retrieve quote object
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_quote;
    }

    /**
     * Import quote address data from customer address object
     *
     * @param   Mage_Customer_Model_Address $address
     * @return  Mage_Sales_Model_Quote_Address
     */
    public function importCustomerAddress(Mage_Customer_Model_Address $address)
    {
        Mage::helper('core')->copyFieldset('customer_address', 'to_quote_address', $address, $this);
        $this->setEmail($address->hasEmail() ? $address->getEmail() : $address->getCustomer()->getEmail());
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
     * @param   Mage_Sales_Model_Order_Address $address
     * @return  Mage_Sales_Model_Quote_Address
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
     * @param   array $arrAttributes
     * @return  array
     */
    public function toArray(array $arrAttributes = array())
    {
        $arr = parent::toArray();
        $arr['rates'] = $this->getShippingRatesCollection()->toArray($arrAttributes);
        $arr['items'] = $this->getItemsCollection()->toArray($arrAttributes);
        foreach ($this->getTotals() as $k=>$total) {
            $arr['totals'][$k] = $total->toArray();
        }
        return $arr;
    }

    /**
     * Retrieve address items collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
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
     * @return array
     */
    public function getAllItems()
    {
        $quoteItems = $this->getQuote()->getItemsCollection();
        $addressItems = $this->getItemsCollection();

        $items = array();
        if ($this->getQuote()->getIsMultiShipping() && $addressItems->count() > 0) {
            foreach ($addressItems as $aItem) {
                if ($aItem->isDeleted()) {
                    continue;
                }

                if (!$aItem->getQuoteItemImported()) {
                    if ($qItem = $this->getQuote()->getItemById($aItem->getQuoteItemId())) {
                        $this->addItem($aItem);
                        $aItem->importQuoteItem($qItem);
                    }
                }
                $items[] = $aItem;
            }
        } else {
            $isQuoteVirtual = $this->getQuote()->isVirtual();
            foreach ($quoteItems as $qItem) {
                if ($qItem->isDeleted()) {
                    continue;
                }
//                if ($this->getAddressType() == self::TYPE_BILLING && $qItem->getProduct()->getIsVirtual()) {
//                    $items[] = $qItem;
//                }
//                elseif ($this->getAddressType() == self::TYPE_SHIPPING && !$qItem->getProduct()->getIsVirtual()) {
//                    $items[] = $qItem;
//                }
                /**
                 * For virtual quote we assign all items to billing address
                 */
                if ($isQuoteVirtual) {
                	if ($this->getAddressType() == self::TYPE_BILLING) {
                		$items[] = $qItem;
                	}
                }
                else {
                	if ($this->getAddressType() == self::TYPE_SHIPPING) {
                		$items[] = $qItem;
                	}
                }
            }
        }

        return $items;
    }

    public function getAllVisibleItems()
    {
        $items = array();
        foreach ($this->getAllItems() as $item) {
            if (!$item->getParentItemId()) {
                $items[] = $item;
            }
        }
        return $items;
    }

    public function getItemQty($itemId=0)
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
            if ($item = $this->getItemById($itemId)) {
                $qty = $item->getQty();
            }
        }
        return $qty;
    }

    public function hasItems()
    {
        return sizeof($this->getAllItems())>0;
    }

    public function getItemById($itemId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getId()==$itemId) {
                return $item;
            }
        }
        return false;
    }

    public function getItemByQuoteItemId($itemId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getQuoteItemId()==$itemId) {
                return $item;
            }
        }
        return false;
    }

    public function removeItem($itemId)
    {
        if ($item = $this->getItemById($itemId)) {
            $item->isDeleted(true);
        }
        return $this;
    }

    /**
     * Add item to address
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @param   int $qty
     * @return  Mage_Sales_Model_Quote_Address
     */
    public function addItem(Mage_Sales_Model_Quote_Item_Abstract $item, $qty=null)
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
        }
        else {
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
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getShippingRatesCollection()
    {
        if (is_null($this->_rates)) {
            $this->_rates = Mage::getModel('sales/quote_address_rate')->getCollection()
                ->setAddressFilter($this->getId());
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
     * @return array
     */
    public function getAllShippingRates()
    {
        $rates = array();
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
     */
    public function getGroupedAllShippingRates()
    {
        $rates = array();
        foreach ($this->getShippingRatesCollection() as $rate) {
            if (!$rate->isDeleted() && $rate->getCarrierInstance()) {
                if (!isset($rates[$rate->getCarrier()])) {
                    $rates[$rate->getCarrier()] = array();
                }

                $rates[$rate->getCarrier()][] = $rate;
                $rates[$rate->getCarrier()][0]->carrier_sort_order = $rate->getCarrierInstance()->getSortOrder();
            }
        }
        uasort($rates, array($this, '_sortRates'));
        return $rates;
    }

    protected function _sortRates($a, $b)
    {
        if ((int)$a[0]->carrier_sort_order < (int)$b[0]->carrier_sort_order) {
            return -1;
        }
        elseif ((int)$a[0]->carrier_sort_order > (int)$b[0]->carrier_sort_order) {
            return 1;
        }
        else {
            return 0;
        }
    }

    /**
     * Retrieve shipping rate by identifier
     *
     * @param   int $rateId
     * @return  Mage_Sales_Model_Quote_Address_Rate | false
     */
    public function getShippingRateById($rateId)
    {
        foreach ($this->getShippingRatesCollection() as $rate) {
            if ($rate->getId()==$rateId) {
                return $rate;
            }
        }
        return false;
    }

    /**
     * Retrieve shipping rate by code
     *
     * @param   string $code
     * @return  Mage_Sales_Model_Quote_Address_Rate
     */
    public function getShippingRateByCode($code)
    {
        foreach ($this->getShippingRatesCollection() as $rate) {
            if ($rate->getCode()==$code) {
                return $rate;
            }
        }
        return false;
    }

    public function removeAllShippingRates()
    {
        foreach ($this->getShippingRatesCollection() as $rate) {
            $rate->isDeleted(true);
        }
        return $this;
    }

    public function addShippingRate(Mage_Sales_Model_Quote_Address_Rate $rate)
    {
        $rate->setAddress($this);
        $this->getShippingRatesCollection()->addItem($rate);
        return $this;
    }

    /**
     * Collecting shipping rates by address
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function collectShippingRates()
    {
        if (!$this->getCollectShippingRates()) {
            return $this;
        }

        $this->setCollectShippingRates(false);

        $this->removeAllShippingRates();

        if (!$this->getCountryId() && !$this->getPostcode()) {
            return $this;
        }

        $request = Mage::getModel('shipping/rate_request');
        $request->setAllItems($this->getAllItems());
        $request->setDestCountryId($this->getCountryId());
        $request->setDestRegionId($this->getRegionId());
        $request->setDestRegionCode($this->getRegionCode());
        /**
         * need to call getStreet with -1
         * to get data in string instead of array
         */
        $request->setDestStreet($this->getStreet(-1));
        $request->setDestCity($this->getCity());
        $request->setDestPostcode($this->getPostcode());
        $request->setPackageValue($this->getBaseSubtotal());
        $request->setPackageValueWithDiscount($this->getBaseSubtotalWithDiscount());
        $request->setPackageWeight($this->getWeight());
        $request->setPackageQty($this->getItemQty());

        $request->setFreeMethodWeight($this->getFreeMethodWeight());

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

        $result = Mage::getModel('shipping/shipping')
            ->collectRates($request)
                ->getResult();

        $found = false;
        if ($result) {
            $shippingRates = $result->getAllRates();

            foreach ($shippingRates as $shippingRate) {
                $rate = Mage::getModel('sales/quote_address_rate')
                    ->importShippingRate($shippingRate);
                $this->addShippingRate($rate);

                if ($this->getShippingMethod()==$rate->getCode()) {
                    $this->setShippingAmount($rate->getPrice());
                    $found = true;
                }
            }
        }

        if (!$found) {
            $this->setShippingAmount(0)
                ->setBaseShippingAmount(0)
                ->setShippingMethod('')
                ->setShippingDescription('');
        }

        return $this;
    }

    public function getTotalModels()
    {
        if (!$this->_totalModels) {
            $totalsConfig = Mage::getConfig()->getNode('global/sales/quote/totals');
            $models = array();
            foreach ($totalsConfig->children() as $totalCode=>$totalConfig) {
                $sort = Mage::getStoreConfig('sales/totals_sort/'.$totalCode);
                while (isset($models[$sort])) {
                    $sort++;
                }
                $class = $totalConfig->getClassName();
                if ($class && ($model = Mage::getModel($class))) {
                    $models[$sort] = $model->setCode($totalCode);
                }
            }
            ksort($models);
            $this->_totalModels = $models;
        }
        return $this->_totalModels;
    }

    public function collectTotals()
    {
        foreach ($this->getTotalModels() as $model) {
            if (is_callable(array($model, 'collect'))) {
                $model->collect($this);
            }
        }
        return $this;
    }

    public function getTotals()
    {
        foreach ($this->getTotalModels() as $model) {
            if (is_callable(array($model, 'fetch'))) {
                $model->fetch($this);
            }
        }
        return $this->_totals;
    }

    public function addTotal($total)
    {
        if (is_array($total)) {
            $totalInstance = Mage::getModel('sales/quote_address_total')
                ->setData($total);
        } elseif ($total instanceof Mage_Sales_Model_Quote_Total) {
            $totalInstance = $total;
        }
        $this->_totals[$totalInstance->getCode()] = $totalInstance;
        return $this;
    }

    public function __clone()
    {
        $this->setId(null);
    }

    public function validateMinimumAmount()
    {
        if ($this->getAddressType()!=self::TYPE_SHIPPING) {
            return true;
        }
        $storeId = $this->getQuote()->getStoreId();
        if (!Mage::getStoreConfigFlag('sales/minimum_order/active', $storeId)) {
            return true;
        }
        $amount = Mage::getStoreConfig('sales/minimum_order/amount', $storeId);
        if ($this->getBaseSubtotalWithDiscount()<$amount) {
            return false;
        }
        return true;
    }

    public function getAppliedTaxes()
    {
        return unserialize($this->getData('applied_taxes'));
    }

    public function setAppliedTaxes($data)
    {
        return $this->setData('applied_taxes', serialize($data));
    }

    public function setShippingAmount($value, $alreadyExclTax = false)
    {
        if (Mage::helper('tax')->shippingPriceIncludesTax()) {
            $includingTax = Mage::helper('tax')->getShippingPrice($value, true, $this, $this->getQuote()->getCustomerTaxClassId());
            if (!$alreadyExclTax) {
                $value = Mage::helper('tax')->getShippingPrice($value, false, $this, $this->getQuote()->getCustomerTaxClassId());
            }
            $this->setShippingTaxAmount($includingTax - $value);
        }
        return $this->setData('shipping_amount', $value);
    }

    public function setBaseShippingAmount($value, $alreadyExclTax = false)
    {
        if (Mage::helper('tax')->shippingPriceIncludesTax()) {
            $includingTax = Mage::helper('tax')->getShippingPrice($value, true, $this, $this->getQuote()->getCustomerTaxClassId());
            if (!$alreadyExclTax) {
                $value = Mage::helper('tax')->getShippingPrice($value, false, $this, $this->getQuote()->getCustomerTaxClassId());
            }
            $this->setBaseShippingTaxAmount($includingTax - $value);
        }
        return $this->setData('base_shipping_amount', $value);
    }
}