<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_GoogleAnalytics
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * GoogleAnalitics Page Block
 *
 * @category   Mage
 * @package    Mage_GoogleAnalytics
 */
class Mage_GoogleAnalytics_Block_Ga extends Mage_Core_Block_Template
{
    protected const CHECKOUT_MODULE_NAME = "checkout";
    protected const CHECKOUT_CONTROLLER_NAME = "onepage";

    /**
     * Render regular page tracking javascript code
     * The custom "page name" may be set from layout or somewhere else. It must start from slash.
     *
     * @param string $accountId
     * @return string
     */
    protected function _getPageTrackingCode($accountId)
    {
        /** @var Mage_GoogleAnalytics_Helper_Data $helper */
        $helper = $this->helper('googleanalytics');
        if ($helper->isUseAnalytics4()) {
            return $this->_getPageTrackingCodeAnalytics4($accountId);
        }

        return '';
    }

    /**
     * Render regular page tracking javascript code
     *
     * @link https://developers.google.com/tag-platform/gtagjs/reference
     * @param string $accountId
     * @return string
     */
    protected function _getPageTrackingCodeAnalytics4($accountId)
    {
        $trackingCode = "
gtag('js', new Date());
";
        if (!$this->helper('googleanalytics')->isDebugModeEnabled()) {
            $trackingCode .= "
gtag('config', '{$this->jsQuoteEscape($accountId)}');
";
        } else {
            $trackingCode .= "
gtag('config', '{$this->jsQuoteEscape($accountId)}', {'debug_mode':true});
";
        }

        //add user_id
        if ($this->helper('googleanalytics')->isUserIdEnabled() && Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $trackingCode.= "
gtag('set', 'user_id', '{$customer->getId()}');
";
        }

        if ($this->helper('googleanalytics')->isDebugModeEnabled()) {
            $this->helper('googleanalytics')->log($trackingCode);
        }

        return $trackingCode;
    }

    /**
     * Render regular page tracking javascript code
     * The custom "page name" may be set from layout or somewhere else. It must start from slash.
     *
     * @param string $accountId
     * @return string
     * @deprecated
     */
    protected function _getPageTrackingCodeUniversal($accountId)
    {
        return '';
    }

    /**
     * Render regular page tracking javascript code
     * The custom "page name" may be set from layout or somewhere else. It must start from slash.
     *
     * @link http://code.google.com/apis/analytics/docs/gaJS/gaJSApiBasicConfiguration.html#_gat.GA_Tracker_._trackPageview
     * @link http://code.google.com/apis/analytics/docs/gaJS/gaJSApi_gaq.html
     * @param string $accountId
     * @return string
     * @deprecated
     */
    protected function _getPageTrackingCodeAnalytics($accountId)
    {
        return '';
    }

    /**
     * Render information about specified orders and their items
     *
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     * @deprecated
     */
    protected function _getOrdersTrackingCode()
    {
        return '';
    }

    /**
     * Render information about specified orders and their items
     *
     * @return string
     * @throws JsonException
     */
    protected function _getEnhancedEcommerceDataForAnalytics4()
    {
        $result = [];
        $request = $this->getRequest();
        $moduleName = $request->getModuleName();
        $controllerName = $request->getControllerName();

        /**
         * This event signifies that an item was removed from a cart.
         *
         * @link https://developers.google.com/tag-platform/gtagjs/reference/events#remove_from_cart
         */
        $removedProducts = Mage::getSingleton('core/session')->getRemovedProductsCart();
        if ($removedProducts) {
            foreach ($removedProducts as $removedProduct) {
                $_removedProduct = Mage::getModel('catalog/product')->load($removedProduct);
                $eventData = [];
                $eventData['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
                $eventData['value'] = number_format($_removedProduct->getFinalPrice(), 2, '.', '');
                $eventData['items'] = [];
                $_item = [
                    'item_id' => $_removedProduct->getSku(),
                    'item_name' => $_removedProduct->getName(),
                    'price' => number_format($_removedProduct->getFinalPrice(), 2, '.', ''),
                ];
                if ($_removedProduct->getAttributeText('manufacturer')) {
                    $_item['item_brand'] = $_removedProduct->getAttributeText('manufacturer');
                }
                $itemCategory = Mage::helper('googleanalytics')->getLastCategoryName($_removedProduct);
                if ($itemCategory) {
                    $_item['item_category'] = $itemCategory;
                }
                array_push($eventData['items'], $_item);
                $result[] = "gtag('event', 'remove_from_cart', " . json_encode($eventData, JSON_THROW_ON_ERROR) . ");";
            }
            Mage::getSingleton('core/session')->unsRemovedProductsCart();
        }

        /**
         * This event signifies that an item was added to a cart for purchase.
         *
         * @link https://developers.google.com/tag-platform/gtagjs/reference/events#add_to_cart
         */
        $addedProducts = Mage::getSingleton('core/session')->getAddedProductsCart();
        if ($addedProducts) {
            foreach ($addedProducts as $addedProduct) {
                $_addedProduct = Mage::getModel('catalog/product')->load($addedProduct);
                $eventData = [];
                $eventData['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
                $eventData['value'] = number_format($_addedProduct->getFinalPrice(), 2, '.', '');
                $eventData['items'] = [];
                $_item = [
                    'item_id' => $_addedProduct->getSku(),
                    'item_name' => $_addedProduct->getName(),
                    'price' => number_format($_addedProduct->getFinalPrice(), 2, '.', ''),
                ];
                if ($_addedProduct->getAttributeText('manufacturer')) {
                    $_item['item_brand'] = $_addedProduct->getAttributeText('manufacturer');
                }

                $itemCategory = Mage::helper('googleanalytics')->getLastCategoryName($_addedProduct);
                if ($itemCategory) {
                    $_item['item_category'] = $itemCategory;
                }
                array_push($eventData['items'], $_item);
                $result[] = "gtag('event', 'add_to_cart', " . json_encode($eventData, JSON_THROW_ON_ERROR) . ");";
                Mage::getSingleton('core/session')->unsAddedProductsCart();
            }
        }

        /**
         * This event signifies that some content was shown to the user. Use this event to discover the most popular items viewed.
         *
         * @link https://developers.google.com/tag-platform/gtagjs/reference/events#view_item
         */
        if ($moduleName == 'catalog' && $controllerName == 'product') {
            $productViewed = Mage::registry('current_product');
            $category = Mage::registry('current_category') ? Mage::registry('current_category')->getName() : false;
            $eventData = [];
            $eventData['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
            $eventData['value'] = number_format($productViewed->getFinalPrice(), 2, '.', '');
            $eventData['items'] = [];
            $_item = [
                'item_id' => $productViewed->getSku(),
                'item_name' => $productViewed->getName(),
                'list_name' => 'Product Detail Page',
                'item_category' => $category,
                'price' => number_format($productViewed->getFinalPrice(), 2, '.', ''),
            ];
            if ($productViewed->getAttributeText('manufacturer')) {
                $_item['item_brand'] = $productViewed->getAttributeText('manufacturer');
            }
            array_push($eventData['items'], $_item);
            $result[] = "gtag('event', 'view_item', " . json_encode($eventData, JSON_THROW_ON_ERROR) . ");";
        }

        /**
         * Log this event when the user has been presented with a list of items of a certain category.
         *
         * @link https://developers.google.com/tag-platform/gtagjs/reference/events#view_item_list
         */
        elseif ($moduleName == 'catalog' && $controllerName == 'category') {
            $layer = Mage::getSingleton('catalog/layer');
            $category = $layer->getCurrentCategory();
            $productCollection = clone $layer->getProductCollection();
            $productCollection->addAttributeToSelect('sku');

            $toolbarBlock = Mage::app()->getLayout()->getBlock('product_list_toolbar');
            $pageSize = $toolbarBlock->getLimit();
            $currentPage = $toolbarBlock->getCurrentPage();
            if ($pageSize !== 'all') {
                $productCollection->setPageSize($pageSize)->setCurPage($currentPage);
            }
            $eventData = [];
            $eventData['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
            $eventData['value'] = 0.00;
            $eventData['item_list_id'] = 'category_'.$category->getUrlKey();
            $eventData['item_list_name'] = $category->getName();
            $eventData['items'] = [];

            $index = 1;
            foreach ($productCollection as $key => $productViewed) {
                $_item = [
                    'item_id' => $productViewed->getSku(),
                    'index' => $index,
                    'item_name' => $productViewed->getName(),
                    'price' => number_format($productViewed->getFinalPrice(), 2, '.', ''),
                ];
                if ($productViewed->getAttributeText('manufacturer')) {
                    $_item['item_brand'] = $productViewed->getAttributeText('manufacturer');
                }
                if ($productViewed->getCategory()->getName()) {
                    $_item['item_category'] = $productViewed->getCategory()->getName();
                }
                array_push($eventData['items'], $_item);
                $index++;
                $eventData['value'] += $productViewed->getFinalPrice();
            }
            $eventData['value'] = number_format($eventData['value'], 2, '.', '');
            $result[] = "gtag('event', 'view_item_list', " . json_encode($eventData, JSON_THROW_ON_ERROR) . ");";
        }

        /**
         * This event signifies that a user viewed his cart.
         *
         * @link https://developers.google.com/tag-platform/gtagjs/reference/events#view_cart
         */
        elseif ($moduleName == 'checkout' && $controllerName == 'cart') {
            $productCollection = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
            $eventData = [];
            $eventData['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
            $eventData['value'] = 0.00;
            $eventData['items'] = [];

            foreach ($productCollection as $productInCart) {
                $_product = Mage::getModel('catalog/product')->load($productInCart->getProductId());
                $_item = [
                    'item_id' => $_product->getSku(),
                    'item_name' => $_product->getName(),
                    'price' => number_format($_product->getFinalPrice(), 2, '.', ''),
                    'quantity' => (int) $productInCart->getQty(),
                ];
                if ($_product->getAttributeText('manufacturer')) {
                    $_item['item_brand'] = $_product->getAttributeText('manufacturer');
                }

                $itemCategory = Mage::helper('googleanalytics')->getLastCategoryName($_product);
                if ($itemCategory) {
                    $_item['item_category'] = $itemCategory;
                }
                array_push($eventData['items'], $_item);
                $eventData['value'] += $_product->getFinalPrice();
            }
            $eventData['value'] = number_format($eventData['value'], 2, '.', '');
            $result[] = "gtag('event', 'view_cart', " . json_encode($eventData, JSON_THROW_ON_ERROR) . ");";
        }

        /**
         * This event signifies that a user has begun a checkout.
         *
         * @link https://developers.google.com/tag-platform/gtagjs/reference/events#begin_checkout
         */
        elseif ($moduleName == static::CHECKOUT_MODULE_NAME && $controllerName == static::CHECKOUT_CONTROLLER_NAME) {
            $productCollection = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
            if ($productCollection) {
                $eventData = [];
                $eventData['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
                $eventData['value'] = 0.00;
                $eventData['items'] = [];
                foreach ($productCollection as $productInCart) {
                    $_product = Mage::getModel('catalog/product')->load($productInCart->getProductId());
                    $_item = [
                        'item_id' => $_product->getSku(),
                        'item_name' => $_product->getName(),
                        'price' => number_format($_product->getFinalPrice(), 2, '.', ''),
                        'quantity' => (int) $productInCart->getQty(),
                    ];
                    if ($_product->getAttributeText('manufacturer')) {
                        $_item['item_brand'] = $_product->getAttributeText('manufacturer');
                    }

                    $itemCategory = Mage::helper('googleanalytics')->getLastCategoryName($_product);
                    if ($itemCategory) {
                        $_item['item_category'] = $itemCategory;
                    }
                    array_push($eventData['items'], $_item);
                    $eventData['value'] += $_product->getFinalPrice();
                }
                $eventData['value'] = number_format($eventData['value'], 2, '.', '');
                $result[] = "gtag('event', 'begin_checkout', " . json_encode($eventData, JSON_THROW_ON_ERROR) . ");";
            }
        }

        /**
         *  This event signifies when one or more items is purchased by a user.
         *
         * @link https://developers.google.com/tag-platform/gtagjs/reference/events?hl=it#purchase
         */
        $orderIds = $this->getOrderIds();
        if (!empty($orderIds) && is_array($orderIds)) {
            $collection = Mage::getResourceModel('sales/order_collection')
                ->addFieldToFilter('entity_id', ['in' => $orderIds]);
            /** @var Mage_Sales_Model_Order $order */
            foreach ($collection as $order) {
                $orderData = [
                    'currency' => $order->getBaseCurrencyCode(),
                    'transaction_id' => $order->getIncrementId(),
                    'value' => number_format($order->getBaseGrandTotal(), 2, '.', ''),
                    'coupon' => strtoupper($order->getCouponCode()),
                    'shipping' => number_format($order->getBaseShippingAmount(), 2, '.', ''),
                    'tax' => number_format($order->getBaseTaxAmount(), 2, '.', ''),
                    'items' => []
                ];

                /** @var Mage_Sales_Model_Order_Item $item */
                foreach ($order->getAllVisibleItems() as $item) {
                    $_item = [
                        'item_id' => $item->getSku(),
                        'item_name' => $item->getName(),
                        'quantity' => (int) $item->getQtyOrdered(),
                        'price' => number_format($item->getBasePrice(), 2, '.', ''),
                        'discount' => number_format($item->getBaseDiscountAmount(), 2, '.', '')
                    ];
                    $_product = Mage::getModel('catalog/product')->load($item->getProductId());
                    if ($_product->getAttributeText('manufacturer')) {
                        $_item['item_brand'] = $_product->getAttributeText('manufacturer');
                    }

                    $itemCategory = Mage::helper('googleanalytics')->getLastCategoryName($_product);
                    if ($itemCategory) {
                        $_item['item_category'] = $itemCategory;
                    }
                    array_push($orderData['items'], $_item);
                }
                $result[] = "gtag('event', 'purchase', " . json_encode($orderData, JSON_THROW_ON_ERROR) . ");";
            }
        }

        if ($this->helper('googleanalytics')->isDebugModeEnabled() && count($result) > 0) {
            $this->helper('googleanalytics')->log($result);
        }
        return implode("\n", $result);
    }

    /**
     * @return bool
     */
    protected function _isAvailable()
    {
        return Mage::helper('googleanalytics')->isGoogleAnalyticsAvailable();
    }

    /**
     * Render GA tracking scripts
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_isAvailable()) {
            return '';
        }
        return parent::_toHtml();
    }
}
