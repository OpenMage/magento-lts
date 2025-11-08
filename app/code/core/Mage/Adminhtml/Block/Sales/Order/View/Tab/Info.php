<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Order information tab
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_View_Tab_Info extends Mage_Adminhtml_Block_Sales_Order_Abstract implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Retrieve order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    /**
     * Retrieve source model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getSource()
    {
        return $this->getOrder();
    }

    /**
     * Retrieve order totals block settings
     *
     * @return array
     */
    public function getOrderTotalData()
    {
        return [
            'can_display_total_due'      => true,
            'can_display_total_paid'     => true,
            'can_display_total_refunded' => true,
        ];
    }

    public function getOrderInfoData()
    {
        return [
            'no_use_order_link' => true,
        ];
    }

    public function getTrackingHtml()
    {
        return $this->getChildHtml('order_tracking');
    }

    public function getItemsHtml()
    {
        return $this->getChildHtml('order_items');
    }

    /**
     * Retrieve giftmessage block html
     *
     * @return string
     * @deprecated after 1.4.2.0, use self::getGiftOptionsHtml() instead
     */
    public function getGiftmessageHtml()
    {
        return $this->getChildHtml('order_giftmessage');
    }

    /**
     * Retrieve gift options container block html
     *
     * @return string
     */
    public function getGiftOptionsHtml()
    {
        return $this->getChildHtml('gift_options');
    }

    public function getPaymentHtml()
    {
        return $this->getChildHtml('order_payment');
    }

    public function getViewUrl($orderId)
    {
        return $this->getUrl('*/*/*', ['order_id' => $orderId]);
    }

    public function getTabLabel()
    {
        return Mage::helper('sales')->__('Information');
    }

    public function getTabTitle()
    {
        return Mage::helper('sales')->__('Information');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
