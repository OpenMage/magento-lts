<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 */

/**
 * Adminhtml sales order create gift message form
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 */
class Mage_GiftMessage_Block_Adminhtml_Sales_Order_Create_Form extends Mage_Adminhtml_Block_Template
{
    /**
     * Indicates that block can display gift message form
     *
     * @return bool
     */
    public function canDisplayGiftmessageForm()
    {
        /** @var Mage_GiftMessage_Helper_Message $helper */
        $helper = $this->helper('giftmessage/message');
        $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
        return $helper->getIsMessagesAvailable($helper::TYPE_ITEMS, $quote, $quote->getStore());
    }
}
