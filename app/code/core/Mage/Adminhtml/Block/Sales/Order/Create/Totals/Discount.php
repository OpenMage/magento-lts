<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Subtotal Total Row Renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Totals_Discount extends Mage_Adminhtml_Block_Sales_Order_Create_Totals_Default
{
    //protected $_template = 'tax/checkout/subtotal.phtml';

    public function displayBoth()
    {
        return Mage::getSingleton('tax/config')->displayCartSubtotalBoth();
    }
}
