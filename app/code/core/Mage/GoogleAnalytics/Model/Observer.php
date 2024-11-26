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
 * Google Analytics module observer
 *
 * @category   Mage
 * @package    Mage_GoogleAnalytics
 */
class Mage_GoogleAnalytics_Model_Observer
{
    /**
     * Add order information into GA block to render on checkout success pages
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
     * Process items added or removed from cart for GA4 block to render event on cart view
     */
    public function processItemsAddedOrRemovedFromCart(Varien_Event_Observer $observer): void
    {
        /** @var Mage_Sales_Model_Quote_Item $item */
        $item = $observer->getEvent()->getItem();
        if ($item->getParentItem()) {
            return;
        }

        // avoid to process the same quote_item more than once
        // this could happen in case of double save of the same quote_item
        $processedProductsRegistry = Mage::registry('processed_quote_items_for_analytics') ?? new ArrayObject();
        if ($processedProductsRegistry->offsetExists($item->getId())) {
            return;
        }
        $processedProductsRegistry[$item->getId()] = true;
        Mage::register('processed_quote_items_for_analytics', $processedProductsRegistry, true);

        $addedQty = 0;
        $removedQty = 0;
        if ($item->isObjectNew()) {
            $addedQty = $item->getQty();
        } elseif ($item->isDeleted()) {
            $removedQty = $item->getQty();
        } elseif ($item->hasDataChanges()) {
            $newQty = $item->getQty();
            $oldQty = $item->getOrigData('qty');
            if ($newQty > $oldQty) {
                $addedQty = $newQty - $oldQty;
            } elseif ($newQty < $oldQty) {
                $removedQty = $oldQty - $newQty;
            }
        }

        if ($addedQty || $removedQty) {
            $product = $item->getProduct();
            $attribute = $product->getResource()->getAttribute('manufacturer');
            $manufacturer = $attribute ? $attribute->getFrontend()->getValue($product) : '';
            $dataForAnalytics = [
                'id' => $product->getId(),
                'sku' => $product->getSku(),
                'name' => $product->getName(),
                'qty' => $addedQty ?: $removedQty,
                'price' => $product->getFinalPrice(),
                'manufacturer' => $manufacturer,
                'category' => Mage::helper('googleanalytics')->getLastCategoryName($product)
            ];

            $session = Mage::getSingleton('core/session');
            if ($addedQty) {
                $addedProducts = $session->getAddedProductsForAnalytics() ?: [];
                $addedProducts[] = $dataForAnalytics;
                $session->setAddedProductsForAnalytics($addedProducts);
            } else {
                $removedProducts = $session->getRemovedProductsForAnalytics() ?: [];
                $removedProducts[] = $dataForAnalytics;
                $session->setRemovedProductsForAnalytics($removedProducts);
            }
        }
    }
}
