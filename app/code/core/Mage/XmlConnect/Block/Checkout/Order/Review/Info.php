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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * One page checkout order info xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Order_Review_Info extends Mage_Checkout_Block_Onepage_Review_Info
{
    /**
     * Render order review items
     *
     * @return string
     */
    protected function _toHtml()
    {
        $itemsXmlObj = Mage::getModel('xmlconnect/simplexml_element', '<products></products>');
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $productSmallImageSize = Mage::getModel('xmlconnect/images')->getImageLimitParam('content/product_small');

        /* @var $item Mage_Sales_Model_Quote_Item */
        foreach ($this->getItems() as $item) {
            $type = $this->_getItemType($item);
            $renderer = $this->getItemRenderer($type)->setItem($item);

            /**
             * General information
             */
            $itemXml = $itemsXmlObj->addChild('item');
            $itemXml->addChild('entity_id', $item->getProduct()->getId());
            $itemXml->addChild('entity_type', $type);
            $itemXml->addChild('item_id', $item->getId());
            $itemXml->addChild('name', $itemsXmlObj->escapeXml($renderer->getProductName()));
            $itemXml->addChild('qty', $renderer->getQty());
            $icon = $renderer->getProductThumbnail()->resize($productSmallImageSize);

            $iconXml = $itemXml->addChild('icon', $icon);
            $iconXml->addAttribute('modification_time', filemtime($icon->getNewFile()));

            /**
             * Price
             */
            $exclPrice = $inclPrice = 0.00;
            if ($this->helper('tax')->displayCartPriceExclTax() || $this->helper('tax')->displayCartBothPrices()) {
                $typeOfDisplay = Mage::helper('weee')->typeOfDisplay($item, array(0, 1, 4), 'sales');
                if ($typeOfDisplay && $item->getWeeeTaxAppliedAmount()) {
                    $exclPrice = $item->getCalculationPrice() + $item->getWeeeTaxAppliedAmount()
                        + $item->getWeeeTaxDisposition();
                } else {
                    $exclPrice = $item->getCalculationPrice();
                }
            }

            if ($this->helper('tax')->displayCartPriceInclTax() || $this->helper('tax')->displayCartBothPrices()) {
                $incl = $this->helper('checkout')->getPriceInclTax($item);
                $typeOfDisplay = Mage::helper('weee')->typeOfDisplay($item, array(0, 1, 4), 'sales');
                if ($typeOfDisplay && $item->getWeeeTaxAppliedAmount()) {
                    $inclPrice = $incl + $item->getWeeeTaxAppliedAmount();
                } else {
                    $inclPrice = $incl - $item->getWeeeTaxDisposition();
                }
            }

            $exclPrice = Mage::helper('xmlconnect')->formatPriceForXml($exclPrice);
            $formattedExclPrice = $quote->getStore()->formatPrice($exclPrice, false);

            $inclPrice = Mage::helper('xmlconnect')->formatPriceForXml($inclPrice);
            $formattedInclPrice = $quote->getStore()->formatPrice($inclPrice, false);

            $priceXmlObj = $itemXml->addChild('price');
            $formattedPriceXmlObj = $itemXml->addChild('formated_price');

            if ($this->helper('tax')->displayCartBothPrices()) {
                $priceXmlObj->addAttribute('excluding_tax', $exclPrice);
                $priceXmlObj->addAttribute('including_tax', $inclPrice);

                $formattedPriceXmlObj->addAttribute('excluding_tax', $formattedExclPrice);
                $formattedPriceXmlObj->addAttribute('including_tax', $formattedInclPrice);
            } else {
                if ($this->helper('tax')->displayCartPriceExclTax()) {
                    $priceXmlObj->addAttribute('regular', $exclPrice);
                    $formattedPriceXmlObj->addAttribute('regular', $formattedExclPrice);
                }
                if ($this->helper('tax')->displayCartPriceInclTax()) {
                    $priceXmlObj->addAttribute('regular', $inclPrice);
                    $formattedPriceXmlObj->addAttribute('regular', $formattedInclPrice);
                }
            }

            /**
             * Subtotal
             */
            $exclPrice = $inclPrice = 0.00;
            if ($this->helper('tax')->displayCartPriceExclTax() || $this->helper('tax')->displayCartBothPrices()) {
                $typeOfDisplay = Mage::helper('weee')->typeOfDisplay($item, array(0, 1, 4), 'sales');
                if ($typeOfDisplay && $item->getWeeeTaxAppliedAmount()) {
                    $exclPrice = $item->getRowTotal() + $item->getWeeeTaxAppliedRowAmount()
                        + $item->getWeeeTaxRowDisposition();
                } else {
                    $exclPrice = $item->getRowTotal();
                }
            }
            if ($this->helper('tax')->displayCartPriceInclTax() || $this->helper('tax')->displayCartBothPrices()) {
                $incl = $this->helper('checkout')->getSubtotalInclTax($item);
                if (Mage::helper('weee')->typeOfDisplay($item, array(0, 1, 4), 'sales')
                    && $item->getWeeeTaxAppliedAmount()
                ) {
                    $inclPrice = $incl + $item->getWeeeTaxAppliedRowAmount();
                } else {
                    $inclPrice = $incl - $item->getWeeeTaxRowDisposition();
                }
            }

            $exclPrice = Mage::helper('xmlconnect')->formatPriceForXml($exclPrice);
            $formattedExclPrice = $quote->getStore()->formatPrice($exclPrice, false);

            $inclPrice = Mage::helper('xmlconnect')->formatPriceForXml($inclPrice);
            $formattedInclPrice = $quote->getStore()->formatPrice($inclPrice, false);

            $subtotalPriceXmlObj = $itemXml->addChild('subtotal');
            $subtotalFormattedPriceXmlObj = $itemXml->addChild('formated_subtotal');

            if ($this->helper('tax')->displayCartBothPrices()) {
                $subtotalPriceXmlObj->addAttribute('excluding_tax', $exclPrice);
                $subtotalPriceXmlObj->addAttribute('including_tax', $inclPrice);

                $subtotalFormattedPriceXmlObj->addAttribute('excluding_tax', $formattedExclPrice);
                $subtotalFormattedPriceXmlObj->addAttribute('including_tax', $formattedInclPrice);
            } else {
                if ($this->helper('tax')->displayCartPriceExclTax()) {
                    $subtotalPriceXmlObj->addAttribute('regular', $exclPrice);
                    $subtotalFormattedPriceXmlObj->addAttribute('regular', $formattedExclPrice);
                }
                if ($this->helper('tax')->displayCartPriceInclTax()) {
                    $subtotalPriceXmlObj->addAttribute('regular', $inclPrice);
                    $subtotalFormattedPriceXmlObj->addAttribute('regular', $formattedInclPrice);
                }
            }

            /**
             * Options list
             */
            $options = $renderer->getOptionList();
            if ($options) {
                $itemOptionsXml = $itemXml->addChild('options');
                foreach ($options as $option) {
                    $formattedOptionValue = $renderer->getFormatedOptionValue($option);
                    $optionXml = $itemOptionsXml->addChild('option');
                    $labelValue = $itemsXmlObj->escapeXml($option['label']);
                    $optionXml->addAttribute('label', $labelValue);
                    $textValue = $itemsXmlObj->escapeXml($formattedOptionValue['value']);
                    $optionXml->addAttribute('text', $textValue);
                }
            }

            /**
             * Downloadable product options
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
        }

        return $itemsXmlObj->asNiceXml();
    }
}
