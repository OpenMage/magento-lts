<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * GiftMessage api
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 */
class Mage_GiftMessage_Model_Api extends Mage_Checkout_Model_Api_Resource_Product
{
    /**
     * Return an Array of attributes.
     *
     * @param array $arr
     * @return array
     */
    protected function _prepareData($arr)
    {
        if (is_array($arr)) {
            return $arr;
        }
        return [];
    }

    /**
     * Raise event for setting a giftMessage.
     *
     * @param String $entityId
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Sales_Model_Quote $quote
     * @return array
     */
    protected function _setGiftMessage($entityId, $request, $quote)
    {
        /**
         * Below code will catch exceptions only in DeveloperMode
         * @see Mage_Core_Model_App::_callObserverMethod($object, $method, $observer)
         * And result of Mage::dispatchEvent will always return an Object of Mage_Core_Model_App.
         */
        try {
            Mage::dispatchEvent(
                'checkout_controller_onepage_save_shipping_method',
                ['request' => $request, 'quote' => $quote],
            );
            return ['entityId' => $entityId, 'result' => true, 'error' => ''];
        } catch (Exception $e) {
            return ['entityId' => $entityId, 'result' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Set GiftMessage for a Quote.
     *
     * @param int $quoteId
     * @param array[] $giftMessage
     * @param string $store
     * @return array[]
     */
    public function setForQuote($quoteId, $giftMessage, $store = null)
    {
        $quote = $this->_getQuote($quoteId, $store);

        $giftMessage = $this->_prepareData($giftMessage);
        if (empty($giftMessage)) {
            $this->_fault('giftmessage_invalid_data');
        }

        $giftMessage['type'] = 'quote';
        $giftMessages = [$quoteId => $giftMessage];
        $request = new Mage_Core_Controller_Request_Http();
        $request->setParam('giftmessage', $giftMessages);

        return $this->_setGiftMessage($quote->getId(), $request, $quote);
    }

    /**
     * Set a GiftMessage to QuoteItem by product
     *
     * @param int $quoteId
     * @param array $productsAndMessages
     * @param string $store
     * @return array
     */
    public function setForQuoteProduct($quoteId, $productsAndMessages, $store = null)
    {
        $quote = $this->_getQuote($quoteId, $store);

        $productsAndMessages = $this->_prepareData($productsAndMessages);
        if (empty($productsAndMessages)) {
            $this->_fault('invalid_data');
        }

        if (count($productsAndMessages) == 2
                && isset($productsAndMessages['product'])
                && isset($productsAndMessages['message'])
        ) {
            $productsAndMessages = [$productsAndMessages];
        }

        $results = [];
        foreach ($productsAndMessages as $productAndMessage) {
            if (isset($productAndMessage['product']) && isset($productAndMessage['message'])) {
                $product = $this->_prepareData($productAndMessage['product']);
                if (empty($product)) {
                    $this->_fault('product_invalid_data');
                }
                $message = $this->_prepareData($productAndMessage['message']);
                if (empty($message)) {
                    $this->_fault('giftmessage_invalid_data');
                }

                if (isset($product['product_id'])) {
                    $productByItem = $this->_getProduct($product['product_id'], $store, 'id');
                } elseif (isset($product['sku'])) {
                    $productByItem = $this->_getProduct($product['sku'], $store, 'sku');
                } else {
                    continue;
                }

                $productObj = $this->_getProductRequest($product);
                $quoteItem = $this->_getQuoteItemByProduct($quote, $productByItem, $productObj);
                $results[] = $this->setForQuoteItem($quoteItem->getId(), $message, $store);
            }
        }

        return $results;
    }

    /**
     * Set GiftMessage for a QuoteItem by its Id.
     *
     * @param string $quoteItemId
     * @param array[] $giftMessage
     * @param string $store
     * @return array[]
     */
    public function setForQuoteItem($quoteItemId, $giftMessage, $store = null)
    {
        /** @var Mage_Sales_Model_Quote_Item $quoteItem */
        $quoteItem = Mage::getModel('sales/quote_item')->load($quoteItemId);
        if (is_null($quoteItem->getId())) {
            $this->_fault('quote_item_not_exists');
        }

        $quote = $this->_getQuote($quoteItem->getQuoteId(), $store);

        $giftMessage = $this->_prepareData($giftMessage);
        $giftMessage['type'] = 'quote_item';

        $giftMessages = [$quoteItem->getId() => $giftMessage];

        $request = new Mage_Core_Controller_Request_Http();
        $request->setParam('giftmessage', $giftMessages);

        return $this->_setGiftMessage($quoteItemId, $request, $quote);
    }
}
