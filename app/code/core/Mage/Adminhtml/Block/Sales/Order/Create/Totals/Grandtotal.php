<?php
/**
 * Subtotal Total Row Renderer
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Totals_Grandtotal extends Mage_Adminhtml_Block_Sales_Order_Create_Totals_Default
{
    protected $_template = 'sales/order/create/totals/grandtotal.phtml';

    public function includeTax()
    {
        return Mage::getSingleton('tax/config')->displayCartTaxWithGrandTotal();
    }

    public function getTotalExclTax()
    {
        $excl = $this->getTotal()->getAddress()->getGrandTotal() - $this->getTotal()->getAddress()->getTaxAmount();
        return max($excl, 0);
    }
}
