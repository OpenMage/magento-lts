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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer order item xml renderer for bundle product type
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Order_Item_Renderer_Bundle extends Mage_Bundle_Block_Sales_Order_Items_Renderer
{
    /**
     * Add item to XML object
     * (get from template: bundle/sales/order/items/renderer.phtml)
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $orderItemXmlObj
     * @return null
     */
    public function addItemToXmlObject(Mage_XmlConnect_Model_Simplexml_Element $orderItemXmlObj)
    {
        if ($this->getNewApi()) {
            $this->addItemToXmlObjectApi23($orderItemXmlObj);
            return;
        }
        /** @var $parentItem Mage_Sales_Model_Order_Item */
        $parentItem     = $this->getItem();
        $items          = array_merge(array($parentItem), $parentItem->getChildrenItems());
        $prevOptionId   = '';

        /** @var $weeeHelper Mage_Weee_Helper_Data */
        $weeeHelper = $this->helper('weee');
        /** @var $taxHelper Mage_Tax_Helper_Data */
        $taxHelper  = $this->helper('tax');

        /** @var $itemXml Mage_XmlConnect_Model_Simplexml_Element */
        $itemXml    = $orderItemXmlObj->addChild('item');
        /** @var $optionsXml Mage_XmlConnect_Model_Simplexml_Element */
        $optionsXml = $itemXml->addChild('related_products');

        $this->setWeeeTaxAppliedAmount($parentItem->getWeeeTaxAppliedAmount());
        $this->setWeeeTaxDisposition($parentItem->getWeeeTaxDisposition());

        $typeOfDisplay1 = $weeeHelper->typeOfDisplay($parentItem, 1, 'sales')
            && $this->getWeeeTaxAppliedAmount();
        $typeOfDisplay2 = $weeeHelper->typeOfDisplay($parentItem, 2, 'sales')
            && $this->getWeeeTaxAppliedAmount();
        $typeOfDisplay4 = $weeeHelper->typeOfDisplay($parentItem, 4, 'sales')
            && $this->getWeeeTaxAppliedAmount();
        $typeOfDisplay014 = $weeeHelper->typeOfDisplay($parentItem, array(0, 1, 4), 'sales')
            && $this->getWeeeTaxAppliedAmount();

        $this->setTypesOfDisplay(array(
            Mage_XmlConnect_Helper_Customer_Order::PRICE_DISPLAY_TYPE_1  => $typeOfDisplay1,
            Mage_XmlConnect_Helper_Customer_Order::PRICE_DISPLAY_TYPE_2  => $typeOfDisplay2,
            Mage_XmlConnect_Helper_Customer_Order::PRICE_DISPLAY_TYPE_4  => $typeOfDisplay4,
            Mage_XmlConnect_Helper_Customer_Order::PRICE_DISPLAY_TYPE_14 => $typeOfDisplay014
        ));
        $this->setWeeeTaxes($weeeHelper->getApplied($parentItem));

        /** @var $item Mage_Sales_Model_Order_Item */
        foreach ($items as $item) {
            $isOption = $item->getParentItem() ? true : false;

            /** @var $objectXml Mage_XmlConnect_Model_Simplexml_Element */
            if ($isOption) {
                $objectXml = $optionsXml->addChild('item');
            } else {
                $objectXml = $itemXml;
            }
            $objectXml->addAttribute('product_id', $item->getProductId());
            $objectXml->addCustomChild('entity_type', $item->getProductType());

            if ($isOption) {
                $attributes = $this->getSelectionAttributes($item);
                if ($prevOptionId != $attributes['option_id']) {
                    $objectXml->addAttribute('label', $objectXml->xmlAttribute($attributes['option_label']));
                    $prevOptionId = $attributes['option_id'];
                }
            }

            $objectXml->addCustomChild('sku', Mage::helper('core/string')->splitInjection($item->getSku()));

            if ($isOption) {
                $name = $this->getValueHtml($item);
            } else {
                $name = $item->getName();
            }
            $objectXml->addCustomChild('name', $name);

            // set prices exactly for the Bundle product, but not for related products
            if (!$isOption) {
                /** @var $priceXml Mage_XmlConnect_Model_Simplexml_Element */
                $priceXml = $objectXml->addChild('price');
                /** @var $subtotalXml Mage_XmlConnect_Model_Simplexml_Element */
                $subtotalXml = $objectXml->addChild('subtotal');

                // Price excluding tax
                if ($taxHelper->displaySalesBothPrices() || $taxHelper->displaySalesPriceExclTax()) {
                    Mage::helper('xmlconnect/customer_order')->addPriceAndSubtotalToXml(
                        $this, $parentItem, $priceXml, $subtotalXml
                    );
                }

                // Price including tax
                if ($taxHelper->displaySalesBothPrices() || $taxHelper->displaySalesPriceInclTax()) {
                    Mage::helper('xmlconnect/customer_order')->addPriceAndSubtotalToXml(
                        $this, $parentItem, $priceXml, $subtotalXml, true
                    );
                }
            }

            // set quantities
            /** @var $qtyXml Mage_XmlConnect_Model_Simplexml_Element */
            if (($isOption && $this->isChildCalculated()) || (!$isOption && !$this->isChildCalculated())) {
                $qtyXml = $objectXml->addChild('qty');
                if ($item->getQtyOrdered() > 0) {
                    $qtyXml->addCustomChild('value', $item->getQtyOrdered() * 1, array(
                        'label' => Mage::helper('sales')->__('Ordered')
                    ));
                }
                if ($item->getQtyShipped() > 0 && !$this->isShipmentSeparately()) {
                    $qtyXml->addCustomChild('value', $item->getQtyShipped() * 1, array(
                        'label' => Mage::helper('sales')->__('Shipped')
                    ));
                }
                if ($item->getQtyCanceled() > 0) {
                    $qtyXml->addCustomChild('value', $item->getQtyCanceled() * 1, array(
                        'label' => Mage::helper('sales')->__('Canceled')
                    ));
                }
                if ($item->getQtyRefunded() > 0) {
                    $qtyXml->addCustomChild('value', $item->getQtyRefunded() * 1, array(
                        'label' => Mage::helper('sales')->__('Refunded')
                    ));
                }
            } elseif ($item->getQtyShipped() > 0 && $isOption && $this->isShipmentSeparately()) {
                $qtyXml = $objectXml->addChild('qty');
                $qtyXml->addCustomChild('value', $item->getQtyShipped() * 1, array(
                    'label' => Mage::helper('sales')->__('Shipped')
                ));
            }
        }

        if ($parentItem->getDescription()) {
            $itemXml->addCustomChild('description', $parentItem->getDescription());
        }

        Mage::helper('xmlconnect/customer_order')->addItemOptionsToXml($this, $itemXml);
    }

    /**
     * Add item to XML object. Api version 23
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $orderItemXmlObj
     * @return null
     */
    public function addItemToXmlObjectApi23(Mage_XmlConnect_Model_Simplexml_Element $orderItemXmlObj)
    {
        /** @var $parentItem Mage_Sales_Model_Order_Item */
        $parentItem     = $this->getItem();
        $items          = array_merge(array($parentItem), $parentItem->getChildrenItems());
        $prevOptionId   = '';

        /** @var $weeeHelper Mage_Weee_Helper_Data */
        $weeeHelper = $this->helper('weee');
        /** @var $taxHelper Mage_Tax_Helper_Data */
        $taxHelper  = $this->helper('tax');

        /** @var $itemXml Mage_XmlConnect_Model_Simplexml_Element */
        $itemXml    = $orderItemXmlObj->addChild('item');
        /** @var $optionsXml Mage_XmlConnect_Model_Simplexml_Element */
        $optionsXml = $itemXml->addChild('related_products');

        $this->setWeeeTaxAppliedAmount($parentItem->getWeeeTaxAppliedAmount());
        $this->setWeeeTaxDisposition($parentItem->getWeeeTaxDisposition());

        $typeOfDisplay1 = $weeeHelper->typeOfDisplay($parentItem, 1, 'sales')
            && $this->getWeeeTaxAppliedAmount();
        $typeOfDisplay2 = $weeeHelper->typeOfDisplay($parentItem, 2, 'sales')
            && $this->getWeeeTaxAppliedAmount();
        $typeOfDisplay4 = $weeeHelper->typeOfDisplay($parentItem, 4, 'sales')
            && $this->getWeeeTaxAppliedAmount();
        $typeOfDisplay014 = $weeeHelper->typeOfDisplay($parentItem, array(0, 1, 4), 'sales')
            && $this->getWeeeTaxAppliedAmount();

        $this->setTypesOfDisplay(array(
            Mage_XmlConnect_Helper_Customer_Order::PRICE_DISPLAY_TYPE_1  => $typeOfDisplay1,
            Mage_XmlConnect_Helper_Customer_Order::PRICE_DISPLAY_TYPE_2  => $typeOfDisplay2,
            Mage_XmlConnect_Helper_Customer_Order::PRICE_DISPLAY_TYPE_4  => $typeOfDisplay4,
            Mage_XmlConnect_Helper_Customer_Order::PRICE_DISPLAY_TYPE_14 => $typeOfDisplay014
        ));
        $this->setWeeeTaxes($weeeHelper->getApplied($parentItem));

        /** @var $item Mage_Sales_Model_Order_Item */
        foreach ($items as $item) {
            $isOption = $item->getParentItem() ? true : false;

            /** @var $objectXml Mage_XmlConnect_Model_Simplexml_Element */
            if ($isOption) {
                $objectXml = $optionsXml->addChild('item');
            } else {
                $objectXml = $itemXml;
            }
            $objectXml->addAttribute('product_id', $item->getProductId());
            $objectXml->addCustomChild('entity_type', $item->getProductType());

            if ($isOption) {
                $attributes = $this->getSelectionAttributes($item);
                if ($prevOptionId != $attributes['option_id']) {
                    $objectXml->addAttribute('label', $objectXml->xmlAttribute($attributes['option_label']));
                    $prevOptionId = $attributes['option_id'];
                }
            }

            $objectXml->addCustomChild('sku', Mage::helper('core/string')->splitInjection($item->getSku()));

            if ($isOption) {
                $name = $this->getValueHtml($item);
            } else {
                $name = $item->getName();
            }
            $objectXml->addCustomChild('name', $name);

            // set prices exactly for the Bundle product, but not for related products
            if (!$isOption) {
                $priceXml = $itemXml->addCustomChild('price_list');
                $priceInfoXml = $priceXml->addCustomChild('prices', null, array('id' => 'price'));
                $subtotalInfoXml = $priceXml->addCustomChild('prices', null, array('id' => 'subtotal'));
                // Price excluding tax
                if ($taxHelper->displaySalesBothPrices() || $taxHelper->displaySalesPriceExclTax()) {
                    Mage::helper('xmlconnect/customer_order')->addPriceAndSubtotalToXmlApi23(
                        $this, $parentItem, $priceInfoXml, $subtotalInfoXml
                    );
                }

                // Price including tax
                if ($taxHelper->displaySalesBothPrices() || $taxHelper->displaySalesPriceInclTax()) {
                    Mage::helper('xmlconnect/customer_order')->addPriceAndSubtotalToXmlApi23(
                        $this, $parentItem, $priceInfoXml, $subtotalInfoXml, true
                    );
                }
            }

            // set quantities
            /** @var $qtyXml Mage_XmlConnect_Model_Simplexml_Element */
            if (($isOption && $this->isChildCalculated()) || (!$isOption && !$this->isChildCalculated())) {
                $qtyXml = $objectXml->addChild('quantity');
                if ($item->getQtyOrdered() > 0) {
                    $qtyXml->addCustomChild('value', $item->getQtyOrdered() * 1, array(
                        'label' => Mage::helper('sales')->__('Ordered')
                    ));
                }
                if ($item->getQtyShipped() > 0 && !$this->isShipmentSeparately()) {
                    $qtyXml->addCustomChild('value', $item->getQtyShipped() * 1, array(
                        'label' => Mage::helper('sales')->__('Shipped')
                    ));
                }
                if ($item->getQtyCanceled() > 0) {
                    $qtyXml->addCustomChild('value', $item->getQtyCanceled() * 1, array(
                        'label' => Mage::helper('sales')->__('Canceled')
                    ));
                }
                if ($item->getQtyRefunded() > 0) {
                    $qtyXml->addCustomChild('value', $item->getQtyRefunded() * 1, array(
                        'label' => Mage::helper('sales')->__('Refunded')
                    ));
                }
            } elseif ($item->getQtyShipped() > 0 && $isOption && $this->isShipmentSeparately()) {
                $qtyXml = $objectXml->addChild('quantity');
                $qtyXml->addCustomChild('value', $item->getQtyShipped() * 1, array(
                    'label' => Mage::helper('sales')->__('Shipped')
                ));
            }
        }

        if ($parentItem->getDescription()) {
            $itemXml->addCustomChild('description', $parentItem->getDescription());
        }

        Mage::helper('xmlconnect/customer_order')->addItemOptionsToXml($this, $itemXml);
    }

    /**
     * Prepare option data for output
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @return string
     */
    public function getValueHtml($item)
    {
        $attributes = $this->getSelectionAttributes($item);
        if ($attributes) {
            return sprintf('%d', $attributes['qty']) . ' x ' . $item->getName() . ' - '
                . $this->_formatPrice($attributes['price']);
        } else {
            return $item->getName();
        }
    }

    /**
     * Format price using order currency
     *
     * @param float $price
     * @return string
     */
    protected function _formatPrice($price)
    {
        return Mage::helper('xmlconnect/customer_order')->formatPrice($this, $price);
    }
}
