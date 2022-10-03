<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Invoice_Total_Subtotal extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * Collect invoice subtotal
     *
     * @param   Mage_Sales_Model_Order_Invoice $invoice
     * @return  Mage_Sales_Model_Order_Invoice_Total_Subtotal
     */
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $subtotal       = 0;
        $baseSubtotal   = 0;
        $subtotalInclTax= 0;
        $baseSubtotalInclTax = 0;

        $order = $invoice->getOrder();

        foreach ($invoice->getAllItems() as $item) {
            if ($item->getOrderItem()->isDummy()) {
                continue;
            }

            $item->calcRowTotal();

            $subtotal            += $item->getRowTotal();
            $baseSubtotal        += $item->getBaseRowTotal();
            $subtotalInclTax     += $item->getRowTotalInclTax() + $item->getWeeeTaxAppliedRowAmount();
            $baseSubtotalInclTax += $item->getBaseRowTotalInclTax() + $item->getBaseWeeeTaxAppliedRowAmount();
        }

        $allowedSubtotal = $order->getSubtotal() - $order->getSubtotalInvoiced();
        $baseAllowedSubtotal = $order->getBaseSubtotal() - $order->getBaseSubtotalInvoiced();

        if ($invoice->isLast()) {
            $subtotal = $allowedSubtotal;
            $baseSubtotal = $baseAllowedSubtotal;
        } else {
            $subtotal = min($allowedSubtotal, $subtotal);
            $baseSubtotal = min($baseAllowedSubtotal, $baseSubtotal);
        }

        $invoice->setSubtotal($subtotal);
        $invoice->setBaseSubtotal($baseSubtotal);
        $invoice->setSubtotalInclTax($subtotalInclTax);
        $invoice->setBaseSubtotalInclTax($baseSubtotalInclTax);

        $invoice->setGrandTotal($invoice->getGrandTotal() + $subtotal);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseSubtotal);
        return $this;
    }
}
