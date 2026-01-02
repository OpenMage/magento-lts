<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_GiftMessage
 */

/**
 * Gift message adminhtml sales order view items
 *
 * @package    Mage_GiftMessage
 */
class Mage_GiftMessage_Block_Adminhtml_Sales_Order_View_Items extends Mage_Adminhtml_Block_Template
{
    /**
     * Gift message array
     *
     * @var array
     */
    protected $_giftMessage = [];

    /**
     * Get Order Item
     *
     * @return Mage_Sales_Model_Order_Item
     */
    public function getItem()
    {
        return $this->getParentBlock()->getItem();
    }

    /**
     * Retrieve default value for giftmessage sender
     *
     * @return string
     */
    public function getDefaultSender()
    {
        if (!$this->getItem()) {
            return '';
        }

        if ($this->getItem()->getOrder()) {
            return $this->getItem()->getOrder()->getBillingAddress()->getName();
        }

        return $this->getItem()->getBillingAddress()->getName();
    }

    /**
     * Retrieve default value for giftmessage recipient
     *
     * @return string
     */
    public function getDefaultRecipient()
    {
        if (!$this->getItem()) {
            return '';
        }

        if ($this->getItem()->getOrder()) {
            if ($this->getItem()->getOrder()->getShippingAddress()) {
                return $this->getItem()->getOrder()->getShippingAddress()->getName();
            }

            if ($this->getItem()->getOrder()->getBillingAddress()) {
                return $this->getItem()->getOrder()->getBillingAddress()->getName();
            }
        }

        if ($this->getItem()->getShippingAddress()) {
            return $this->getItem()->getShippingAddress()->getName();
        }

        if ($this->getItem()->getBillingAddress()) {
            return $this->getItem()->getBillingAddress()->getName();
        }

        return '';
    }

    /**
     * Retrieve real name for field
     *
     * @param  string $name
     * @return string
     */
    public function getFieldName($name)
    {
        return 'giftmessage[' . $this->getItem()->getId() . '][' . $name . ']';
    }

    /**
     * Retrieve real html id for field
     *
     * @param  string $id
     * @return string
     */
    public function getFieldId($id)
    {
        return $this->getFieldIdPrefix() . $id;
    }

    /**
     * Retrieve field html id prefix
     *
     * @return string
     */
    public function getFieldIdPrefix()
    {
        return 'giftmessage_' . $this->getItem()->getId() . '_';
    }

    /**
     * Initialize gift message for entity
     *
     * @return Mage_GiftMessage_Block_Adminhtml_Sales_Order_View_Items
     */
    protected function _initMessage()
    {
        /** @var Mage_GiftMessage_Helper_Message $helper */
        $helper = $this->helper('giftmessage/message');
        $this->_giftMessage[$this->getItem()->getGiftMessageId()] = $helper->getGiftMessage($this->getItem()->getGiftMessageId());

        // init default values for giftmessage form
        if (!$this->getMessage()->getSender()) {
            $this->getMessage()->setSender($this->getDefaultSender());
        }

        if (!$this->getMessage()->getRecipient()) {
            $this->getMessage()->setRecipient($this->getDefaultRecipient());
        }

        return $this;
    }

    /**
     * Retrieve gift message for entity
     *
     * @return Mage_GiftMessage_Model_Message
     */
    public function getMessage()
    {
        if (!isset($this->_giftMessage[$this->getItem()->getGiftMessageId()])) {
            $this->_initMessage();
        }

        return $this->_giftMessage[$this->getItem()->getGiftMessageId()];
    }

    /**
     * Retrieve save url
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/sales_order_view_giftmessage/save', [
            'entity'    => $this->getItem()->getId(),
            'type'      => 'order_item',
            'reload'    => true,
        ]);
    }

    /**
     * Retrieve block html id
     *
     * @return string
     */
    public function getHtmlId()
    {
        return substr($this->getFieldIdPrefix(), 0, -1);
    }

    /**
     * Indicates that block can display giftmessages form
     *
     * @return bool
     */
    public function canDisplayGiftmessage()
    {
        return $this->getItem()->getGiftMessageId();
    }

    /**
     * Retrieve gift message sender
     *
     * @return string
     */
    public function getSender()
    {
        return $this->escapeHtml($this->getMessage()->getSender());
    }

    /**
     * Retrieve gift message recipient
     *
     * @return string
     */
    public function getRecipient()
    {
        return $this->escapeHtml($this->getMessage()->getRecipient());
    }

    /**
     * Retrieve gift message text
     *
     * @return string
     */
    public function getMessageText()
    {
        return $this->escapeHtml($this->getMessage()->getMessage());
    }
}
