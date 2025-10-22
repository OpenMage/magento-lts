<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml sales order item renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_View_Items_Renderer_Default extends Mage_Adminhtml_Block_Sales_Items_Abstract
{
    public function getItem()
    {
        return $this->_getData('item');
    }

    /**
     * Retrieve real html id for field
     *
     * @param string $id
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
        return 'order_item_' . $this->getItem()->getId() . '_';
    }

    /**
     * Indicate that block can display container
     *
     * @return bool
     * @throws Exception
     */
    public function canDisplayContainer()
    {
        return $this->getRequest()->getParam('reload') != 1;
    }

    /**
     * Giftmessage object
     *
     * @deprecated after 1.4.2.0
     * @var Mage_GiftMessage_Model_Message
     */
    protected $_giftMessage = [];

    /**
     * Retrieve default value for giftmessage sender
     *
     * @deprecated after 1.4.2.0
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
     * @deprecated after 1.4.2.0
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
     * @deprecated after 1.4.2.0
     * @param string $name
     * @return string
     */
    public function getFieldName($name)
    {
        return 'giftmessage[' . $this->getItem()->getId() . '][' . $name . ']';
    }

    /**
     * Initialize gift message for entity
     *
     * @deprecated after 1.4.2.0
     * @return $this
     */
    protected function _initMessage()
    {
        /** @var Mage_GiftMessage_Helper_Message $helper */
        $helper = $this->helper('giftmessage/message');

        $this->_giftMessage[$this->getItem()->getGiftMessageId()] =
            $helper->getGiftMessage($this->getItem()->getGiftMessageId());

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
     * @deprecated after 1.4.2.0
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
     * @deprecated after 1.4.2.0
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
     * @deprecated after 1.4.2.0
     * @return string
     */
    public function getHtmlId()
    {
        return substr($this->getFieldIdPrefix(), 0, -1);
    }

    /**
     * Indicates that block can display giftmessages form
     *
     * TODO set return type
     * @return bool
     */
    public function canDisplayGiftmessage()
    {
        if (!$this->isModuleOutputEnabled('Mage_GiftMessage')) {
            return false;
        }

        /** @var Mage_GiftMessage_Helper_Message $helper */
        $helper = $this->helper('giftmessage/message');
        return $helper->getIsMessagesAvailable(
            $helper::TYPE_ORDER_ITEM,
            $this->getItem(),
            $this->getItem()->getOrder()->getStoreId(),
        );
    }

    /**
     * Display susbtotal price including tax
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @return string
     */
    public function displaySubtotalInclTax($item)
    {
        /** @var Mage_Checkout_Helper_Data $helper */
        $helper = $this->helper('checkout');
        return $this->displayPrices(
            $helper->getBaseSubtotalInclTax($item),
            $helper->getSubtotalInclTax($item),
        );
    }

    /**
     * Display item price including tax
     *
     * @return string
     */
    public function displayPriceInclTax(Varien_Object $item)
    {
        /** @var Mage_Checkout_Helper_Data $helper */
        $helper = $this->helper('checkout');
        return $this->displayPrices(
            $helper->getBasePriceInclTax($item),
            $helper->getPriceInclTax($item),
        );
    }
}
