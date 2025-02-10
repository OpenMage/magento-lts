<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Order_Invoice_Total_Cost extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * Collect total cost of invoiced items
     *
     * @return $this
     */
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $baseInvoiceTotalCost = 0;
        foreach ($invoice->getAllItems() as $item) {
            if (!$item->getOrderItem()->getHasChildren()) {
                $baseInvoiceTotalCost += $item->getBaseCost() * $item->getQty();
            }
        }
        $invoice->setBaseCost($baseInvoiceTotalCost);
        return $this;
    }
}
