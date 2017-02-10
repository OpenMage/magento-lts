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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart items renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Cart_Items extends Mage_Checkout_Block_Cart_Abstract
{
    /**
     * Add product block to cart
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $xmlObject
     * @param Mage_Sales_Model_Quote $quote
     * @return null
     */
    public function addCartProductsToXmlObj($xmlObject, $quote)
    {
        $productSmallImageSize = Mage::getModel('xmlconnect/images')->getImageLimitParam('content/product_small');
        $products = $xmlObject->addCustomChild('products');
        /* @var $item Mage_Sales_Model_Quote_Item */
        foreach ($this->getItems() as $item) {
            $type = $item->getProductType();
            $renderer = $this->getItemRenderer($type)->setItem($item);
            /**
             * General information
             */
            $itemXml = $products->addCustomChild('item', null, array('entity_id' => $item->getProduct()->getId()));
            $itemXml->addCustomChild('entity_type', $type);
            $itemXml->addCustomChild('item_id', $item->getId());
            $itemXml->addCustomChild('name', $xmlObject->escapeXml($renderer->getProductName()));
            $itemXml->addCustomChild('code', 'cart[' . $item->getId() . '][qty]');
            $itemXml->addCustomChild('qty', $renderer->getQty());
            $icon = $renderer->getProductThumbnail()->resize($productSmallImageSize);
            $iconXml = $itemXml->addChild('icon', $icon);
            $iconXml->addAttribute('modification_time', filemtime($icon->getNewFile()));

            /**
             * Price
             */
            $pricesXmlObj = $itemXml->addCustomChild('price_list');
            $exclPrice = $inclPrice = 0;
            if ($this->helper('tax')->displayCartPriceExclTax() || $this->helper('tax')->displayCartBothPrices()) {
                if (Mage::helper('weee')->typeOfDisplay($item, array(0, 1, 4), 'sales')
                    && $item->getWeeeTaxAppliedAmount()
                ) {
                    $exclPrice = $item->getCalculationPrice() + $item->getWeeeTaxAppliedAmount()
                        + $item->getWeeeTaxDisposition();
                } else {
                    $exclPrice = $item->getCalculationPrice();
                }
            }
            if ($this->helper('tax')->displayCartPriceInclTax() || $this->helper('tax')->displayCartBothPrices()) {
                $incl = $this->helper('checkout')->getPriceInclTax($item);
                if (Mage::helper('weee')->typeOfDisplay($item, array(0, 1, 4), 'sales')
                    && $item->getWeeeTaxAppliedAmount()
                ) {
                    $inclPrice = $incl + $item->getWeeeTaxAppliedAmount();
                } else {
                    $inclPrice = $incl - $item->getWeeeTaxDisposition();
                }
            }
            $exclPrice = Mage::helper('xmlconnect')->formatPriceForXml($exclPrice);
            $paypalPrice = Mage::helper('xmlconnect')->formatPriceForXml($item->getCalculationPrice());
            $formattedExclPrice = $quote->getStore()->formatPrice($exclPrice, false);
            $inclPrice = Mage::helper('xmlconnect')->formatPriceForXml($inclPrice);
            $formattedInclPrice = $quote->getStore()->formatPrice($inclPrice, false);

            $priceXmlObj = $pricesXmlObj->addCustomChild('prices', null, array('id' => 'price'));

            if ($this->helper('tax')->displayCartBothPrices()) {
                $priceXmlObj->addCustomChild('price', $exclPrice, array(
                    'id' => 'excluding_tax',
                    'label' => $this->__('Excl. Tax'),
                    'formatted_value' => $formattedExclPrice
                ));

                $priceXmlObj->addCustomChild('price', $inclPrice, array(
                    'id' => 'including_tax',
                    'label' => $this->__('Incl. Tax'),
                    'formatted_value' => $formattedInclPrice
                ));
            } else {
                if ($this->helper('tax')->displayCartPriceExclTax()) {
                    $priceXmlObj->addCustomChild('price', $exclPrice, array(
                        'id' => 'regular',
                        'label' => $this->__('Unit Price'),
                        'formatted_value' => $formattedExclPrice
                    ));
                }
                if ($this->helper('tax')->displayCartPriceInclTax()) {
                    $priceXmlObj->addCustomChild('price', $inclPrice, array(
                        'id' => 'regular',
                        'label' => $this->__('Unit Price'),
                        'formatted_value' => $formattedInclPrice
                    ));
                }
            }

            /**
             * Info for paypal MEP if it's enabled
             */
            $appConfig = Mage::helper('xmlconnect')->getApplication()->loadConfiguration()->getRenderConf();
            $isMepActive = $appConfig['paypal']['isActive'];

            $paypalMepIsAvailable = Mage::getModel('xmlconnect/payment_method_paypal_mep')->isAvailable(null);
            if ($paypalMepIsAvailable && $isMepActive) {
                $paypalPriceXmlObj = $pricesXmlObj->addCustomChild('prices', null, array('id' => 'paypal'));

                $paypalPriceXmlObj->addCustomChild('price', $paypalPrice, array(
                    'id' => 'regular',
                    'label' => $this->__('Unit Price'),
                    'formatted_value' => $quote->getStore()->formatPrice($paypalPrice, false)
                ));

                $paypalSubtotalPrice = Mage::helper('xmlconnect')->formatPriceForXml($item->getRowTotal());
                $paypalPriceXmlObj->addCustomChild('price', $paypalSubtotalPrice, array(
                    'id' => 'subtotal',
                    'label' => $this->__('Subtotal'),
                    'formatted_value' => $quote->getStore()->formatPrice($paypalSubtotalPrice, false)
                ));
            }

            /**
             * Subtotal
             */
            $subtotalExclTax = $subtotalInclTax = 0;
            if ($this->helper('tax')->displayCartPriceExclTax() || $this->helper('tax')->displayCartBothPrices()) {
                if (Mage::helper('weee')->typeOfDisplay($item, array(0, 1, 4), 'sales')
                    && $item->getWeeeTaxAppliedAmount()
                ) {
                    $subtotalExclTax = $item->getRowTotal() + $item->getWeeeTaxAppliedRowAmount()
                        + $item->getWeeeTaxRowDisposition();
                } else {
                     $subtotalExclTax = $item->getRowTotal();
                }
            }
            if ($this->helper('tax')->displayCartPriceInclTax() || $this->helper('tax')->displayCartBothPrices()) {
                $incl = $this->helper('checkout')->getSubtotalInclTax($item);
                if (Mage::helper('weee')->typeOfDisplay($item, array(0, 1, 4), 'sales')
                    && $item->getWeeeTaxAppliedAmount()
                ) {
                    $subtotalInclTax = $incl + $item->getWeeeTaxAppliedRowAmount();
                } else {
                    $subtotalInclTax = $incl - $item->getWeeeTaxRowDisposition();
                }
            }

            $subtotalExclTax = Mage::helper('xmlconnect')->formatPriceForXml($subtotalExclTax);
            $formattedSubtotalExcl = $quote->getStore()->formatPrice($subtotalExclTax, false);

            $subtotalInclTax = Mage::helper('xmlconnect')->formatPriceForXml($subtotalInclTax);
            $formattedSubtotalIncl = $quote->getStore()->formatPrice($subtotalInclTax, false);

            $priceXmlObj = $pricesXmlObj->addCustomChild('prices', null, array('id' => 'subtotal'));

            if ($this->helper('tax')->displayCartBothPrices()) {
                $priceXmlObj->addCustomChild('price', $subtotalExclTax, array(
                    'id' => 'excluding_tax',
                    'label' => $this->__('Subtotal Excl. Tax'),
                    'formatted_value' => $formattedSubtotalExcl
                ));

                $priceXmlObj->addCustomChild('price', $subtotalInclTax, array(
                    'id' => 'including_tax',
                    'label' => $this->__('Subtotal Incl. Tax'),
                    'formatted_value' => $formattedSubtotalIncl
                ));
            } else {
                if ($this->helper('tax')->displayCartPriceExclTax()) {
                    $priceXmlObj->addCustomChild('price', $subtotalExclTax, array(
                        'id' => 'regular',
                        'label' => $this->__('Subtotal'),
                        'formatted_value' => $formattedSubtotalExcl
                    ));
                }
                if ($this->helper('tax')->displayCartPriceInclTax()) {
                    $priceXmlObj->addCustomChild('price', $subtotalInclTax, array(
                        'id' => 'regular',
                        'label' => $this->__('Subtotal'),
                        'formatted_value' => $formattedSubtotalIncl
                    ));
                }
            }

            /**
             * Options list
             */
            $options = $renderer->getOptionList();
            if ($options) {
                $itemOptionsXml = $itemXml->addCustomChild('options');
                foreach ($options as $_option) {
                    $formattedOptionValue = $renderer->getFormatedOptionValue($_option);
                    $itemOptionsXml->addCustomChild('option', null, array(
                        'label' => $xmlObject->xmlAttribute($_option['label']),
                        'text' => $xmlObject->xmlAttribute($formattedOptionValue['value'])
                    ));
                }
            }

            /**
             * Downloadable products
             */
            $links = $renderer->getLinks();
            if ($links) {
                $itemOptionsXml = $itemXml->addCustomChild('options', null, array(
                    'label' => $renderer->getLinksTitle()
                ));
                foreach ($links as $link) {
                    $itemOptionsXml->addCustomChild('option', null, array('label' => $link->getTitle()));
                }
            }

            /**
             * Item messages
             */
            $messages = $renderer->getMessages();
            if ($messages) {
                $itemMessagesXml = $itemXml->addCustomChild('messages');
                foreach ($messages as $message) {
                    $messageXml = $itemMessagesXml->addCustomChild('option');
                    $messageXml->addCustomChild('type', $message['type']);
                    $messageXml->addCustomChild('text', $xmlObject->escapeXml($message['text']));
                }
            }
        }
    }
}
