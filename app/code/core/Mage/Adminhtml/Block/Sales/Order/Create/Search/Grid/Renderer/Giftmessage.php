<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml sales create order product search grid giftmessage column renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @deprecated after 1.4.2.0 - gift column has been removed from search grid
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid_Renderer_Giftmessage extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Checkbox
{
    /**
     * Renders grid column
     *
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        /** @var Mage_GiftMessage_Helper_Message $helper */
        $helper = $this->helper('giftmessage/message');
        if (!$helper->getIsMessagesAvailable($helper::TYPE_ORDER_ITEM, $row, $this->getColumn()->getGrid()->getStore())) {
            return '&nbsp;';
        }

        return parent::render($row);
    }
}
