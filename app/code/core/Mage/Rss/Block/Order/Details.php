<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Rss
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
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
        if (!Mage::helper('core')->isModuleOutputEnabled('Mage_GiftMessage')) {
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
        if (!Mage::helper('core')->isModuleOutputEnabled('Mage_GiftMessage')) {
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
