<?php
/**
 * Subtotal Total Row Renderer
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Tax
 */
class Mage_Tax_Block_Checkout_Grandtotal extends Mage_Checkout_Block_Total_Default
{
    protected $_template = 'tax/checkout/grandtotal.phtml';

    /**
     * Check if we have include tax amount between grandtotal incl/excl tax
     *
     * @return bool
     */
    public function includeTax()
    {
        if ($this->getTotal()->getAddress()->getGrandTotal()) {
            return Mage::getSingleton('tax/config')->displayCartTaxWithGrandTotal($this->getStore());
        }
        return false;
    }

    /**
     * Get grandtotal exclude tax
     *
     * @return float
     */
    public function getTotalExclTax()
    {
        $excl = $this->getTotal()->getAddress()->getGrandTotal() - $this->getTotal()->getAddress()->getTaxAmount();
        return max($excl, 0);
    }
}
