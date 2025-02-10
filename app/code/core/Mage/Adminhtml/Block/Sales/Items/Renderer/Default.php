<?php
/**
 * Adminhtml sales order item renderer
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Items_Renderer_Default extends Mage_Adminhtml_Block_Sales_Items_Abstract
{
    public function getItem()
    {
        return $this->_getData('item');//->getOrderItem();
    }
}
