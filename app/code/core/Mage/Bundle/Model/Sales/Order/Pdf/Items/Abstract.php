<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

/**
 * Sales Order Pdf Items renderer
 *
 * @package    Mage_Bundle
 */
abstract class Mage_Bundle_Model_Sales_Order_Pdf_Items_Abstract extends Mage_Sales_Model_Order_Pdf_Items_Abstract
{
    /**
     * Getting all available children for Invoice, Shipmen or Creditmemo item
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
                $parentItem = $orderItem->getOrderItem()->getParentItem();
                if ($parentItem) {
                    $itemsArray[$parentItem->getId()][$orderItem->getOrderItemId()] = $orderItem;
                } else {
                    $itemsArray[$orderItem->getOrderItem()->getId()][$orderItem->getOrderItemId()] = $orderItem;
                }
            }
        }

        return $itemsArray[$item->getOrderItem()->getId()] ?? null;
    }

    /**
     * Retrieve is Shipment Separately flag for Item
     *
     * @param Varien_Object $item
     * @return bool
     */
    public function isShipmentSeparately($item = null)
    {
        if ($item) {
            if ($item->getOrderItem()) {
                $item = $item->getOrderItem();
            }

            $parentItem = $item->getParentItem();
            if ($parentItem) {
                $options = $parentItem->getProductOptions();
                if ($options) {
                    if (isset($options['shipment_type'])
                        && $options['shipment_type'] == Mage_Catalog_Model_Product_Type_Abstract::SHIPMENT_SEPARATELY
                    ) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else {
                $options = $item->getProductOptions();
                if ($options) {
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

        $options = $this->getOrderItem()->getProductOptions();
        if ($options) {
            if (isset($options['shipment_type'])
                && $options['shipment_type'] == Mage_Catalog_Model_Product_Type_Abstract::SHIPMENT_SEPARATELY
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve is Child Calculated
     *
     * @param Varien_Object $item
     * @return bool
     */
    public function isChildCalculated($item = null)
    {
        if ($item) {
            if ($item->getOrderItem()) {
                $item = $item->getOrderItem();
            }

            $parentItem = $item->getParentItem();
            if ($parentItem) {
                $options = $parentItem->getProductOptions();
                if ($options) {
                    if (isset($options['product_calculations'])
                        && $options['product_calculations'] == Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD
                    ) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else {
                $options = $item->getProductOptions();
                if ($options) {
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

        $options = $this->getOrderItem()->getProductOptions();
        if ($options) {
            if (isset($options['product_calculations'])
                && $options['product_calculations'] == Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve Bundle Options
     *
     * @param Varien_Object $item
     * @return array
     */
    public function getBundleOptions($item = null)
    {
        $options = $this->getOrderItem()->getProductOptions();
        if ($options) {
            if (isset($options['bundle_options'])) {
                return $options['bundle_options'];
            }
        }

        return [];
    }

    /**
     * Retrieve Selection attributes
     *
     * @param Varien_Object $item
     * @return mixed
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
     * Retrieve Order options
     *
     * @param Varien_Object $item
     * @return array
     */
    public function getOrderOptions($item = null)
    {
        $result = [];

        $options = $this->getOrderItem()->getProductOptions();
        if ($options) {
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
     * @throws Mage_Core_Exception
     */
    public function getOrderItem()
    {
        return $this->getItem()->getOrderItem();
    }

    /**
     * Retrieve Value HTML
     *
     * @param Mage_Sales_Model_Order_Invoice_Item $item
     * @return string
     */
    public function getValueHtml($item)
    {
        $result = strip_tags($item->getName());
        if (!$this->isShipmentSeparately($item)) {
            $attributes = $this->getSelectionAttributes($item);
            if ($attributes) {
                $result =  sprintf('%d', $attributes['qty']) . ' x ' . $result;
            }
        }

        if (!$this->isChildCalculated($item)) {
            $attributes = $this->getSelectionAttributes($item);
            if ($attributes) {
                $result .= ' ' . strip_tags($this->getOrderItem()->getOrder()->formatPrice($attributes['price']));
            }
        }

        return $result;
    }

    /**
     * Can show price info for item
     *
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
