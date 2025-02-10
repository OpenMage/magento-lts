<?php
/**
 * Adminhtml sales order create gift options block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_GiftMessage
 */
class Mage_GiftMessage_Block_Adminhtml_Sales_Order_Create_Giftoptions extends Mage_Adminhtml_Block_Template
{
    /**
     * Get order item object from parent block
     *
     * @return Mage_Sales_Model_Order_Item
     */
    public function getItem()
    {
        return $this->getParentBlock()->getData('item');
    }
}
