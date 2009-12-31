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
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shoping cart model
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Model_Cart extends Varien_Object
{
    protected $_summaryQty = null;
    protected $_productIds = null;

    protected function _getResource()
    {
        return Mage::getResourceSingleton('checkout/cart');
    }

    /**
     * Retrieve checkout session model
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Retrieve custome session model
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function getItems()
    {
        if (!$this->getQuote()->getId()) {
            return array();
        }
        return $this->getQuote()->getItemsCollection();
    }

    /**
     * Retrieve array of cart product ids
     *
     * @return array
     */
    public function getQuoteProductIds()
    {
        $products = $this->getData('product_ids');
        if (is_null($products)) {
            $products = array();
            foreach ($this->getQuote()->getAllItems() as $item) {
                $products[$item->getProductId()] = $item->getProductId();
            }
            $this->setData('product_ids', $products);
        }
        return $products;
    }


    /**
     * Retrieve current quote object
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckoutSession()->getQuote();
    }

    public function init()
    {
        $this->getQuote()->setCheckoutMethod('');

        /**
         * If user try do checkout, reset shipiing and payment data
         */
        if ($this->getCheckoutSession()->getCheckoutState() !== Mage_Checkout_Model_Session::CHECKOUT_STATE_BEGIN) {
            $this->getQuote()
                ->removeAllAddresses()
                ->removePayment();
            $this->getCheckoutSession()->resetCheckout();
        }

        if (!$this->getQuote()->hasItems()) {
            $this->getQuote()->getShippingAddress()
                ->setCollectShippingRates(false)
                ->removeAllShippingRates();
        }

        return $this;
    }

    /**
     * Convert order item to quote item
     *
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @param mixed $qtyFlag if is null set product qty like in order
     * @return Mage_Checkout_Model_Cart
     */
    public function addOrderItem($orderItem, $qtyFlag=null)
    {
        /* @var $orderItem Mage_Sales_Model_Order_Item */
        if (is_null($orderItem->getParentItem())) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($orderItem->getProductId());
            if (!$product->getId()) {
                return $this;
            }

            $info = $orderItem->getProductOptionByCode('info_buyRequest');
            $info = new Varien_Object($info);
            if (is_null($qtyFlag)) {
                $info->setQty($orderItem->getQtyOrdered());
            } else {
                $info->setQty(1);
            }

            $this->addProduct($product, $info);
        }
        return $this;
    }

    /**
     * Get product for product information
     *
     * @param   mixed $productInfo
     * @return  Mage_Catalog_Model_Product
     */
    protected function _getProduct($productInfo)
    {
        if ($productInfo instanceof Mage_Catalog_Model_Product) {
            $product = $productInfo;
        }
        elseif (is_int($productInfo)) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productInfo);
        }
        else {

        }
        return $product;
    }

    /**
     * Get request for product add to cart procedure
     *
     * @param   mixed $requestInfo
     * @return  Varien_Object
     */
    protected function _getProductRequest($requestInfo)
    {
        if ($requestInfo instanceof Varien_Object) {
            $request = $requestInfo;
        }
        elseif (is_numeric($requestInfo)) {
            $request = new Varien_Object();
            $request->setQty($requestInfo);
        }
        else {
            $request = new Varien_Object($requestInfo);
        }

        if (!$request->hasQty()) {
            $request->setQty(1);
        }
        return $request;
    }

    /**
     * Add product to shopping cart (quote)
     *
     * @param   int $productId
     * @param   int $qty
     * @return  Mage_Checkout_Model_Cart
     */
    public function addProduct($product, $info=null)
    {
        $product = $this->_getProduct($product);
        $request = $this->_getProductRequest($info);

        //Check if current product already exists in cart
        $productId = $product->getId();
        $items = $this->getQuote()->getAllItems();
        $quoteProduct = null;
        foreach ($items as $item) {
            if ($item->getProductId() == $productId) {
                $quoteProduct = $item;
                break;
            }
        }

        if ($product->getStockItem()) {
            $minimumQty = $product->getStockItem()->getMinSaleQty();
            //If product was not found in cart and there is set minimal qty for it
            if($minimumQty > 0 && $request->getQty() < $minimumQty && $quoteProduct === null){
                $request->setQty($minimumQty);
            }
        }

        if ($product->getId()) {

            $result = $this->getQuote()->addProduct($product, $request);

            /**
             * String we can get if prepare process has error
             */
            if (is_string($result)) {

                $this->getCheckoutSession()->setRedirectUrl($product->getProductUrl());
                if ($this->getCheckoutSession()->getUseNotice() === null) {
                    $this->getCheckoutSession()->setUseNotice(true);
                }
                Mage::throwException($result);
            }
        }
        else {
            Mage::throwException(Mage::helper('checkout')->__('Product does not exist'));
        }

        Mage::dispatchEvent('checkout_cart_product_add_after', array('quote_item'=>$result, 'product'=>$product));
        $this->getCheckoutSession()->setLastAddedProductId($product->getId());
        return $this;
    }

    /**
     * Adding products to cart by ids
     *
     * @param   array $productIds
     * @return  Mage_Checkout_Model_Cart
     */
    public function addProductsByIds($productIds)
    {
        $allAvailable = true;
        $allAdded     = true;

        if (!empty($productIds)) {
            foreach ($productIds as $productId) {
                $productId = (int) $productId;
                if (!$productId) {
                    continue;
                }
                $product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($productId);
                if ($product->getId() && $product->isVisibleInCatalog()) {
                    try {
                        $this->getQuote()->addProduct($product);
                    }
                    catch (Exception $e){
                        $allAdded = false;
                    }
                }
                else {
                    $allAvailable = false;
                }
            }

            if (!$allAvailable) {
                $this->getCheckoutSession()->addError(
                    Mage::helper('checkout')->__('Some of the products you requested are unavailable')
                );
            }
            if (!$allAdded) {
                $this->getCheckoutSession()->addError(
                    Mage::helper('checkout')->__('Some of the products you requested are not available in the desired quantity')
                );
            }
        }
        return $this;
    }

    /**
     * Update cart items information
     *
     * @param   array $data
     * @return  Mage_Checkout_Model_Cart
     */
    public function updateItems($data)
    {
        Mage::dispatchEvent('checkout_cart_update_items_before', array('cart'=>$this, 'info'=>$data));

        foreach ($data as $itemId => $itemInfo) {
            $item = $this->getQuote()->getItemById($itemId);
            if (!$item) {
                continue;
            }

            if (!empty($itemInfo['remove']) || (isset($itemInfo['qty']) && $itemInfo['qty']=='0')) {
                $this->removeItem($itemId);
                continue;
            }

            $qty = isset($itemInfo['qty']) ? (float) $itemInfo['qty'] : false;
            if ($qty > 0) {
                $item->setQty($qty);
            }
        }

        Mage::dispatchEvent('checkout_cart_update_items_after', array('cart'=>$this, 'info'=>$data));
        return $this;
    }

    /**
     * Remove item from cart
     *
     * @param   int $itemId
     * @return  Mage_Checkout_Model_Cart
     */
    public function removeItem($itemId)
    {
        $this->getQuote()->removeItem($itemId);
        return $this;
    }

    /**
     * Save cart
     *
     * @return Mage_Checkout_Model_Cart
     */
    public function save()
    {
        $this->getQuote()->getBillingAddress();
        $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
        $this->getQuote()->collectTotals();
        $this->getQuote()->save();
        $this->getCheckoutSession()->setQuoteId($this->getQuote()->getId());
        /**
         * Cart save usually called after chenges with cart items.
         */
        Mage::dispatchEvent('checkout_cart_save_after', array('cart'=>$this));
        return $this;
    }

    public function truncate()
    {
        foreach ($this->getQuote()->getItemsCollection() as $item) {
            $item->isDeleted(true);
        }
    }

//    /**
//     * Retrieve cart information for sidebar
//     *
//     * @return Varien_Object
//     */
//    public function getCartInfo()
//    {
//        $store = Mage::app()->getStore();
//        $quoteId = $this->getCheckoutSession()->getQuoteId();
//
////        $cacheKey = 'CHECKOUT_QUOTE'.$quoteId.'_STORE'.$store->getId();
////        if (Mage::app()->useCache('checkout_quote') && $cache = Mage::app()->loadCache($cacheKey)) {
////            return unserialize($cache);
////        }
//
//        $cart = array('items'=>array(), 'subtotal'=>0);
////        $cacheTags = array('checkout_quote', 'catalogrule_product_price', 'checkout_quote_'.$quoteId);
//
//        if ($this->getSummaryQty($quoteId)>0) {
//
//            $itemsArr = $this->_getResource()->fetchItems($quoteId);
//            $productIds = array();
//            foreach ($itemsArr as $item) {
//                $productIds[] = $item['product_id'];
//                if (!empty($item['super_product_id'])) {
//                    $productIds[] = $item['super_product_id'];
//                }
//            }
//
//            $productIds = array_unique($productIds);
//            foreach ($productIds as $id) {
//                $cacheTags[] = 'catalog_product_'.$id;
//            }
//            /* +MK
//            $quoteItems = Mage::getModel('sales/quote_item')
//                ->getCollection()
//                ->setQuote( Mage::getSingleton('checkout/session')->getQuote())
//                ->addAttributeToSelect('*');
//            */
//            $products = Mage::getModel('catalog/product')->getCollection()
//                ->addAttributeToSelect('*')
//                ->addMinimalPrice()
//                ->addStoreFilter()
//                ->addIdFilter($productIds);
//
//
//            foreach ($itemsArr as $it) {
//                $product = $products->getItemById($it['product_id']);
//                if (!$product) {
//                    continue;
//                }
//                $product->setDoNotUseCategoryId(true);
//
//                //-MK:
//                $item = new Varien_Object($it);
//                //+MK $item = $quoteItems->getItemById($it['id']);
//                $item->setProduct($product);
//
//                $superProduct = null;
//                if (!empty($it['super_product_id'])) {
//                    $superProduct = $products->getItemById($it['super_product_id']);
//                    $item->setSuperProduct($superProduct);
//                    $product->setProduct($product);
//                    $product->setSuperProduct($superProduct);
//                    $superProduct->setDoNotUseCategoryId(true);
//                }
//                /* +MK
//                if ($item->getCalculationPrice()) {
//                    $item->setPrice($item->getCalculationPrice());
//                }
//                */
//                $item->setProductName(!empty($superProduct) ? $superProduct->getName() : $product->getName());
//                $item->setProductUrl(!empty($superProduct) ? $superProduct->getProductUrl() : $product->getProductUrl());
//                //-MK:
//                $item->setPrice($product->getFinalPrice($it['qty']));
//
//                $thumbnailObjOrig = Mage::helper('checkout')->getQuoteItemProductThumbnail($item);
//                $thumbnailObj = Mage::getModel('catalog/product');
//                foreach ($thumbnailObjOrig->getData() as $k=>$v) {
//                    if (is_scalar($v)) {
//                        $thumbnailObj->setData($k, $v);
//                    }
//                }
//                $item->setThumbnailObject($thumbnailObj);
//
//                $item->setProductDescription(Mage::helper('catalog/product')->getProductDescription($product));
//
//                Mage::dispatchEvent('checkout_cart_info_item_unset_product_before', array(
//                    'item' => $item
//                ));
//
//                $item->unsProduct()->unsSuperProduct();
//
//                $cart['items'][] = $item;
//
//                $cart['subtotal'] += $item->getPrice()*$item->getQty();
//                //+MK $cart['subtotal'] += $item->getCalculationPrice()*$item->getQty();
//            }
//        }
//
//        $cartObj = new Varien_Object($cart);
////        if (Mage::app()->useCache('checkout_quote')) {
////            Mage::app()->saveCache(serialize($cartObj), $cacheKey, $cacheTags);
////        }
//
//        return $cartObj;
//    }

    public function getProductIds()
    {
        $quoteId = Mage::getSingleton('checkout/session')->getQuoteId();
        if (null === $this->_productIds) {
            $this->_productIds = array();
            if ($this->getSummaryQty()>0) {
               foreach ($this->getQuote()->getAllItems() as $item) {
                   $this->_productIds[] = $item->getProductId();
               }
            }
            $this->_productIds = array_unique($this->_productIds);
        }
        return $this->_productIds;
    }

    /**
     * Get shopping cart items summary (inchlude config settings)
     *
     * @return decimal
     */
    public function getSummaryQty()
    {
        $quoteId = Mage::getSingleton('checkout/session')->getQuoteId();

        //If there is no quote id in session trying to load quote
        //and get new quote id. This is done for cases when quote was created
        //not by customer (from backend for example).
        if (!$quoteId && Mage::getSingleton('customer/session')->isLoggedIn()) {
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            $quoteId = Mage::getSingleton('checkout/session')->getQuoteId();
        }

        if ($quoteId && $this->_summaryQty === null) {
            if (Mage::getStoreConfig('checkout/cart_link/use_qty')) {
                $this->_summaryQty = $this->getItemsQty();
            }
            else {
                $this->_summaryQty = $this->getItemsCount();
            }
        }
        return $this->_summaryQty;
    }

    /**
     * Get shopping cart items count
     *
     * @return int
     */
    public function getItemsCount()
    {
        return $this->getQuote()->getItemsCount()*1;
    }

    /**
     * Get shopping cart summary qty
     *
     * @return decimal
     */
    public function getItemsQty()
    {
        return $this->getQuote()->getItemsQty()*1;
    }
}
