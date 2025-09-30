<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Order create errors block
 *
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
