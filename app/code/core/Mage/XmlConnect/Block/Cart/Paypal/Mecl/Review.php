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
 * PayPal MECL Shopping cart review xml renderer
 *
 * @category    Mage
 * @package     Mage_Xmlconnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Cart_Paypal_Mecl_Review extends Mage_Paypal_Block_Express_Review
{
    /**
     * Render PayPal MECL details xml
     *
     * @return string xml
     */
    protected function _toHtml()
    {
        /** @var $reviewXmlObj Mage_XmlConnect_Model_Simplexml_Element */
        $reviewXmlObj = Mage::getModel('xmlconnect/simplexml_element', '<mecl_cart_details></mecl_cart_details>');

        if ($this->getPaypalMessages()) {
            $reviewXmlObj->addChild('paypal_message', implode('\n', $this->getPaypalMessages()));
        }

        if ($this->getShippingAddress()) {
            $shipping = Mage::helper('xmlconnect')->trimLineBreaks($this->getShippingAddress()->format('text'));
            $reviewXmlObj->addCustomChild('shipping_address', $shipping, array(
                'label' => $this->__('Shipping Address')
            ));
        }

        if ($this->_quote->isVirtual()) {
            $reviewXmlObj->addCustomChild('shipping_method', null, array(
                'label' => $this->__('No shipping method required.')
            ));
        } elseif ($this->getCanEditShippingMethod() || !$this->getCurrentShippingRate()) {
            if ($groups = $this->getShippingRateGroups()) {
                $currentRate = $this->getCurrentShippingRate();
                foreach ($groups as $code => $rates) {
                    foreach ($rates as $rate) {
                        if ($currentRate === $rate) {
                            $reviewXmlObj->addCustomChild('shipping_method', null, array(
                                'rate' => strip_tags($this->renderShippingRateOption($rate)),
                                'label' => $this->getCarrierName($code)
                            ));
                            break(2);
                        }
                    }
                }
            }
        }
        $reviewXmlObj->addCustomChild('payment_method', $this->escapeHtml($this->getPaymentMethodTitle()), array(
            'label' => $this->__('Payment Method')
        ));
        $billing = Mage::helper('xmlconnect')->trimLineBreaks($this->getBillingAddress()->format('text'));
        $reviewXmlObj->addCustomChild('billing_address', $billing, array(
            'label' => $this->__('Billing Address'),
            'payer_email' => $this->__('Payer Email: %s', $this->getBillingAddress()->getEmail())
        ));

        $this->getChild('details')->addDetailsToXmlObj($reviewXmlObj);

        $agreements = $this->getChildHtml('agreements');
        if ($agreements) {
            $agreementsXmlObj = Mage::getModel('xmlconnect/simplexml_element', $agreements);
            $reviewXmlObj->appendChild($agreementsXmlObj);
        }

        return $reviewXmlObj->asNiceXml();
    }
}
