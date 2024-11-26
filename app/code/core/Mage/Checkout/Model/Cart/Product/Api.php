<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart api for product
 *
 * @category   Mage
 * @package    Mage_Checkout
 */
class Mage_Checkout_Model_Cart_Product_Api extends Mage_Checkout_Model_Api_Resource_Product
{
    /**
     * Base preparation of product data
     *
     * @param mixed $data
     * @return null|array
     */
    protected function _prepareProductsData($data)
    {
        return is_array($data) ? $data : null;
    }

    /**
     * @param  int $quoteId
     * @param  array $productsData
     * @param  string|int $store
     * @return bool
     */
    public function add($quoteId, $productsData, $store = null)
    {
        $quote = $this->_getQuote($quoteId, $store);
        if (empty($store)) {
            $store = $quote->getStoreId();
        }

        $productsData = $this->_prepareProductsData($productsData);
        if (empty($productsData)) {
            $this->_fault('invalid_product_data');
        }

        $errors = [];
        foreach ($productsData as $productItem) {
            if (isset($productItem['product_id'])) {
                $productByItem = $this->_getProduct($productItem['product_id'], $store, 'id');
            } elseif (isset($productItem['sku'])) {
                $productByItem = $this->_getProduct($productItem['sku'], $store, 'sku');
            } else {
                $errors[] = Mage::helper('checkout')->__('One item of products do not have identifier or sku');
                continue;
            }

            $productRequest = $this->_getProductRequest($productItem);
            try {
                $result = $quote->addProduct($productByItem, $productRequest);
                if (is_string($result)) {
                    Mage::throwException($result);
                }
            } catch (Mage_Core_Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (!empty($errors)) {
            $this->_fault('add_product_fault', implode(PHP_EOL, $errors));
        }

        try {
            $quote->collectTotals()->save();
        } catch (Exception $e) {
            $this->_fault('add_product_quote_save_fault', $e->getMessage());
        }

        return true;
    }

    /**
     * @param  int $quoteId
     * @param  array $productsData
     * @param  string|int $store
     * @return bool
     */
    public function update($quoteId, $productsData, $store = null)
    {
        $quote = $this->_getQuote($quoteId, $store);
        if (empty($store)) {
            $store = $quote->getStoreId();
        }

        $productsData = $this->_prepareProductsData($productsData);
        if (empty($productsData)) {
            $this->_fault('invalid_product_data');
        }

        $errors = [];
        foreach ($productsData as $productItem) {
            if (isset($productItem['product_id'])) {
                $productByItem = $this->_getProduct($productItem['product_id'], $store, 'id');
            } elseif (isset($productItem['sku'])) {
                $productByItem = $this->_getProduct($productItem['sku'], $store, 'sku');
            } else {
                $errors[] = Mage::helper('checkout')->__('One item of products do not have identifier or sku');
                continue;
            }

            $quoteItem = $this->_getQuoteItemByProduct(
                $quote,
                $productByItem,
                $this->_getProductRequest($productItem)
            );
            if (is_null($quoteItem->getId())) {
                $errors[] = Mage::helper('checkout')->__('One item of products is not belong any of quote item');
                continue;
            }

            if ($productItem['qty'] > 0) {
                $quoteItem->setQty($productItem['qty']);
            }
        }

        if (!empty($errors)) {
            $this->_fault('update_product_fault', implode(PHP_EOL, $errors));
        }

        try {
            $quote->collectTotals()->save();
        } catch (Exception $e) {
            $this->_fault('update_product_quote_save_fault', $e->getMessage());
        }

        return true;
    }

    /**
     * @param  int $quoteId
     * @param  array $productsData
     * @param  string|int $store
     * @return bool
     */
    public function remove($quoteId, $productsData, $store = null)
    {
        $quote = $this->_getQuote($quoteId, $store);
        if (empty($store)) {
            $store = $quote->getStoreId();
        }

        $productsData = $this->_prepareProductsData($productsData);
        if (empty($productsData)) {
            $this->_fault('invalid_product_data');
        }

        $errors = [];
        foreach ($productsData as $productItem) {
            if (isset($productItem['product_id'])) {
                $productByItem = $this->_getProduct($productItem['product_id'], $store, 'id');
            } elseif (isset($productItem['sku'])) {
                $productByItem = $this->_getProduct($productItem['sku'], $store, 'sku');
            } else {
                $errors[] = Mage::helper('checkout')->__('One item of products do not have identifier or sku');
                continue;
            }

            try {
                $quoteItem = $this->_getQuoteItemByProduct(
                    $quote,
                    $productByItem,
                    $this->_getProductRequest($productItem)
                );
                if (is_null($quoteItem->getId())) {
                    $errors[] = Mage::helper('checkout')->__('One item of products is not belong any of quote item');
                    continue;
                }
                $quote->removeItem($quoteItem->getId());
            } catch (Mage_Core_Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (!empty($errors)) {
            $this->_fault('remove_product_fault', implode(PHP_EOL, $errors));
        }

        try {
            $quote->collectTotals()->save();
        } catch (Exception $e) {
            $this->_fault('remove_product_quote_save_fault', $e->getMessage());
        }

        return true;
    }

    /**
     * @param  int $quoteId
     * @param  string|int $store
     * @return array
     */
    public function items($quoteId, $store = null)
    {
        $quote = $this->_getQuote($quoteId, $store);
        if (empty($store)) {
            $store = $quote->getStoreId();
        }

        if (!$quote->getItemsCount()) {
            return [];
        }

        $productsResult = [];
        foreach ($quote->getAllItems() as $item) {
            $product = $item->getProduct();
            $productsResult[] = [ // Basic product data
                'product_id'   => $product->getId(),
                'sku'          => $product->getSku(),
                'name'         => $product->getName(),
                'set'          => $product->getAttributeSetId(),
                'type'         => $product->getTypeId(),
                'category_ids' => $product->getCategoryIds(),
                'website_ids'  => $product->getWebsiteIds()
            ];
        }

        return $productsResult;
    }

    /**
     * @param  int $quoteId
     * @param  array $productsData
     * @param  string|int $store
     * @return bool
     */
    public function moveToCustomerQuote($quoteId, $productsData, $store = null)
    {
        $quote = $this->_getQuote($quoteId, $store);

        if (empty($store)) {
            $store = $quote->getStoreId();
        }

        $customer = $quote->getCustomer();
        if (is_null($customer->getId())) {
            $this->_fault('customer_not_set_for_quote');
        }

        /** @var Mage_Sales_Model_Quote $customerQuote */
        $customerQuote = Mage::getModel('sales/quote')
            ->setStoreId($store)
            ->loadByCustomer($customer);

        if (is_null($customerQuote->getId())) {
            $this->_fault('customer_quote_not_exist');
        }

        if ($customerQuote->getId() == $quote->getId()) {
            $this->_fault('quotes_are_similar');
        }

        $productsData = $this->_prepareProductsData($productsData);
        if (empty($productsData)) {
            $this->_fault('invalid_product_data');
        }

        $errors = [];
        foreach ($productsData as $key => $productItem) {
            if (isset($productItem['product_id'])) {
                $productByItem = $this->_getProduct($productItem['product_id'], $store, 'id');
            } elseif (isset($productItem['sku'])) {
                $productByItem = $this->_getProduct($productItem['sku'], $store, 'sku');
            } else {
                $errors[] = Mage::helper('checkout')->__('One item of products do not have identifier or sku');
                continue;
            }

            try {
                $quoteItem = $this->_getQuoteItemByProduct(
                    $quote,
                    $productByItem,
                    $this->_getProductRequest($productItem)
                );
                if ($quoteItem && $quoteItem->getId()) {
                    $newQuoteItem = clone $quoteItem;
                    $newQuoteItem->setId(null);
                    $customerQuote->addItem($newQuoteItem);
                    $quote->removeItem($quoteItem->getId());
                    unset($productsData[$key]);
                } else {
                    $errors[] = Mage::helper('checkout')->__('One item of products is not belong any of quote item');
                }
            } catch (Mage_Core_Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (count($productsData) || !empty($errors)) {
            $this->_fault('unable_to_move_all_products', implode(PHP_EOL, $errors));
        }

        try {
            $customerQuote
                ->collectTotals()
                ->save();

            $quote
                ->collectTotals()
                ->save();
        } catch (Exception $e) {
            $this->_fault('product_move_quote_save_fault', $e->getMessage());
        }

        return true;
    }
}
