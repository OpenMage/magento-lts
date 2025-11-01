<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_GiftMessage
 */

/**
 * Gift Message helper
 *
 * @package    Mage_GiftMessage
 */
class Mage_GiftMessage_Helper_Message extends Mage_Core_Helper_Data
{
    /**
     * Giftmessages allow section in configuration
     */
    public const XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ITEMS = 'sales/gift_options/allow_items';

    public const XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ORDER = 'sales/gift_options/allow_order';

    public const TYPE_ADDRESS_ITEM  = 'address_item';

    public const TYPE_CONFIG        = 'config';

    public const TYPE_ITEM          = 'item';

    public const TYPE_ITEMS         = 'items';

    public const TYPE_ORDER         = 'order';

    public const TYPE_ORDER_ITEM    = 'order_item';

    protected $_moduleName = 'Mage_GiftMessage';

    /**
     * Next id for edit gift message block
     *
     * @var int
     */
    protected $_nextId = 0;

    /**
     * Inner cache
     *
     * @var array
     */
    protected $_innerCache = [];

    /**
     * Retrieve old stule edit button html for editing of giftmessage in popup
     *
     * @param string $type
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
     * Retrieve inline giftmessage edit form for specified entity
     *
     * @param string $type
     * @param bool $dontDisplayContainer
     * @return string
     */
    public function getInline($type, Varien_Object $entity, $dontDisplayContainer = false)
    {
        if (!in_array($type, ['onepage_checkout','multishipping_adress'])
            && !$this->isMessagesAvailable($type, $entity)
        ) {
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
     * @param self::TYPE_* $type $type
     * @param null|bool|int|Mage_Core_Model_Store|string $store
     * @return bool
     */
    public function isMessagesAvailable($type, Varien_Object $entity, $store = null)
    {
        switch ($type) {
            case self::TYPE_ITEMS:
                $items = $entity->getAllItems();
                if (!is_array($items) || empty($items)) {
                    return Mage::getStoreConfigFlag(self::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ITEMS, $store);
                }

                if ($entity instanceof Mage_Sales_Model_Quote) {
                    $_type = $entity->getIsMultiShipping() ? self::TYPE_ADDRESS_ITEM : self::TYPE_ITEM;
                } else {
                    $_type = self::TYPE_ORDER_ITEM;
                }

                foreach ($items as $item) {
                    if ($item->getParentItem()) {
                        continue;
                    }

                    return $this->isMessagesAvailable($_type, $item, $store);
                }
                // no break
            case self::TYPE_ITEM:
                return $this->_getDependenceFromStoreConfig(
                    $entity->getProduct()->getGiftMessageAvailable(),
                    $store,
                );
            case self::TYPE_ORDER_ITEM:
                return $this->_getDependenceFromStoreConfig(
                    $entity->getGiftMessageAvailable(),
                    $store,
                );
            case self::TYPE_ADDRESS_ITEM:
                $storeId = is_numeric($store) ? $store : Mage::app()->getStore($store)->getId();
                $cacheId = self::TYPE_ADDRESS_ITEM . '_' . $entity->getProductId();

                if (!$this->isCached($cacheId)) {
                    $this->setCached(
                        $cacheId,
                        Mage::getModel('catalog/product')
                            ->setStoreId($storeId)
                            ->load($entity->getProductId())
                            ->getGiftMessageAvailable(),
                    );
                }

                return $this->_getDependenceFromStoreConfig(
                    $this->getCached($cacheId),
                    $store,
                );
            default:
                return Mage::getStoreConfigFlag(self::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ORDER, $store);
        }
    }

    /**
     * Check availability of gift messages from store config if flag eq 2.
     *
     * @param bool $productGiftMessageAllow
     * @param null|bool|int|Mage_Core_Model_Store|string $store
     * @return bool
     */
    protected function _getDependenceFromStoreConfig($productGiftMessageAllow, $store = null)
    {
        $result = Mage::getStoreConfigFlag(self::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ITEMS, $store);
        if ($productGiftMessageAllow === '' || is_null($productGiftMessageAllow)) {
            return $result;
        } else {
            return $productGiftMessageAllow;
        }
    }

    /**
     * Alias for isMessagesAvailable(...)
     *
     * @param self::TYPE_* $type
     * @param null|bool|int|Mage_Core_Model_Store|string $store
     * @return bool
     */
    public function getIsMessagesAvailable($type, Varien_Object $entity, $store = null)
    {
        return $this->isMessagesAvailable($type, $entity, $store);
    }

    /**
     * Retrieve escaped and preformatted gift message text for specified entity
     *
     * @return null|string
     */
    public function getEscapedGiftMessage(Varien_Object $entity)
    {
        $message = $this->getGiftMessageForEntity($entity);
        if ($message) {
            return nl2br($this->escapeHtml($message->getMessage()));
        }

        return null;
    }

    /**
     * Retrieve gift message for entity. If message not exists return null
     *
     * @return Mage_GiftMessage_Model_Message
     */
    public function getGiftMessageForEntity(Varien_Object $entity)
    {
        if ($entity->getGiftMessageId() && !$entity->getGiftMessage()) {
            $message = $this->getGiftMessage($entity->getGiftMessageId());
            $entity->setGiftMessage($message);
        }

        return $entity->getGiftMessage();
    }

    /**
     * Retrieve internal cached data with specified key.
     *
     * If cached data not found return null.
     *
     * @param string $key
     * @return null|mixed
     */
    public function getCached($key)
    {
        if ($this->isCached($key)) {
            return $this->_innerCache[$key];
        }

        return null;
    }

    /**
     * Check availability for internal cached data with specified key
     *
     * @param string $key
     * @return bool
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
     * @return $this
     */
    public function setCached($key, $value)
    {
        $this->_innerCache[$key] = $value;
        return $this;
    }

    /**
     * Check availability for onepage checkout items
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param int|Mage_Core_Model_Store $store
     * @return bool
     */
    public function getAvailableForQuoteItems($quote, $store = null)
    {
        foreach ($quote->getAllItems() as $item) {
            if ($this->isMessagesAvailable('item', $item, $store)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check availability for multishiping checkout items
     *
     * @param array $items
     * @param int|Mage_Core_Model_Store $store
     * @return bool
     */
    public function getAvailableForAddressItems($items, $store = null)
    {
        foreach ($items as $item) {
            if ($this->isMessagesAvailable('address_item', $item, $store)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve gift message with specified id
     *
     * @param int $messageId
     * @return Mage_GiftMessage_Model_Message
     */
    public function getGiftMessage($messageId = null)
    {
        $message = Mage::getModel('giftmessage/message');
        if (!is_null($messageId)) {
            $message->load($messageId);
        }

        return $message;
    }
}
