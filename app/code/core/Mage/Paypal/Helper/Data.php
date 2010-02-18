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
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Paypal Data helper
 */
class Mage_Paypal_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Get line items and totals from sales quote or order
     *
     * PayPal calculates grand total by this formula:
     * sum(item_base_price * qty) + subtotal + shipping + shipping_discount
     * where subtotal doesn't include anything, shipping_discount is negative
     * the items discount should go as separate cart line item with negative amount
     * the shipping_discount is outlined in PayPal API docs, but ignored for some reason. Hence commented out.
     *
     * @param Mage_Sales_Model_Quote|Mage_Sales_Model_Order $salesEntity
     * @return array (array of $items, array of totals, $discountTotal, $shippingTotal)
     */
    public function prepareLineItems(Mage_Core_Model_Abstract $salesEntity, $discountTotalAsItem = true, $shippingTotalAsItem = false)
    {
        $items = array();
        foreach ($salesEntity->getAllItems() as $item) {
            if (!$item->getParentItem()) {
                $items[] = new Varien_Object($this->_prepareLineItemFields($salesEntity, $item));
            }
        }
        $discountAmount = 0; // this amount always includes the shipping discount
        $shippingDescription = '';
        if ($salesEntity instanceof Mage_Sales_Model_Order) {
            $discountAmount = abs(1 * $salesEntity->getBaseDiscountAmount());
            $shippingDescription = $salesEntity->getShippingDescription();
            $totals = array(
                'subtotal' => $salesEntity->getBaseSubtotal() - $discountAmount,
                'tax'      => $salesEntity->getBaseTaxAmount(),
                'shipping' => $salesEntity->getBaseShippingAmount(),
//                'shipping_discount' => -1 * abs($salesEntity->getBaseShippingDiscountAmount()),
            );
        } else {
            $address = $salesEntity->getIsVirtual() ? $salesEntity->getBillingAddress() : $salesEntity->getShippingAddress();
            $discountAmount = abs(1 * $address->getBaseDiscountAmount());
            $shippingDescription = $address->getShippingDescription();
            $totals = array (
                'subtotal' => $salesEntity->getBaseSubtotal() - $discountAmount,
                'tax'      => $address->getBaseTaxAmount(),
                'shipping' => $address->getBaseShippingAmount(),
                'discount' => $discountAmount,
//                'shipping_discount' => -1 * abs($address->getBaseShippingDiscountAmount()),
            );
        }
        // discount total as line item (negative)
        if ($discountTotalAsItem && $discountAmount) {
            $items[] = new Varien_Object(array(
                'name'   => Mage::helper('paypal')->__('Discount'),
                'qty'    => 1,
                'amount' => -1.00 * $discountAmount,
            ));
        }
        // shipping total as line item
        if ($shippingTotalAsItem && (!$salesEntity->getIsVirtual()) && (float)$totals['shipping']) {
            $items[] = new Varien_Object(array(
                'id'     => Mage::helper('paypal')->__('Shipping'),
                'name'   => $shippingDescription,
                'qty'    => 1,
                'amount' => (float)$totals['shipping'],
            ));
        }
        return array($items, $totals, $discountAmount, $totals['shipping']);
    }

    /**
     * Compare order total amount with cart's items cost sum
     *
     * @param Mage_Sales_Model_Quote|Mage_Sales_Model_Order $salesEntity
     * @param float $orderAmount
     * @return bool
     */
    public function doLineItemsMatchAmount(Mage_Core_Model_Abstract $salesEntity, $orderAmount)
    {
        $total = 0;
        foreach ($salesEntity->getAllItems() as $item) {
            if ($salesEntity instanceof Mage_Sales_Model_Order) {
                $qty = $item->getQtyOrdered();
                $amount = $item->getBasePrice();
                $shipping = $salesEntity->getBaseShippingAmount();
            } else {
                $address = $salesEntity->getIsVirtual() ? $salesEntity->getBillingAddress() : $salesEntity->getShippingAddress();
                $qty = $item->getTotalQty();
                $amount = $item->getBaseCalculationPrice();
                $shipping = $address->getBaseShippingAmount();
            }
            $total += (float)$amount*$qty;
        }

        if ($total == $orderAmount || $total+$shipping == $orderAmount) {
            return true;
        }
        return false;
    }

    /**
     * Get one line item key-value array
     *
     * @param Mage_Core_Model_Abstract $salesEntity
     * @param Varien_Object $item
     * @return array
     */
    protected function _prepareLineItemFields(Mage_Core_Model_Abstract $salesEntity, Varien_Object $item)
    {
        if ($salesEntity instanceof Mage_Sales_Model_Order) {
            $qty = $item->getQtyOrdered();
            $amount = $item->getBasePrice();
        } else {
            $qty = $item->getTotalQty();
            $amount = $item->getBaseCalculationPrice();
        }
        // workaround in case if item subtotal precision is not compatible with PayPal (.2)
        $subAggregatedLabel = '';
        if ((float)$amount - round((float)$amount, 2)) {
            $amount = $amount * $qty;
            $subAggregatedLabel = ' x' . $qty;
            $qty = 1;
        }
        return array(
            'id'     => $item->getSku(),
            'name'   => $item->getName() . $subAggregatedLabel,
            'qty'    => $qty,
            'amount' => (float)$amount,
        );
    }
}
