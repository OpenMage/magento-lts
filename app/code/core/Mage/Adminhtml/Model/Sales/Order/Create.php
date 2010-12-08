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
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Order create model
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_Sales_Order_Create extends Varien_Object
{
    /**
     * Quote session object
     *
     * @var Mage_Adminhtml_Model_Session_Quote
     */
    protected $_session;

    /**
     * Quote customer wishlist model object
     *
     * @var Mage_Wishlist_Model_Wishlist
     */
    protected $_wishlist;

    /**
     * Sales Quote instance
     *
     * @var Mage_Sales_Model_Quote
     */
    protected $_cart;

    /**
     * Catalog Compare List instance
     *
     * @var Mage_Catalog_Model_Product_Compare_List
     */
    protected $_compareList;

    /**
     * Re-collect quote flag
     *
     * @var boolean
     */
    protected $_needCollect;

    /**
     * Re-collect cart flag
     *
     * @var boolean
     */
    protected $_needCollectCart = false;

    /**
     * Collect (import) data and validate it flag
     *
     * @var boolean
     */
    protected $_isValidate              = false;

    /**
     * Customer instance
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * Customer Address Form instance
     *
     * @var Mage_Customer_Model_Form
     */
    protected $_customerAddressForm;

    /**
     * Customer Form instance
     *
     * @var Mage_Customer_Model_Form
     */
    protected $_customerForm;

    /**
     * Array of validate errors
     *
     * @var array
     */
    protected $_errors = array();

    public function __construct()
    {
        $this->_session = Mage::getSingleton('adminhtml/session_quote');
    }

    /**
     * Set validate data in import data flag
     *
     * @param boolean $flag
     * @return Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function setIsValidate($flag)
    {
        $this->_isValidate = (bool)$flag;
        return $this;
    }

    /**
     * Return is validate data in import flag
     *
     * @return boolean
     */
    public function getIsValidate()
    {
        return $this->_isValidate;
    }

    /**
     * Retrieve quote item
     *
     * @param   mixed $item
     * @return  Mage_Sales_Model_Quote_Item
     */
    protected function _getQuoteItem($item)
    {
        if ($item instanceof Mage_Sales_Model_Quote_Item) {
            return $item;
        }
        elseif (is_numeric($item)) {
            return $this->getSession()->getQuote()->getItemById($item);
        }
        return false;
    }

    /**
     * Initialize data for price rules
     *
     * @return Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function initRuleData()
    {
        Mage::register('rule_data', new Varien_Object(array(
            'store_id'  => $this->_session->getStore()->getId(),
            'website_id'  => $this->_session->getStore()->getWebsiteId(),
            'customer_group_id' => $this->getCustomerGroupId(),
        )));
        return $this;
    }

    /**
     * Set collect totals flag for quote
     *
     * @param   bool $flag
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function setRecollect($flag)
    {
        $this->_needCollect = $flag;
        return $this;
    }

    /**
     * Quote saving
     *
     * @return Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function saveQuote()
    {
        if (!$this->getQuote()->getId()) {
            return $this;
        }

        if ($this->_needCollect) {
            $this->getQuote()->collectTotals();
        }

        $this->getQuote()->save();
        return $this;
    }

    /**
     * Retrieve session model object of quote
     *
     * @return Mage_Adminhtml_Model_Session_Quote
     */
    public function getSession()
    {
        return $this->_session;
    }

    /**
     * Retrieve quote object model
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getSession()->getQuote();
    }

    /**
     * Initialize creation data from existing order
     *
     * @param Mage_Sales_Model_Order $order
     * @return unknown
     */
    public function initFromOrder(Mage_Sales_Model_Order $order)
    {
        if (!$order->getReordered()) {
            $this->getSession()->setOrderId($order->getId());
        } else {
            $this->getSession()->setReordered($order->getId());
        }

        /**
         * Check if we edit quest order
         */
        $this->getSession()->setCurrencyId($order->getOrderCurrencyCode());
        if ($order->getCustomerId()) {
            $this->getSession()->setCustomerId($order->getCustomerId());
        } else {
            $this->getSession()->setCustomerId(false);
        }

        $this->getSession()->setStoreId($order->getStoreId());

        /**
         * Initialize catalog rule data with new session values
         */
        $this->initRuleData();

        foreach ($order->getItemsCollection(
            array_keys(Mage::getConfig()->getNode('adminhtml/sales/order/create/available_product_types')->asArray()),
            true
            ) as $orderItem) {
            /* @var $orderItem Mage_Sales_Model_Order_Item */
            if (!$orderItem->getParentItem()) {
                if ($order->getReordered()) {
                    $qty = $orderItem->getQtyOrdered();
                }
                else {
                    $qty = $orderItem->getQtyOrdered() - $orderItem->getQtyShipped() - $orderItem->getQtyInvoiced();
                }

                if ($qty > 0) {
                    $item = $this->initFromOrderItem($orderItem, $qty);
                    if (is_string($item)) {
                        Mage::throwException($item);
                    }
                }
            }
        }

        $this->_initBillingAddressFromOrder($order);
        $this->_initShippingAddressFromOrder($order);

        $this->setShippingMethod($order->getShippingMethod());
        $this->getQuote()->getShippingAddress()->setShippingDescription($order->getShippingDescription());

        $this->getQuote()->getPayment()->addData($order->getPayment()->getData());


        $orderCouponCode = $order->getCouponCode();
        if ($orderCouponCode) {
            $this->getQuote()->setCouponCode($orderCouponCode);
        }

        if ($this->getQuote()->getCouponCode()) {
            $this->getQuote()->collectTotals();
        }

        Mage::helper('core')->copyFieldset(
            'sales_copy_order',
            'to_edit',
            $order,
            $this->getQuote()
        );

        Mage::dispatchEvent('sales_convert_order_to_quote', array(
            'order' => $order,
            'quote' => $this->getQuote()
        ));

        if (!$order->getCustomerId()) {
            $this->getQuote()->setCustomerIsGuest(true);
        }

        if ($this->getSession()->getUseOldShippingMethod(true)) {
            /*
             * if we are making reorder or editing old order
             * we need to show old shipping as preselected
             * so for this we need to collect shipping rates
             */
            $this->collectShippingRates();
        } else {
            /*
             * if we are creating new order then we don't need to collect
             * shipping rates before customer hit appropriate button
             */
            $this->collectRates();
        }

        // Make collect rates when user click "Get shipping methods and rates" in order creating
        // $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
        // $this->getQuote()->getShippingAddress()->collectShippingRates();

        $this->getQuote()->save();

        return $this;
    }

    protected function _initBillingAddressFromOrder(Mage_Sales_Model_Order $order)
    {
        $this->getQuote()->getBillingAddress()->setCustomerAddressId('');
        Mage::helper('core')->copyFieldset(
            'sales_copy_order_billing_address',
            'to_order',
            $order->getBillingAddress(),
            $this->getQuote()->getBillingAddress()
        );
    }

    protected function _initShippingAddressFromOrder(Mage_Sales_Model_Order $order)
    {
        $this->getQuote()->getShippingAddress()->setCustomerAddressId('');
        Mage::helper('core')->copyFieldset(
            'sales_copy_order_shipping_address',
            'to_order',
            $order->getShippingAddress(),
            $this->getQuote()->getShippingAddress()
        );
    }

    /**
     * Initialize creation data from existing order Item
     *
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @return Mage_Sales_Model_Quote_Item | string
     */
    public function initFromOrderItem(Mage_Sales_Model_Order_Item $orderItem, $qty = 1)
    {
        if (!$orderItem->getId()) {
            return $this;
        }

        $product = Mage::getModel('catalog/product')
            ->setStoreId($this->getSession()->getStoreId())
            ->load($orderItem->getProductId());

        if ($product->getId()) {
            $info = $orderItem->getProductOptionByCode('info_buyRequest');
            $info = new Varien_Object($info);
            $product->setSkipCheckRequiredOption(true);
            $item = $this->getQuote()->addProduct($product,$info);
            if (is_string($item)) {
                return $item;
            }
            $item->setQty($qty);
            if ($additionalOptions = $orderItem->getProductOptionByCode('additional_options')) {
                $item->addOption(new Varien_Object(
                    array(
                        'product' => $item->getProduct(),
                        'code' => 'additional_options',
                        'value' => serialize($additionalOptions)
                    )
                ));
            }
            Mage::dispatchEvent('sales_convert_order_item_to_quote_item', array(
                'order_item' => $orderItem,
                'quote_item' => $item
            ));

            return $item;
        }

        return $this;
    }

    /**
     * Retrieve customer wishlist model object
     *
     * @return Mage_Wishlist_Model_Wishlist
     */
    public function getCustomerWishlist()
    {
        if (!is_null($this->_wishlist)) {
            return $this->_wishlist;
        }

        if ($this->getSession()->getCustomer()->getId()) {
            $this->_wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer(
                $this->getSession()->getCustomer(), true
            );
            $this->_wishlist->setStore($this->getSession()->getStore())
                ->setSharedStoreIds($this->getSession()->getStore()->getWebsite()->getStoreIds());
        }
        else {
            $this->_wishlist = false;
        }

        return $this->_wishlist;
    }

    /**
     * Retrieve customer cart quote object model
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getCustomerCart()
    {
        if (!is_null($this->_cart)) {
            return $this->_cart;
        }

        $this->_cart = Mage::getModel('sales/quote');

        if ($this->getSession()->getCustomer()->getId()) {
            $this->_cart->setStore($this->getSession()->getStore())
                ->loadByCustomer($this->getSession()->getCustomer()->getId());
            if (!$this->_cart->getId()) {
                $this->_cart->assignCustomer($this->getSession()->getCustomer());
                $this->_cart->save();
            }
        }

        return $this->_cart;
    }

    /**
     * Retrieve customer compare list model object
     *
     * @return Mage_Catalog_Model_Product_Compare_List
     */
    public function getCustomerCompareList()
    {
        if (!is_null($this->_compareList)) {
            return $this->_compareList;
        }

        if ($this->getSession()->getCustomer()->getId()) {
            $this->_compareList = Mage::getModel('catalog/product_compare_list');
        }
        else {
            $this->_compareList = false;
        }
        return $this->_compareList;
    }

    public function getCustomerGroupId()
    {
        $groupId = $this->getQuote()->getCustomerGroupId();
        if (!$groupId) {
            $groupId = $this->getSession()->getCustomerGroupId();
        }
        return $groupId;
    }

    /**
     * Move quote item to another items store
     *
     * @param   mixed $item
     * @param   string $mogeTo
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function moveQuoteItem($item, $moveTo, $qty)
    {
        if ($item = $this->_getQuoteItem($item)) {
            switch ($moveTo) {
                case 'order':
                    $info = array();
                    if ($info = $item->getOptionByCode('info_buyRequest')) {
                        $info = new Varien_Object(
                            unserialize($info->getValue())
                        );
                        $info->setOptions($this->_prepareOptionsForRequest($item));
                    }

                    $product = Mage::getModel('catalog/product')
                        ->setStoreId($this->getQuote()->getStoreId())
                        ->load($item->getProduct()->getId());

                    $product->setSkipCheckRequiredOption(true);

                    $newItem = $this->getQuote()->addProduct($product, $info);

                    $this->removeItem($item->getId(), 'cart');

                    if (is_string($newItem)) {
                        Mage::throwException($newItem);
                    }
                    $product->unsSkipCheckRequiredOption();
                    $newItem->checkData();
                    $newItem->setQty($qty);
                    $this->_needCollectCart = true;
                    break;
                case 'cart':
                    if (($cart = $this->getCustomerCart()) && is_null($item->getOptionByCode('additional_options'))) {
                        //options and info buy request
                        $product = Mage::getModel('catalog/product')
                            ->setStoreId($this->getQuote()->getStoreId())
                            ->load($item->getProduct()->getId());
                        $product->setSkipCheckRequiredOption(true);

                        $info = array();
                        if ($info = $item->getOptionByCode('info_buyRequest')) {
                            $info = new Varien_Object(
                                unserialize($info->getValue())
                            );
                            $info->setQty($qty);
                            $info->setOptions($this->_prepareOptionsForRequest($item));
                        } else {
                            $info = new Varien_Object(array(
                                'product_id' => $product->getId(),
                                'qty' => $qty,
                                'options' => $this->_prepareOptionsForRequest($item)
                            ));
                        }

                        $cartItem = $cart->addProduct($product, $info);
                        if (is_string($cartItem)) {
                            Mage::throwException($cartItem);
                        }
                        $product->unsSkipCheckRequiredOption();
                        $cartItem->setPrice($item->getProduct()->getPrice());
                        $this->_needCollectCart = true;
                    }
                    break;
                case 'wishlist':
                    if ($wishlist = $this->getCustomerWishlist()) {
                        $wishlist->addNewItem($item->getProduct()->getId());
                    }
                    break;
                case 'comparelist':

                    break;
                default:
                    break;
            }
            $this->getQuote()->removeItem($item->getId());
            $this->setRecollect(true);
        }
        return $this;
    }

    public function applySidebarData($data)
    {
        // skip item duplicates based on info_buyRequest option
        $infoBuyRequests = array();

        if (isset($data['reorder'])) {
            foreach ($data['reorder'] as $orderItemId=>$value) {
                $orderItem = Mage::getModel('sales/order_item')->load($orderItemId);
                $item = $this->initFromOrderItem($orderItem);
                if (is_string($item)) {
                    Mage::throwException($item);
                }
                $infobuyRequest = $item->getOptionByCode('info_buyRequest');
                if ($infobuyRequest !== null) {
                    $infoBuyRequests[] = $infobuyRequest->getValue();
                }
            }
        }
        if (isset($data['cartItem'])) {
            foreach ($data['cartItem'] as $itemId => $qty) {
                if ($item = $this->getCustomerCart()->getItemById($itemId)) {
                    $infobuyRequest = $item->getOptionByCode('info_buyRequest');
                    if ($infobuyRequest === null || !in_array($infobuyRequest->getValue(), $infoBuyRequests)) {
                        $this->moveQuoteItem($item, 'order', $qty);
                    }
                    $this->removeItem($itemId, 'cart');
                }
            }
        }
        if (isset($data['add'])) {
            foreach ($data['add'] as $productId => $qty) {
                $this->addProduct($productId, $qty);
            }
        }
        if (isset($data['remove'])) {
            foreach ($data['remove'] as $itemId => $from) {
                $this->removeItem($itemId, $from);
            }
        }
        return $this;
    }

    /**
     * Remove item from some of customer items storage (shopping cart, wishlist etc.)
     *
     * @param   int $itemId
     * @param   string $from
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function removeItem($itemId, $from)
    {
        switch ($from) {
            case 'quote':
                $this->removeQuoteItem($itemId);
                break;
            case 'cart':
                if ($cart = $this->getCustomerCart()) {
                    $cart->removeItem($itemId);
                    $cart->collectTotals()
                        ->save();
                }
                break;
            case 'wishlist':
                if ($wishlist = $this->getCustomerWishlist()) {
                    $item = Mage::getModel('wishlist/item')->load($itemId);
                    $item->delete();
                }
                break;
            case 'compared':
                $item = Mage::getModel('catalog/product_compare_item')
                    ->load($itemId)
                    ->delete();
                break;
        }
        return $this;
    }

    /**
     * Remove quote item
     *
     * @param   int $item
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function removeQuoteItem($item)
    {
        $this->getQuote()->removeItem($item);
        $this->setRecollect(true);
        return $this;
    }

    /**
     * Add product to current order quote
     *
     * @param   mixed $product
     * @param   mixed $qty
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function addProduct($product, $qty=1)
    {
        $qty = (float)$qty;
        if (!($product instanceof Mage_Catalog_Model_Product)) {
            $productId = $product;
            $product = Mage::getModel('catalog/product')
                ->setStore($this->getSession()->getStore())
                ->setStoreId($this->getSession()->getStoreId())
                ->load($product);
            if (!$product->getId()) {
                Mage::throwException(Mage::helper('adminhtml')->__('Failed to add a product to cart by id "%s".', $productId));
            }
        }

        if($product->getStockItem()) {
            if (!$product->getStockItem()->getIsQtyDecimal()) {
                $qty = (int)$qty;
            }
            else {
                $product->setIsQtyDecimal(1);
            }
        }
        $qty = $qty > 0 ? $qty : 1;
        if ($item = $this->getQuote()->getItemByProduct($product)) {
            $item->setQty($item->getQty()+$qty);
        }
        else {
            $product->setSkipCheckRequiredOption(true);
            $item = $this->getQuote()->addProduct($product, $qty);
            $product->unsSkipCheckRequiredOption();
            $item->checkData();
        }

        $this->setRecollect(true);
        return $this;
    }

    /**
     * Add multiple products to current order quote
     *
     * @param   array $products
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function addProducts(array $products)
    {
        foreach ($products as $productId => $data) {
            $qty = isset($data['qty']) ? (float)$data['qty'] : 1;
            try {
                $this->addProduct($productId, $qty);
            }
            catch (Mage_Core_Exception $e){
                $this->getSession()->addError($e->getMessage());
            }
            catch (Exception $e){
                return $e;
            }
        }
        return $this;
    }

    /**
     * Update quantity of order quote items
     *
     * @param   array $data
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function updateQuoteItems($data)
    {
        if (is_array($data)) {
            foreach ($data as $itemId => $info) {
                $item       = $this->getQuote()->getItemById($itemId);
                $itemQty    = (float)$info['qty'];
                if ($item && $item->getProduct()->getStockItem()) {
                    if (!$item->getProduct()->getStockItem()->getIsQtyDecimal()) {
                        $itemQty = (int)$info['qty'];
                    }
                    else {
                        $item->setIsQtyDecimal(1);
                    }
                }
                $itemQty    = $itemQty > 0 ? $itemQty : 1;
                if (isset($info['custom_price'])) {
                    $itemPrice  = $this->_parseCustomPrice($info['custom_price']);
                }
                else {
                    $itemPrice = null;
                }
                $noDiscount = !isset($info['use_discount']);

//                if ($item = $this->getQuote()->getItemById($itemId)) {
//                    $this->_assignOptionsToItem(
//                        $item,
//                        $this->_parseOptions($item, $info['options'])
//                    );
//                    if (empty($info['action'])) {
//                        $item->setQty($itemQty);
//                        $item->setCustomPrice($itemPrice);
//                        $item->setNoDiscount($noDiscount);
//                    }
//                    else {
//                        $this->moveQuoteItem($item, $info['action'], $itemQty);
//                    }
//                }

                if (empty($info['action'])) {
                    if ($item) {
                        $item->setQty($itemQty);
                        $item->setCustomPrice($itemPrice);
                        $item->setOriginalCustomPrice($itemPrice);
                        $item->setNoDiscount($noDiscount);
                        $item->getProduct()->setIsSuperMode(true);

                        $this->_assignOptionsToItem(
                            $item,
                            $this->_parseOptions($item, $info['options'])
                        );
                        $item->checkData();
                    }
                }
                else {
                    $this->moveQuoteItem($itemId, $info['action'], $itemQty);
                }
            }
            if ($this->_needCollectCart === true) {
                $this->getCustomerCart()
                    ->collectTotals()
                    ->save();
            }
            $this->setRecollect(true);
        }
        return $this;
    }

    /**
     * Parse additional options and sync them with product options
     *
     * @param Mage_Sales_Model_Quote_Item $product
     * @param array $options
     */
    protected function _parseOptions(Mage_Sales_Model_Quote_Item $item, $additionalOptions)
    {
        $productOptions = Mage::getSingleton('catalog/product_option_type_default')
            ->setProduct($item->getProduct())
            ->getProductOptions();

        $newOptions = array();
        $newAdditionalOptions = array();

        foreach (explode("\n", $additionalOptions) as $_additionalOption) {
            if (strlen(trim($_additionalOption))) {
                try {
                    if (strpos($_additionalOption, ':') === false) {
                        Mage::throwException(Mage::helper('adminhtml')->__('There is an error in one of the option rows.'));
                    }
                    list($label,$value) = explode(':', $_additionalOption, 2);
                } catch (Exception $e) {
                    Mage::throwException(Mage::helper('adminhtml')->__('There is an error in one of the option rows.'));
                }
                $label = trim($label);
                $value = trim($value);
                if (empty($value)) {
                    continue;
                }

                if (array_key_exists($label, $productOptions)) {
                    $optionId = $productOptions[$label]['option_id'];
                    $option = $item->getProduct()->getOptionById($optionId);

                    $group = Mage::getSingleton('catalog/product_option')->groupFactory($option->getType())
                        ->setOption($option)
                        ->setProduct($item->getProduct());

                    $parsedValue = $group->parseOptionValue($value, $productOptions[$label]['values']);

                    if ($parsedValue !== null) {
                        $newOptions[$optionId] = $parsedValue;
                    } else {
                        $newAdditionalOptions[] = array(
                            'label' => $label,
                            'value' => $value
                        );
                    }
                } else {
                    $newAdditionalOptions[] = array(
                        'label' => $label,
                        'value' => $value
                    );
                }
            }
        }

        return array(
            'options' => $newOptions,
            'additional_options' => $newAdditionalOptions
        );
    }

    /**
     * Assign options to item
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @param array $options
     */
    protected function _assignOptionsToItem(Mage_Sales_Model_Quote_Item $item, $options)
    {
        if ($optionIds = $item->getOptionByCode('option_ids')) {
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                $item->removeOption('option_'.$optionId);
            }
            $item->removeOption('option_ids');
        }
        if ($item->getOptionByCode('additional_options')) {
            $item->removeOption('additional_options');
        }
        $item->save();
        if (!empty($options['options'])) {
            $item->addOption(new Varien_Object(
                array(
                    'product' => $item->getProduct(),
                    'code' => 'option_ids',
                    'value' => implode(',', array_keys($options['options']))
                )
            ));

            foreach ($options['options'] as $optionId => $optionValue) {
                $item->addOption(new Varien_Object(
                    array(
                        'product' => $item->getProduct(),
                        'code' => 'option_'.$optionId,
                        'value' => $optionValue
                    )
                ));
            }
        }
        if (!empty($options['additional_options'])) {
            $item->addOption(new Varien_Object(
                array(
                    'product' => $item->getProduct(),
                    'code' => 'additional_options',
                    'value' => serialize($options['additional_options'])
                )
            ));
        }

        return $this;
    }

    /**
     * Prepare options array for info buy request
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return array
     */
    protected function _prepareOptionsForRequest($item)
    {
        $newInfoOptions = array();
        if ($optionIds = $item->getOptionByCode('option_ids')) {
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                $option = $item->getProduct()->getOptionById($optionId);
                $optionValue = $item->getOptionByCode('option_'.$optionId)->getValue();

                $group = Mage::getSingleton('catalog/product_option')->groupFactory($option->getType())
                    ->setOption($option)
                    ->setQuoteItem($item);

                $newInfoOptions[$optionId] = $group->prepareOptionValueForRequest($optionValue);
            }
        }
        return $newInfoOptions;
    }

    protected function _parseCustomPrice($price)
    {
        $price = Mage::app()->getLocale()->getNumber($price);
        $price = $price>0 ? $price : 0;
        return $price;
    }

    /**
     * Retrieve oreder quote shipping address
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getShippingAddress()
    {
        return $this->getQuote()->getShippingAddress();
    }

    /**
     * Return Customer (Checkout) Form instance
     *
     * @return Mage_Customer_Model_Form
     */
    protected function _getCustomerForm()
    {
        if (is_null($this->_customerForm)) {
            $this->_customerForm = Mage::getModel('customer/form')
                ->setFormCode('adminhtml_checkout')
                ->ignoreInvisible(false);
        }
        return $this->_customerForm;
    }

    /**
     * Return Customer Address Form instance
     *
     * @return Mage_Customer_Model_Form
     */
    protected function _getCustomerAddressForm()
    {
        if (is_null($this->_customerAddressForm)) {
            $this->_customerAddressForm = Mage::getModel('customer/form')
                ->setFormCode('adminhtml_customer_address')
                ->ignoreInvisible(false);
        }
        return $this->_customerAddressForm;
    }

    /**
     * Set and validate Quote address
     * All errors added to _errors
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @param array $data
     * @return Mage_Adminhtml_Model_Sales_Order_Create
     */
    protected function _setQuoteAddress(Mage_Sales_Model_Quote_Address $address, array $data)
    {
        $addressForm    = $this->_getCustomerAddressForm()
            ->setEntity($address)
            ->setEntityType(Mage::getSingleton('eav/config')->getEntityType('customer_address'))
            ->setIsAjaxRequest(!$this->getIsValidate());

        // prepare request
        // save original request structure for files
        if ($address->getAddressType() == Mage_Sales_Model_Quote_Address::TYPE_SHIPPING) {
            $requestData  = array('order' => array('shipping_address' => $data));
            $requestScope = 'order/shipping_address';
        } else {
            $requestData = array('order' => array('billing_address' => $data));
            $requestScope = 'order/billing_address';
        }
        $request        = $addressForm->prepareRequest($requestData);
        $addressData    = $addressForm->extractData($request, $requestScope);
        if ($this->getIsValidate()) {
            $errors = $addressForm->validateData($addressData);
            if ($errors !== true) {
                if ($address->getAddressType() == Mage_Sales_Model_Quote_Address::TYPE_SHIPPING) {
                    $typeName = Mage::helper('adminhtml')->__('Shipping Address: ');
                } else {
                    $typeName = Mage::helper('adminhtml')->__('Billing Address: ');
                }
                foreach ($errors as $error) {
                    $this->_errors[] = $typeName . $error;
                }
                $addressForm->restoreData($addressData);
            } else {
                $addressForm->compactData($addressData);
            }
        } else {
            $addressForm->restoreData($addressData);
        }

        return $this;
    }

    public function setShippingAddress($address)
    {
        if (is_array($address)) {
            $address['save_in_address_book'] = isset($address['save_in_address_book']) ? (empty($address['save_in_address_book']) ? 0 : 1) : 0;
            $shippingAddress = Mage::getModel('sales/quote_address')
                ->setData($address)
                ->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING);
            if (!$this->getQuote()->isVirtual()) {
                $this->_setQuoteAddress($shippingAddress, $address);
            }
            $shippingAddress->implodeStreetAddress();
        }
        if ($address instanceof Mage_Sales_Model_Quote_Address) {
            $shippingAddress = $address;
        }

        $this->setRecollect(true);
        $this->getQuote()->setShippingAddress($shippingAddress);
        return $this;
    }

    public function setShippingAsBilling($flag)
    {
        if ($flag) {
            $tmpAddress = clone $this->getBillingAddress();
            $tmpAddress->unsAddressId()
                ->unsAddressType();
            $this->getShippingAddress()->addData($tmpAddress->getData());
        }
        $this->getShippingAddress()->setSameAsBilling($flag);
        $this->setRecollect(true);
        return $this;
    }

    /**
     * Retrieve quote billing address
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getBillingAddress()
    {
        return $this->getQuote()->getBillingAddress();
    }

    public function setBillingAddress($address)
    {
        if (is_array($address)) {
            $address['save_in_address_book'] = isset($address['save_in_address_book']) ? 1 : 0;
            $billingAddress = Mage::getModel('sales/quote_address')
                ->setData($address)
                ->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_BILLING);
            $this->_setQuoteAddress($billingAddress, $address);
            $billingAddress->implodeStreetAddress();
        }

        if ($this->getShippingAddress()->getSameAsBilling()) {
            $shippingAddress = clone $billingAddress;
            $shippingAddress->setSameAsBilling(true);
            $shippingAddress->setSaveInAddressBook(false);
            $address['save_in_address_book'] = 0;
            $this->setShippingAddress($address);
        }

        $this->getQuote()->setBillingAddress($billingAddress);
        return $this;
    }

    public function setShippingMethod($method)
    {
        $this->getShippingAddress()->setShippingMethod($method);
        $this->setRecollect(true);
        return $this;
    }

    public function resetShippingMethod()
    {
        $this->getShippingAddress()->setShippingMethod(false);
        $this->getShippingAddress()->removeAllShippingRates();
        return $this;
    }

    /**
     * Collect shipping data for quote shipping address
     */
    public function collectShippingRates()
    {
        $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
        $this->collectRates();
        return $this;
    }

    public function collectRates()
    {
        $this->getQuote()->collectTotals();
    }

    public function setPaymentMethod($method)
    {
        $this->getQuote()->getPayment()->setMethod($method);
        return $this;
    }

    public function setPaymentData($data)
    {
        if (!isset($data['method'])) {
            $data['method'] = $this->getQuote()->getPayment()->getMethod();
        }
        $this->getQuote()->getPayment()->importData($data);
        return $this;
    }

    public function applyCoupon($code)
    {
        $code = trim((string)$code);
        $this->getQuote()->setCouponCode($code);
        $this->setRecollect(true);
        return $this;
    }

    public function setAccountData($accountData)
    {
        $customer   = $this->getQuote()->getCustomer();
        $form       = $this->_getCustomerForm();
        $form->setEntity($customer);

        // emulate request
        $request = $form->prepareRequest($accountData);
        $data    = $form->extractData($request);
        $form->restoreData($data);

        $data = array();
        foreach ($form->getAttributes() as $attribute) {
            $code = sprintf('customer_%s', $attribute->getAttributeCode());
            $data[$code] = $customer->getData($attribute->getAttributeCode());
        }

        if (isset($data['customer_group_id'])) {
            $groupModel = Mage::getModel('customer/group')->load($data['customer_group_id']);
            $data['customer_tax_class_id'] = $groupModel->getTaxClassId();
            $this->setRecollect(true);
        }

        $this->getQuote()->addData($data);
        return $this;
    }

    /**
     * Parse data retrieved from request
     *
     * @param   array $data
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function importPostData($data)
    {
        if (is_array($data)) {
            $this->addData($data);
        }
        else {
            return $this;
        }

        if (isset($data['account'])) {
            $this->setAccountData($data['account']);
        }

        if (isset($data['comment'])) {
            $this->getQuote()->addData($data['comment']);
            if (empty($data['comment']['customer_note_notify'])) {
                $this->getQuote()->setCustomerNoteNotify(false);
            } else {
                $this->getQuote()->setCustomerNoteNotify(true);
            }
        }

        if (isset($data['billing_address'])) {
            $this->setBillingAddress($data['billing_address']);
        }

        if (isset($data['shipping_address'])) {
            $this->setShippingAddress($data['shipping_address']);
        }

        if (isset($data['shipping_method'])) {
            $this->setShippingMethod($data['shipping_method']);
        }

        if (isset($data['payment_method'])) {
            $this->setPaymentMethod($data['payment_method']);
        }

        if (isset($data['coupon']['code'])) {
            $this->applyCoupon($data['coupon']['code']);
        }
        return $this;
    }

    /**
     * Check whether we need to create new customer (for another website) during order creation
     *
     * @param   Mage_Core_Model_Store $store
     * @return  boolean
     */
    protected function _customerIsInStore($store)
    {
        $customer = $this->getSession()->getCustomer();
        if ($customer->getWebsiteId() == $store->getWebsiteId()) {
            return true;
        }
        return $customer->isInStore($store);
    }

    /**
     * Set and validate Customer data
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Adminhtml_Model_Sales_Order_Create
     */
    protected function _setCustomerData(Mage_Customer_Model_Customer $customer)
    {
        $form = $this->_getCustomerForm();
        $form->setEntity($customer);

        // emulate request
        $request = $form->prepareRequest(array('order' => $this->getData()));
        $data    = $form->extractData($request, 'order/account');
        if ($this->getIsValidate()) {
            $errors = $form->validateData($data);
            if ($errors !== true) {
                foreach ($errors as $error) {
                    $this->_errors[] = $error;
                }
                $form->restoreData($data);
            } else {
                $form->compactData($data);
            }
        } else {
            $form->restoreData($data);
        }

        return $this;
    }

    /**
     * Prepare quote customer
     */
    public function _prepareCustomer()
    {
        $quote = $this->getQuote();
        if ($quote->getCustomerIsGuest()) {
            return $this;
        }

        $customer        = $this->getSession()->getCustomer();
        $store           = $this->getSession()->getStore();
        $billingAddress  = null;
        $shippingAddress = null;

        if ($customer->getId()) {
            if (!$this->_customerIsInStore($store)) {
                $customer->setId(null)
                    ->setStore($store)
                    ->setDefaultBilling(null)
                    ->setDefaultShipping(null)
                    ->setPassword($customer->generatePassword());
                $this->_setCustomerData($customer);
            }
            if ($this->getBillingAddress()->getSaveInAddressBook()) {
                $billingAddress = $this->getBillingAddress()->exportCustomerAddress();
                $customerAddressId = $this->getBillingAddress()->getCustomerAddressId();
                if ($customerAddressId && $customer->getId()) {
                    $customer->getAddressItemById($customerAddressId)->addData($billingAddress->getData());
                } else {
                    $customer->addAddress($billingAddress);
                }
            }
            if (!$this->getQuote()->isVirtual() && $this->getShippingAddress()->getSaveInAddressBook()) {
                $shippingAddress = $this->getShippingAddress()->exportCustomerAddress();
                $customerAddressId = $this->getShippingAddress()->getCustomerAddressId();
                if ($customerAddressId && $customer->getId()) {
                    $customer->getAddressItemById($customerAddressId)->addData($shippingAddress->getData());
                } elseif ($billingAddress !== null && $this->getBillingAddress()->getCustomerAddressId() == $customerAddressId) {
                    $billingAddress->setIsDefaultShipping(true);
                } else {
                    $customer->addAddress($shippingAddress);
                }
            }

            if (is_null($customer->getDefaultBilling()) && $billingAddress) {
                $billingAddress->setIsDefaultBilling(true);
            }
            if (is_null($customer->getDefaultShipping())) {
                if ($this->getShippingAddress()->getSameAsBilling() && $billingAddress) {
                    $billingAddress->setIsDefaultShipping(true);
                } elseif ($shippingAddress) {
                    $shippingAddress->setIsDefaultShipping(true);
                }
            }
        } else {
            $customer->addData($this->getBillingAddress()->exportCustomerAddress()->getData())
                ->setPassword($customer->generatePassword())
                ->setStore($store);
            $customer->setEmail($this->_getNewCustomerEmail($customer));
            $this->_setCustomerData($customer);

            $customerBilling = $this->getBillingAddress()->exportCustomerAddress();
            $customerBilling->setIsDefaultBilling(true);
            $customer->addAddress($customerBilling);

            $shipping = $this->getShippingAddress();
            if (!$this->getQuote()->isVirtual() && !$shipping->getSameAsBilling()) {
                $customerShipping = $shipping->exportCustomerAddress();
                $customerShipping->setIsDefaultShipping(true);
                $customer->addAddress($customerShipping);
            } else {
                $customerBilling->setIsDefaultShipping(true);
            }
        }

        // set quote customer data to customer
        $this->_setCustomerData($customer);

        // set customer to quote and convert customer data to quote
        $quote->setCustomer($customer);

        // add user defined attributes to quote
        $form = $this->_getCustomerForm()->setEntity($customer);
        foreach ($form->getUserAttributes() as $attribute) {
            $quoteCode = sprintf('customer_%s', $attribute->getAttributeCode());
            $quote->setData($quoteCode, $customer->getData($attribute->getAttributeCode()));
        }

        if ($customer->getId()) {
            // we should not change account data for existing customer, so restore it
            $this->_getCustomerForm()
                ->setEntity($customer)
                ->resetEntityData();
        } else {
            $quote->setCustomerId(true);
        }

        return $this;
    }

    /**
     * Prepare item otions
     */
    protected function _prepareQuoteItems()
    {
        foreach ($this->getQuote()->getAllItems() as $item) {
            $options = array();
            $productOptions = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
            if ($productOptions) {
                $productOptions['info_buyRequest']['options'] = $this->_prepareOptionsForRequest($item);
                $options = $productOptions;
            }
            $addOptions = $item->getOptionByCode('additional_options');
            if ($addOptions) {
                $options['additional_options'] = unserialize($addOptions->getValue());
            }
            $item->setProductOrderOptions($options);
        }
        return $this;
    }

    /**
     * Create new order
     *
     * @return Mage_Sales_Model_Order
     */
    public function createOrder()
    {
        $this->_prepareCustomer();
        $this->_validate();
        $quote = $this->getQuote();
        $this->_prepareQuoteItems();

        if (! $quote->getCustomer()->getId() || ! $quote->getCustomer()->isInStore($this->getSession()->getStore())) {
            $quote->getCustomer()->sendNewAccountEmail('registered', '', $quote->getStoreId());
        }
        $service = Mage::getModel('sales/service_quote', $quote);
        if ($this->getSession()->getOrder()->getId()) {
            $oldOrder = $this->getSession()->getOrder();
            $originalId = $oldOrder->getOriginalIncrementId() ? $oldOrder->getOriginalIncrementId() : $oldOrder->getIncrementId();
            $orderData = array(
                'original_increment_id'     => $originalId,
                'relation_parent_id'        => $oldOrder->getId(),
                'relation_parent_real_id'   => $oldOrder->getIncrementId(),
                'edit_increment'            => $oldOrder->getEditIncrement()+1,
                'increment_id'              => $originalId.'-'.($oldOrder->getEditIncrement()+1)
            );
            $quote->setReservedOrderId($orderData['increment_id']);
            $service->setOrderData($orderData);
        }

        $order = $service->submit();
        if (!$quote->getCustomer()->getId() || !$quote->getCustomer()->isInStore($this->getSession()->getStore())) {
            $quote->getCustomer()->setCreatedAt($order->getCreatedAt());
            $quote->getCustomer()->save();
        }
        if ($this->getSession()->getOrder()->getId()) {
            $oldOrder = $this->getSession()->getOrder();

            $this->getSession()->getOrder()->setRelationChildId($order->getId());
            $this->getSession()->getOrder()->setRelationChildRealId($order->getIncrementId());
            $this->getSession()->getOrder()->cancel()
                ->save();
            $order->save();
        }
        if ($this->getSendConfirmation()) {
            $order->sendNewOrderEmail();
        }
        Mage::dispatchEvent('checkout_submit_all_after', array('order' => $order, 'quote' => $quote));

        return $order;
    }

    /**
     * Validate quote data before order creation
     *
     * @return Mage_Adminhtml_Model_Sales_Order_Create
     */
    protected function _validate()
    {
        $customerId = $this->getSession()->getCustomerId();
        if (is_null($customerId)) {
            Mage::throwException(Mage::helper('adminhtml')->__('Please select a customer.'));
        }

        if (!$this->getSession()->getStore()->getId()) {
            Mage::throwException(Mage::helper('adminhtml')->__('Please select a store.'));
        }
        $items = $this->getQuote()->getAllItems();

        if (count($items) == 0) {
            $this->_errors[] = Mage::helper('adminhtml')->__('You need to specify order items.');
        }

        if (!$this->getQuote()->isVirtual()) {
            if (!$this->getQuote()->getShippingAddress()->getShippingMethod()) {
                $this->_errors[] = Mage::helper('adminhtml')->__('Shipping method must be specified.');
            }
        }

        if (!$this->getQuote()->getPayment()->getMethod()) {
            $this->_errors[] = Mage::helper('adminhtml')->__('Payment method must be specified.');
        } else {
            $method = $this->getQuote()->getPayment()->getMethodInstance();
            if (!$method) {
                $this->_errors[] = Mage::helper('adminhtml')->__('Payment method instance is not available.');
            } else {
                if (!$method->isAvailable($this->getQuote())) {
                    $this->_errors[] = Mage::helper('adminhtml')->__('Payment method is not available.');
                } else {
                    try {
                        $method->validate();
                    } catch (Mage_Core_Exception $e) {
                        $this->_errors[] = $e->getMessage();
                    }
                }
            }
        }

        if (!empty($this->_errors)) {
            foreach ($this->_errors as $error) {
                $this->getSession()->addError($error);
            }
            Mage::throwException('');
        }
        return $this;
    }

    /**
     * Retrieve new customer email
     *
     * @param   Mage_Customer_Model_Customer $customer
     * @return  string
     */
    protected function _getNewCustomerEmail($customer)
    {
        $email = $this->getData('account/email');
        if (empty($email)) {
            $host = $this->getSession()->getStore()->getConfig(Mage_Customer_Model_Customer::XML_PATH_DEFAULT_EMAIL_DOMAIN);
            $account = $customer->getIncrementId() ? $customer->getIncrementId() : time();
            $email = $account.'@'. $host;
            $account = $this->getData('account');
            $account['email'] = $email;
            $this->setData('account', $account);
        }
        return $email;
    }

    /**
     * Create customer model and assign it to quote
     * @deprecated after 1.4.0.0.
     */
    protected function _putCustomerIntoQuote()
    {
        if (!$this->getSession()->getCustomer()->getId()) {
            /** @var Mage_Customer_Model_Customer*/
            $customer = Mage::getModel('customer/customer');

            $customer->addData($this->getBillingAddress()->exportCustomerAddress()->getData())
                     ->addData($this->getData('account'))
                     ->setPassword($customer->generatePassword())
                     ->setWebsiteId($this->getSession()->getStore()->getWebsiteId())
                     ->setStoreId($this->getSession()->getStore()->getId())
                     ->setEmail($this->_getNewCustomerEmail($customer));
        } elseif (($customer = $this->getSession()->getCustomer())
                && $customer->getId()
                && !$this->getSession()->getCustomer(true,true)->getId()) {
            $customer = clone $customer;
            $customer->setStore($this->getSession()->getStore())
                ->save();
            $this->getSession()->setCustomer($customer);
            $customer->addData($this->getData('account'));
        } else {
            $customer = $this->getSession()->getCustomer();
            $customer->addData($this->getData('account'));
        }
        $this->getQuote()->setCustomer($customer);
        $this->_customer = $customer;
    }

    /**
     * Save customer
     *
     * @deprecated after 1.4.0.0.
     * @param Mage_Customer_Model_Customer $order
     */
    protected function _saveCustomerAfterOrder($order)
    {
        if ($this->_customer) {
            if (! $this->_customer->getId()) {
                $billing          = $this->getBillingAddress();
                $customerBilling  = $billing->exportCustomerAddress();
                $shipping         = $this->getShippingAddress();
                $customerShipping = $shipping->exportCustomerAddress();

                $this->_customer->addAddress($customerBilling);

                if (! $shipping->getSameAsBilling()) {
                    $this->_customer->addAddress($customerShipping);
                }
                // preliminary save to find addresses id
                $this->_customer->save();
                // setting default addresses id
                $this->_customer->setDefaultBilling($customerBilling->getId())
                                ->setDefaultShipping($shipping->getSameAsBilling() ? $customerBilling->getId() : $customerShipping->getId())
                                ->save();

                $order->setCustomerId($this->_customer->getId());
                $billing->setCustomerId($this->_customer->getId());
                $shipping->setCustomerId($this->_customer->getId());
                $this->_customer->sendNewAccountEmail();
            } else {
                $saveCusstomerAddress = false;

                if ($this->getBillingAddress()->getSaveInAddressBook()) {
                    $billingAddress = $this->getBillingAddress()->exportCustomerAddress();
                    if ($this->getBillingAddress()->getCustomerAddressId()) {
                        $billingAddress->setId($this->getBillingAddress()->getCustomerAddressId());
                    }
                    $this->_customer->addAddress($billingAddress);
                    $saveCusstomerAddress = true;
                }
                if ($this->getShippingAddress()->getSaveInAddressBook()) {
                    $shippingAddress = $this->getShippingAddress()->exportCustomerAddress();
                    if ($this->getShippingAddress()->getCustomerAddressId()) {
                        $shippingAddress->setId($this->getShippingAddress()->getCustomerAddressId());
                    }
                    $this->_customer->addAddress($shippingAddress);
                    $saveCusstomerAddress = true;
                }
                if ($saveCusstomerAddress) {
                    $this->_customer->save();
                }
            }
        }
    }

    /**
     * @deprecated after 1.1.7
     * @return unknown
     */
    protected function _saveCustomer()
    {
        if (!$this->getSession()->getCustomer()->getId()) {
            $customer = Mage::getModel('customer/customer');
            /* @var $customer Mage_Customer_Model_Customer*/

            $billingAddress = $this->getBillingAddress()->exportCustomerAddress();

            $customer->addData($billingAddress->getData())
                ->addData($this->getData('account'))
                ->setPassword($customer->generatePassword())
                ->setWebsiteId($this->getSession()->getStore()->getWebsiteId())
                ->setStoreId($this->getSession()->getStore()->getId())
                ->addAddress($billingAddress);

            if (!$this->getShippingAddress()->getSameAsBilling()) {
                $shippingAddress = $this->getShippingAddress()->exportCustomerAddress();
                $customer->addAddress($shippingAddress);
            }
            else {
                $shippingAddress = $billingAddress;
            }
            $customer->save();


            $customer->setEmail($this->_getNewCustomerEmail($customer))
                ->setDefaultBilling($billingAddress->getId())
                ->setDefaultShipping($shippingAddress->getId())
                ->save();

            $this->getBillingAddress()->setCustomerId($customer->getId());
            $this->getShippingAddress()->setCustomerId($customer->getId());

            $customer->sendNewAccountEmail();
        }
        else {
            $customer = $this->getSession()->getCustomer();

            $saveCusstomerAddress = false;

            if ($this->getBillingAddress()->getSaveInAddressBook()) {
                $billingAddress = $this->getBillingAddress()->exportCustomerAddress();
                if ($this->getBillingAddress()->getCustomerAddressId()) {
                    $billingAddress->setId($this->getBillingAddress()->getCustomerAddressId());
                }
                $customer->addAddress($billingAddress);
                $saveCusstomerAddress = true;
            }
            if ($this->getShippingAddress()->getSaveInAddressBook()) {
                $shippingAddress = $this->getShippingAddress()->exportCustomerAddress();
                if ($this->getShippingAddress()->getCustomerAddressId()) {
                    $shippingAddress->setId($this->getShippingAddress()->getCustomerAddressId());
                }
                $customer->addAddress($shippingAddress);
                $saveCusstomerAddress = true;
            }
            if ($saveCusstomerAddress) {
                $customer->save();
            }

            $customer->addData($this->getData('account'));
            /**
             * don't save account information, use it only for order creation
             */
            //$customer->save();
        }
        $this->getQuote()->setCustomer($customer);
        return $this;
    }
}
