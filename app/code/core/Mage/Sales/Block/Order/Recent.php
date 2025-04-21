<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Sales order history block
 *
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Resource_Order_Collection getOrders()
 * @method $this setOrders(Mage_Sales_Model_Resource_Order_Collection $orders)
 */
class Mage_Sales_Block_Order_Recent extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();

        //TODO: add full name logic
        $orders = Mage::getResourceModel('sales/order_collection')
            ->addAttributeToSelect('*')
            ->joinAttribute(
                'shipping_firstname',
                'order_address/firstname',
                'shipping_address_id',
                null,
                'left',
            )
            ->joinAttribute(
                'shipping_middlename',
                'order_address/middlename',
                'shipping_address_id',
                null,
                'left',
            )
            ->joinAttribute(
                'shipping_lastname',
                'order_address/lastname',
                'shipping_address_id',
                null,
                'left',
            )
            ->addAttributeToFilter(
                'customer_id',
                Mage::getSingleton('customer/session')->getCustomer()->getId(),
            )
            ->addAttributeToFilter(
                'state',
                ['in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()],
            )
            ->addAttributeToSort('created_at', 'desc')
            ->setPageSize(5)
            ->load()
        ;

        $this->setOrders($orders);
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    public function getViewUrl($order)
    {
        return $this->getUrl('sales/order/view', ['order_id' => $order->getId()]);
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    public function getTrackUrl($order)
    {
        return $this->getUrl('sales/order/track', ['order_id' => $order->getId()]);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getOrders()->getSize() > 0) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    public function getReorderUrl($order)
    {
        return $this->getUrl('sales/order/reorder', ['order_id' => $order->getId()]);
    }
}
