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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Weee_Model_Total_Creditmemo_Weee extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $store = $creditmemo->getStore();

        $totalTax              = 0;
        $baseTotalTax          = 0;

        $weeeTaxAmount = 0;
        $baseWeeeTaxAmount = 0;

        foreach ($creditmemo->getAllItems() as $item) {
            $orderItem = $item->getOrderItem();
            if ($orderItem->isDummy()) {
                continue;
            }
            $orderItemQty = $orderItem->getQtyOrdered();

            $weeeRowDiscountAmount = $orderItem->getDiscountAppliedForWeeeTax();
            $weeeDiscountAmount = $creditmemo->roundPrice(
                $weeeRowDiscountAmount / $orderItemQty * $item->getQty(),
                'regular', true
            );
            $baseWeeeRowDiscountAmount = $orderItem->getBaseDiscountAppliedForWeeeTax();
            $baseWeeeDiscountAmount = $creditmemo->roundPrice(
                $baseWeeeRowDiscountAmount / $orderItemQty * $item->getQty(),
                'base', true
            );

            $weeeAmountExclTax = (Mage::helper('weee')->getWeeeTaxInclTax($item)
                - Mage::helper('weee')->getTotalTaxAppliedForWeeeTax($item)) * $item->getQty();
            $totalTax += $weeeAmountExclTax - $weeeDiscountAmount;

            $baseWeeeAmountExclTax = (Mage::helper('weee')->getBaseWeeeTaxInclTax($item)
                - Mage::helper('weee')->getBaseTotalTaxAppliedForWeeeTax($item)) * $item->getQty();
            $baseTotalTax += $baseWeeeAmountExclTax - $baseWeeeDiscountAmount;

            $item->setWeeeTaxAppliedRowAmount($weeeAmountExclTax);
            $item->setBaseWeeeTaxAppliedRowAmount($baseWeeeAmountExclTax);

            $weeeTaxAmount += (Mage::helper('weee')->getWeeeTaxInclTax($item)) * $item->getQty();
            $baseWeeeTaxAmount += (Mage::helper('weee')->getBaseWeeeTaxInclTax($item)) * $item->getQty();

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
        }

        /*
         * please refer the description in weee - invoice section for reasoning
         */

        if (Mage::helper('weee')->includeInSubtotal($store)) {
            $creditmemo->setSubtotal($creditmemo->getSubtotal() + $totalTax);
            $creditmemo->setBaseSubtotal($creditmemo->getBaseSubtotal() + $baseTotalTax);
        } else {
            $creditmemo->setTaxAmount($creditmemo->getTaxAmount() + $totalTax);
            $creditmemo->setBaseTaxAmount($creditmemo->getBaseTaxAmount() + $baseTotalTax);
        }

        //Increment the subtotal
        $creditmemo->setSubtotalInclTax($creditmemo->getSubtotalInclTax() + $weeeTaxAmount);
        $creditmemo->setBaseSubtotalInclTax($creditmemo->getBaseSubtotalInclTax() + $baseWeeeTaxAmount);

        //Increment the grand total
        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $totalTax);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseTotalTax);

        return $this;
    }
}
