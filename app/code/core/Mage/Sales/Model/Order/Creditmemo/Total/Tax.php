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
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Sales_Model_Order_Creditmemo_Total_Tax extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $totalTax       = 0;
        $baseTotalTax   = 0;

        foreach ($creditmemo->getAllItems() as $item) {
            if ($item->getOrderItem()->isDummy()) {
                continue;
            }
            $orderItemTax     = $item->getOrderItem()->getTaxAmount();
            $baseOrderItemTax = $item->getOrderItem()->getBaseTaxAmount();
            $orderItemQty = $item->getOrderItem()->getQtyOrdered();

            if ($orderItemTax && $orderItemQty) {
                $tax = $orderItemTax*$item->getQty()/$orderItemQty;
                $baseTax = $baseOrderItemTax*$item->getQty()/$orderItemQty;

                $tax = $creditmemo->getStore()->roundPrice($tax);
                $baseTax = $creditmemo->getStore()->roundPrice($baseTax);

                $item->setTaxAmount($tax);
                $item->setBaseTaxAmount($baseTax);

                $totalTax += $tax;
                $baseTotalTax += $baseTax;
            }
        }

        if ($invoice = $creditmemo->getInvoice()) {
            $totalTax += $invoice->getShippingTaxAmount();
            $baseTotalTax += $invoice->getBaseShippingTaxAmount();

            $creditmemo->setShippingTaxAmount($invoice->getShippingTaxAmount());
            $creditmemo->setBaseShippingTaxAmount($invoice->getBaseShippingTaxAmount());
        } else {
            $totalTax += $creditmemo->getShippingTaxAmount();
            $baseTotalTax += $creditmemo->getBaseShippingTaxAmount();
        }

        $totalTax       = $totalTax - $creditmemo->getOrder()->getTaxRefunded();
        $baseTotalTax   = $baseTotalTax - $creditmemo->getOrder()->getBaseTaxRefunded();

        if ($baseTotalTax<0) {
            $baseTotalTax = 0;
            $totalTax = 0;
        }

        $creditmemo->setTaxAmount($totalTax);
        $creditmemo->setBaseTaxAmount($baseTotalTax);

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $totalTax);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseTotalTax);
        return $this;
    }
}