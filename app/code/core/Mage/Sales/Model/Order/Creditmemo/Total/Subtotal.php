<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Order_Creditmemo_Total_Subtotal extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    /**
     * Collect Creditmemo subtotal
     *
     * @return Mage_Sales_Model_Order_Creditmemo_Total_Subtotal
     */
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $subtotal       = 0;
        $baseSubtotal   = 0;
        $subtotalInclTax = 0;
        $baseSubtotalInclTax = 0;

        foreach ($creditmemo->getAllItems() as $item) {
            if ($item->getOrderItem()->isDummy()) {
                continue;
            }

            $item->calcRowTotal();

            $subtotal       += $item->getRowTotal();
            $baseSubtotal   += $item->getBaseRowTotal();
            $subtotalInclTax += $item->getRowTotalInclTax();
            $baseSubtotalInclTax += $item->getBaseRowTotalInclTax();
        }

        $creditmemo->setSubtotal($subtotal);
        $creditmemo->setBaseSubtotal($baseSubtotal);
        $creditmemo->setSubtotalInclTax($subtotalInclTax);
        $creditmemo->setBaseSubtotalInclTax($baseSubtotalInclTax);

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $subtotal);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseSubtotal);

        return $this;
    }
}
