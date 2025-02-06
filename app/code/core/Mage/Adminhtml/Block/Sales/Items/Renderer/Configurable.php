<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml sales order item renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Items_Renderer_Configurable extends Mage_Adminhtml_Block_Sales_Items_Abstract
{
    public function getItem()
    {
        return $this->_getData('item');//->getOrderItem();
    }
}
