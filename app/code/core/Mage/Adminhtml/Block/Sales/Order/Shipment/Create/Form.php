<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml shipment create form
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Shipment_Create_Form extends Mage_Adminhtml_Block_Sales_Order_Abstract
{
    /**
     * Retrieve invoice order
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getShipment()->getOrder();
    }

    /**
     * Retrieve source
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function getSource()
    {
        return $this->getShipment();
    }

    /**
     * Retrieve shipment model instance
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function getShipment()
    {
        return Mage::registry('current_shipment');
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'items',
            $this->getLayout()->createBlock('adminhtml/sales_order_shipment_create_items'),
        );
        $this->setChild(
            'tracking',
            $this->getLayout()->createBlock('adminhtml/sales_order_shipment_create_tracking'),
        );

        return $this;
    }

    public function getPaymentHtml()
    {
        return $this->getChildHtml('order_payment');
    }

    public function getItemsHtml()
    {
        return $this->getChildHtml('order_items');
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', ['order_id' => $this->getShipment()->getOrderId()]);
    }
}
