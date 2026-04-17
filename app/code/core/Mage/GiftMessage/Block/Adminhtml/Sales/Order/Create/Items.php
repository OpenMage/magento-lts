<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_GiftMessage
 */

/**
 * Gift message adminhtml sales order create items
 *
 * @package    Mage_GiftMessage
 */
class Mage_GiftMessage_Block_Adminhtml_Sales_Order_Create_Items extends Mage_Adminhtml_Block_Template
{
    /**
     * Get order item
     *
     * @return Mage_Sales_Model_Quote_Item
     */
    public function getItem()
    {
        return $this->getParentBlock()->getItem();
    }

    /**
     * Indicates that block can display gift messages form
     *
     * TODO set return type
     * @return bool
     */
    public function canDisplayGiftMessage()
    {
        $item = $this->getItem();
        if (!$item) {
            return false;
        }

        /** @var Mage_GiftMessage_Helper_Message $helper */
        $helper = $this->helper('giftmessage/message');
        return $helper->getIsMessagesAvailable(
            $helper::TYPE_ITEM,
            $item,
            $item->getStoreId(),
        );
    }

    /**
     * Return form html
     *
     * @return string
     */
    public function getFormHtml()
    {
        return $this->getLayout()->createBlock('adminhtml/sales_order_create_giftmessage_form')
            ->setEntity($this->getItem())
            ->setEntityType('item')
            ->toHtml();
    }

    /**
     * Retrieve gift message for item
     *
     * @return string
     */
    public function getMessageText()
    {
        if ($this->getItem()->getGiftMessageId()) {
            /** @var Mage_GiftMessage_Helper_Message $helper */
            $helper = $this->helper('giftmessage/message');
            $model = $helper->getGiftMessage($this->getItem()->getGiftMessageId());
            return $this->escapeHtml($model->getMessage());
        }

        return '';
    }
}
