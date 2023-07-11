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
 * Google Analytics module observer
 *
 * @category   Mage
 * @package    Mage_GoogleAnalytics
 */
class Mage_GoogleAnalytics_Model_Observer
{
    /**
     * Add order information into GA block to render on checkout success pages
     *
     * @param Varien_Event_Observer $observer
     */
    public function setGoogleAnalyticsOnOrderSuccessPageView(Varien_Event_Observer $observer)
    {
        $orderIds = $observer->getEvent()->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }
        $block = Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('google_analytics');
        if ($block) {
            $block->setOrderIds($orderIds);
        }
    }

    /**
     * Add 'removed item' from cart into session for GA4 block to render event on cart view
     *
     * @param Varien_Event_Observer $observer
     */
    public function removeItemFromCartGoogleAnalytics(Varien_Event_Observer $observer)
    {
        $productRemoved = $observer->getEvent()->getQuoteItem()->getProduct();
        if ($productRemoved) {
            $_removedProducts = Mage::getSingleton('core/session')->getRemovedProductsCart() ?: [];
            $_removedProducts[] = $productRemoved->getId();
            $_removedProducts = array_unique($_removedProducts);
            Mage::getSingleton('core/session')->setRemovedProductsCart($_removedProducts);
        }
    }

    /**
     * Add 'added item' to cart into session for GA4 block to render event on cart view
     *
     * @param Varien_Event_Observer $observer
     */
    public function addItemToCartGoogleAnalytics(Varien_Event_Observer $observer)
    {
        $items = $observer->getEvent()->getItems();
        if ($items) {
            $_addedProducts = Mage::getSingleton('core/session')->getAddedProductsCart() ?: [];

            /** @var Mage_Sales_Model_Quote_Item $item */
            foreach ($items as $item) {
                $product = $item->getProduct();

                if ($product->getParentProductId()) {
                    // Fix double add to cart for configurable products, skip child product
                    continue;
                }

                if ($product->getParentItem()) {
                    $product = $product->getParentItem();
                }

                $_addedProduct = [
                    'id' => $product->getId(),
                    'sku' => $product->getSku(),
                    'name' => $product->getName(),
                    'qty' => $item->getQtyToAdd(),
                    'price' => $product->getFinalPrice(),
                    'manufacturer' => '',
                    'category' => ''
                ];

                if ($product->getAttributeText('manufacturer')) {
                    $_addedProduct['manufacturer'] = $product->getAttributeText('manufacturer');
                }

                $productCategory = Mage::helper('googleanalytics')->getLastCategoryName($product);
                if ($productCategory) {
                    $_addedProduct['category'] = $productCategory;
                }

                $_addedProducts[] = $_addedProduct;
            }

            Mage::getSingleton('core/session')->setAddedProductsCart($_addedProducts);
        }
    }
}
