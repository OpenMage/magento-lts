<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Weee
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Weee
 */
class Mage_Weee_Model_Total_Creditmemo_Weee extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    /**
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return $this|Mage_Sales_Model_Order_Creditmemo_Total_Abstract
     */
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
                'regular',
                true
            );
            $baseWeeeRowDiscountAmount = $orderItem->getBaseDiscountAppliedForWeeeTax();
            $baseWeeeDiscountAmount = $creditmemo->roundPrice(
                $baseWeeeRowDiscountAmount / $orderItemQty * $item->getQty(),
                'base',
                true
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
