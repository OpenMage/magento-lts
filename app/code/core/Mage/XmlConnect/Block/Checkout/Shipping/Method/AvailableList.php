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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * One page checkout shipping methods xml renderer. API version 23
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Shipping_Method_AvailableList
    extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{
    /**
     * Render shipping methods xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $methodsXmlObj Mage_XmlConnect_Model_Simplexml_Element */
        $methodsXmlObj = Mage::getModel('xmlconnect/simplexml_element', '<shipping_methods></shipping_methods>');
        $shippingRateGroups = $this->getShippingRates();
        if ($shippingRateGroups) {
            $store = $this->getQuote()->getStore();
            $sole = count($shippingRateGroups) == 1;
            foreach ($shippingRateGroups as $code => $rates) {
                $methodXmlObj = $methodsXmlObj->addChild('method');
                $methodXmlObj->addAttribute('label', $methodsXmlObj->escapeXml($this->getCarrierName($code)));
                $ratesXmlObj = $methodXmlObj->addChild('rates');

                $sole = $sole && count($rates) == 1;
                foreach ($rates as $rate) {
                    $rateOptions = array();
                    $rateOptions['label'] = $rate->getMethodTitle();
                    $rateOptions['code'] = $rate->getCode();
                    if ($rate->getErrorMessage()) {
                        $rateXmlObj = $ratesXmlObj->addCustomChild('rate', null, $rateOptions);
                        $rateXmlObj->addChild('error_message', $methodsXmlObj->escapeXml($rate->getErrorMessage()));
                    } else {
                        $price = Mage::helper('tax')->getShippingPrice(
                            $rate->getPrice(),
                            Mage::helper('tax')->displayShippingPriceIncludingTax(),
                            $this->getAddress()
                        );
                        $rateOptions['price'] = Mage::helper('xmlconnect')->formatPriceForXml(
                            $store->convertPrice($price, false, false)
                        );
                        $rateOptions['formatted_price'] = $store->convertPrice($price, true, false);
                        $ratesXmlObj->addCustomChild('rate', null, $rateOptions);
                    }
                }
            }
        } else {
            Mage::throwException($this->__('Shipping to this address is not possible.'));
        }
        return $methodsXmlObj->asNiceXml();
    }
}
