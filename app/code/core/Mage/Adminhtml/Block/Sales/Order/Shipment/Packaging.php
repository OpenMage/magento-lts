<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Adminhtml shipment packaging
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Shipment_Packaging extends Mage_Adminhtml_Block_Template
{
    /**
     * Retrieve shipment model instance
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function getShipment()
    {
        return Mage::registry('current_shipment');
    }

    /**
     * Configuration for popup window for packaging
     *
     * @return string
     */
    public function getConfigDataJson()
    {
        $shipmentId = $this->getShipment()->getId();
        $orderId = $this->getRequest()->getParam('order_id');
        $urlParams = [];

        $createLabelUrl     = '';
        $itemsGridUrl       = '';
        $itemsQty           = [];
        $itemsPrice         = [];
        $itemsName          = [];
        $itemsWeight        = [];
        $itemsProductId     = [];
        $itemsOrderItemId   = [];

        if ($shipmentId) {
            $urlParams['shipment_id'] = $shipmentId;
            $createLabelUrl = $this->getUrl('*/sales_order_shipment/createLabel', $urlParams);
            $itemsGridUrl = $this->getUrl('*/sales_order_shipment/getShippingItemsGrid', $urlParams);
            foreach ($this->getShipment()->getAllItems() as $item) {
                $itemId = $item->getId();
                $itemsQty[$itemId]           = $item->getQty();
                $itemsPrice[$itemId]         = $item->getPrice();
                $itemsName[$itemId]          = $item->getName();
                $itemsWeight[$itemId]        = $item->getWeight();
                $itemsProductId[$itemId]     = $item->getProductId();
                $itemsOrderItemId[$itemId]   = $item->getOrderItemId();
            }
        } elseif ($orderId) {
            $urlParams['order_id'] = $orderId;
            $createLabelUrl = $this->getUrl('*/sales_order_shipment/save', $urlParams);
            $itemsGridUrl = $this->getUrl('*/sales_order_shipment/getShippingItemsGrid', $urlParams);

            foreach ($this->getShipment()->getAllItems() as $item) {
                $orderItemId = $item->getOrderItemId();
                $itemsQty[$orderItemId]          = $item->getQty() * 1;
                $itemsPrice[$orderItemId]        = $item->getPrice();
                $itemsName[$orderItemId]         = $item->getName();
                $itemsWeight[$orderItemId]       = $item->getWeight();
                $itemsProductId[$orderItemId]    = $item->getProductId();
                $itemsOrderItemId[$orderItemId]  = $orderItemId;
            }
        }
        $data = [
            'createLabelUrl'            => $createLabelUrl,
            'itemsGridUrl'              => $itemsGridUrl,
            'errorQtyOverLimit'         => Mage::helper('sales')->__('The quantity you want to add exceeds the total shipped quantity for some of selected Product(s)'),
            'titleDisabledSaveBtn'      => Mage::helper('sales')->__('Products should be added to package(s)'),
            'validationErrorMsg'        => Mage::helper('sales')->__('The value that you entered is not valid.'),
            'shipmentItemsQty'          => $itemsQty,
            'shipmentItemsPrice'        => $itemsPrice,
            'shipmentItemsName'         => $itemsName,
            'shipmentItemsWeight'       => $itemsWeight,
            'shipmentItemsProductId'    => $itemsProductId,
            'shipmentItemsOrderItemId'  => $itemsOrderItemId,
            'customizable'              => $this->_getCustomizableContainers(),
        ];
        return Mage::helper('core')->jsonEncode($data);
    }

    /**
     * Return container types of carrier
     *
     * @return array
     */
    public function getContainers()
    {
        $order = $this->getShipment()->getOrder();
        $storeId = $this->getShipment()->getStoreId();
        $address = $order->getShippingAddress();
        $carrier = $order->getShippingCarrier();
        $countryShipper = Mage::getStoreConfig(Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID, $storeId);
        if ($carrier) {
            $params = new Varien_Object([
                'method' => $order->getShippingMethod(true)->getMethod(),
                'country_shipper' => $countryShipper,
                'country_recipient' => $address->getCountryId(),
            ]);
            return $carrier->getContainerTypes($params);
        }
        return [];
    }

    /**
     * Get codes of customizable container types of carrier
     *
     * @return array
     */
    protected function _getCustomizableContainers()
    {
        $carrier = $this->getShipment()->getOrder()->getShippingCarrier();
        if ($carrier) {
            return $carrier->getCustomizableContainerTypes();
        }
        return [];
    }

    /**
     * Return name of container type by its code
     *
     * @param string $code
     * @return string
     */
    public function getContainerTypeByCode($code)
    {
        $carrier = $this->getShipment()->getOrder()->getShippingCarrier();
        if ($carrier) {
            $containerTypes = $carrier->getContainerTypes();
            return !empty($containerTypes[$code]) ? $containerTypes[$code] : '';
        }
        return '';
    }

    /**
     * Return name of delivery confirmation type by its code
     *
     * @param string $code
     * @return string
     */
    public function getDeliveryConfirmationTypeByCode($code)
    {
        $countryId = $this->getShipment()->getOrder()->getShippingAddress()->getCountryId();
        $carrier = $this->getShipment()->getOrder()->getShippingCarrier();
        if ($carrier) {
            $params = new Varien_Object(['country_recipient' => $countryId]);
            $confirmationTypes = $carrier->getDeliveryConfirmationTypes($params);
            return !empty($confirmationTypes[$code]) ? $confirmationTypes[$code] : '';
        }
        return '';
    }

    /**
     * Return name of content type by its code
     *
     * @param string $code
     * @return string
     */
    public function getContentTypeByCode($code)
    {
        $contentTypes = $this->getContentTypes();
        if (!empty($contentTypes[$code])) {
            return $contentTypes[$code];
        }
        return '';
    }

    /**
     * Get packed products in packages
     *
     * @return array
     */
    public function getPackages()
    {
        $packages = $this->getShipment()->getPackages();
        if ($packages) {
            $packages = unserialize($packages, ['allowed_classes' => false]);
        } else {
            $packages = [];
        }
        return $packages;
    }

    /**
     * Get item of shipment by its id
     *
     * @param int $itemId
     * @param string $itemsOf
     * @return Varien_Object
     */
    public function getShipmentItem($itemId, $itemsOf)
    {
        $items = $this->getShipment()->getAllItems();
        foreach ($items as $item) {
            if ($itemsOf == 'order' && $item->getOrderItemId() == $itemId) {
                return $item;
            } elseif ($itemsOf == 'shipment' && $item->getId() == $itemId) {
                return $item;
            }
        }
        return new Varien_Object();
    }

    /**
     * Can display customs value
     *
     * @return bool
     */
    public function displayCustomsValue()
    {
        $storeId = $this->getShipment()->getStoreId();
        $order = $this->getShipment()->getOrder();
        $address = $order->getShippingAddress();
        $shipperAddressCountryCode = Mage::getStoreConfig(
            Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID,
            $storeId
        );
        $recipientAddressCountryCode = $address->getCountryId();
        if ($shipperAddressCountryCode != $recipientAddressCountryCode) {
            return true;
        }
        return false;
    }

    /**
     * Return delivery confirmation types of current carrier
     *
     * @return array
     */
    public function getDeliveryConfirmationTypes()
    {
        $countryId = $this->getShipment()->getOrder()->getShippingAddress()->getCountryId();
        $carrier = $this->getShipment()->getOrder()->getShippingCarrier();
        $params = new Varien_Object(['country_recipient' => $countryId]);
        if ($carrier && is_array($carrier->getDeliveryConfirmationTypes($params))) {
            return $carrier->getDeliveryConfirmationTypes($params);
        }
        return [];
    }

    /**
     * Print button for creating pdf
     *
     * @return string
     */
    public function getPrintButton()
    {
        $data['shipment_id'] = $this->getShipment()->getId();
        $url = $this->getUrl('*/sales_order_shipment/printPackage', $data);
        return $this->getLayout()
            ->createBlock('adminhtml/widget_button')
            ->setData([
                'label'   => Mage::helper('sales')->__('Print'),
                'onclick' => Mage::helper('core/js')->getSetLocationJs($url)
            ])
            ->toHtml();
    }

    /**
     * Check whether girth is allowed for current carrier
     *
     * @return bool
     */
    public function isGirthAllowed()
    {
        return $this
            ->getShipment()
            ->getOrder()
            ->getShippingCarrier()
            ->isGirthAllowed($this->getShipment()->getOrder()->getShippingAddress()->getCountryId());
    }

    /**
     * Return content types of package
     *
     * @return array
     */
    public function getContentTypes()
    {
        $order = $this->getShipment()->getOrder();
        $storeId = $this->getShipment()->getStoreId();
        $address = $order->getShippingAddress();
        $carrier = $order->getShippingCarrier();
        $countryShipper = Mage::getStoreConfig(Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID, $storeId);
        if ($carrier) {
            $params = new Varien_Object([
                'method' => $order->getShippingMethod(true)->getMethod(),
                'country_shipper' => $countryShipper,
                'country_recipient' => $address->getCountryId(),
            ]);
            return $carrier->getContentTypes($params);
        }
        return [];
    }

    /**
     * Get Currency Code for Custom Value
     *
     * @return string
     */
    public function getCustomValueCurrencyCode()
    {
        $orderInfo = $this->getShipment()->getOrder();
        return $orderInfo->getBaseCurrency()->getCurrencyCode();
    }

    /**
     * Display formatted price
     *
     * @param float $price
     * @return string
     */
    public function displayPrice($price)
    {
        return $this->getShipment()->getOrder()->formatPriceTxt($price);
    }

    /**
     * Display formatted customs price
     *
     * @param float $price
     * @return string
     */
    public function displayCustomsPrice($price)
    {
        $orderInfo = $this->getShipment()->getOrder();
        return $orderInfo->getBaseCurrency()->formatTxt($price);
    }

    /**
     * Get ordered qty of item
     *
     * @param int $itemId
     * @return int|null
     */
    public function getQtyOrderedItem($itemId)
    {
        if ($itemId) {
            return $this->getShipment()->getOrder()->getItemById($itemId)->getQtyOrdered() * 1;
        } else {
            return;
        }
    }
}
