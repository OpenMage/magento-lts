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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer order details xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Order_Details extends Mage_Payment_Block_Info
{
    /**
     * Render customer orders list xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $orderXmlObj Mage_XmlConnect_Model_Simplexml_Element */
        $orderXmlObj = Mage::getModel('xmlconnect/simplexml_element', '<order_details></order_details>');
        /** @var $order Mage_Sales_Model_Order */
        $order = Mage::registry('current_order');
        if (!($order instanceof Mage_Sales_Model_Order)) {
            Mage::throwException($this->__('Model of order is not loaded.'));
        }
        $orderDate = $this->formatDate($order->getCreatedAtStoreDate(), 'long');
        $orderXmlObj->addCustomChild(
            'order',
            null,
            array(
                 'label' => Mage::helper('sales')->__('Order #%s - %s', $order->getRealOrderId(), $order->getStatusLabel()),
                 'order_date' => Mage::helper('sales')->__('Order Date: %s', $orderDate)
            )
        );
        if (!$order->getIsVirtual()) {
            $shipping = preg_replace(
                array('@\r@', '@\n+@'),
                array('', "\n"),
                $order->getShippingAddress()->format('text')
            );
            $billing = preg_replace(
                array('@\r@', '@\n+@'),
                array('', "\n"),
                $order->getBillingAddress()->format('text')
            );
            $orderXmlObj->addCustomChild('shipping_address', $shipping);
            $orderXmlObj->addCustomChild('billing_address', $billing);

            if ($order->getShippingDescription()) {
                $shippingMethodDescription = $order->getShippingDescription();
            } else {
                $shippingMethodDescription = Mage::helper('sales')->__('No shipping information available');
            }
            $orderXmlObj->addCustomChild('shipping_method', $shippingMethodDescription);
        }
        /**
         * Pre-defined array of methods that we are going to render
         */
        $methodArray = array(
            'ccsave' => 'Mage_XmlConnect_Block_Checkout_Payment_Method_Info_Ccsave',
            'checkmo' => 'Mage_XmlConnect_Block_Checkout_Payment_Method_Info_Checkmo',
            'purchaseorder' => 'Mage_XmlConnect_Block_Checkout_Payment_Method_Info_Purchaseorder',
            'authorizenet' => 'Mage_XmlConnect_Block_Checkout_Payment_Method_Info_Authorizenet',
        );
        // TODO: create info blocks for Payment Bridge methods
//        /**
//         * Check if the Payment Bridge module is available and add methods for rendering
//         */
//        if (is_object(Mage::getConfig()->getNode('modules/Enterprise_Pbridge'))) {
//            $pbridgeMethodArray = array(
//                'pbridge_authorizenet'  => 'Enterprise_Pbridge_Model_Payment_Method_Authorizenet',
//                'pbridge_paypal'        => 'Enterprise_Pbridge_Model_Payment_Method_Paypal',
//                'pbridge_verisign'      => 'Enterprise_Pbridge_Model_Payment_Method_Payflow_Pro',
//                'pbridge_paypaluk'      => 'Enterprise_Pbridge_Model_Payment_Method_Paypaluk',
//            );
//            $methodArray = $methodArray + $pbridgeMethodArray;
//        }

        // TODO: it's need to create an info blocks for other payment methods (including Enterprise)

        $method = $this->helper('payment')->getInfoBlock($order->getPayment())->getMethod();
        $methodCode = $method->getCode();

        $paymentNode = $orderXmlObj->addChild('payment_method');
        if (array_key_exists($methodCode, $methodArray)) {
            $currentBlockRenderer = 'xmlconnect/checkout_payment_method_info_' . $methodCode;
            $currentBlockName = 'xmlconnect.checkout.payment.method.info.' . $methodCode;
            $this->getLayout()->addBlock($currentBlockRenderer, $currentBlockName);
            $this->setChild($methodCode, $currentBlockName);
            $renderer = $this->getChild($methodCode)->setInfo($order->getPayment());
            $renderer->addPaymentInfoToXmlObj($paymentNode);
        } else {
            $paymentNode->addAttribute('type', $methodCode);
            $paymentNode->addAttribute('title', $orderXmlObj->xmlAttribute($method->getTitle()));

            $this->setInfo($order->getPayment());

            $specificInfo = array_merge(
                (array)$order->getPayment()->getAdditionalInformation(),
                (array)$this->getSpecificInformation()
            );
            if (!empty($specificInfo)) {
                foreach ($specificInfo as $label => $value) {
                    if ($value) {
                        $paymentNode->addCustomChild(
                            'item',
                            implode($this->getValueAsArray($value, true), PHP_EOL),
                            array('label' => $label)
                        );
                    }
                }
            }
        }

        $itemsBlock = $this->getLayout()->getBlock('xmlconnect.customer.order.items');
        if ($itemsBlock) {
            /** @var $itemsBlock Mage_XmlConnect_Block_Customer_Order_Items */
            $itemsBlock->setItems($order->getItemsCollection());
            $itemsBlock->addItemsToXmlObject($orderXmlObj);
            $totalsBlock = $this->getLayout()->getBlock('xmlconnect.customer.order.totals');
            if ($totalsBlock) {
                $totalsBlock->setOrder($order);
                $totalsBlock->addTotalsToXmlObject($orderXmlObj);
            }
        } else {
            $orderXmlObj->addChild('ordered_items');
        }

        return $orderXmlObj->asNiceXml();
    }
}
