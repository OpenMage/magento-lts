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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Sales_Model_Order_Invoice_Total_Tax extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $totalTax = 0;
        $baseTotalTax = 0;
        $order = $invoice->getOrder();
        foreach ($invoice->getAllItems() as $item) {
            $orderItem = $item->getOrderItem();
            $orderItemTax       = $orderItem->getTaxAmount();
            $baseOrderItemTax   = $orderItem->getBaseTaxAmount();
            $orderItemQty = $orderItem->getQtyOrdered();

            if ($orderItemTax && $orderItemQty) {
                if ($item->getOrderItem()->isDummy()) {
                    continue;
                }
                /**
                 * Resolve rounding problems
                 */
                if ($item->isLast()) {
                    $tax = $orderItemTax - $orderItem->getTaxInvoiced();
                    $baseTax = $baseOrderItemTax - $orderItem->getBaseTaxInvoiced();
                }
                else {
                    $tax = $orderItemTax*$item->getQty()/$orderItemQty;
                    $baseTax = $baseOrderItemTax*$item->getQty()/$orderItemQty;

                    $tax = $invoice->getStore()->roundPrice($tax);
                    $baseTax = $invoice->getStore()->roundPrice($baseTax);
                }

                $item->setTaxAmount($tax);
                $item->setBaseTaxAmount($baseTax);

                $totalTax += $tax;
                $baseTotalTax += $baseTax;
            }
        }

        $includeShippingTax = true;
        /**
         * Check shipping amount in previus invoices
         */
        foreach ($order->getInvoiceCollection() as $previusInvoice) {
            if ($previusInvoice->getShippingAmount() && !$previusInvoice->isCanceled()) {
                $includeShippingTax = false;
            }
        }

        if ($includeShippingTax) {
            $totalTax += $order->getShippingTaxAmount();
            $baseTotalTax += $order->getBaseShippingTaxAmount();
            $invoice->setShippingTaxAmount($order->getShippingTaxAmount());
            $invoice->setBaseShippingTaxAmount($order->getBaseShippingTaxAmount());
        }

        $allowedTax = $order->getTaxAmount() - $order->getTaxInvoiced();
        $allowedBaseTax = $order->getBaseTaxAmount() - $order->getBaseTaxInvoiced();;

        $totalTax = min($allowedTax, $totalTax);
        $baseTotalTax = min($allowedBaseTax, $baseTotalTax);

        $invoice->setTaxAmount($totalTax);
        $invoice->setBaseTaxAmount($baseTotalTax);

        $invoice->setGrandTotal($invoice->getGrandTotal() + $totalTax);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseTotalTax);

        return $this;
    }
}
