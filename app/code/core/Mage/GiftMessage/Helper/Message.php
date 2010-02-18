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
 * Gift Message helper
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_GiftMessage_Helper_Message extends Mage_Core_Helper_Data
{
    /**
     * Giftmessages allow section in configuration
     *
     */
    const XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ITEMS = 'sales/gift_messages/allow_items';
    const XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ORDER = 'sales/gift_messages/allow_order';

    /**
     * Next id for edit gift message block
     *
     * @var integer
     */
    protected $_nextId = 0;

    /**
     * Inner cache
     *
     * @var array
     */
    protected $_innerCache = array();

    /**
     * Retrive old stule edit button html for editing of giftmessage in popup
     *
     * @param string $type
     * @param Varien_Object $entity
     * @return string
     */
    public function getButton($type, Varien_Object $entity)
    {
        if (!$this->isMessagesAvailable($type, $entity)) {
            return '&nbsp;';
        }

        return Mage::getSingleton('core/layout')->createBlock('giftmessage/message_helper')
            ->setId('giftmessage_button_' . $this->_nextId++)
            ->setCanDisplayContainer(true)
            ->setEntity($entity)
            ->setType($type)->toHtml();
    }

    /**
     * Retrive inline giftmessage edit form for specified entity
     *
     * @param string $type
     * @param Varien_Object $entity
     * @param boolean $dontDisplayContainer
     * @return string
     */
    public function getInline($type, Varien_Object $entity, $dontDisplayContainer=false)
    {
        if (in_array($type, array('onepage_checkout','multishipping_adress'))) {
            if (!$this->isMessagesAvailable('items', $entity)) {
                return '';
            }
        } elseif (!$this->isMessagesAvailable($type, $entity)) {
            return '';
        }

        return Mage::getSingleton('core/layout')->createBlock('giftmessage/message_inline')
            ->setId('giftmessage_form_' . $this->_nextId++)
            ->setDontDisplayContainer($dontDisplayContainer)
            ->setEntity($entity)
            ->setType($type)->toHtml();
    }

    /**
     * Check availability of giftmessages for specified entity.
     *
     * @param string $type
     * @param Varien_Object $entity
     * @param Mage_Core_Model_Store|integer $store
     * @return boolean
     */
    public function isMessagesAvailable($type, Varien_Object $entity, $store=null)
    {
        $resultItems = Mage::getStoreConfig(self::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ITEMS, $store);
        $resultOrder = Mage::getStoreConfig(self::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ORDER, $store);

        if ($type == 'items') {
            return $resultItems || $resultOrder;
        }

        if (is_object($store)) {
            $storeId = $store->getId();
        } elseif (is_numeric($store)) {
            $storeId = $store;
        } else {
            $storeId = Mage::app()->getStore()->getId();
        }

        if ($type=='item') {
            return !$entity->getProduct()->isVirtual() && $resultItems && $this->_getDependenceFromStoreConfig(
                        $entity->getProduct()->getGiftMessageAvailable(),
                        $store
                   );
        } elseif ($type=='order_item') {
            return !$entity->getIsVirtual() && $resultItems && $this->_getDependenceFromStoreConfig(
                        (is_null($entity->getGiftMessageAvailable()) ? 2 : $entity->getGiftMessageAvailable()),
                        $store
                    );
        } elseif ($type=='address_item') {
            if (!$resultItems) {
                return false;
            }
            if (!$this->isCached('address_item_' . $entity->getProductId())) {
                $this->setCached(
                    'address_item_' . $entity->getProductId(),
                    Mage::getModel('catalog/product')
                        ->setStoreId($storeId)
                        ->load($entity->getProductId())
                        ->getGiftMessageAvailable()
                );
            }
            return !$entity->getProduct()->isVirtual() && $this->_getDependenceFromStoreConfig(
                        $this->getCached('address_item_' . $entity->getProductId()),
                        $store
                   );
        } else {
            return $resultOrder;
        }

        return false;
    }

    /**
     * Check availablity of gift messages from store config if flag eq 2.
     *
     * @param int $productGiftMessageAllow
     * @param Mage_Core_Model_Store|integer $store
     * @return boolean
     */
    protected function _getDependenceFromStoreConfig($productGiftMessageAllow, $store=null)
    {
        $result = Mage::getStoreConfig(self::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ITEMS, $store);

        if ($productGiftMessageAllow==2 || is_null($productGiftMessageAllow)) {
            return $result;
        } else {
            return $productGiftMessageAllow == 1;
        }
    }

    /**
     * Alias for isMessagesAvailable(...)
     *
     * @param string $type
     * @param Varien_Object $entity
     * @param Mage_Core_Model_Store|integer $store
     * @return boolen
     */
    public function getIsMessagesAvailable($type, Varien_Object $entity, $store=null)
    {
        return $this->isMessagesAvailable($type, $entity, $store);
    }

    /**
     * Retrive escaped and preformated gift message text for specified entity
     *
     * @param Varien_Object $entity
     * @return unknown
     */
    public function getEscapedGiftMessage(Varien_Object $entity)
    {
        $message = $this->getGiftMessageForEntity($entity);
        if ($message) {
            return nl2br($this->htmlEscape($message->getMessage()));
        }
        return null;
    }

    /**
     * Retrive gift message for entity. If message not exists return null
     *
     * @param Varien_Object $entity
     * @return Mage_GiftMessage_Model_Message
     */
    public function getGiftMessageForEntity(Varien_Object $entity)
    {
        if($entity->getGiftMessageId() && !$entity->getGiftMessage()) {
            $message = $this->getGiftMessage($entity->getGiftMessageId());
            $entity->setGiftMessage($message);
        }
        return $entity->getGiftMessage();
    }

    /**
     * Retrive internal cached data with specified key.
     *
     * If cached data not found return null.
     *
     * @param string $key
     * @return mixed|null
     */
    public function getCached($key)
    {
        if($this->isCached($key)) {
            return $this->_innerCache[$key];
        }

        return null;
    }

    /**
     * Check availability for internal cached data with specified key
     *
     * @param string $key
     * @return boolean
     */
    public function isCached($key)
    {
        return isset($this->_innerCache[$key]);
    }

    /**
     * Set internal cache data with specified key
     *
     * @param string $key
     * @param mixed $value
     * @return Mage_GiftMessage_Helper_Message
     */
    public function setCached($key, $value)
    {
        $this->_innerCache[$key] = $value;
        return $this;
    }

    /**
     * Check availability for onepage checkout items
     *
     * @param array $items
     * @param Mage_Core_Model_Store|integer $store
     * @return boolen
     */
    public function getAvailableForQuoteItems($quote, $store=null)
    {
        foreach($quote->getAllItems() as $item) {
            if($this->isMessagesAvailable('item', $item, $store)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check availability for multishiping checkout items
     *
     * @param array $items
     * @param Mage_Core_Model_Store|integer $store
     * @return boolen
     */
    public function getAvailableForAddressItems($items, $store=null)
    {
        foreach($items as $item) {
            if($this->isMessagesAvailable('address_item', $item, $store)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retrive gift message with specified id
     *
     * @param integer $messageId
     * @return Mage_GiftMessage_Model_Message
     */
    public function getGiftMessage($messageId=null)
    {
        $message = Mage::getModel('giftmessage/message');
        if(!is_null($messageId)) {
            $message->load($messageId);
        }

        return $message;
    }

}
