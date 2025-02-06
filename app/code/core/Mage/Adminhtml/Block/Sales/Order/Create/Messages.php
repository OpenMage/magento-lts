<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Order create errors block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Messages extends Mage_Adminhtml_Block_Messages
{
    public function _prepareLayout()
    {
        $this->addMessages(Mage::getSingleton('adminhtml/session_quote')->getMessages(true));
        return parent::_prepareLayout();
    }
}
