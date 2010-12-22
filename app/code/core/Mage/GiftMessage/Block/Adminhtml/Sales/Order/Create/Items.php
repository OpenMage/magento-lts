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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Gift message adminhtml sales order create items
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GiftMessage_Block_Adminhtml_Sales_Order_Create_Items extends Mage_Adminhtml_Block_Sales_Items_Abstract
{
    /**
     * Get Order Item
     *
     * @return Mage_Sales_Model_Quote_Item
     */
    public function getItem()
    {
        $item = $this->getParentBlock()->getData('item');
        return $item;
    }


    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        $_item = $this->getItem();
        if ($_item && $this->isGiftMessagesAvailable($_item)) {
            return parent::_toHtml();
        } else {
            return false;
        }
    }

    /**
     * Check available Gift Messages
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return boolean
     */
    public function isGiftMessagesAvailable($item=null)
    {
        if(is_null($item)) {
            return $this->helper('giftmessage/message')->getIsMessagesAvailable(
                'items', $this->getQuote(), $this->getStore()
            );
        }

        return $this->helper('giftmessage/message')->getIsMessagesAvailable(
            'item', $item, $this->getStore()
        );
    }

    /**
     * Checks allowed quote item for gift messages
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return boolean
     */
    public function isAllowedForGiftMessage($item)
    {

        return Mage::getSingleton('adminhtml/giftmessage_save')->getIsAllowedQuoteItem($item);
    }

}
