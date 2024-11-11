<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2018-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order item renderer
 *
 * @category   Mage
 * @package    Mage_Bundle
 */
class Mage_Bundle_Block_Adminhtml_Sales_Order_Items_Renderer extends Mage_Adminhtml_Block_Sales_Items_Renderer_Default
{
    /**
     * Getting all available children for Invoice, Shipmen or Credit memo item
     *
     * @param Varien_Object $item
     * @return array
     */
    public function getChilds($item)
    {
        $orderItems = [];
        $itemsArray = [];

        if ($item instanceof Mage_Sales_Model_Order_Invoice_Item) {
            $orderItems = $item->getInvoice()->getAllItems();
        } elseif ($item instanceof Mage_Sales_Model_Order_Shipment_Item) {
            $orderItems = $item->getShipment()->getAllItems();
        } elseif ($item instanceof Mage_Sales_Model_Order_Creditmemo_Item) {
            $orderItems = $item->getCreditmemo()->getAllItems();
        }

        if ($orderItems) {
            foreach ($orderItems as $orderItem) {
                if ($parentItem = $orderItem->getOrderItem()->getParentItem()) {
                    $itemsArray[$parentItem->getId()][$orderItem->getOrderItemId()] = $orderItem;
                } else {
                    $itemsArray[$orderItem->getOrderItem()->getId()][$orderItem->getOrderItemId()] = $orderItem;
                }
            }
        }

        return $itemsArray[$item->getOrderItem()->getId()] ?? null;
    }

    /**
     * @param Mage_Sales_Model_Order_Invoice_Item $item
     * @return bool
     */
    public function isShipmentSeparately($item = null)
    {
        if ($item) {
            if ($item->getOrderItem()) {
                $item = $item->getOrderItem();
            }
            if ($parentItem = $item->getParentItem()) {
                if ($options = $parentItem->getProductOptions()) {
                    if (isset($options['shipment_type'])
                        && $options['shipment_type'] == Mage_Catalog_Model_Product_Type_Abstract::SHIPMENT_SEPARATELY
                    ) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else {
                if ($options = $item->getProductOptions()) {
                    if (isset($options['shipment_type'])
                        && $options['shipment_type'] == Mage_Catalog_Model_Product_Type_Abstract::SHIPMENT_SEPARATELY
                    ) {
                        return false;
                    } else {
                        return true;
                    }
                }
            }
        }

        if ($options = $this->getOrderItem()->getProductOptions()) {
            if (isset($options['shipment_type'])
                && $options['shipment_type'] == Mage_Catalog_Model_Product_Type_Abstract::SHIPMENT_SEPARATELY
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Mage_Sales_Model_Order_Invoice_Item $item
     * @return bool
     */
    public function isChildCalculated($item = null)
    {
        if ($item) {
            if ($item->getOrderItem()) {
                $item = $item->getOrderItem();
            }
            if ($parentItem = $item->getParentItem()) {
                if ($options = $parentItem->getProductOptions()) {
                    if (isset($options['product_calculations'])
                        && $options['product_calculations'] == Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD
                    ) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else {
                if ($options = $item->getProductOptions()) {
                    if (isset($options['product_calculations'])
                        && $options['product_calculations'] == Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD
                    ) {
                        return false;
                    } else {
                        return true;
                    }
                }
            }
        }

        if ($options = $this->getOrderItem()->getProductOptions()) {
            if (isset($options['product_calculations'])
                && $options['product_calculations'] == Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Varien_Object|Mage_Sales_Model_Order_Invoice_Item $item
     * @return mixed|null
     */
    public function getSelectionAttributes($item)
    {
        if ($item instanceof Mage_Sales_Model_Order_Item) {
            $options = $item->getProductOptions();
        } else {
            $options = $item->getOrderItem()->getProductOptions();
        }
        if (isset($options['bundle_selection_attributes'])) {
            return unserialize($options['bundle_selection_attributes'], ['allowed_classes' => false]);
        }
        return null;
    }

    /**
     * @param null $item deprecated
     * @return array
     */
    public function getOrderOptions($item = null)
    {
        $result = [];

        if ($options = $this->getOrderItem()->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (!empty($options['attributes_info'])) {
                $result = array_merge($options['attributes_info'], $result);
            }
        }
        return $result;
    }

    /**
     * Retrieve Order Item
     *
     * @return Mage_Sales_Model_Order_Item
     */
    public function getOrderItem()
    {
        return $this->getItem()->getOrderItem();
    }

    /**
     * @param Mage_Sales_Model_Order_Invoice_Item $item
     * @return string
     */
    public function getValueHtml($item)
    {
        $result = $this->escapeHtml($item->getName());
        if (!$this->isShipmentSeparately($item)) {
            if ($attributes = $this->getSelectionAttributes($item)) {
                $result =  sprintf('%d', $attributes['qty']) . ' x ' . $result;
            }
        }
        if (!$this->isChildCalculated($item)) {
            if ($attributes = $this->getSelectionAttributes($item)) {
                $result .= ' ' . $this->getOrderItem()->getOrder()->formatPrice($attributes['price']);
            }
        }
        return $result;
    }

    /**
     * @param Mage_Sales_Model_Order_Invoice_Item $item
     * @return bool
     */
    public function canShowPriceInfo($item)
    {
        if (($item->getOrderItem()->getParentItem() && $this->isChildCalculated())
                || (!$item->getOrderItem()->getParentItem() && !$this->isChildCalculated())
        ) {
            return true;
        }
        return false;
    }
}
