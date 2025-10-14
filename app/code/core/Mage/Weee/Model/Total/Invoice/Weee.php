<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Weee
 */

/**
 * @package    Mage_Weee
 */
class Mage_Weee_Model_Total_Invoice_Weee extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * Weee tax collector
     *
     * @return $this
     */
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $store = $invoice->getStore();

        $totalTax = 0;
        $baseTotalTax = 0;
        $weeeInclTax = 0;
        $baseWeeeInclTax = 0;

        foreach ($invoice->getAllItems() as $item) {
            $orderItem = $item->getOrderItem();
            $orderItemQty = $orderItem->getQtyOrdered();

            if (!$orderItemQty || $orderItem->isDummy()) {
                continue;
            }

            $weeeRowDiscountAmount = $orderItem->getDiscountAppliedForWeeeTax();
            $weeeDiscountAmount = $invoice->roundPrice(
                $weeeRowDiscountAmount / $orderItemQty * $item->getQty(),
                'regular',
                true,
            );
            $baseWeeeRowDiscountAmount = $orderItem->getBaseDiscountAppliedForWeeeTax();
            $baseWeeeDiscountAmount = $invoice->roundPrice(
                $baseWeeeRowDiscountAmount / $orderItemQty * $item->getQty(),
                'base',
                true,
            );
            $weeeTaxAmount = $item->getWeeeTaxAppliedAmount() * $item->getQty();
            $baseWeeeTaxAmount = $item->getBaseWeeeTaxAppliedAmount() * $item->getQty();

            $weeeTaxAmountInclTax = Mage::helper('weee')->getWeeeTaxInclTax($item) * $item->getQty();
            $baseWeeeTaxAmountInclTax = Mage::helper('weee')->getBaseWeeeTaxInclTax($item) * $item->getQty();

            $item->setWeeeTaxAppliedRowAmount($weeeTaxAmount);
            $item->setBaseWeeeTaxAppliedRowAmount($baseWeeeTaxAmount);
            $newApplied = [];
            $applied = Mage::helper('weee')->getApplied($item);
            foreach ($applied as $one) {
                $one['base_row_amount'] = $one['base_amount'] * $item->getQty();
                $one['row_amount'] = $one['amount'] * $item->getQty();
                $one['base_row_amount_incl_tax'] = $one['base_amount_incl_tax'] * $item->getQty();
                $one['row_amount_incl_tax'] = $one['amount_incl_tax'] * $item->getQty();

                $one['weee_discount'] = $weeeDiscountAmount;
                $one['base_weee_discount'] = $baseWeeeDiscountAmount;

                $newApplied[] = $one;
            }

            Mage::helper('weee')->setApplied($item, $newApplied);

            $item->setWeeeTaxRowDisposition($item->getWeeeTaxDisposition() * $item->getQty());
            $item->setBaseWeeeTaxRowDisposition($item->getBaseWeeeTaxDisposition() * $item->getQty());

            $totalTax += $weeeTaxAmount - $weeeDiscountAmount;
            $baseTotalTax += $baseWeeeTaxAmount - $baseWeeeDiscountAmount;

            $weeeInclTax += $weeeTaxAmountInclTax;
            $baseWeeeInclTax += $baseWeeeTaxAmountInclTax;
        }

        /*
         * Add FPT to totals
         * Notice that we check restriction on allowed tax, because
         * a) for last invoice we don't need to collect FPT - it is automatically collected by subtotal/tax collector,
         * that adds whole remaining (not invoiced) subtotal/tax value, so fpt is automatically included into it
         * b) FPT tax is included into order subtotal/tax value, so after multiple invoices with partial item quantities
         * it can happen that other collector will take some FPT value from shared subtotal/tax order value
         */
        $order = $invoice->getOrder();
        if (Mage::helper('weee')->includeInSubtotal($store)) {
            $allowedSubtotal = $order->getSubtotal() - $order->getSubtotalInvoiced() - $invoice->getSubtotal();
            $allowedBaseSubtotal = $order->getBaseSubtotal() - $order->getBaseSubtotalInvoiced()
                - $invoice->getBaseSubtotal();
            $totalTax = min($allowedSubtotal, $totalTax);
            $baseTotalTax = min($allowedBaseSubtotal, $baseTotalTax);

            $invoice->setSubtotal($invoice->getSubtotal() + $totalTax);
            $invoice->setBaseSubtotal($invoice->getBaseSubtotal() + $baseTotalTax);
        } else {
            $allowedTax = $order->getTaxAmount() - $order->getTaxInvoiced() - $invoice->getTaxAmount();
            $allowedBaseTax = $order->getBaseTaxAmount() - $order->getBaseTaxInvoiced() - $invoice->getBaseTaxAmount();
            $totalTax = min($allowedTax, $totalTax);
            $baseTotalTax = min($allowedBaseTax, $baseTotalTax);

            $invoice->setTaxAmount($invoice->getTaxAmount() + $totalTax);
            $invoice->setBaseTaxAmount($invoice->getBaseTaxAmount() + $baseTotalTax);
        }

        if (!$invoice->isLast()) {
            $invoice->setSubtotalInclTax($invoice->getSubtotalInclTax() + $weeeInclTax);
            $invoice->setBaseSubtotalInclTax($invoice->getBaseSubtotalInclTax() + $baseWeeeInclTax);
        }

        $invoice->setGrandTotal($invoice->getGrandTotal() + $totalTax);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseTotalTax);

        return $this;
    }
}
