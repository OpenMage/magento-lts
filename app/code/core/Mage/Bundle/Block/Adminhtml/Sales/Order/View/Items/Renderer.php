<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

/**
 * Adminhtml sales order item renderer
 *
 * @package    Mage_Bundle
 */
class Mage_Bundle_Block_Adminhtml_Sales_Order_View_Items_Renderer extends Mage_Adminhtml_Block_Sales_Order_View_Items_Renderer_Default
{
    /**
     * @param Mage_Sales_Model_Order_Item $item
     * @return bool
     */
    public function isShipmentSeparately($item = null)
    {
        if ($item) {
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
            } elseif ($options = $item->getProductOptions()) {
                if (isset($options['shipment_type'])
                    && $options['shipment_type'] == Mage_Catalog_Model_Product_Type_Abstract::SHIPMENT_SEPARATELY
                ) {
                    return false;
                } else {
                    return true;
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
     * @param Mage_Sales_Model_Order_Item $item
     * @return bool
     */
    public function isChildCalculated($item = null)
    {
        if ($item) {
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
            } elseif ($options = $item->getProductOptions()) {
                if (isset($options['product_calculations'])
                    && $options['product_calculations'] == Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD
                ) {
                    return false;
                } else {
                    return true;
                }
            }
        }

        if ($options = $this->getItem()->getProductOptions()) {
            if (isset($options['product_calculations'])
                && $options['product_calculations'] == Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Varien_Object|Mage_Sales_Model_Order_Item $item
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
     * @return array
     */
    public function getOrderOptions()
    {
        $result = [];
        if ($options = $this->getItem()->getProductOptions()) {
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
     * @param Mage_Sales_Model_Order_Item $item
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
                $result .= ' ' . $this->getItem()->getOrder()->formatPrice($attributes['price']);
            }
        }
        return $result;
    }

    /**
     * @param Mage_Sales_Model_Order_Item $item
     * @return bool
     */
    public function canShowPriceInfo($item)
    {
        if (($item->getParentItem() && $this->isChildCalculated())
                || (!$item->getParentItem() && !$this->isChildCalculated())
        ) {
            return true;
        }
        return false;
    }
}
