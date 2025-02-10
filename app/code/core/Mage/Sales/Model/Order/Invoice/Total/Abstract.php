<?php
/**
 * Base class for invoice total
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Sales
 */
abstract class Mage_Sales_Model_Order_Invoice_Total_Abstract extends Mage_Sales_Model_Order_Total_Abstract
{
    /**
     * Collect invoice subtotal
     *
     * @return Mage_Sales_Model_Order_Invoice_Total_Abstract
     */
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        return $this;
    }
}
