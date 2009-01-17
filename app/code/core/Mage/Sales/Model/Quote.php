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
 * Quote model
 *
 * Supported events:
 *  sales_quote_load_after
 *  sales_quote_save_before
 *  sales_quote_save_after
 *  sales_quote_delete_before
 *  sales_quote_delete_after
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Quote extends Mage_Core_Model_Abstract
{
    /**
     * Performance +30% without cache
     */
//    const CACHE_TAG         = 'sales_quote';
//    protected $_cacheTag    = 'sales_quote';

    protected $_eventPrefix = 'sales_quote';
    protected $_eventObject = 'quote';

    /**
     * Quote customer model object
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * Quote addresses collection
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected $_addresses = null;

    /**
     * Quote items collection
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected $_items = null;

    /**
     * Quote payments
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected $_payments = null;

    /**
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init('sales/quote');
    }

    /**
     * Get quote store identifier
     *
     * @return int
     */
    public function getStoreId()
    {
        if (!$this->hasStoreId()) {
            return Mage::app()->getStore()->getId();
        }
        return $this->_getData('store_id');
    }

    /**
     * Get quote store model object
     *
     * @return  Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore($this->getStoreId());
    }

    /**
     * Declare quote store model
     *
     * @param   Mage_Core_Model_Store $store
     * @return  Mage_Sales_Model_Quote
     */
    public function setStore(Mage_Core_Model_Store $store)
    {
        $this->setStoreId($store->getId());
        return $this;
    }

    /**
     * Get all available store ids for quote
     *
     * @return array
     */
    public function getSharedStoreIds()
    {
        $ids = $this->_getData('shared_store_ids');
        if (is_null($ids) || !is_array($ids)) {
            if ($website = $this->getWebsite()) {
                return $website->getStoreIds();
            }
            return $this->getStore()->getWebsite()->getStoreIds();
        }
        return $ids;
    }

    /**
     * Prepare data before save
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _beforeSave()
    {
//        $baseCurrencyCode  = Mage::app()->getBaseCurrencyCode();
        $baseCurrencyCode  = $this->getStore()->getBaseCurrencyCode();
        $storeCurrency = $this->getStore()->getBaseCurrency();

        if ($this->hasForcedCurrency()){
            $quoteCurrency = $this->getForcedCurrency();
        } else {
            $quoteCurrency = $this->getStore()->getCurrentCurrency();
        }

        $this->setBaseCurrencyCode($baseCurrencyCode);
        $this->setStoreCurrencyCode($storeCurrency->getCode());
        $this->setQuoteCurrencyCode($quoteCurrency->getCode());
        $this->setStoreToBaseRate($storeCurrency->getRate($baseCurrencyCode));
        $this->setStoreToQuoteRate($storeCurrency->getRate($quoteCurrency));

        if (!$this->hasChangedFlag() || $this->getChangedFlag() == true) {
            $this->setIsChanged(1);
        } else {
            $this->setIsChanged(0);
        }

        parent::_beforeSave();
    }

    /**
     * Save related items
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _afterSave()
    {
        parent::_afterSave();

        if (null !== $this->_addresses) {
            $this->getAddressesCollection()->save();
        }

        if (null !== $this->_items) {
            $this->getItemsCollection()->save();
        }

        if (null !== $this->_payments) {
            $this->getPaymentsCollection()->save();
        }
        return $this;
    }

    /**
     * Loading quote data by customer
     *
     * @return Mage_Sales_Model_Quote
     */
    public function loadByCustomer($customer)
    {
        if ($customer instanceof Mage_Customer_Model_Customer) {
            $customerId = $customer->getId();
            $this->setStoreId($customer->getStoreId());
        }
        else {
            $customerId = (int) $customer;
        }
        $this->_getResource()->loadByCustomerId($this, $customerId);
        $this->_afterLoad();
        return $this;
    }

    /**
     * Assign customer model object data to quote
     *
     * @param   Mage_Customer_Model_Customer $customer
     * @return  Mage_Sales_Model_Quote
     */
    public function assignCustomer(Mage_Customer_Model_Customer $customer)
    {
        if ($customer->getId()) {
            $this->setCustomer($customer);

            $defaultBillingAddress = $customer->getDefaultBillingAddress();
            if ($defaultBillingAddress && $defaultBillingAddress->getId()) {
                $billingAddress = Mage::getModel('sales/quote_address')
                    ->importCustomerAddress($defaultBillingAddress);
                $this->setBillingAddress($billingAddress);
            }

            $defaultShippingAddress= $customer->getDefaultShippingAddress();
            if ($defaultShippingAddress && $defaultShippingAddress->getId()) {
                $shippingAddress = Mage::getModel('sales/quote_address')
                ->importCustomerAddress($defaultShippingAddress);
            }
            else {
                $shippingAddress = Mage::getModel('sales/quote_address');
            }
            $this->setShippingAddress($shippingAddress);
        }

        return $this;
    }

    /**
     * Define customer object
     *
     * @param   Mage_Customer_Model_Customer $customer
     * @return  Mage_Sales_Model_Quote
     */
    public function setCustomer(Mage_Customer_Model_Customer $customer)
    {
        $this->_customer = $customer;
        Mage::helper('core')->copyFieldset('customer_account', 'to_quote', $customer, $this);
        return $this;
    }

    /**
     * Retrieve customer model object
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (is_null($this->_customer)) {
            $this->_customer = Mage::getModel('customer/customer');
            if ($customerId = $this->getCustomerId()) {
                $this->_customer->load($customerId);
                if (!$this->_customer->getId()) {
                    $this->_customer->setCustomerId(null);
                }
            }
        }
        return $this->_customer;
    }

    public function getCustomerTaxClassId()
    {
        /*
        * tax class can vary at any time. so instead of using the value from session, we need to retrieve from db everytime
        * to get the correct tax class
        */
        //if (!$this->getData('customer_group_id') && !$this->getData('customer_tax_class_id')) {
            $classId = Mage::getModel('customer/group')->getTaxClassId($this->getCustomerGroupId());
            $this->setCustomerTaxClassId($classId);
        //}
        return $this->getData('customer_tax_class_id');
    }

    /**
     * Retrieve quote address collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getAddressesCollection()
    {
        if (is_null($this->_addresses)) {
            $this->_addresses = Mage::getModel('sales/quote_address')->getCollection()
                ->setQuoteFilter($this->getId());

            if ($this->getId()) {
                foreach ($this->_addresses as $address) {
                    $address->setQuote($this);
                }
            }
        }
        return $this->_addresses;
    }

    /**
     * Retrieve quote address by type
     *
     * @param   string $type
     * @return  Mage_Sales_Model_Quote_Address
     */
    protected function _getAddressByType($type)
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getAddressType() == $type && !$address->isDeleted()) {
                return $address;
            }
        }

        $address = Mage::getModel('sales/quote_address')->setAddressType($type);
        $this->addAddress($address);
        return $address;
    }

    /**
     * Retrieve quote billing address
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getBillingAddress()
    {
        return $this->_getAddressByType(Mage_Sales_Model_Quote_Address::TYPE_BILLING);
    }

    /**
     * retrieve quote shipping address
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getShippingAddress()
    {
        return $this->_getAddressByType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING);
    }

    public function getAllShippingAddresses()
    {
        $addresses = array();
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getAddressType()==Mage_Sales_Model_Quote_Address::TYPE_SHIPPING
                && !$address->isDeleted()) {
                $addresses[] = $address;
            }
        }
        return $addresses;
    }

    public function getAllAddresses()
    {
        $addresses = array();
        foreach ($this->getAddressesCollection() as $address) {
            if (!$address->isDeleted()) {
                $addresses[] = $address;
            }
        }
        return $addresses;
    }

    /**
     *
     * @param int $addressId
     * @return Mage_Sales_Model_Quote_Address
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

    public function getAddressByCustomerAddressId($addressId)
    {
        foreach ($this->getAddressesCollection() as $address) {
            if (!$address->isDeleted() && $address->getCustomerAddressId()==$addressId) {
                return $address;
            }
        }
        return false;
    }

    public function getShippingAddressByCustomerAddressId($addressId)
    {
        foreach ($this->getAddressesCollection() as $address) {
            if (!$address->isDeleted() && $address->getAddressType()==Mage_Sales_Model_Quote_Address::TYPE_SHIPPING
                && $address->getCustomerAddressId()==$addressId) {
                return $address;
            }
        }
        return false;
    }

    public function removeAddress($addressId)
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getId()==$addressId) {
                $address->isDeleted(true);
                break;
            }
        }
        return $this;
    }

    public function removeAllAddresses()
    {
        foreach ($this->getAddressesCollection() as $address) {
            $address->isDeleted(true);
        }
        return $this;
    }

    public function addAddress(Mage_Sales_Model_Quote_Address $address)
    {
        $address->setQuote($this);
        if (!$address->getId()) {
            $this->getAddressesCollection()->addItem($address);
        }
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Mage_Sales_Model_Quote
     */
    public function setBillingAddress(Mage_Sales_Model_Quote_Address $address)
    {
        $old = $this->getBillingAddress();

        if (!empty($old)) {
            $old->addData($address->getData());
        } else {
            $this->addAddress($address->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_BILLING));
        }
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Mage_Sales_Model_Quote
     */
    public function setShippingAddress(Mage_Sales_Model_Quote_Address $address)
    {
        if ($this->getIsMultiShipping()) {
            $this->addAddress($address->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING));
        }
        else {
            $old = $this->getShippingAddress();

            if (!empty($old)) {
                $old->addData($address->getData());
            } else {
                $this->addAddress($address->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING));
            }
        }
        return $this;
    }

    public function addShippingAddress(Mage_Sales_Model_Quote_Address $address)
    {
        $this->setShippingAddress($address);
        return $this;
    }

    /**
     * Retrieve quote items collection
     *
     * @param   bool $loaded
     * @return  Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getItemsCollection($useCache = true)
    {
        if (is_null($this->_items)) {
            $this->_items = Mage::getModel('sales/quote_item')->getCollection();
            $this->_items->setQuote($this);
        }
        return $this->_items;
    }

    /**
     * Retrieve quote items array
     *
     * @return array
     */
    public function getAllItems()
    {
        $items = array();
        foreach ($this->getItemsCollection() as $item) {
            if (!$item->isDeleted()) {
                $items[] =  $item;
            }
        }
        return $items;
    }

    /**
     * Get array of all items what can be display directly
     *
     * @return array
     */
    public function getAllVisibleItems()
    {
        $items = array();
        foreach ($this->getItemsCollection() as $item) {
            if (!$item->isDeleted() && !$item->getParentItemId()) {
                $items[] =  $item;
            }
        }
        return $items;
    }

    /**
     * Checking items availability
     *
     * @return bool
     */
    public function hasItems()
    {
        return sizeof($this->getAllItems())>0;
    }

    /**
     * Checking availability of items with decimal qty
     *
     * @return bool
     */
    public function hasItemsWithDecimalQty()
    {
        foreach ($this->getAllItems() as $item) {
            if ($item->getProduct()->getStockItem()
                && $item->getProduct()->getStockItem()->getIsQtyDecimal()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieve item model object by item identifier
     *
     * @param   int $itemId
     * @return  Mage_Sales_Model_Quote_Item
     */
    public function getItemById($itemId)
    {
        return $this->getItemsCollection()->getItemById($itemId);
    }

    /**
     * Remove quote item by item identifier
     *
     * @param   int $itemId
     * @return  Mage_Sales_Model_Quote
     */
    public function removeItem($itemId)
    {
        if ($item = $this->getItemById($itemId)) {
            /**
             * If we remove item from quote - we can't use multishipping mode
             */
            $this->setIsMultiShipping(false);
            $item->isDeleted(true);
            if ($item->getHasChildren()) {
                foreach ($item->getChildren() as $child) {
                    $child->isDeleted(true);
                }
            }
        }
        return $this;
    }

    /**
     * Adding new item to quote
     *
     * @param   Mage_Sales_Model_Quote_Item $item
     * @return  Mage_Sales_Model_Quote
     */
    public function addItem(Mage_Sales_Model_Quote_Item $item)
    {
        $item->setQuote($this);
        if (!$item->getId()) {
            $this->getItemsCollection()->addItem($item);
        }
        return $this;
    }

    /**
     * Add product to quote
     *
     * return error message if product type instance can't prepare product
     *
     * @param   mixed $product
     * @return  Mage_Sales_Model_Quote_Item || string
     */
    public function addProduct(Mage_Catalog_Model_Product $product, $request=null)
    {

        if ($request === null) {
            $request = 1;
        }
        if (is_numeric($request)) {
            $request = new Varien_Object(array('qty'=>$request));
        }
        if (!($request instanceof Varien_Object)) {
            Mage::throwException(Mage::helper('sales')->__('Invalid request for adding product to quote'));
        }

        $cartCandidates = $product->getTypeInstance()->prepareForCart($request);

        /**
         * Error message
         */

        if (is_string($cartCandidates)) {
            return $cartCandidates;
        }

        /**
         * If prepare process return one object
         */
        if (!is_array($cartCandidates)) {
            $cartCandidates = array($cartCandidates);
        }




        $parentItem = null;
        $errors = array();
        foreach ($cartCandidates as $candidate) {
            $item = $this->_addCatalogProduct($candidate, $candidate->getCartQty());

            /**
             * As parent item we should always use the item of first added product
             */
            if (!$parentItem) {
                $parentItem = $item;
            }
            if ($parentItem && $candidate->getParentProductId()) {
                $item->setParentItem($parentItem);
            }

            /**
             * We specify qty after we know about parent (for stocj)
             */
            $item->addQty($candidate->getCartQty());

            // collect errors instead of throwing first one
            if ($item->getHasError()) {
                $errors[] = $item->getMessage();
            }
        }
        if (!empty($errors)) {
            Mage::throwException(implode("\n", $errors));
        }
        return $item;
    }

    /**
     * Adding catalog product object data to quote
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_Sales_Model_Quote_Item
     */
    protected function _addCatalogProduct(Mage_Catalog_Model_Product $product, $qty=1)
    {

        $item = $this->getItemByProduct($product);
        if (!$item) {
            $item = Mage::getModel('sales/quote_item');
            $item->setQuote($this);
        }

        /**
         * We can't modify existing child items
         */
        if ($item->getId() && $product->getParentProductId()) {
            return $item;
        }

        $item->setOptions($product->getCustomOptions())
            ->setProduct($product);

        $this->addItem($item);

        return $item;
    }

    /**
     * Retrieve quote item by product id
     *
     * @param   int $productId
     * @return  Mage_Sales_Model_Quote_Item || false
     */
    public function getItemByProduct($product)
    {
        foreach ($this->getAllItems() as $item) {
            if ($item->representProduct($product)) {
                return $item;
            }
        }
        return false;
    }

    public function getItemsSummaryQty()
    {
        $qty = $this->getData('all_items_qty');
        if (is_null($qty)) {
            $qty = 0;
            foreach ($this->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }

                if (($children = $item->getChildren()) && $item->isShipSeparately()) {
                    foreach ($children as $child) {
                        $qty+= $child->getQty()*$item->getQty();
                    }
                } else {
                    $qty+= $item->getQty();
                }
            }
            $this->setData('all_items_qty', $qty);
        }
        return $qty;
    }

    public function getItemVirtualQty()
    {
        $qty = $this->getData('virtual_items_qty');
        if (is_null($qty)) {
            $qty = 0;
            foreach ($this->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }

                if (($children = $item->getChildren()) && $item->isShipSeparately()) {
                    foreach ($children as $child) {
                        if ($child->getProduct()->getIsVirtual()) {
                            $qty+= $child->getQty();
                        }
                    }
                } else {
                    if ($item->getProduct()->getIsVirtual()) {
                        $qty+= $item->getQty();
                    }
                }
            }
            $this->setData('virtual_items_qty', $qty);
        }
        return $qty;
    }

    /*********************** PAYMENTS ***************************/
    public function getPaymentsCollection()
    {
        if (is_null($this->_payments)) {
            $this->_payments = Mage::getModel('sales/quote_payment')->getCollection()
                ->setQuoteFilter($this->getId());

            if ($this->getId()) {
                foreach ($this->_payments as $payment) {
                    $payment->setQuote($this);
                }
            }
        }
        return $this->_payments;
    }

    /**
     * @return Mage_Sales_Model_Quote_Payment
     */
    public function getPayment()
    {
        foreach ($this->getPaymentsCollection() as $payment) {
            if (!$payment->isDeleted()) {
                return $payment;
            }
        }
        $payment = Mage::getModel('sales/quote_payment');
        $this->addPayment($payment);
        return $payment;
    }

    public function getPaymentById($paymentId)
    {
        foreach ($this->getPaymentsCollection() as $payment) {
            if ($payment->getId()==$paymentId) {
                return $payment;
            }
        }
        return false;
    }

    public function addPayment(Mage_Sales_Model_Quote_Payment $payment)
    {
        $payment->setQuote($this);
        if (!$payment->getId()) {
            $this->getPaymentsCollection()->addItem($payment);
        }
        return $this;
    }

    public function setPayment(Mage_Sales_Model_Quote_Payment $payment)
    {
        if (!$this->getIsMultiPayment() && ($old = $this->getPayment())) {
            $payment->setId($old->getId());
        }
        $this->addPayment($payment);

        return $payment;
    }

    public function removePayment()
    {
        $this->getPayment()->isDeleted(true);
        return $this;
    }

    /**
     * Collect totals
     *
     * @return Mage_Sales_Model_Quote
     */
    public function collectTotals()
    {
        $this->setSubtotal(0);
        $this->setBaseSubtotal(0);

        $this->setSubtotalWithDiscount(0);
        $this->setBaseSubtotalWithDiscount(0);

        $this->setGrandTotal(0);
        $this->setBaseGrandTotal(0);

        foreach ($this->getAllAddresses() as $address) {
            $address->setSubtotal(0);
            $address->setBaseSubtotal(0);

            $address->setSubtotalWithDiscount(0);
            $address->setBaseSubtotalWithDiscount(0);

            $address->setGrandTotal(0);
            $address->setBaseGrandTotal(0);

            $address->collectTotals();

            $this->setSubtotal((float) $this->getSubtotal()+$address->getSubtotal());
            $this->setBaseSubtotal((float) $this->getBaseSubtotal()+$address->getBaseSubtotal());

            $this->setSubtotalWithDiscount((float) $this->getSubtotalWithDiscount()+$address->getSubtotalWithDiscount());
            $this->setBaseSubtotalWithDiscount((float) $this->getBaseSubtotalWithDiscount()+$address->getBaseSubtotalWithDiscount());

            $this->setGrandTotal((float) $this->getGrandTotal()+$address->getGrandTotal());
            $this->setBaseGrandTotal((float) $this->getBaseGrandTotal()+$address->getBaseGrandTotal());
        }

        Mage::helper('sales')->checkQuoteAmount($this, $this->getGrandTotal());
        Mage::helper('sales')->checkQuoteAmount($this, $this->getBaseGrandTotal());

        $this->setItemsCount(0);
        $this->setItemsQty(0);
        $this->setVirtualItemsQty(0);

        foreach ($this->getAllVisibleItems() as $item) {
            //if ($item->getProduct()->getIsVirtual()) {
            //    $this->setVirtualItemsQty($this->getVirtualItemsQty() + $item->getQty());
            //}

            if ($item->getParentItem()) {
                continue;
            }

            if (($children = $item->getChildren()) && $item->isShipSeparately()) {
                foreach ($children as $child) {
                    if ($child->getProduct()->getIsVirtual()) {
                        $this->setVirtualItemsQty($this->getVirtualItemsQty() + $child->getQty()*$item->getQty());
                    }
                    $this->setItemsCount($this->getItemsCount()+1);
                    $this->setItemsQty((float) $this->getItemsQty()+$child->getQty()*$item->getQty());
                }
            } else {
                if ($item->getProduct()->getIsVirtual()) {
                    $this->setVirtualItemsQty($this->getVirtualItemsQty() + $item->getQty());
                }
                $this->setItemsCount($this->getItemsCount()+1);
                $this->setItemsQty((float) $this->getItemsQty()+$item->getQty());
            }

            //$this->setItemsCount($this->getItemsCount()+1);
            //$this->setItemsQty((float) $this->getItemsQty()+$item->getQty());
        }
        return $this;
    }

    /**
     * Get all quote totals
     *
     * @return array
     */
    public function getTotals()
    {
        $totals = $this->getShippingAddress()->getTotals();
        foreach ($this->getBillingAddress()->getTotals() as $code => $total) {
            if (isset($totals[$code])) {
                $totals[$code]->setValue($totals[$code]->getValue()+$total->getValue());
            }
            else {
                $totals[$code] = $total;
            }
        }
        return $totals;
    }

    public function addMessage($message, $index='error')
    {
        $messages = $this->getData('messages');
        if (is_null($messages)) {
            $messages = array();
        }

        if (isset($messages[$index])) {
            return $this;
        }

        if (is_string($message)) {
            $message = Mage::getSingleton('core/message')->error($message);
        }

        $messages[$index] = $message;
        $this->setData('messages', $messages);
        return $this;
    }

    public function getMessages()
    {
        $messages = $this->getData('messages');
        if (is_null($messages)) {
            $messages = array();
            $this->setData('messages', $messages);
        }
        return $messages;
    }

    public function reserveOrderId()
    {
        if (!$this->getReservedOrderId()) {
            $this->setReservedOrderId($this->_getResource()->getReservedOrderId($this));
        } else {
            //checking if reserved order id was already used for some order
            //if yes reserving new one if not using old one
            if ($this->_getResource()->isOrderIncrementIdUsed($this->getReservedOrderId())) {
                $this->setReservedOrderId($this->_getResource()->getReservedOrderId($this));
            }
        }
        return $this;
    }

    public function validateMinimumAmount()
    {
        foreach ($this->getAllAddresses() as $address) {
            if (!$address->validateMinimumAmount()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check quote for virtual product only
     *
     * @return bool
     */
    public function isVirtual()
    {
        $isVirtual = true;
        $countItems = 0;
        foreach ($this->getItemsCollection() as $_item) {
            /* @var $_item Mage_Sales_Model_Quote_Item */
            if ($_item->isDeleted() || $_item->getParentItemId()) {
                continue;
            }
            $countItems ++;
            if (!$_item->getProduct()->getIsVirtual()) {
                $isVirtual = false;
            }
        }
        return $countItems == 0 ? false : $isVirtual;
    }

    /**
     * Check quote for virtual product only
     *
     * @return bool
     */
    public function getIsVirtual()
    {
        return intval($this->isVirtual());
    }

    /**
     * Has a virtual products on quote
     *
     * @return bool
     */
    public function hasVirtualItems()
    {
        $hasVirtual = false;
        foreach ($this->getItemsCollection() as $_item) {
            if ($_item->getParentItemId()) {
                continue;
            }
            if ($_item->getProduct()->getTypeInstance()->isVirtual()) {
                $hasVirtual = true;
            }
        }
        return $hasVirtual;
    }

    public function isAllowedGuestCheckout()
    {
        return Mage::getStoreConfig('checkout/options/guest_checkout');
    }

    /**
     * Merge quotes
     *
     * @param   Mage_Sales_Model_Quote $quote
     * @return  Mage_Sales_Model_Quote
     */
    public function merge(Mage_Sales_Model_Quote $quote)
    {
        foreach ($quote->getAllVisibleItems() as $item) {
            $found = false;
            foreach ($this->getAllItems() as $quoteItem) {
                if ($quoteItem->compare($item)) {
                    $quoteItem->setQty($quoteItem->getQty() + $item->getQty());
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $newItem = clone $item;
                $this->addItem($newItem);
                if ($item->getHasChildren()) {
                    foreach ($item->getChildren() as $child) {
                        $newChild = clone $child;
                        $newChild->setParentItem($newItem);
                        $this->addItem($newChild);
                    }
                }
            }
        }

        if ($quote->getCouponCode()) {
            $this->setCouponCode($quote->getCouponCode());
        }
        return $this;
    }
}