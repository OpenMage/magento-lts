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
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
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
    protected const CHECKOUT_MODULE_NAME = 'checkout';
    protected const CHECKOUT_CONTROLLER_NAME = 'onepage';

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
            $trackingCode .= "
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
        $helper = Mage::helper('googleanalytics');

        /**
         * This event signifies that an item was removed from a cart.
         *
         * @link https://developers.google.com/tag-platform/gtagjs/reference/events#remove_from_cart
         */
        $removedProducts = Mage::getSingleton('core/session')->getRemovedProductsForAnalytics();
        if ($removedProducts) {
            foreach ($removedProducts as $removedProduct) {
                $eventData = [];
                $eventData['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
                $eventData['value'] = $helper->formatPrice($removedProduct['price'] * $removedProduct['qty']);
                $eventData['items'] = [];
                $_item = [
                    'item_id' => $removedProduct['sku'],
                    'item_name' => $removedProduct['name'],
                    'price' => $helper->formatPrice($removedProduct['price']),
                    'quantity' => (int) $removedProduct['qty'],
                    'item_brand' => $removedProduct['manufacturer'],
                    'item_category' => $removedProduct['category'],
                ];
                $eventData['items'][] = $_item;
                $result[] = ['remove_from_cart', $eventData];
            }
            Mage::getSingleton('core/session')->unsRemovedProductsForAnalytics();
        }

        /**
         * This event signifies that an item was added to a cart for purchase.
         *
         * @link https://developers.google.com/tag-platform/gtagjs/reference/events#add_to_cart
         */
        $addedProducts = Mage::getSingleton('core/session')->getAddedProductsForAnalytics();
        if ($addedProducts) {
            foreach ($addedProducts as $_addedProduct) {
                $eventData = [];
                $eventData['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
                $eventData['value'] = $helper->formatPrice($_addedProduct['price'] * $_addedProduct['qty']);
                $eventData['items'] = [];
                $_item = [
                    'item_id' => $_addedProduct['sku'],
                    'item_name' => $_addedProduct['name'],
                    'price' => $helper->formatPrice($_addedProduct['price']),
                    'quantity' => (int) $_addedProduct['qty'],
                    'item_brand' => $_addedProduct['manufacturer'],
                    'item_category' => $_addedProduct['category'],
                ];
                $eventData['items'][] = $_item;
                $result[] = ['add_to_cart', $eventData];
                Mage::getSingleton('core/session')->unsAddedProductsForAnalytics();
            }
        }

        if ($moduleName == 'catalog' && $controllerName == 'product') {
            // This event signifies that some content was shown to the user. Use this event to discover the most popular items viewed.
            // @see https://developers.google.com/tag-platform/gtagjs/reference/events#view_item
            $productViewed = Mage::registry('current_product');
            $category = Mage::registry('current_category') ? Mage::registry('current_category')->getName() : false;
            $eventData = [];
            $eventData['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
            $eventData['value'] = $helper->formatPrice($productViewed->getFinalPrice());
            $eventData['items'] = [];
            $_item = [
                'item_id' => $productViewed->getSku(),
                'item_name' => $productViewed->getName(),
                'list_name' => 'Product Detail Page',
                'item_category' => $category,
                'price' => $helper->formatPrice($productViewed->getFinalPrice()),
            ];
            if ($productViewed->getAttributeText('manufacturer')) {
                $_item['item_brand'] = $productViewed->getAttributeText('manufacturer');
            }
            $eventData['items'][] = $_item;
            $result[] = ['view_item', $eventData];
        } elseif ($moduleName == 'catalog' && $controllerName == 'category') {
            // Log this event when the user has been presented with a list of items of a certain category.
            // @see https://developers.google.com/tag-platform/gtagjs/reference/events#view_item_list
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
            $eventData['item_list_id'] = 'category_' . $category->getUrlKey();
            $eventData['item_list_name'] = $category->getName();
            $eventData['items'] = [];

            $index = 1;
            foreach ($productCollection as $key => $productViewed) {
                $_item = [
                    'item_id' => $productViewed->getSku(),
                    'index' => $index,
                    'item_name' => $productViewed->getName(),
                    'price' => $helper->formatPrice($productViewed->getFinalPrice()),
                ];
                if ($productViewed->getAttributeText('manufacturer')) {
                    $_item['item_brand'] = $productViewed->getAttributeText('manufacturer');
                }
                if ($productViewed->getCategory()->getName()) {
                    $_item['item_category'] = $productViewed->getCategory()->getName();
                }
                $eventData['items'][] = $_item;
                $index++;
                $eventData['value'] += $productViewed->getFinalPrice();
            }
            $eventData['value'] = $helper->formatPrice($eventData['value']);
            $result[] = ['view_item_list', $eventData];
        } elseif ($moduleName == 'checkout' && $controllerName == 'cart') {
            // This event signifies that a user viewed his cart.
            // @see https://developers.google.com/tag-platform/gtagjs/reference/events#view_cart
            $productCollection = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
            $eventData = [];
            $eventData['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
            $eventData['value'] = 0.00;
            $eventData['items'] = [];

            foreach ($productCollection as $productInCart) {
                if ($productInCart->getParentItem()) {
                    continue;
                }
                $_product = $productInCart->getProduct();
                $_item = [
                    'item_id' => $_product->getSku(),
                    'item_name' => $_product->getName(),
                    'price' => $helper->formatPrice($_product->getFinalPrice()),
                    'quantity' => (int) $productInCart->getQty(),
                ];
                if ($_product->getAttributeText('manufacturer')) {
                    $_item['item_brand'] = $_product->getAttributeText('manufacturer');
                }
                $itemCategory = $helper->getLastCategoryName($_product);
                if ($itemCategory) {
                    $_item['item_category'] = $itemCategory;
                }
                $eventData['items'][] = $_item;
                $eventData['value'] += $_product->getFinalPrice() * $productInCart->getQty();
            }
            $eventData['value'] = $helper->formatPrice($eventData['value']);
            $result[] = ['view_cart', $eventData];
        } elseif ($moduleName == static::CHECKOUT_MODULE_NAME && $controllerName == static::CHECKOUT_CONTROLLER_NAME) {
            // This event signifies that a user has begun a checkout.
            // @see https://developers.google.com/tag-platform/gtagjs/reference/events#begin_checkout
            $productCollection = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
            if ($productCollection) {
                $eventData = [];
                $eventData['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
                $eventData['value'] = 0.00;
                $eventData['items'] = [];
                foreach ($productCollection as $productInCart) {
                    if ($productInCart->getParentItem()) {
                        continue;
                    }
                    $_product = $productInCart->getProduct();
                    $_item = [
                        'item_id' => $_product->getSku(),
                        'item_name' => $_product->getName(),
                        'price' => $helper->formatPrice($_product->getFinalPrice()),
                        'quantity' => (int) $productInCart->getQty(),
                    ];
                    if ($_product->getAttributeText('manufacturer')) {
                        $_item['item_brand'] = $_product->getAttributeText('manufacturer');
                    }
                    $itemCategory = $helper->getLastCategoryName($_product);
                    if ($itemCategory) {
                        $_item['item_category'] = $itemCategory;
                    }
                    $eventData['items'][] = $_item;
                    $eventData['value'] += $_product->getFinalPrice();
                }
                $eventData['value'] = $helper->formatPrice($eventData['value']);
                $result[] = ['begin_checkout', $eventData];
            }
        }

        // This event signifies when one or more items is purchased by a user.
        // @see https://developers.google.com/tag-platform/gtagjs/reference/events?hl=it#purchase
        $orderIds = $this->getOrderIds();
        if (!empty($orderIds) && is_array($orderIds)) {
            $collection = Mage::getResourceModel('sales/order_collection')
                ->addFieldToFilter('entity_id', ['in' => $orderIds]);
            /** @var Mage_Sales_Model_Order $order */
            foreach ($collection as $order) {
                $orderData = [
                    'currency' => $order->getBaseCurrencyCode(),
                    'transaction_id' => $order->getIncrementId(),
                    'value' => $helper->formatPrice($order->getBaseGrandTotal()),
                    'coupon' => strtoupper((string) $order->getCouponCode()),
                    'shipping' => $helper->formatPrice($order->getBaseShippingAmount()),
                    'tax' => $helper->formatPrice($order->getBaseTaxAmount()),
                    'items' => [],
                ];

                /** @var Mage_Sales_Model_Order_Item $item */
                foreach ($order->getAllItems() as $item) {
                    if ($item->getParentItem()) {
                        continue;
                    }
                    $_product = $item->getProduct();
                    $_item = [
                        'item_id' => $item->getSku(),
                        'item_name' => $item->getName(),
                        'quantity' => (int) $item->getQtyOrdered(),
                        'price' => $helper->formatPrice($item->getBasePrice()),
                        'discount' => $helper->formatPrice($item->getBaseDiscountAmount()),
                    ];
                    if ($_product->getAttributeText('manufacturer')) {
                        $_item['item_brand'] = $_product->getAttributeText('manufacturer');
                    }
                    $itemCategory = $helper->getLastCategoryName($_product);
                    if ($itemCategory) {
                        $_item['item_category'] = $itemCategory;
                    }
                    $orderData['items'][] = $_item;
                }
                $result[] = ['purchase', $orderData];
            }
        }

        $ga4DataTransport = new Varien_Object();
        $ga4DataTransport->setData($result);
        Mage::dispatchEvent('googleanalytics_ga4_send_data_before', ['ga4_data_transport' => $ga4DataTransport]);
        $result = $ga4DataTransport->getData();

        if ($this->helper('googleanalytics')->isDebugModeEnabled() && count($result) > 0) {
            $this->helper('googleanalytics')->log($result);
        }

        foreach ($result as $k => $ga4Event) {
            $result[$k] = "gtag('event', '{$ga4Event[0]}', " . json_encode($ga4Event[1], JSON_THROW_ON_ERROR) . ');';
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
