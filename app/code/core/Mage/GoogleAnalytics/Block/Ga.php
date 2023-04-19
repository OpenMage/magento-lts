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
    /**
     * @deprecated after 1.4.1.1
     * @see self::_getOrdersTrackingCode()
     * @return string
     */
    public function getQuoteOrdersHtml()
    {
        return '';
    }

    /**
     * @deprecated after 1.4.1.1
     * self::_getOrdersTrackingCode()
     * @return string
     */
    public function getOrderHtml()
    {
        return '';
    }

    /**
     * @deprecated after 1.4.1.1
     * @see _toHtml()
     * @return string
     */
    public function getAccount()
    {
        return '';
    }

    /**
     * Get a specific page name (may be customized via layout)
     *
     * @return string
     */
    public function getPageName()
    {
        return $this->_getData('page_name') ?? '';
    }

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
        if ($helper->isUseUniversalAnalytics()) {
            return $this->_getPageTrackingCodeUniversal($accountId);
        }

        return $this->_getPageTrackingCodeAnalytics($accountId);
    }

    /**
     * Render regular page tracking javascript code
     * The custom "page name" may be set from layout or somewhere else. It must start from slash.
     *
     * @param string $accountId
     * @return string
     */
    protected function _getPageTrackingCodeUniversal($accountId)
    {
        return "
ga('create', '{$this->jsQuoteEscape($accountId)}', 'auto');
" . $this->_getAnonymizationCode() . "
ga('send', 'pageview');
";
    }

    /**
     * Render regular page tracking javascript code
     * The custom "page name" may be set from layout or somewhere else. It must start from slash.
     *
     * @link http://code.google.com/apis/analytics/docs/gaJS/gaJSApiBasicConfiguration.html#_gat.GA_Tracker_._trackPageview
     * @link http://code.google.com/apis/analytics/docs/gaJS/gaJSApi_gaq.html
     * @param string $accountId
     * @return string
     */
    protected function _getPageTrackingCodeAnalytics($accountId)
    {
        $pageName   = trim($this->getPageName());
        $optPageURL = '';
        if ($pageName && preg_match('/^\/.*/i', $pageName)) {
            $optPageURL = ", '{$this->jsQuoteEscape($pageName)}'";
        }
        return "
_gaq.push(['_setAccount', '{$this->jsQuoteEscape($accountId)}']);
" . $this->_getAnonymizationCode() . "
_gaq.push(['_trackPageview'{$optPageURL}]);
";
    }

    /**
     * Render information about specified orders and their items
     *
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _getOrdersTrackingCode()
    {
        /** @var Mage_GoogleAnalytics_Helper_Data $helper */
        $helper = $this->helper('googleanalytics');
        if ($helper->isUseAnalytics4()) {
            return $this->_getOrdersTrackingCodeAnalytics4();
        } elseif ($helper->isUseUniversalAnalytics()) {
            return $this->_getOrdersTrackingCodeUniversal();
        }

        return $this->_getOrdersTrackingCodeAnalytics();
    }

    /**
     * Render information about specified orders and their items
     *
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _getOrdersTrackingCodeUniversal()
    {
        $orderIds = $this->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }
        $collection = Mage::getResourceModel('sales/order_collection')
            ->addFieldToFilter('entity_id', ['in' => $orderIds]);
        $result = [];
        $result[] = "ga('require', 'ecommerce')";
        foreach ($collection as $order) {
            $result[] = sprintf(
                "ga('ecommerce:addTransaction', {
'id': '%s',
'affiliation': '%s',
'revenue': '%s',
'tax': '%s',
'shipping': '%s'
});",
                $order->getIncrementId(),
                $this->jsQuoteEscape(Mage::app()->getStore()->getFrontendName()),
                $order->getBaseGrandTotal(),
                $order->getBaseTaxAmount(),
                $order->getBaseShippingAmount()
            );
            foreach ($order->getAllVisibleItems() as $item) {
                $result[] = sprintf(
                    "ga('ecommerce:addItem', {
'id': '%s',
'sku': '%s',
'name': '%s',
'category': '%s',
'price': '%s',
'quantity': '%s'
});",
                    $order->getIncrementId(),
                    $this->jsQuoteEscape($item->getSku()),
                    $this->jsQuoteEscape($item->getName()),
                    null, // there is no "category" defined for the order item
                    $item->getBasePrice(),
                    $item->getQtyOrdered()
                );
            }
            $result[] = "ga('ecommerce:send');";
        }
        return implode("\n", $result);
    }

    /**
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _getOrdersTrackingCodeAnalytics4()
    {
        $orderIds = $this->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return '';
        }
        $collection = Mage::getResourceModel('sales/order_collection')
            ->addFieldToFilter('entity_id', ['in' => $orderIds]);
        $result = [];
        /** @var Mage_Sales_Model_Order $order */
        foreach ($collection as $order) {
            $orderData = [
                'currency' => $order->getBaseCurrencyCode(),
                'transaction_id' => $order->getIncrementId(),
                'value' => number_format($order->getBaseGrandTotal(), 2),
                'coupon' => strtoupper($order->getCouponCode()),
                'shipping' => number_format($order->getBaseShippingAmount(), 2),
                'tax' => number_format($order->getBaseTaxAmount(), 2),
                'items' => []
            ];

            /** @var Mage_Sales_Model_Order_Item $item */
            foreach ($order->getAllVisibleItems() as $item) {
                $orderData['items'][] = [
                    'item_id' => $item->getSku(),
                    'item_name' => $item->getName(),
                    'quantity' => $item->getQtyOrdered(),
                    'price' => $item->getBasePrice(),
                    'discount' => $item->getBaseDiscountAmount()
                ];
            }
            $result[] = "gtag('event', 'purchase', " . json_encode($orderData, JSON_THROW_ON_ERROR) . ");";
        }
        return implode("\n", $result);
    }

    /**
     * Render information about specified orders and their items
     *
     * @link http://code.google.com/apis/analytics/docs/gaJS/gaJSApiEcommerce.html#_gat.GA_Tracker_._addTrans
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _getOrdersTrackingCodeAnalytics()
    {
        $orderIds = $this->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }
        $collection = Mage::getResourceModel('sales/order_collection')
            ->addFieldToFilter('entity_id', ['in' => $orderIds]);
        $result = [];
        foreach ($collection as $order) {
            if ($order->getIsVirtual()) {
                $address = $order->getBillingAddress();
            } else {
                $address = $order->getShippingAddress();
            }
            $result[] = sprintf(
                "_gaq.push(['_addTrans', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']);",
                $order->getIncrementId(),
                $this->jsQuoteEscape(Mage::app()->getStore()->getFrontendName()),
                $order->getBaseGrandTotal(),
                $order->getBaseTaxAmount(),
                $order->getBaseShippingAmount(),
                $this->jsQuoteEscape(Mage::helper('core')->escapeHtml($address->getCity())),
                $this->jsQuoteEscape(Mage::helper('core')->escapeHtml($address->getRegion())),
                $this->jsQuoteEscape(Mage::helper('core')->escapeHtml($address->getCountry()))
            );
            foreach ($order->getAllVisibleItems() as $item) {
                $result[] = sprintf(
                    "_gaq.push(['_addItem', '%s', '%s', '%s', '%s', '%s', '%s']);",
                    $order->getIncrementId(),
                    $this->jsQuoteEscape($item->getSku()),
                    $this->jsQuoteEscape($item->getName()),
                    null, // there is no "category" defined for the order item
                    $item->getBasePrice(),
                    $item->getQtyOrdered()
                );
            }
            $result[] = "_gaq.push(['_trackTrans']);";
        }
        return implode("\n", $result);
    }

    /**
     * Render Enhanced eCommerce for GA4
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _getEnhancedECommCodeAnalytics4()
    {
        $helper = $this->helper('googleanalytics');
        if (!$helper->isEnhancedECommEnabled()) {
            return '';
        }

        $result = [];

        //product page
        if ($this->getRequest()->getModuleName() == 'catalog' && $this->getRequest()->getControllerName() == 'product') {
            $productViewed = Mage::registry('current_product');
            $eventData = [];
            $eventData['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
            $eventData['value'] = number_format($productViewed->getFinalPrice(), 2);
            $eventData['items'] = [];
            $eventData['items'][] = [
                'id' => $productViewed->getSku(),
                'name' => $productViewed->getName(),
                'list_name' => 'Product Detail Page',
                'brand' => $productViewed->getAttributeText('manufacturer'),
                'category' => 'Products',
                'price' => number_format($productViewed->getFinalPrice(), 2),
            ];

            $result[] = "gtag('event', 'view_item', " . json_encode($eventData, JSON_THROW_ON_ERROR) . ");";
        }

        //category page
        elseif ($this->getRequest()->getModuleName()=='catalog' && $this->getRequest()->getControllerName()=='category') {
            $layer = Mage::getSingleton('catalog/layer');
            $category = $layer->getCurrentCategory();
            $productCollection = $layer->getProductCollection();
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

            foreach ($productCollection as $productViewed) {
                $eventData['items'][] = [
                    'id' => $productViewed->getSku(),
                    'name' => $productViewed->getName(),
                    'list_name' => 'Product Detail Page',
                    'brand' => $productViewed->getAttributeText('manufacturer'),
                    'category' => 'Products',
                    'price' => number_format($productViewed->getFinalPrice(), 2),
                ];
                $eventData['value'] += $productViewed->getFinalPrice();
            }
            $eventData['value'] = number_format($eventData['value'], 2);
            $result[] = "gtag('event', 'view_item_list', " . json_encode($eventData, JSON_THROW_ON_ERROR) . ");";
        }

        //cart
        elseif ($this->getRequest()->getModuleName() == 'checkout' && $this->getRequest()->getControllerName() == 'cart') {
            $removedProduct = Mage::getSingleton('core/session')->getRemovedProductCart();
            if ($removedProduct) {
                //product removed from cart
                $_removedProduct = Mage::getModel('catalog/product')->load($removedProduct);
                $eventData = [];
                $eventData['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
                $eventData['value'] = number_format($_removedProduct->getFinalPrice(), 2);
                $eventData['items'] = [];
                $eventData['items'][] = [
                    'id' => $_removedProduct->getSku(),
                    'name' => $_removedProduct->getName(),
                    'list_name' => 'Product Detail Page',
                    'brand' => $_removedProduct->getAttributeText('manufacturer'),
                    'category' => 'Products',
                    'price' => number_format($_removedProduct->getFinalPrice(), 2),
                ];
                $result[] = "gtag('event', 'remove_from_cart', " . json_encode($eventData, JSON_THROW_ON_ERROR) . ");";
                Mage::getSingleton('core/session')->unsRemovedProductCart();
            }

            $addedProduct = Mage::getSingleton('core/session')->getAddedProductCart();
            if ($addedProduct) {
                //product added to cart
                $_addedProduct = Mage::getModel('catalog/product')->load($addedProduct);
                $eventData = [];
                $eventData['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
                $eventData['value'] = number_format($_addedProduct->getFinalPrice(), 2);
                $eventData['items'] = [];
                $eventData['items'][] = [
                    'id' => $_addedProduct->getSku(),
                    'name' => $_addedProduct->getName(),
                    'list_name' => 'Product Detail Page',
                    'brand' => $_addedProduct->getAttributeText('manufacturer'),
                    'category' => 'Products',
                    'price' => number_format($_addedProduct->getFinalPrice(), 2),
                ];
                $result[] = "gtag('event', 'add_to_cart', " . json_encode($eventData, JSON_THROW_ON_ERROR) . ");";
                Mage::getSingleton('core/session')->unsAddedProductCart();
            }

            $productCollection = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
            $eventData = [];
            $eventData['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
            $eventData['value'] = 0.00;
            $eventData['items'] = [];

            foreach ($productCollection as $productInCart) {
                $eventData['items'][] = [
                    'id' => $productInCart->getSku(),
                    'name' => $productInCart->getName(),
                    'list_name' => 'Product Detail Page',
                    'brand' => $productInCart->getAttributeText('manufacturer'),
                    'category' => 'Products',
                    'price' => number_format($productInCart->getFinalPrice(), 2),
                ];
                $eventData['value'] += $productInCart->getFinalPrice();
            }
            $eventData['value'] = number_format($eventData['value'], 2);
            $result[] = "gtag('event', 'view_cart', " . json_encode($eventData, JSON_THROW_ON_ERROR) . ");";
        }

        //begin checkout
        elseif ($this->getRequest()->getModuleName() == 'checkout' && $this->getRequest()->getControllerName() == 'onepage') {
            $productCollection = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
            $eventData = [];
            $eventData['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
            $eventData['value'] = 0.00;
            $eventData['items'] = [];

            foreach ($productCollection as $productInCart) {
                $eventData['items'][] = [
                    'id' => $productInCart->getSku(),
                    'name' => $productInCart->getName(),
                    'list_name' => 'Product Detail Page',
                    'brand' => $productInCart->getAttributeText('manufacturer'),
                    'category' => 'Products',
                    'price' => number_format($productInCart->getFinalPrice(), 2),
                ];
                $eventData['value'] += $productInCart->getFinalPrice();
            }
            $eventData['value'] = number_format($eventData['value'], 2);
            $result[] = "gtag('event', 'begin_checkout', " . json_encode($eventData, JSON_THROW_ON_ERROR) . ");";
        }

        return implode("\n", $result);
    }

    /**
     * Render IP anonymization code for page tracking javascript code
     *
     * @return string
     */
    protected function _getAnonymizationCode()
    {
        if (!Mage::helper('googleanalytics')->isIpAnonymizationEnabled()) {
            return '';
        }

        /** @var Mage_GoogleAnalytics_Helper_Data $helper */
        $helper = $this->helper('googleanalytics');
        if ($helper->isUseUniversalAnalytics()) {
            return $this->_getAnonymizationCodeUniversal();
        }

        return $this->_getAnonymizationCodeAnalytics();
    }

    /**
     * Render IP anonymization code for page tracking javascript universal analytics code
     *
     * @return string
     */
    protected function _getAnonymizationCodeUniversal()
    {
        return "ga('set', 'anonymizeIp', true);";
    }

    /**
     * Render IP anonymization code for page tracking javascript google analytics code
     *
     * @return string
     */
    protected function _getAnonymizationCodeAnalytics()
    {
        return "_gaq.push (['_gat._anonymizeIp']);";
    }

    /**
     * Is ga available
     *
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
