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
 * Customer order details item xml
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Order_Item_Renderer_Downloadable
    extends Mage_Downloadable_Block_Sales_Order_Item_Renderer_Downloadable
{
    /**
     * Add item to XML object
     * (get from template: downloadable/sales/order/items/renderer/downloadable.phtml)
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
        /** @var $item Mage_Sales_Model_Order_Item */
        $item = $this->getItem();

        /** @var $itemXml Mage_XmlConnect_Model_Simplexml_Element */
        $itemXml = $orderItemXmlObj->addCustomChild('item', null, array(
            'product_id' => $item->getProductId()
        ));
        $itemXml->addCustomChild('name', $item->getName());

        /** @var $weeeHelper Mage_Weee_Helper_Data */
        $weeeHelper = $this->helper('weee');
        /** @var $taxHelper Mage_Tax_Helper_Data */
        $taxHelper  = $this->helper('tax');

        Mage::helper('xmlconnect/customer_order')->addItemOptionsToXml($this, $itemXml);

        $links = $this->getLinks();
        if ($links) {
            $linksXml = $itemXml->addCustomChild('links', null, array('label' => $this->getLinksTitle()));
            foreach ($links->getPurchasedItems() as $link) {
                $linksXml->addCustomChild('link', $link->getLinkTitle());
            }
        }

        $itemXml->addCustomChild('entity_type', $item->getProductType());
        $itemXml->addCustomChild('description', $item->getDescription());
        $itemXml->addCustomChild('sku', Mage::helper('core/string')->splitInjection($this->getSku()));

        /** @var $priceXml Mage_XmlConnect_Model_Simplexml_Element */
        $priceXml = $itemXml->addChild('price');

        // Quantity: Ordered, Shipped, Cancelled, Refunded
        Mage::helper('xmlconnect/customer_order')->addQuantityToXml($this, $itemXml->addChild('qty'), $item);

        /** @var $subtotalXml Mage_XmlConnect_Model_Simplexml_Element */
        $subtotalXml = $itemXml->addChild('subtotal');

        $this->setWeeeTaxAppliedAmount($item->getWeeeTaxAppliedAmount());
        $this->setWeeeTaxDisposition($item->getWeeeTaxDisposition());

        $typeOfDisplay1 = $weeeHelper->typeOfDisplay($item, 1, 'sales')
            && $this->getWeeeTaxAppliedAmount();
        $typeOfDisplay2 = $weeeHelper->typeOfDisplay($item, 2, 'sales')
            && $this->getWeeeTaxAppliedAmount();
        $typeOfDisplay4 = $weeeHelper->typeOfDisplay($item, 4, 'sales')
            && $this->getWeeeTaxAppliedAmount();
        $typeOfDisplay014 = $weeeHelper->typeOfDisplay($item, array(0, 1, 4), 'sales')
            && $this->getWeeeTaxAppliedAmount();

        $this->setTypesOfDisplay(array(
            Mage_XmlConnect_Helper_Customer_Order::PRICE_DISPLAY_TYPE_1   => $typeOfDisplay1,
            Mage_XmlConnect_Helper_Customer_Order::PRICE_DISPLAY_TYPE_2   => $typeOfDisplay2,
            Mage_XmlConnect_Helper_Customer_Order::PRICE_DISPLAY_TYPE_4   => $typeOfDisplay4,
            Mage_XmlConnect_Helper_Customer_Order::PRICE_DISPLAY_TYPE_14  => $typeOfDisplay014
        ));
        $this->setWeeeTaxes($weeeHelper->getApplied($item));

        // Price & subtotal - excluding tax
        if ($taxHelper->displaySalesBothPrices() || $taxHelper->displaySalesPriceExclTax()) {
            Mage::helper('xmlconnect/customer_order')->addPriceAndSubtotalToXml($this, $item, $priceXml, $subtotalXml);
        }

        // Price & subtotal - including tax
        if ($taxHelper->displaySalesBothPrices() || $taxHelper->displaySalesPriceInclTax()) {
            Mage::helper('xmlconnect/customer_order')->addPriceAndSubtotalToXml(
                $this, $item, $priceXml, $subtotalXml, true
            );
        }
    }

    /**
     * Add item to XML object. Api version 23
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $orderItemXmlObj
     * @return null
     */
    public function addItemToXmlObjectApi23(Mage_XmlConnect_Model_Simplexml_Element $orderItemXmlObj)
    {
        /** @var $item Mage_Sales_Model_Order_Item */
        $item = $this->getItem();

        /** @var $itemXml Mage_XmlConnect_Model_Simplexml_Element */
        $itemXml = $orderItemXmlObj->addCustomChild('item', null, array(
            'product_id' => $item->getProductId()
        ));
        $itemXml->addCustomChild('name', $item->getName());

        /** @var $weeeHelper Mage_Weee_Helper_Data */
        $weeeHelper = $this->helper('weee');
        /** @var $taxHelper Mage_Tax_Helper_Data */
        $taxHelper  = $this->helper('tax');

        Mage::helper('xmlconnect/customer_order')->addItemOptionsToXml($this, $itemXml);

        $links = $this->getLinks();
        if ($links) {
            $linksXml = $itemXml->addCustomChild('links', null, array('label' => $this->getLinksTitle()));
            foreach ($links->getPurchasedItems() as $link) {
                $linksXml->addCustomChild('link', $link->getLinkTitle());
            }
        }

        $itemXml->addCustomChild('entity_type', $item->getProductType());
        $itemXml->addCustomChild('description', $item->getDescription());
        $itemXml->addCustomChild('sku', Mage::helper('core/string')->splitInjection($this->getSku()));

        // Quantity: Ordered, Shipped, Cancelled, Refunded
        Mage::helper('xmlconnect/customer_order')->addQuantityToXml($this, $itemXml->addChild('quantity'), $item);

        $this->setWeeeTaxAppliedAmount($item->getWeeeTaxAppliedAmount());
        $this->setWeeeTaxDisposition($item->getWeeeTaxDisposition());

        $typeOfDisplay1 = $weeeHelper->typeOfDisplay($item, 1, 'sales')
            && $this->getWeeeTaxAppliedAmount();
        $typeOfDisplay2 = $weeeHelper->typeOfDisplay($item, 2, 'sales')
            && $this->getWeeeTaxAppliedAmount();
        $typeOfDisplay4 = $weeeHelper->typeOfDisplay($item, 4, 'sales')
            && $this->getWeeeTaxAppliedAmount();
        $typeOfDisplay014 = $weeeHelper->typeOfDisplay($item, array(0, 1, 4), 'sales')
            && $this->getWeeeTaxAppliedAmount();

        $this->setTypesOfDisplay(array(
            Mage_XmlConnect_Helper_Customer_Order::PRICE_DISPLAY_TYPE_1   => $typeOfDisplay1,
            Mage_XmlConnect_Helper_Customer_Order::PRICE_DISPLAY_TYPE_2   => $typeOfDisplay2,
            Mage_XmlConnect_Helper_Customer_Order::PRICE_DISPLAY_TYPE_4   => $typeOfDisplay4,
            Mage_XmlConnect_Helper_Customer_Order::PRICE_DISPLAY_TYPE_14  => $typeOfDisplay014
        ));
        $this->setWeeeTaxes($weeeHelper->getApplied($item));

        $priceXml = $itemXml->addCustomChild('price_list');
        $priceInfoXml = $priceXml->addCustomChild('prices', null, array('id' => 'price'));
        $subtotalInfoXml = $priceXml->addCustomChild('prices', null, array('id' => 'subtotal'));

        // Price & subtotal - excluding tax
        if ($taxHelper->displaySalesBothPrices() || $taxHelper->displaySalesPriceExclTax()) {
            Mage::helper('xmlconnect/customer_order')->addPriceAndSubtotalToXmlApi23(
                $this, $item, $priceInfoXml, $subtotalInfoXml
            );
        }

        // Price & subtotal - including tax
        if ($taxHelper->displaySalesBothPrices() || $taxHelper->displaySalesPriceInclTax()) {
            Mage::helper('xmlconnect/customer_order')->addPriceAndSubtotalToXmlApi23(
                $this, $item, $priceInfoXml,$subtotalInfoXml, true
            );
        }
    }
}
