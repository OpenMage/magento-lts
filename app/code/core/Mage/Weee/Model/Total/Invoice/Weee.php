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
 * @package     Mage_Weee
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Weee_Model_Total_Invoice_Weee extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $store = $invoice->getStore();

        $totalTax = 0;
        $baseTotalTax = 0;

        foreach ($invoice->getAllItems() as $item) {
            $orderItem = $item->getOrderItem();
            $orderItemQty = $orderItem->getQtyOrdered();

            if ($orderItemQty) {
                if ($orderItem->isDummy()) {
                    continue;
                }

                $weeeTaxAmount = $item->getWeeeTaxAppliedAmount()*$item->getQty();
                $baseWeeeTaxAmount = $item->getBaseWeeeTaxAppliedAmount()*$item->getQty();

                $item->setWeeeTaxAppliedRowAmount($weeeTaxAmount);
                $item->setBaseWeeeTaxAppliedRowAmount($baseWeeeTaxAmount);
                $newApplied = array();
                $applied = Mage::helper('weee')->getApplied($item);
                foreach ($applied as $one) {
                    $one['base_row_amount'] = $one['base_amount']*$item->getQty();
                    $one['row_amount'] = $one['amount']*$item->getQty();
                    $one['base_row_amount_incl_tax'] = $one['base_amount_incl_tax']*$item->getQty();
                    $one['row_amount_incl_tax'] = $one['amount_incl_tax']*$item->getQty();

                    $newApplied[] = $one;
                }
                Mage::helper('weee')->setApplied($item, $newApplied);

                $item->setWeeeTaxRowDisposition($item->getWeeeTaxDisposition()*$item->getQty());
                $item->setBaseWeeeTaxRowDisposition($item->getBaseWeeeTaxDisposition()*$item->getQty());

                $totalTax += $weeeTaxAmount;
                $baseTotalTax += $baseWeeeTaxAmount;
            }
        }
        if (Mage::helper('weee')->includeInSubtotal($store)) {
            $invoice->setSubtotal($invoice->getSubtotal() + $totalTax);
            $invoice->setBaseSubtotal($invoice->getBaseSubtotal() + $baseTotalTax);
        } else {
            $invoice->setTaxAmount($invoice->getTaxAmount() + $totalTax);
            $invoice->setBaseTaxAmount($invoice->getBaseTaxAmount() + $baseTotalTax);
        }

        $invoice->setGrandTotal($invoice->getGrandTotal() + $totalTax);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseTotalTax);

        return $this;
    }
}
