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
 * PayPal MECL Shipping method list xml renderer
 *
 * @category    Mage
 * @package     Mage_Xmlconnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Cart_Paypal_Mecl_Shippingmethods extends Mage_Paypal_Block_Express_Review
{
    /**
     * Add price details to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $xmlObj
     * @param Mage_Sales_Model_Quote_Address_Rate $rate
     * @return Mage_XmlConnect_Block_Cart_Paypal_Mecl_Shippingmethods
     */
    protected function _addPriceToXmlObj($xmlObj, $rate)
    {
        $price = $this->_getShippingPrice($rate->getPrice(), $this->helper('tax')->displayShippingPriceIncludingTax());
        $incl = $this->_getShippingPrice($rate->getPrice(), true);
        $renderedInclTax = '';
        if (($incl != $price) && $this->helper('tax')->displayShippingBothPrices()) {
            $inclTaxFormat = ' (%s %s)';
            $renderedInclTax = sprintf($inclTaxFormat, Mage::helper('tax')->__('Incl. Tax'), $incl);
        }
        $price .= $renderedInclTax;
        $xmlObj->addAttribute('price', $rate->getPrice() * 1);
        $xmlObj->addAttribute('formatted_price', $xmlObj->escapeXml($price));
        return $this;
    }

    /**
     * Render PayPal MECL shipping method list xml
     *
     * @return string xml
     */
    protected function _toHtml()
    {
        /** @var $listXmlObj Mage_XmlConnect_Model_Simplexml_Element */
        $methodListXmlObj = Mage::getModel(
            'xmlconnect/simplexml_element', '<shipping_method_list></shipping_method_list>'
        );
        $methodListXmlObj->addAttribute('label', $this->__('Shipping Method'));

        if (Mage::helper('xmlconnect')->checkApiVersion(Mage_XmlConnect_Helper_Data::DEVICE_API_V_23)) {
            if ($this->getCanEditShippingMethod() || !$this->getCurrentShippingRate()) {
                $groups = $this->getShippingRateGroups();
                if ($groups) {
                    $currentRate = $this->getCurrentShippingRate();
                    foreach ($groups as $code => $rates) {
                        $rateListXmlObj = $this->_addRatesToXmlObj($methodListXmlObj, $code);
                        foreach ($rates as $rate) {
                            $rateAttributes = array(
                                'label' => $rate->getMethodTitle(),
                                'code' => $this->renderShippingRateValue($rate)
                            );
                            $rateXmlObj = $rateListXmlObj->addCustomChild('rate', null, $rateAttributes);
                            if ($rate->getErrorMessage()) {
                                $rateXmlObj->addChild('error_message', $rateXmlObj->escapeXml(
                                    $rate->getErrorMessage()
                                ));
                            } else {
                                $this->_addPriceToXmlObj($rateXmlObj, $rate);
                            }
                            if ($currentRate === $rate) {
                                $rateXmlObj->addAttribute('selected', 1);
                            }
                        }
                    }
                } else {
                    $this->_addNoShippingMessage($methodListXmlObj);
                }
            } else {
                $rateListXmlObj = $this->_addRatesToXmlObj($methodListXmlObj);
                $rate = $this->getCurrentShippingRate();
                $rateXmlObj = $rateListXmlObj->addCustomChild('rate', null, array(
                    'label' => $rate->getMethodTitle(),
                    'code' => $this->renderShippingRateValue($rate),
                    'selected' => 1
                ));
                $this->_addPriceToXmlObj($rateXmlObj, $rate);
            }
        } else {
            if ($this->getCanEditShippingMethod() || !$this->getCurrentShippingRate()) {
                $groups = $this->getShippingRateGroups();
                if ($groups) {
                    $currentRate = $this->getCurrentShippingRate();
                    foreach ($groups as $code => $rates) {
                        $rateListXmlObj = $this->_addRatesToXmlObj($methodListXmlObj, $code);
                        foreach ($rates as $rate) {
                            if ($rate->getErrorMessage()) {
                                $title = $rate->getErrorMessage();
                            } else {
                                $title = $this->renderShippingRateOption($rate);
                            }
                            $rateAttributes = array(
                                'label' => $rateListXmlObj->escapeXml($title),
                                'code' => $this->renderShippingRateValue($rate)
                            );
                            if ($currentRate === $rate) {
                                $rateAttributes += array('selected' => 1);
                            }
                            $rateListXmlObj->addCustomChild('rate', null, $rateAttributes);
                        }
                    }
                } else {
                    $this->_addNoShippingMessage($methodListXmlObj);
                }
            } else {
                $rateXmlObj = $this->_addRatesToXmlObj($methodListXmlObj);
                $rateXmlObj->addCustomChild('rate', null, array(
                    'label' => $this->renderShippingRateOption($this->getCurrentShippingRate()),
                    'selected' => 1
                ));
            }
        }

        return $methodListXmlObj->asNiceXml();
    }

    /**
     * Add message to describe that shipping is not required or not available
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $methodListXmlObj
     * @return Mage_XmlConnect_Block_Cart_Paypal_Mecl_Shippingmethods
     */
    protected function _addNoShippingMessage($methodListXmlObj)
    {
        $message = $this->_quote->isVirtual() ? $this->__('No shipping method required.')
            : $this->__('Sorry, no quotes are available for this order at this time.');
        $methodListXmlObj->addCustomChild('method', null, array('label' => $message));
        return $this;
    }

    /**
     * Add cart details to XML object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $methodListXmlObj
     * @param string $code
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    protected function _addRatesToXmlObj($methodListXmlObj, $code = '')
    {
        $attributes = $code ? array('label' => $this->getCarrierName($code)) : array();
        return $methodListXmlObj->addCustomChild('method', null, $attributes)->addCustomChild('rates');
    }
}
