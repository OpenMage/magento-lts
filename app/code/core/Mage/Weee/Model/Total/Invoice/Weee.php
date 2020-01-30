<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Weee
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Weee_Model_Total_Invoice_Weee extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * Weee tax collector
     *
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return Mage_Weee_Model_Total_Invoice_Weee
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
                'regular', true
            );
            $baseWeeeRowDiscountAmount = $orderItem->getBaseDiscountAppliedForWeeeTax();
            $baseWeeeDiscountAmount = $invoice->roundPrice(
                $baseWeeeRowDiscountAmount / $orderItemQty * $item->getQty(),
                'base', true
            );
            $weeeTaxAmount = $item->getWeeeTaxAppliedAmount() * $item->getQty();
            $baseWeeeTaxAmount = $item->getBaseWeeeTaxAppliedAmount() * $item->getQty();

            $weeeTaxAmountInclTax = Mage::helper('weee')->getWeeeTaxInclTax($item) * $item->getQty();
            $baseWeeeTaxAmountInclTax = Mage::helper('weee')->getBaseWeeeTaxInclTax($item) * $item->getQty();

            $item->setWeeeTaxAppliedRowAmount($weeeTaxAmount);
            $item->setBaseWeeeTaxAppliedRowAmount($baseWeeeTaxAmount);
            $newApplied = array();
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
