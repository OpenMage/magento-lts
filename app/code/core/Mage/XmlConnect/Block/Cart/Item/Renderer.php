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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart default item xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Cart_Item_Renderer extends Mage_Checkout_Block_Cart_Item_Renderer
{
    /**
     * Add product details to XML object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $reviewXmlObj
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    public function addProductToXmlObj(Mage_XmlConnect_Model_Simplexml_Element $reviewXmlObj)
    {
        $item = $this->getItem();
        $productXmlObj = $reviewXmlObj->addCustomChild('item');
        $productXmlObj->addCustomChild('name', $this->escapeHtml($this->getProductName()));

        if ($options = $this->getOptionList()) {
            $optionsXmlObj = $productXmlObj->addChild('options');
            foreach ($options as $option) {
                $formattedOptionValue = $this->getFormatedOptionValue($option);

                if (isset($formattedOptionValue['full_view'])) {
                    $value = $formattedOptionValue['full_view'];
                } else {
                    $value = null;
                }

                $optionsXmlObj->addCustomChild('option', $value, array(
                    'label' => $this->escapeHtml($option['label']),
                    'value' => $formattedOptionValue['value']
                ));
            }
        }

        if (Mage::helper('xmlconnect')->checkApiVersion(Mage_XmlConnect_Helper_Data::DEVICE_API_V_23)) {
            $priceListXmlObj = $productXmlObj->addCustomChild('price_list');
            $this->_addPriceToXmlObj23($priceListXmlObj);
            $this->_addSubtotalToXmlObj23($priceListXmlObj);
        } else {
            $this->_addPriceToXmlObj($productXmlObj);
            $this->_addSubtotalToXmlObj($productXmlObj);
        }

        $productXmlObj->addCustomChild('qty', $item->getQty());
        $icon = $this->helper('xmlconnect/catalog_product_image')->init($this->getProduct(), 'thumbnail')
            ->resize(Mage::getModel('xmlconnect/images')->getImageLimitParam('content/product_small'));
        $iconXml = $productXmlObj->addChild('icon', $icon);
        $iconXml->addAttribute('modification_time', filemtime($icon->getNewFile()));

        return $reviewXmlObj;
    }

    /**
     * Add product price info to xml object. API version 23
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $priceListXmlObj
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    protected function _addPriceToXmlObj23(Mage_XmlConnect_Model_Simplexml_Element $priceListXmlObj)
    {
        $item = $this->getItem();
        $priceType = 'price';
        $priceXmlObj = $priceListXmlObj->addCustomChild('prices', null, array('id' => $priceType));

        if ($this->helper('tax')->displayCartPriceExclTax() || $this->helper('tax')->displayCartBothPrices()) {
            if (Mage::helper('weee')->typeOfDisplay($item, array(0, 1, 4), 'sales')
                && $item->getWeeeTaxAppliedAmount()
            ) {
                $exclPrice = $item->getCalculationPrice() + $item->getWeeeTaxAppliedAmount()
                    + $item->getWeeeTaxDisposition();
            } else {
                $exclPrice = $item->getCalculationPrice();
            }
            $exclPrice = $this->_formatPrice($exclPrice);
            $priceXmlObj->addCustomChild('price', $exclPrice, array(
                'id' => $priceType . '_excluding_tax',
                'label' => $this->__('Excl. Tax'),
                'formatted_value' => $priceXmlObj->escapeXml($exclPrice)
            ));
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
            $inclPrice = $this->_formatPrice($inclPrice);

            $priceXmlObj->addCustomChild('price', $inclPrice, array(
                'id' => $priceType . '_including_tax',
                'label' => $this->__('Incl. Tax'),
                'formatted_value' => $priceXmlObj->escapeXml($inclPrice)
            ));
        }

        if (Mage::helper('weee')->getApplied($item)) {
            $this->_addWeeeToXmlObj23($priceXmlObj);
        }

        return $priceListXmlObj;
    }

    /**
     * Add product subtotal info to xml object. API version 23
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $priceListXmlObj
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    protected function _addSubtotalToXmlObj23(Mage_XmlConnect_Model_Simplexml_Element $priceListXmlObj)
    {
        $item = $this->getItem();
        $priceType = 'subtotal';
        $subtotalXmlObj = $priceListXmlObj->addCustomChild('prices', null, array('id' => $priceType));

        if ($this->helper('tax')->displayCartPriceExclTax() || $this->helper('tax')->displayCartBothPrices()) {
            if (Mage::helper('weee')->typeOfDisplay($item, array(0, 1, 4), 'sales')
                && $item->getWeeeTaxAppliedAmount()) {
                $exclPrice = $item->getRowTotal() + $item->getWeeeTaxAppliedRowAmount()
                    + $item->getWeeeTaxRowDisposition();
            } else {
                $exclPrice = $item->getRowTotal();
            }
            $exclPrice = $this->_formatPrice($exclPrice);

            $subtotalXmlObj->addCustomChild('price', null, array(
                'id' => $priceType . '_excluding_tax',
                'label' => $this->__('Subtotal Excl. Tax'),
                'formatted_value' => $subtotalXmlObj->escapeXml($exclPrice)
            ));
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
            $inclPrice = $this->_formatPrice($inclPrice);

            $subtotalXmlObj->addCustomChild('price', null, array(
                'id' => $priceType . '_including_tax',
                'label' => $this->__('Subtotal Incl. Tax'),
                'formatted_value' => $subtotalXmlObj->escapeXml($inclPrice)
            ));
        }

        if (Mage::helper('weee')->getApplied($item)) {
            $this->_addWeeeToXmlObj23($subtotalXmlObj, true);
        }

        return $priceListXmlObj;
    }

    /**
     * Add weee tax product info to xml object API version 23
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $priceListXmlObj
     * @param bool $subtotalFlag use true to get subtotal product info
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    protected function _addWeeeToXmlObj23($priceListXmlObj, $subtotalFlag = false)
    {
        $item = $this->getItem();
        $weeeXmlObj = $priceListXmlObj->addCustomChild('price', null, array('id' => 'weee'));

        if ($subtotalFlag) {
            $incl = $this->helper('checkout')->getSubtotalInclTax($item);
        } else {
            $incl = $this->helper('checkout')->getPriceInclTax($item);
        }

        $typeOfDisplay2 = Mage::helper('weee')->typeOfDisplay($item, 2, 'sales');

        if (Mage::helper('weee')->typeOfDisplay($item, 1, 'sales') && $item->getWeeeTaxAppliedAmount()) {
            foreach (Mage::helper('weee')->getApplied($item) as $tax) {

                if ($subtotalFlag) {
                    $amount = $tax['row_amount'];
                } else {
                    $amount = $tax['amount'];
                }

                $weeeXmlObj->addCustomChild('item', $amount * 1, array(
                    'id' => 'weee_tax', 'label' => $tax['title'], 'formatted_value' => $this->_formatPrice($amount)
                ));
            }
        } elseif ($item->getWeeeTaxAppliedAmount()
            && ($typeOfDisplay2 || Mage::helper('weee')->typeOfDisplay($item, 4, 'sales'))
        ) {
            foreach (Mage::helper('weee')->getApplied($item) as $tax) {
                if ($subtotalFlag) {
                    $amount = $tax['row_amount_incl_tax'];
                } else {
                    $amount = $tax['amount_incl_tax'];
                }

                $weeeXmlObj->addCustomChild('item', $amount * 1, array(
                    'id' => 'weee_tax', 'label' => $tax['title'], 'formatted_value' => $this->_formatPrice($amount)
                ));
            }
        }

        if ($typeOfDisplay2 && $item->getWeeeTaxAppliedAmount()) {
            if ($subtotalFlag) {
                $totalExcl = $item->getRowTotal() + $item->getWeeeTaxAppliedRowAmount()
                    + $item->getWeeeTaxRowDisposition();
            } else {
                $totalExcl = $item->getCalculationPrice() + $item->getWeeeTaxAppliedAmount()
                    + $item->getWeeeTaxDisposition();
            }

            $weeeXmlObj->addCustomChild('item', $totalExcl * 1, array(
                'id' => 'total_excluding_tax',
                'label' => $this->__('Total'),
                'formatted_value' => $priceListXmlObj->escapeXml($this->_formatPrice($totalExcl))
            ));
        }

        if ($typeOfDisplay2 && $item->getWeeeTaxAppliedAmount()) {
            if ($subtotalFlag) {
                $totalIncl = $incl + $item->getWeeeTaxAppliedRowAmount();
            } else {
                $totalIncl = $incl + $item->getWeeeTaxAppliedAmount();
            }

            $weeeXmlObj->addCustomChild('item', $totalIncl * 1, array(
                'id' => 'total_including_tax',
                'label' => $this->__('Total incl. tax'),
                'formatted_value' => $priceListXmlObj->escapeXml($this->_formatPrice($totalIncl))
            ));
        }

        return $priceListXmlObj;
    }

    /**
     * Add product subtotal info to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $productXmlObj
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    protected function _addSubtotalToXmlObj(Mage_XmlConnect_Model_Simplexml_Element $productXmlObj)
    {
        $item = $this->getItem();
        $subtotalXmlObj = $productXmlObj->addCustomChild('subtotal');

        if ($this->helper('tax')->displayCartPriceExclTax() || $this->helper('tax')->displayCartBothPrices()) {
            if (Mage::helper('weee')->typeOfDisplay($item, array(0, 1, 4), 'sales')
                && $item->getWeeeTaxAppliedAmount()) {
                $exclPrice = $item->getRowTotal() + $item->getWeeeTaxAppliedRowAmount()
                    + $item->getWeeeTaxRowDisposition();
            } else {
                $exclPrice = $item->getRowTotal();
            }
            $exclPrice = $this->_formatPrice($exclPrice);

            $subtotalXmlObj->addAttribute('excluding_tax', $subtotalXmlObj->escapeXml($exclPrice));
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
            $inclPrice = $this->_formatPrice($inclPrice);

            $subtotalXmlObj->addAttribute('including_tax', $subtotalXmlObj->escapeXml($inclPrice));
        }

        if (Mage::helper('weee')->getApplied($item)) {
            $this->_addWeeeToXmlObj($subtotalXmlObj, true);
        }

        return $productXmlObj;
    }

    /**
     * Format product price
     *
     * @param int $price
     * @return float
     */
    protected function _formatPrice($price)
    {
        return $this->getQuote()->getStore()->formatPrice($price, false);
    }

    /**
     * Add product price info to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $productXmlObj
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    protected function _addPriceToXmlObj(Mage_XmlConnect_Model_Simplexml_Element $productXmlObj)
    {
        $item = $this->getItem();
        $priceXmlObj = $productXmlObj->addCustomChild('price');

        if ($this->helper('tax')->displayCartPriceExclTax() || $this->helper('tax')->displayCartBothPrices()) {
            if (Mage::helper('weee')->typeOfDisplay($item, array(0, 1, 4), 'sales')
                && $item->getWeeeTaxAppliedAmount()
            ) {
                $exclPrice = $item->getCalculationPrice() + $item->getWeeeTaxAppliedAmount()
                    + $item->getWeeeTaxDisposition();
            } else {
                $exclPrice = $item->getCalculationPrice();
            }
            $exclPrice = $this->_formatPrice($exclPrice);
            $priceXmlObj->addAttribute('excluding_tax', $priceXmlObj->escapeXml($exclPrice));
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
            $inclPrice = $this->_formatPrice($inclPrice);

            $priceXmlObj->addAttribute('including_tax', $priceXmlObj->escapeXml($inclPrice));
        }

        if (Mage::helper('weee')->getApplied($item)) {
            $this->_addWeeeToXmlObj($priceXmlObj);
        }

        return $productXmlObj;
    }

    /**
     * Add weee tax product info to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $priceXmlObj
     * @param bool $subtotalFlag use true to get subtotal product info
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    protected function _addWeeeToXmlObj(Mage_XmlConnect_Model_Simplexml_Element $priceXmlObj, $subtotalFlag = false)
    {
        $item = $this->getItem();
        $weeeXmlObj = $priceXmlObj->addCustomChild('weee');

        if ($subtotalFlag) {
            $incl = $this->helper('checkout')->getSubtotalInclTax($item);
        } else {
            $incl = $this->helper('checkout')->getPriceInclTax($item);
        }

        $typeOfDisplay2 = Mage::helper('weee')->typeOfDisplay($item, 2, 'sales');

        if (Mage::helper('weee')->typeOfDisplay($item, 1, 'sales') && $item->getWeeeTaxAppliedAmount()) {
            foreach (Mage::helper('weee')->getApplied($item) as $tax) {

                if ($subtotalFlag) {
                    $amount = $tax['row_amount'];
                } else {
                    $amount = $tax['amount'];
                }

                $weeeXmlObj->addCustomChild('item', null, array(
                    'name' => $tax['title'], 'amount' => $this->_formatPrice($amount)
                ));
            }
        } elseif ($item->getWeeeTaxAppliedAmount()
            && ($typeOfDisplay2 || Mage::helper('weee')->typeOfDisplay($item, 4, 'sales'))
        ) {
            foreach (Mage::helper('weee')->getApplied($item) as $tax) {
                if ($subtotalFlag) {
                    $amount = $tax['row_amount_incl_tax'];
                } else {
                    $amount = $tax['amount_incl_tax'];
                }

                $weeeXmlObj->addCustomChild('item', null, array(
                    'name' => $tax['title'], 'amount' => $this->_formatPrice($amount)
                ));
            }
        }

        if ($typeOfDisplay2 && $item->getWeeeTaxAppliedAmount()) {
            if ($subtotalFlag) {
                $totalExcl = $item->getRowTotal() + $item->getWeeeTaxAppliedRowAmount()
                    + $item->getWeeeTaxRowDisposition();
            } else {
                $totalExcl = $item->getCalculationPrice() + $item->getWeeeTaxAppliedAmount()
                    + $item->getWeeeTaxDisposition();
            }

            $totalExcl = $this->_formatPrice($totalExcl);
            $priceXmlObj->addAttribute('total_excluding_tax', $priceXmlObj->escapeXml($totalExcl));
        }

        if ($typeOfDisplay2 && $item->getWeeeTaxAppliedAmount()) {
            if ($subtotalFlag) {
                $totalIncl = $incl + $item->getWeeeTaxAppliedRowAmount();
            } else {
                $totalIncl = $incl + $item->getWeeeTaxAppliedAmount();
            }

            $totalIncl = $this->_formatPrice($totalIncl);
            $priceXmlObj->addAttribute('total_including_tax', $priceXmlObj->escapeXml($totalIncl));
        }

        return $priceXmlObj;
    }
}
