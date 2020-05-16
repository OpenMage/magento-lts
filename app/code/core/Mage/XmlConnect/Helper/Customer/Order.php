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
 * Customer order details helper
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Helper_Customer_Order extends Mage_Core_Helper_Abstract
{
    /**#@+
     * Price display type
     *
     * @see Mage_Weee_Helper_Data::typeOfDisplay(...);
     */
    const PRICE_DISPLAY_TYPE_1 = 1;
    const PRICE_DISPLAY_TYPE_2 = 2;
    const PRICE_DISPLAY_TYPE_4 = 4;
    const PRICE_DISPLAY_TYPE_14 = 14;
    /**#@-*/

    /**
     * Including tax id
     */
    const INCLUDING_TAX_ID = 'including_tax';

    /**
     * Excluding tax id
     */
    const EXCLUDING_TAX_ID = 'excluding_tax';

    /**
     * Add Weee taxes child to the XML
     *
     * @param Mage_Core_Block_Template $renderer Product renderer
     * @param Mage_Sales_Model_Order_Item $item
     * @param Mage_XmlConnect_Model_Simplexml_Element $priceXml
     * @param Mage_XmlConnect_Model_Simplexml_Element $subtotalXml
     * @param bool $isIncludeTax
     * @return null
     */
    public function addPriceAndSubtotalToXml(Mage_Core_Block_Template $renderer, Mage_Sales_Model_Order_Item $item,
        Mage_XmlConnect_Model_Simplexml_Element $priceXml, Mage_XmlConnect_Model_Simplexml_Element $subtotalXml,
        $isIncludeTax = false)
    {
        $weeeParams = array();

        $typesOfDisplay = $renderer->getTypesOfDisplay();
        if ($isIncludeTax) {
            $nodeName = self::INCLUDING_TAX_ID;
            $nodeLabel = $this->__('Incl. Tax');

            $inclPrice      = $renderer->helper('checkout')->getPriceInclTax($item);
            $inclSubtotal   = $renderer->helper('checkout')->getSubtotalInclTax($item);

            if ($typesOfDisplay[self::PRICE_DISPLAY_TYPE_14]) {
                $price      = $inclPrice + $renderer->getWeeeTaxAppliedAmount();
                $subtotal   = $inclSubtotal + $item->getWeeeTaxAppliedRowAmount();
            } else {
                $price      = $inclPrice - $renderer->getWeeeTaxDisposition();
                $subtotal   = $inclSubtotal - $item->getWeeeTaxRowDisposition();
            }
            $weeeParams['include'] = $inclPrice;
        } else {
            $nodeName = self::EXCLUDING_TAX_ID;
            $nodeLabel = $this->__('Excl. Tax');

            if ($typesOfDisplay[self::PRICE_DISPLAY_TYPE_14]) {
                $price = $item->getPrice() + $renderer->getWeeeTaxAppliedAmount()
                    + $renderer->getWeeeTaxDisposition();
                $subtotal = $item->getRowTotal() + $item->getWeeeTaxAppliedRowAmount()
                    + $item->getWeeeTaxRowDisposition();
            } else {
                $price = $item->getPrice();
                $subtotal = $item->getRowTotal();
            }
        }

        $configNode = array('value' => $this->formatPrice($renderer, $price));

        if ($renderer->helper('tax')->displaySalesBothPrices()) {
            $configNode['label'] = $nodeLabel;
        }

        $this->addWeeeTaxesToPriceXml(
            $renderer, $item, $priceXml->addCustomChild($nodeName, null, $configNode), $weeeParams
        );

        $configNode['value']        = $this->formatPrice($renderer, $subtotal);
        $weeeParams['include']      = $isIncludeTax ? $inclSubtotal : null;
        $weeeParams['is_subtotal']  = true;

        $this->addWeeeTaxesToPriceXml(
            $renderer, $item, $subtotalXml->addCustomChild($nodeName, null, $configNode), $weeeParams
        );
    }

    /**
     * Add Weee taxes child to the XML. Api version 23
     *
     * @param Mage_Core_Block_Template $renderer Product renderer
     * @param Mage_Sales_Model_Order_Item $item
     * @param Mage_XmlConnect_Model_Simplexml_Element $priceXml
     * @param Mage_XmlConnect_Model_Simplexml_Element $subtotalXml
     * @param bool $isIncludeTax
     * @return null
     */
    public function addPriceAndSubtotalToXmlApi23(Mage_Core_Block_Template $renderer, Mage_Sales_Model_Order_Item $item,
        Mage_XmlConnect_Model_Simplexml_Element $priceXml,
        Mage_XmlConnect_Model_Simplexml_Element $subtotalXml,
        $isIncludeTax = false
    ) {
        $weeeParams = array();

        $typesOfDisplay = $renderer->getTypesOfDisplay();
        if ($isIncludeTax) {
            $nodeId = self::INCLUDING_TAX_ID;
            $nodeLabel = $this->__('Incl. Tax');

            $inclPrice      = $renderer->helper('checkout')->getPriceInclTax($item);
            $inclSubtotal   = $renderer->helper('checkout')->getSubtotalInclTax($item);

            if ($typesOfDisplay[self::PRICE_DISPLAY_TYPE_14]) {
                $price      = $inclPrice + $renderer->getWeeeTaxAppliedAmount();
                $subtotal   = $inclSubtotal + $item->getWeeeTaxAppliedRowAmount();
            } else {
                $price      = $inclPrice - $renderer->getWeeeTaxDisposition();
                $subtotal   = $inclSubtotal - $item->getWeeeTaxRowDisposition();
            }
            $weeeParams['include'] = $inclPrice;
        } else {
            $nodeId = self::EXCLUDING_TAX_ID;
            $nodeLabel = $this->__('Excl. Tax');

            if ($typesOfDisplay[self::PRICE_DISPLAY_TYPE_14]) {
                $price = $item->getPrice() + $renderer->getWeeeTaxAppliedAmount()
                    + $renderer->getWeeeTaxDisposition();
                $subtotal = $item->getRowTotal() + $item->getWeeeTaxAppliedRowAmount()
                    + $item->getWeeeTaxRowDisposition();
            } else {
                $price = $item->getPrice();
                $subtotal = $item->getRowTotal();
            }
        }

        $configNode = array('id' => $nodeId);
        $priceValue = $this->formatPrice($renderer, $price);

        if ($renderer->helper('tax')->displaySalesBothPrices()) {
            $configNode['label'] = $nodeLabel;
        }

        $priceConfig = $configNode;
        $idPrefix = 'price_';
        $priceConfig['id'] = $idPrefix . $priceConfig['id'];
        $priceConfig['formatted_price'] = $priceValue;
        $price = Mage::helper('xmlconnect')->formatPriceForXml($price);
        $priceXml->addCustomChild('price', $price, $priceConfig);
        $this->addWeeeTaxesToPriceXmlApi23($renderer, $item, $priceXml, $weeeParams, $idPrefix, $isIncludeTax);

        $priceValue                 = $this->formatPrice($renderer, $subtotal);
        $weeeParams['include']      = $isIncludeTax ? $inclSubtotal : null;
        $weeeParams['is_subtotal']  = true;

        $subtotalConfig = $configNode;
        $idPrefix = 'subtotal_';
        $subtotalConfig['id'] = $idPrefix . $subtotalConfig['id'];
        $subtotalConfig['formatted_price'] = $priceValue;
        $subtotal = Mage::helper('xmlconnect')->formatPriceForXml($subtotal);
        $subtotalXml->addCustomChild('price', $subtotal, $subtotalConfig);
        $this->addWeeeTaxesToPriceXmlApi23($renderer, $item, $subtotalXml, $weeeParams, $idPrefix, $isIncludeTax);
    }

    /**
     * Add Product options to XML
     *
     * @param Mage_Core_Block_Template $renderer Product renderer
     * @param Mage_XmlConnect_Model_Simplexml_Element $itemXml
     * @return null
     */
    public function addItemOptionsToXml(Mage_Core_Block_Template $renderer,
        Mage_XmlConnect_Model_Simplexml_Element $itemXml
    ) {
        $options = $renderer->getItemOptions();
        if (!empty($options)) {
            $optionsXml = $itemXml->addCustomChild('options');

            foreach ($options as $option) {
                $value = false;
                $formattedOptionValue = $renderer->getFormatedOptionValue($option);
                if (isset($formattedOptionValue['full_view']) && isset($formattedOptionValue['value'])) {
                    $value = $formattedOptionValue['value'];
                } elseif (isset($option['print_value'])) {
                    $value = $option['print_value'];
                } elseif (isset($option['value'])) {
                    $value = $option['value'];
                }
                if ($value) {
                    $optionsXml->addCustomChild('option', $optionsXml->escapeXml($value), array(
                        'label' => $option['label']
                    ));
                }
            }
        }
    }

    /**
     * Add Weee taxes child to the XML
     *
     * @param Mage_Core_Block_Template $renderer Product renderer
     * @param Mage_Sales_Model_Order_Item $item
     * @param Mage_XmlConnect_Model_Simplexml_Element $parentXml
     * @param array $params Params for Weee taxes: 'include' - Price including tax, 'is_subtotal' - Flag of subtotal
     * @return null
     */
    public function addWeeeTaxesToPriceXml(Mage_Core_Block_Template $renderer, Mage_Sales_Model_Order_Item $item,
        Mage_XmlConnect_Model_Simplexml_Element $parentXml, $params = array()
    ) {
        $weeTaxes = $renderer->getWeeeTaxes();
        if (empty($weeTaxes)) {
            return;
        }

        $typesOfDisplay = $renderer->getTypesOfDisplay();

        $row = isset($params['is_subtotal']) && $params['is_subtotal'] ? 'row_' : '';

        /** @var $weeeXml Mage_XmlConnect_Model_Simplexml_Element */
        if ($typesOfDisplay[self::PRICE_DISPLAY_TYPE_1]) {
            $weeeXml = $parentXml->addChild('weee');
            foreach ($weeTaxes as $tax) {
                $weeeXml->addCustomChild('tax', $this->formatPrice($renderer, $tax[$row . 'amount']), array(
                    'label' => $tax['title']
                ));
            }
        } elseif ($typesOfDisplay[self::PRICE_DISPLAY_TYPE_2] || $typesOfDisplay[self::PRICE_DISPLAY_TYPE_4]) {
            $weeeXml = $parentXml->addChild('weee');
            foreach ($weeTaxes as $tax) {
                $weeeXml->addCustomChild('tax', $this->formatPrice($renderer, $tax[$row . 'amount_incl_tax']), array(
                    'label' => $tax['title']
                ));
            }
        }

        if ($typesOfDisplay[self::PRICE_DISPLAY_TYPE_2]) {
            if (!empty($params['include'])) {
                // including tax
                if (isset($params['is_subtotal'])) {
                    $total = $params['include'] + $item->getWeeeTaxAppliedRowAmount();
                } else {
                    $total = $params['include'] + $renderer->getWeeeTaxAppliedAmount();
                }
            } else {
                // excluding tax
                if (isset($params['is_subtotal'])) {
                    $total = $item->getRowTotal() + $item->getWeeeTaxAppliedRowAmount()
                        + $item->getWeeeTaxRowDisposition();
                } else {
                    $total = $item->getPrice() + $renderer->getWeeeTaxAppliedAmount()
                        + $renderer->getWeeeTaxDisposition();
                }
            }

            if (!isset($weeeXml)) {
                $weeeXml = $parentXml->addChild('weee');
            }

            $weeeXml->addCustomChild('total', $this->formatPrice($renderer, $total), array(
                'label' => $renderer->helper('weee')->__('Total')
            ));
        }
    }

    /**
     * Add Weee taxes child to the XML. Api version 23
     *
     * @param Mage_Core_Block_Template $renderer Product renderer
     * @param Mage_Sales_Model_Order_Item $item
     * @param Mage_XmlConnect_Model_Simplexml_Element $parentXml
     * @param array $params Params for Weee taxes: 'include' - Price including tax, 'is_subtotal' - Flag of subtotal
     * @param string $idPrefix
     * @param bool $isIncludeTax
     * @return null
     */
    public function addWeeeTaxesToPriceXmlApi23(Mage_Core_Block_Template $renderer, Mage_Sales_Model_Order_Item $item,
        Mage_XmlConnect_Model_Simplexml_Element $parentXml, $params = array(), $idPrefix, $isIncludeTax
    ) {
        $weeTaxes = $renderer->getWeeeTaxes();
        if (empty($weeTaxes)) {
            return;
        }

        $typesOfDisplay = $renderer->getTypesOfDisplay();

        $row = isset($params['is_subtotal']) && $params['is_subtotal'] ? 'row_' : '';

        if ($isIncludeTax) {
            $weeeXml = $parentXml->addCustomChild('price', null, array('id' => 'weee'));
            /** @var $weeeXml Mage_XmlConnect_Model_Simplexml_Element */
            if ($typesOfDisplay[self::PRICE_DISPLAY_TYPE_1]) {
                foreach ($weeTaxes as $tax) {
                    $taxAmount = Mage::helper('xmlconnect')->formatPriceForXml($tax[$row . 'amount']);
                    $weeeXml->addCustomChild('item', $taxAmount, array(
                        'label' => $tax['title'],
                        'formatted_price' => $this->formatPrice($renderer, $tax[$row . 'amount'])
                    ));
                }
            } elseif ($typesOfDisplay[self::PRICE_DISPLAY_TYPE_2] || $typesOfDisplay[self::PRICE_DISPLAY_TYPE_4]) {
                foreach ($weeTaxes as $tax) {
                    $taxAmount = Mage::helper('xmlconnect')->formatPriceForXml($tax[$row . 'amount_incl_tax']);
                    $weeeXml->addCustomChild('item', $taxAmount, array(
                        'label' => $tax['title'],
                        'formatted_price' => $this->formatPrice($renderer, $tax[$row . 'amount_incl_tax'])
                    ));
                }
            }
        }

        if ($typesOfDisplay[self::PRICE_DISPLAY_TYPE_2]) {
            if (!empty($params['include'])) {
                // including tax
                if (isset($params['is_subtotal'])) {
                    $total = $params['include'] + $item->getWeeeTaxAppliedRowAmount();
                } else {
                    $total = $params['include'] + $renderer->getWeeeTaxAppliedAmount();
                }
            } else {
                // excluding tax
                if (isset($params['is_subtotal'])) {
                    $total = $item->getRowTotal() + $item->getWeeeTaxAppliedRowAmount()
                        + $item->getWeeeTaxRowDisposition();
                } else {
                    $total = $item->getPrice() + $renderer->getWeeeTaxAppliedAmount()
                        + $renderer->getWeeeTaxDisposition();
                }
            }

            $totalNodeId = $idPrefix . 'fpt_total_' . ($isIncludeTax ? self::INCLUDING_TAX_ID : self::EXCLUDING_TAX_ID);
            $parentXml->addCustomChild('price', Mage::helper('xmlconnect')->formatPriceForXml($total), array(
                'id'    => $totalNodeId,
                'label' => $isIncludeTax ? $renderer->helper('weee')->__('Total incl. tax')
                    : $renderer->helper('weee')->__('Total excl. tax'),
                'formatted_price' => $this->formatPrice($renderer, $total)
            ));
        }
    }

    /**
     * Add item quantities to the XML
     *
     * @param Mage_Core_Block_Template $renderer Product renderer
     * @param Mage_XmlConnect_Model_Simplexml_Element $quantityXml
     * @param Mage_Sales_Model_Order_Item $item
     * @return null
     */
    public function addQuantityToXml(Mage_Core_Block_Template $renderer,
        Mage_XmlConnect_Model_Simplexml_Element $quantityXml, Mage_Sales_Model_Order_Item $item
    ) {
        $qty = 1 * $item->getQtyOrdered();
        if ($qty > 0) {
            $quantityXml->addCustomChild('value', $qty, array('label' => $this->__('Ordered')));
        }
        $qty = 1 * $item->getQtyShipped();
        if ($qty > 0) {
            $quantityXml->addCustomChild('value', $qty, array('label' => $this->__('Shipped')));
        }
        $qty = 1 * $item->getQtyCanceled();
        if ($qty > 0) {
            $quantityXml->addCustomChild('value', $qty, array('label' => $this->__('Canceled')));
        }
        $qty = 1 * $item->getQtyRefunded();
        if ($qty > 0) {
            $quantityXml->addCustomChild('value', $qty, array('label' => $this->__('Refunded')));
        }
    }

    /**
     * Format price using order currency
     *
     * @param Mage_Core_Block_Template $renderer Product renderer
     * @param float $price
     * @return string
     */
    public function formatPrice(Mage_Core_Block_Template $renderer, $price)
    {
        return $renderer->getOrder()->getOrderCurrency()->formatPrecision($price, 2, array(), false);
    }
}
