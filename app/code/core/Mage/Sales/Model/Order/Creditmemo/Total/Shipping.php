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
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order creditmemo shipping total calculation model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Creditmemo_Total_Shipping extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();
        $allowedAmount          = $order->getShippingAmount()-$order->getShippingRefunded();
        $baseAllowedAmount      = $order->getBaseShippingAmount()-$order->getBaseShippingRefunded();

        $shipping               = $order->getShippingAmount();
        $baseShipping           = $order->getBaseShippingAmount();
        $shippingInclTax        = $order->getShippingInclTax();
        $baseShippingInclTax    = $order->getBaseShippingInclTax();

        $isShippingInclTax = Mage::getSingleton('tax/config')->displaySalesShippingInclTax($order->getStoreId());

        /**
         * Check if shipping amount was specified (from invoice or another source).
         * Using has magic method to allow setting 0 as shipping amount.
         */
        if ($creditmemo->hasBaseShippingAmount()) {
            $baseShippingAmount = Mage::app()->getStore()->roundPrice($creditmemo->getBaseShippingAmount());
            if ($isShippingInclTax && $baseShippingInclTax != 0) {
                $part = $baseShippingAmount/$baseShippingInclTax;
                $shippingInclTax    = Mage::app()->getStore()->roundPrice($shippingInclTax*$part);
                $baseShippingInclTax= $baseShippingAmount;
                $baseShippingAmount = Mage::app()->getStore()->roundPrice($baseShipping*$part);
            }
            /*
             * Rounded allowed shipping refund amount is the highest acceptable shipping refund amount.
             * Shipping refund amount shouldn't cause errors, if it doesn't exceed that limit.
             * Note: ($x < $y + 0.0001) means ($x <= $y) for floats
             */
            if ($baseShippingAmount < Mage::app()->getStore()->roundPrice($baseAllowedAmount) + 0.0001) {
                /*
                 * Shipping refund amount should be equated to allowed refund amount,
                 * if it exceeds that limit.
                 * Note: ($x > $y - 0.0001) means ($x >= $y) for floats
                 */
                if ($baseShippingAmount > $baseAllowedAmount - 0.0001) {
                    $shipping     = $allowedAmount;
                    $baseShipping = $baseAllowedAmount;
                } else {
                    if ($baseShipping != 0) {
                        $shipping = $shipping * $baseShippingAmount / $baseShipping;
                    }
                    $shipping     = Mage::app()->getStore()->roundPrice($shipping);
                    $baseShipping = $baseShippingAmount;
                }
            } else {
                $baseAllowedAmount = $order->getBaseCurrency()->format($baseAllowedAmount,null,false);
                Mage::throwException(
                    Mage::helper('sales')->__('Maximum shipping amount allowed to refund is: %s', $baseAllowedAmount)
                );
            }
        } else {
            if ($baseShipping != 0) {
                $allowedTaxAmount = $order->getShippingTaxAmount() - $order->getShippingTaxRefunded();
                $baseAllowedTaxAmount = $order->getBaseShippingTaxAmount() - $order->getBaseShippingTaxRefunded();

                $shippingInclTax = Mage::app()->getStore()->roundPrice($allowedAmount + $allowedTaxAmount);
                $baseShippingInclTax = Mage::app()->getStore()->roundPrice($baseAllowedAmount + $baseAllowedTaxAmount);
            }
            $shipping           = $allowedAmount;
            $baseShipping       = $baseAllowedAmount;
        }

        $creditmemo->setShippingAmount($shipping);
        $creditmemo->setBaseShippingAmount($baseShipping);
        $creditmemo->setShippingInclTax($shippingInclTax);
        $creditmemo->setBaseShippingInclTax($baseShippingInclTax);

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal()+$shipping);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal()+$baseShipping);
        return $this;
    }
}
