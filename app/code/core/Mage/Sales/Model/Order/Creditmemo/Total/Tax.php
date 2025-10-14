<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Class Mage_Sales_Model_Order_Creditmemo_Total_Tax
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Order_Creditmemo_Total_Tax extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    /**
     * Collects the total tax for the credit memo
     *
     * @return $this
     */
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $shippingTaxAmount     = 0;
        $baseShippingTaxAmount = 0;
        $totalTax              = 0;
        $baseTotalTax          = 0;
        $totalHiddenTax        = 0;
        $baseTotalHiddenTax    = 0;
        $weeeTaxAmount         = 0;
        $baseWeeeTaxAmount     = 0;

        $order = $creditmemo->getOrder();

        foreach ($creditmemo->getAllItems() as $item) {
            $orderItem = $item->getOrderItem();
            if ($orderItem->isDummy()) {
                continue;
            }

            $orderItemTax     = $orderItem->getTaxInvoiced();
            $baseOrderItemTax = $orderItem->getBaseTaxInvoiced();
            $orderItemHiddenTax = $orderItem->getHiddenTaxInvoiced();
            $baseOrderItemHiddenTax = $orderItem->getBaseHiddenTaxInvoiced();
            $orderItemQty     = $orderItem->getQtyInvoiced();

            if (($orderItemTax || $orderItemHiddenTax) && $orderItemQty) {
                /**
                 * Check item tax amount
                 */
                $tax            = $orderItemTax - $orderItem->getTaxRefunded();
                $baseTax        = $baseOrderItemTax - $orderItem->getBaseTaxRefunded();
                $hiddenTax      = $orderItemHiddenTax - $orderItem->getHiddenTaxRefunded();
                $baseHiddenTax  = $baseOrderItemHiddenTax - $orderItem->getBaseHiddenTaxRefunded();
                if (!$item->isLast()) {
                    $availableQty  = $orderItemQty - $orderItem->getQtyRefunded();
                    $tax           = $creditmemo->roundPrice($tax / $availableQty * $item->getQty());
                    $baseTax       = $creditmemo->roundPrice($baseTax / $availableQty * $item->getQty(), 'base');
                    $hiddenTax     = $creditmemo->roundPrice($hiddenTax / $availableQty * $item->getQty());
                    $baseHiddenTax = $creditmemo->roundPrice($baseHiddenTax / $availableQty * $item->getQty(), 'base');
                }

                $item->setTaxAmount($tax);
                $item->setBaseTaxAmount($baseTax);
                $item->setHiddenTaxAmount($hiddenTax);
                $item->setBaseHiddenTaxAmount($baseHiddenTax);

                $totalTax += $tax;
                $baseTotalTax += $baseTax;
                $totalHiddenTax += $hiddenTax;
                $baseTotalHiddenTax += $baseHiddenTax;
            }
        }

        $invoice = $creditmemo->getInvoice();

        if ($invoice) {
            //recalculate tax amounts in case if refund shipping value was changed
            if ($order->getBaseShippingAmount() && $creditmemo->getBaseShippingAmount()) {
                $taxFactor = $creditmemo->getBaseShippingAmount() / $order->getBaseShippingAmount();
                $shippingTaxAmount           = $invoice->getShippingTaxAmount() * $taxFactor;
                $baseShippingTaxAmount       = $invoice->getBaseShippingTaxAmount() * $taxFactor;
                $totalHiddenTax             += $invoice->getShippingHiddenTaxAmount() * $taxFactor;
                $baseTotalHiddenTax         += $invoice->getBaseShippingHiddenTaxAmount() * $taxFactor;
                $shippingHiddenTaxAmount     = $invoice->getShippingHiddenTaxAmount() * $taxFactor;
                $baseShippingHiddenTaxAmount = $invoice->getBaseShippingHiddenTaxAmount() * $taxFactor;
                $shippingTaxAmount           = $creditmemo->roundPrice($shippingTaxAmount);
                $baseShippingTaxAmount       = $creditmemo->roundPrice($baseShippingTaxAmount, 'base');
                $totalHiddenTax              = $creditmemo->roundPrice($totalHiddenTax);
                $baseTotalHiddenTax          = $creditmemo->roundPrice($baseTotalHiddenTax, 'base');
                $shippingHiddenTaxAmount     = $creditmemo->roundPrice($shippingHiddenTaxAmount);
                $baseShippingHiddenTaxAmount = $creditmemo->roundPrice($baseShippingHiddenTaxAmount, 'base');
                $totalTax                   += $shippingTaxAmount;
                $baseTotalTax               += $baseShippingTaxAmount;
            }
        } else {
            $orderShippingAmount = $order->getShippingAmount();
            $baseOrderShippingAmount = $order->getBaseShippingAmount();
            $orderShippingHiddenTaxAmount = $order->getShippingHiddenTaxAmount();
            $baseOrderShippingHiddenTaxAmount = $order->getBaseShippingHiddenTaxAmount();

            $baseOrderShippingRefundedAmount = $order->getBaseShippingRefunded();
            $baseOrderShippingHiddenTaxRefunded = $order->getBaseShippingHiddenTaxRefunded();

            $shippingTaxAmount = 0;
            $baseShippingTaxAmount = 0;
            $shippingHiddenTaxAmount = 0;
            $baseShippingHiddenTaxAmount = 0;

            $shippingDelta = $baseOrderShippingAmount - $baseOrderShippingRefundedAmount;

            if ($shippingDelta > $creditmemo->getBaseShippingAmount()) {
                $part       = $creditmemo->getShippingAmount() / $orderShippingAmount;
                $basePart   = $creditmemo->getBaseShippingAmount() / $baseOrderShippingAmount;
                $shippingTaxAmount          = $order->getShippingTaxAmount() * $part;
                $baseShippingTaxAmount      = $order->getBaseShippingTaxAmount() * $basePart;
                $shippingHiddenTaxAmount    = $order->getShippingHiddenTaxAmount() * $part;
                $baseShippingHiddenTaxAmount = $order->getBaseShippingHiddenTaxAmount() * $basePart;
                $shippingTaxAmount          = $creditmemo->roundPrice($shippingTaxAmount);
                $baseShippingTaxAmount      = $creditmemo->roundPrice($baseShippingTaxAmount, 'base');
                $shippingHiddenTaxAmount    = $creditmemo->roundPrice($shippingHiddenTaxAmount);
                $baseShippingHiddenTaxAmount = $creditmemo->roundPrice($baseShippingHiddenTaxAmount, 'base');
            } elseif ($shippingDelta == $creditmemo->getBaseShippingAmount()) {
                $shippingTaxAmount          = $order->getShippingTaxAmount() - $order->getShippingTaxRefunded();
                $baseShippingTaxAmount      = $order->getBaseShippingTaxAmount() - $order->getBaseShippingTaxRefunded();
                $shippingHiddenTaxAmount    = $order->getShippingHiddenTaxAmount()
                        - $order->getShippingHiddenTaxRefunded();
                $baseShippingHiddenTaxAmount = $order->getBaseShippingHiddenTaxAmount()
                        - $order->getBaseShippingHiddenTaxRefunded();
            }

            $totalTax           += $shippingTaxAmount;
            $baseTotalTax       += $baseShippingTaxAmount;
            $totalHiddenTax     += $shippingHiddenTaxAmount;
            $baseTotalHiddenTax += $baseShippingHiddenTaxAmount;
        }

        $allowedTax = $order->getTaxInvoiced() - $order->getTaxRefunded() - $creditmemo->getTaxAmount();
        $allowedBaseTax = $order->getBaseTaxInvoiced() - $order->getBaseTaxRefunded()
                - $creditmemo->getBaseTaxAmount();
        $allowedHiddenTax = $order->getHiddenTaxInvoiced() + $order->getShippingHiddenTaxAmount()
            - $order->getHiddenTaxRefunded() - $order->getShippingHiddenTaxRefunded();
        $allowedBaseHiddenTax = $order->getBaseHiddenTaxInvoiced() + $order->getBaseShippingHiddenTaxAmount()
            - $order->getBaseHiddenTaxRefunded() - $order->getBaseShippingHiddenTaxRefunded();

        $totalTax = min($allowedTax, $totalTax);
        $baseTotalTax = min($allowedBaseTax, $baseTotalTax);
        $totalHiddenTax = min($allowedHiddenTax, $totalHiddenTax);
        $baseTotalHiddenTax = min($allowedBaseHiddenTax, $baseTotalHiddenTax);

        $creditmemo->setTaxAmount($creditmemo->getTaxAmount() + $totalTax);
        $creditmemo->setBaseTaxAmount($creditmemo->getBaseTaxAmount() +  $baseTotalTax);
        $creditmemo->setHiddenTaxAmount($totalHiddenTax);
        $creditmemo->setBaseHiddenTaxAmount($baseTotalHiddenTax);

        $creditmemo->setShippingTaxAmount($shippingTaxAmount);
        $creditmemo->setBaseShippingTaxAmount($baseShippingTaxAmount);

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $totalTax + $totalHiddenTax);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseTotalTax + $baseTotalHiddenTax);
        return $this;
    }
}
