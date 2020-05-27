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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer orders history xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Order_List extends Mage_Core_Block_Abstract
{
    /**
     * Orders count limit
     */
    const ORDERS_LIST_LIMIT = 10;

    /**
     * Render customer orders list xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $ordersXmlObj Mage_XmlConnect_Model_Simplexml_Element */
        $ordersXmlObj = Mage::getModel('xmlconnect/simplexml_element', '<orders></orders>');

        /** @var $orders Mage_Sales_Model_Resource_Order_Collection */
        $orders = Mage::getResourceModel('sales/order_collection')->addFieldToSelect('*')->addFieldToFilter(
            'customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId()
        )->addFieldToFilter('state', array(
            'in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()
        ))->setOrder('created_at', 'desc');

        /** @var $request Mage_Core_Controller_Request_Http */
        $request = $this->getRequest();
        /**
         * Apply offset and count
         */
        $count = abs((int)$request->getParam('count', 0));
        $count = $count ? $count : self::ORDERS_LIST_LIMIT;
        $offset = abs((int)$request->getParam('offset', 0));


        $ordersXmlObj->addAttribute('orders_count', $ordersXmlObj->escapeXml($orders->count()));
        $ordersXmlObj->addAttribute('offset', $ordersXmlObj->escapeXml($offset));

        $orders->clear()->getSelect()->limit($count, $offset);
        $orders->load();

        if ($orders->count()) {
            foreach ($orders as $order) {
                $item = $ordersXmlObj->addChild('item');
                $item->addChild('entity_id', $order->getId());
                $item->addChild('number', $order->getRealOrderId());
                $item->addChild('date', $this->formatDate($order->getCreatedAtStoreDate()));
                if ($order->getShippingAddress()) {
                    $item->addChild('ship_to', $ordersXmlObj->escapeXml($order->getShippingAddress()->getName()));
                }
                $item->addChild('total', $order->getOrderCurrency()->formatPrecision(
                    $order->getGrandTotal(), 2, array(), false, false
                ));
                $item->addChild('status', $order->getStatusLabel());
            }
        }
        return $ordersXmlObj->asNiceXml();
    }
}
