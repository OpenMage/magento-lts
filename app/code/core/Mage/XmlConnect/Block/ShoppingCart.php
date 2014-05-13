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
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart xml renderer
 *
 * @category    Mage
 * @package     Mage_Xmlconnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_ShoppingCart extends Mage_Checkout_Block_Cart_Abstract
{
    /**
     * Render shopping cart xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $xmlObject Mage_XmlConnect_Model_Simplexml_Element */
        $xmlObject = Mage::getModel('xmlconnect/simplexml_element', '<cart></cart>');
        $cartMessages = $this->getMessages();
        $quote = $this->getQuote();

        $this->_cartSummary($xmlObject, $quote);
        /**
         * Cart items
         */
        $this->getChild('items')->addCartProductsToXmlObj($xmlObject, $quote);

        /**
         * Cart messages
         */
        if ($cartMessages) {
            $messagesXml = $xmlObject->addCustomChild('messages');
            foreach ($cartMessages as $status => $messages) {
                foreach ($messages as $message) {
                    $messageXml = $messagesXml->addCustomChild('message');
                    $messageXml->addCustomChild('status', $status);
                    $messageXml->addCustomChild('text', strip_tags($message));
                }
            }
        }

        /**
         * Cross Sell Products
         */
        if (count($this->getItems())) {
            $crossellXml = $this->getChildHtml('crosssell');
            $crossSellXmlObj = Mage::getModel('xmlconnect/simplexml_element', $crossellXml);
            $xmlObject->appendChild($crossSellXmlObj);
        }

        /**
         * Cart Totals
         */
        $this->getChild('totals')->setCartXmlObject($xmlObject)->toHtml();

        return $xmlObject->asNiceXml();
    }

    /**
     * Add summary block to cart
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $xmlObject
     * @param Mage_Sales_Model_Quote $quote
     * @return Mage_XmlConnect_Block_Cart
     */
    protected function _cartSummary($xmlObject, $quote)
    {
        $cartSummary = $xmlObject->addCustomChild('summary');

        $cartSummary->addCustomChild(
            'item', (int)$this->helper('checkout/cart')->getIsVirtualQuote(), array('label' => 'virtual')
        );
        $cartSummary->addCustomChild(
            'item', (int)$this->helper('checkout/cart')->getSummaryCount(), array('label' => 'total_qty')
        );

        if (strlen($quote->getCouponCode())) {
            $cartSummary->addCustomChild('item', 1, array('label' => 'has_coupon_code'));
        }
        return $this;
    }
}
