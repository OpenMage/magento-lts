<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Rss
 */
class Mage_Rss_Block_Order_Details extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('rss/order/details.phtml');
    }

    public function getGiftMessageItem(Mage_Sales_Model_Order_Item $item): ?Mage_GiftMessage_Model_Message
    {
        if (!$this->isModuleOutputEnabled('Mage_GiftMessage')) {
            return null;
        }
        /** @var Mage_GiftMessage_Helper_Message $helper */
        $helper = $this->helper('giftmessage/message');
        if ($item->getGiftMessageId()) {
            return $helper->getGiftMessage($item->getGiftMessageId());
        }
        return null;
    }
    public function getGiftMessageOrder(): ?Mage_GiftMessage_Model_Message
    {
        $order = $this->getOrder();
        if (!$this->isModuleOutputEnabled('Mage_GiftMessage')) {
            return null;
        }
        /** @var Mage_GiftMessage_Helper_Message $helper */
        $helper = $this->helper('giftmessage/message');
        if ($order->getGiftMessageId()) {
            return $helper->getGiftMessage($order->getGiftMessageId());
        }
        return null;
    }
}
