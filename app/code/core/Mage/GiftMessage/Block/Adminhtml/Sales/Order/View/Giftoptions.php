<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 */

/**
 * Adminhtml sales order view gift options block
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 */
class Mage_GiftMessage_Block_Adminhtml_Sales_Order_View_Giftoptions extends Mage_Adminhtml_Block_Template
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
