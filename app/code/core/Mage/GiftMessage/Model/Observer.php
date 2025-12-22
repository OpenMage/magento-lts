<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_GiftMessage
 */

/**
 * Gift Message Observer Model
 *
 * @package    Mage_GiftMessage
 */
class Mage_GiftMessage_Model_Observer extends Varien_Object
{
    /**
     * Set gift messages to order item on import item
     *
     * @return $this
     */
    public function salesEventConvertQuoteItemToOrderItem(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order_Item $orderItem */
        $orderItem = $observer->getEvent()->getOrderItem();
        /** @var Mage_Sales_Model_Quote_Item $quoteItem */
        $quoteItem = $observer->getEvent()->getItem();

        $isAvailable = Mage::helper('giftmessage/message')->getIsMessagesAvailable(
            'item',
            $quoteItem,
            $quoteItem->getStoreId(),
        );

        $orderItem->setGiftMessageId($quoteItem->getGiftMessageId())
            ->setGiftMessageAvailable($isAvailable);
        return $this;
    }

    /**
     * Set gift messages to order from quote address
     *
     * @return $this
     */
    public function salesEventConvertQuoteAddressToOrder(Varien_Event_Observer $observer)
    {
        if ($observer->getEvent()->getAddress()->getGiftMessageId()) {
            $observer->getEvent()->getOrder()
                ->setGiftMessageId($observer->getEvent()->getAddress()->getGiftMessageId());
        }

        return $this;
    }

    /**
     * Set gift messages to order from quote address
     *
     * @return $this
     */
    public function salesEventConvertQuoteToOrder(Varien_Event_Observer $observer)
    {
        $observer->getEvent()->getOrder()
            ->setGiftMessageId($observer->getEvent()->getQuote()->getGiftMessageId());
        return $this;
    }

    /**
     * Geter for available gift messages value from product
     *
     * @param  int|Mage_Catalog_Model_Product $product
     * @return null|int
     * @deprecated after 1.5.0.0
     */
    protected function _getAvailable($product)
    {
        if (is_object($product)) {
            return $product->getGiftMessageAvailable();
        }

        return Mage::getModel('catalog/product')->load($product)->getGiftMessageAvailable();
    }

    /**
     * Operate with gift messages on checkout process
     *
     * @return $this
     */
    public function checkoutEventCreateGiftMessage(Varien_Event_Observer $observer)
    {
        $giftMessages = $observer->getEvent()->getRequest()->getParam('giftmessage');
        $quote = $observer->getEvent()->getQuote();
        /** @var Mage_Sales_Model_Quote $quote */
        if (is_array($giftMessages)) {
            foreach ($giftMessages as $entityId => $message) {
                $giftMessage = Mage::getModel('giftmessage/message');

                $entity = match ($message['type']) {
                    'quote' => $quote,
                    'quote_item' => $quote->getItemById($entityId),
                    'quote_address' => $quote->getAddressById($entityId),
                    'quote_address_item' => $quote->getAddressById($message['address'])->getItemById($entityId),
                    default => $quote,
                };

                if ($entity->getGiftMessageId()) {
                    $giftMessage->load($entity->getGiftMessageId());
                }

                if (trim($message['message']) == '') {
                    if ($giftMessage->getId()) {
                        try {
                            $giftMessage->delete();
                            $entity->setGiftMessageId(0)
                                ->save();
                        } catch (Exception) {
                        }
                    }

                    continue;
                }

                try {
                    $giftMessage->setSender($message['from'])
                        ->setRecipient($message['to'])
                        ->setMessage($message['message'])
                        ->save();

                    $entity->setGiftMessageId($giftMessage->getId())
                        ->save();
                } catch (Exception) {
                }
            }
        }

        return $this;
    }

    /**
     * Set giftmessage available default value to product
     * on catalog products collection load
     *
     * @return $this
     * @deprecated after 1.4.2.0-beta1
     */
    public function catalogEventProductCollectionAfterLoad(Varien_Event_Observer $observer)
    {
        return $this;
    }

    /**
     * Duplicates giftmessage from order to quote on import or reorder
     *
     * @return $this
     */
    public function salesEventOrderToQuote(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getEvent()->getOrder();
        // Do not import giftmessage data if order is reordered
        if ($order->getReordered()) {
            return $this;
        }

        if (!Mage::helper('giftmessage/message')->isMessagesAvailable('order', $order, $order->getStore())) {
            return $this;
        }

        $giftMessageId = $order->getGiftMessageId();
        if ($giftMessageId) {
            $giftMessage = Mage::getModel('giftmessage/message')->load($giftMessageId)
                ->setId(null)
                ->save();
            $observer->getEvent()->getQuote()->setGiftMessageId($giftMessage->getId());
        }

        return $this;
    }

    /**
     * Duplicates giftmessage from order item to quote item on import or reorder
     *
     * @return $this
     */
    public function salesEventOrderItemToQuoteItem(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order_Item $orderItem */
        $orderItem = $observer->getEvent()->getOrderItem();
        // Do not import giftmessage data if order is reordered
        $order = $orderItem->getOrder();
        if ($order && $order->getReordered()) {
            return $this;
        }

        $isAvailable = Mage::helper('giftmessage/message')->isMessagesAvailable(
            'order_item',
            $orderItem,
            $orderItem->getStoreId(),
        );
        if (!$isAvailable) {
            return $this;
        }

        /** @var Mage_Sales_Model_Quote_Item $quoteItem */
        $quoteItem = $observer->getEvent()->getQuoteItem();
        if ($giftMessageId = $orderItem->getGiftMessageId()) {
            $giftMessage = Mage::getModel('giftmessage/message')->load($giftMessageId)
                ->setId(null)
                ->save();
            $quoteItem->setGiftMessageId($giftMessage->getId());
        }

        return $this;
    }
}
