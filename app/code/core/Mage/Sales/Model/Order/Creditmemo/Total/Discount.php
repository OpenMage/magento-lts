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
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Sales_Model_Order_Creditmemo_Total_Discount extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $creditmemo->setDiscountAmount(0);
        $creditmemo->setBaseDiscountAmount(0);

        $totalDiscountAmount = 0;
        $baseTotalDiscountAmount = 0;
        foreach ($creditmemo->getAllItems() as $item) {
            if ($item->getOrderItem()->isDummy()) {
                continue;
            }
            $orderItemDiscount      = (float) $item->getOrderItem()->getDiscountAmount();
            $baseOrderItemDiscount  = (float) $item->getOrderItem()->getBaseDiscountAmount();
            $orderItemQty       = $item->getOrderItem()->getQtyOrdered();

            if ($orderItemDiscount && $orderItemQty) {
                $discount = $orderItemDiscount*$item->getQty()/$orderItemQty;
                $baseDiscount = $baseOrderItemDiscount*$item->getQty()/$orderItemQty;

                $discount = $creditmemo->getStore()->roundPrice($discount);
                $baseDiscount = $creditmemo->getStore()->roundPrice($baseDiscount);

                $item->setDiscountAmount($discount);
                $item->setBaseDiscountAmount($baseDiscount);

                $totalDiscountAmount += $discount;
                $baseTotalDiscountAmount+= $baseDiscount;
            }
        }

        $creditmemo->setDiscountAmount($totalDiscountAmount);
        $creditmemo->setBaseDiscountAmount($baseTotalDiscountAmount);

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $totalDiscountAmount);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseTotalDiscountAmount);
        return $this;
    }
}