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
 * @package     Mage_GiftMessage
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Gift Message Observer Model
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_GiftMessage_Model_Observer extends Varien_Object
{

    /**
     * Set gift messages to order item on import item
     *
     * @param Varien_Object $observer
     * @return Mage_GiftMessage_Model_Observer
     */
    public function salesEventConvertQuoteItemToOrderItem($observer)
    {
        $observer->getEvent()->getOrderItem()
            ->setGiftMessageId($observer->getEvent()->getItem()->getGiftMessageId())
            ->setGiftMessageAvailable($this->_getAvailable($observer->getEvent()->getItem()->getProductId()));
        return $this;
    }

    /**
     * Set gift messages to order from quote address
     *
     * @param Varien_Object $observer
     * @return Mage_GiftMessage_Model_Observer
     */
    public function salesEventConvertQuoteAddressToOrder($observer)
    {
        if($observer->getEvent()->getAddress()->getGiftMessageId()) {
            $observer->getEvent()->getOrder()
                ->setGiftMessageId($observer->getEvent()->getAddress()->getGiftMessageId());
        }
        return $this;
    }

    /**
     * Set gift messages to order from quote address
     *
     * @param Varien_Object $observer
     * @return Mage_GiftMessage_Model_Observer
     */
    public function salesEventConvertQuoteToOrder($observer)
    {
        $observer->getEvent()->getOrder()
            ->setGiftMessageId($observer->getEvent()->getQuote()->getGiftMessageId());
        return $this;
    }

    /**
     * Geter for available gift messages value from product
     *
     * @param Mage_Catalog_Model_Product|integer $product
     * @return integer|null
     */
    protected function _getAvailable($product)
    {
        if(is_object($product)) {
            return $product->getGiftMessageAvailable();
        }
        return Mage::getModel('catalog/product')->load($product)->getGiftMessageAvailable();
    }

    /**
     * Operate with gift messages on checkout proccess
     *
     * @param Varieb_Object $observer
     * @return Mage_GiftMessage_Model_Observer
     */
    public function checkoutEventCreateGiftMessage($observer)
    {
        $giftMessages = $observer->getEvent()->getRequest()->getParam('giftmessage');
        $quote = $observer->getEvent()->getQuote();
        /* @var $quote Mage_Sales_Model_Quote */
        if(is_array($giftMessages)) {
            foreach ($giftMessages as $entityId=>$message) {

                $giftMessage = Mage::getModel('giftmessage/message');

                switch ($message['type']) {
                    case 'quote':
                        $entity = $quote;
                        break;
                    case 'quote_item':
                        $entity = $quote->getItemById($entityId);
                        break;
                    case 'quote_address':
                        $entity = $quote->getAddressById($entityId);
                        break;
                    case 'quote_address_item':
                        $entity = $quote->getAddressById($message['address'])->getItemById($entityId);
                        break;
                    default:
                        $entity = $quote;
                        break;
                }

                if($entity->getGiftMessageId()) {
                    $giftMessage->load($entity->getGiftMessageId());
                }

                if(trim($message['message'])=='') {
                    if($giftMessage->getId()) {
                        try{
                            $giftMessage->delete();
                            $entity->setGiftMessageId(0)
                                ->save();
                        }
                        catch (Exception $e) { }
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

                }
                catch (Exception $e) { }
            }
        }
        return $this;
    }

    /**
     * Set giftmessage available default value to product
     * on catalog products collection load
     *
     * @param Varien_Object $observer
     * @return Mage_GiftMessage_Model_Observer
     */
    public function catalogEventProductCollectionAfterLoad($observer)
    {
        $collection = $observer->getEvent()->getCollection();
        foreach ($collection as $item) {
            if($item->getGiftMessageAvailable()===null) {
                $item->setGiftMessageAvailable(2);
            }
        }
        return $this;
    }

    /**
     * Duplicates giftmessage from order to quote on import or reorder
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_GiftMessage_Model_Observer
     */
    public function salesEventOrderToQuote($observer)
    {
        if($giftMessageId = $observer->getEvent()->getOrder()->getGiftMessageId()) {
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
     * @param Varien_Event_Observer $observer
     * @return Mage_GiftMessage_Model_Observer
     */
    public function salesEventOrderItemToQuoteItem($observer)
    {
        $orderItem = $observer->getEvent()->getOrderItem();
        $quoteItem = $observer->getEvent()->getQuoteItem();
        /* @var $orderItem Mage_Sales_Model_Order_Item */
        /* @var $quoteItem Mage_Sales_Model_Quote_Item */
        if ($giftMessageId = $orderItem->getGiftMessageId()) {
            $giftMessage = Mage::getModel('giftmessage/message')->load($giftMessageId)
                ->setId(null)
                ->save();
            $quoteItem->setGiftMessageId($giftMessage->getId());
        }

        return $this;
    }
}
