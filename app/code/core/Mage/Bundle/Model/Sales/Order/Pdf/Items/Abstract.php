<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2018-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales Order Pdf Items renderer
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @author     Magento Core Team <core@magentocommerce.com>
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
        $_itemsArray = [];

        if ($item instanceof Mage_Sales_Model_Order_Invoice_Item) {
            $_items = $item->getInvoice()->getAllItems();
        } elseif ($item instanceof Mage_Sales_Model_Order_Shipment_Item) {
            $_items = $item->getShipment()->getAllItems();
        } elseif ($item instanceof Mage_Sales_Model_Order_Creditmemo_Item) {
            $_items = $item->getCreditmemo()->getAllItems();
        }

        if ($_items) {
            foreach ($_items as $_item) {
                $parentItem = $_item->getOrderItem()->getParentItem();
                if ($parentItem) {
                    $_itemsArray[$parentItem->getId()][$_item->getOrderItemId()] = $_item;
                } else {
                    $_itemsArray[$_item->getOrderItem()->getId()][$_item->getOrderItemId()] = $_item;
                }
            }
        }

        return $_itemsArray[$item->getOrderItem()->getId()] ?? null;
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
                        && $options['shipment_type'] == Mage_Catalog_Model_Product_Type_Abstract::SHIPMENT_SEPARATELY) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else {
                $options = $item->getProductOptions();
                if ($options) {
                    if (isset($options['shipment_type'])
                        && $options['shipment_type'] == Mage_Catalog_Model_Product_Type_Abstract::SHIPMENT_SEPARATELY) {
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
                && $options['shipment_type'] == Mage_Catalog_Model_Product_Type_Abstract::SHIPMENT_SEPARATELY) {
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
                    if (isset($options['product_calculations']) &&
                        $options['product_calculations'] == Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD
                    ) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else {
                $options = $item->getProductOptions();
                if ($options) {
                    if (isset($options['product_calculations']) &&
                        $options['product_calculations'] == Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD
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
                && $options['product_calculations'] == Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD) {
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
     * @param Mage_Sales_Model_Order_Item $item
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
                $result .= " " . strip_tags($this->getOrderItem()->getOrder()->formatPrice($attributes['price']));
            }
        }
        return $result;
    }

    /**
     * Can show price info for item
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @return bool
     */
    public function canShowPriceInfo($item)
    {
        if (($item->getOrderItem()->getParentItem() && $this->isChildCalculated())
                || (!$item->getOrderItem()->getParentItem() && !$this->isChildCalculated())) {
            return true;
        }
        return false;
    }
}
