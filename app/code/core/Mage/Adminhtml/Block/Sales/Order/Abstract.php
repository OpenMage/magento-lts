<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml order abstract block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Abstract extends Mage_Adminhtml_Block_Widget
{
    /**
     * Retrieve available order
     *
     * @return Mage_Sales_Model_Order
     * @throws Mage_Core_Exception
     */
    public function getOrder()
    {
        if ($this->hasOrder()) {
            return $this->getData('order');
        }

        if (Mage::registry('current_order')) {
            return Mage::registry('current_order');
        }

        if (Mage::registry('order')) {
            return Mage::registry('order');
        }

        Mage::throwException(Mage::helper('sales')->__('Cannot get order instance'));
    }

    public function getPriceDataObject()
    {
        $obj = $this->getData('price_data_object');
        if (is_null($obj)) {
            return $this->getOrder();
        }

        return $obj;
    }

    /**
     * @param  string $code
     * @param  false  $strong
     * @param  string $separator
     * @return string
     */
    public function displayPriceAttribute($code, $strong = false, $separator = '<br/>')
    {
        /** @var Mage_Adminhtml_Helper_Sales $helper */
        $helper = $this->helper('adminhtml/sales');
        return $helper->displayPriceAttribute($this->getPriceDataObject(), $code, $strong, $separator);
    }

    /**
     * @param  float  $basePrice
     * @param  float  $price
     * @param  false  $strong
     * @param  string $separator
     * @return string
     */
    public function displayPrices($basePrice, $price, $strong = false, $separator = '<br/>')
    {
        /** @var Mage_Adminhtml_Helper_Sales $helper */
        $helper = $this->helper('adminhtml/sales');
        return $helper->displayPrices($this->getPriceDataObject(), $basePrice, $price, $strong, $separator);
    }

    /**
     * Retrieve order totals block settings
     *
     * @return array
     */
    public function getOrderTotalData()
    {
        return [];
    }

    /**
     * Retrieve order info block settings
     *
     * @return array
     */
    public function getOrderInfoData()
    {
        return [];
    }

    /**
     * Retrieve subtotal price include tax html formatted content
     *
     * @param  Mage_Sales_Model_Order $order
     * @return string
     */
    public function displayShippingPriceInclTax($order)
    {
        $shipping = $order->getShippingInclTax();
        if ($shipping) {
            $baseShipping = $order->getBaseShippingInclTax();
        } else {
            $shipping       = $order->getShippingAmount() + $order->getShippingTaxAmount();
            $baseShipping   = $order->getBaseShippingAmount() + $order->getBaseShippingTaxAmount();
        }

        return $this->displayPrices($baseShipping, $shipping, false, ' ');
    }
}
