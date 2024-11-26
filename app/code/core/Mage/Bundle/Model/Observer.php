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
 * Bundle Products Observer
 *
 * @category   Mage
 * @package    Mage_Bundle
 */
class Mage_Bundle_Model_Observer
{
    /**
     * Setting Bundle Items Data to product for father processing
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function prepareProductSave($observer)
    {
        /** @var Mage_Core_Controller_Request_Http $request */
        $request = $observer->getEvent()->getRequest();
        /** @var Mage_Catalog_Model_Product $product */
        $product = $observer->getEvent()->getProduct();

        if (($items = $request->getPost('bundle_options')) && !$product->getCompositeReadonly()) {
            $product->setBundleOptionsData($items);
        }

        if (($selections = $request->getPost('bundle_selections')) && !$product->getCompositeReadonly()) {
            $product->setBundleSelectionsData($selections);
        }

        if ($product->getPriceType() == '0' && !$product->getOptionsReadonly()) {
            $product->setCanSaveCustomOptions(true);
            if ($customOptions = $product->getProductOptions()) {
                foreach (array_keys($customOptions) as $key) {
                    $customOptions[$key]['is_delete'] = 1;
                }
                $product->setProductOptions($customOptions);
            }
        }

        $product->setCanSaveBundleSelections(
            (bool)$request->getPost('affect_bundle_product_selections') && !$product->getCompositeReadonly()
        );

        return $this;
    }

    /**
     * Append bundles in upsell list for current product
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function appendUpsellProducts($observer)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $observer->getEvent()->getProduct();

        /**
         * Check is current product type is allowed for bundle selection product type
         */
        if (!in_array($product->getTypeId(), Mage::helper('bundle')->getAllowedSelectionTypes())) {
            return $this;
        }

        /** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Link_Product_Collection $collection */
        $collection = $observer->getEvent()->getCollection();
        $limit      = $observer->getEvent()->getLimit();
        if (is_array($limit)) {
            $limit = $limit['upsell'] ?? 0;
        }

        /** @var Mage_Bundle_Model_Resource_Selection $resource */
        $resource   = Mage::getResourceSingleton('bundle/selection');

        $productIds = array_keys($collection->getItems());
        if (!is_null($limit) && $limit <= count($productIds)) {
            return $this;
        }

        // retrieve bundle product ids
        $bundleIds  = $resource->getParentIdsByChild($product->getId());
        // exclude up-sell product ids
        $bundleIds  = array_diff($bundleIds, $productIds);

        if (!$bundleIds) {
            return $this;
        }

        $bundleCollection = $product->getCollection()
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addStoreFilter()
            ->addPriceData()
            ->addTaxPercents();

        Mage::getSingleton('catalog/product_visibility')
            ->addVisibleInCatalogFilterToCollection($bundleCollection);

        if (!is_null($limit)) {
            $bundleCollection->setPageSize($limit);
        }
        $bundleCollection->addFieldToFilter('entity_id', ['in' => $bundleIds])
            ->setFlag('do_not_use_category_id', true);

        if ($collection instanceof Varien_Data_Collection) {
            foreach ($bundleCollection as $item) {
                $collection->addItem($item);
            }
        } elseif ($collection instanceof Varien_Object) {
            $items = $collection->getItems();
            foreach ($bundleCollection as $item) {
                $items[$item->getEntityId()] = $item;
            }
            $collection->setItems($items);
        }

        return $this;
    }

    /**
     * Append selection attributes to selection's order item
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function appendBundleSelectionData($observer)
    {
        /** @var Mage_Sales_Model_Order_Item $orderItem */
        $orderItem = $observer->getEvent()->getOrderItem();
        /** @var Mage_Sales_Model_Quote_Item_Abstract $quoteItem */
        $quoteItem = $observer->getEvent()->getItem();

        if ($attributes = $quoteItem->getProduct()->getCustomOption('bundle_selection_attributes')) {
            $productOptions = $orderItem->getProductOptions();
            $productOptions['bundle_selection_attributes'] = $attributes->getValue();
            $orderItem->setProductOptions($productOptions);
        }

        return $this;
    }

    /**
     * Add price index data for catalog product collection
     * only for front end
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function loadProductOptions($observer)
    {
        $collection = $observer->getEvent()->getCollection();
        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection->addPriceData();

        return $this;
    }

    /**
     * duplicating bundle options and selections
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function duplicateProduct($observer)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $observer->getEvent()->getCurrentProduct();

        if ($product->getTypeId() != Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            //do nothing if not bundle
            return $this;
        }

        /** @var Mage_Catalog_Model_Product $newProduct */
        $newProduct = $observer->getEvent()->getNewProduct();

        /** @var Mage_Bundle_Model_Product_Type $productType */
        $productType = $product->getTypeInstance(true);

        $productType->setStoreFilter($product->getStoreId(), $product);
        $optionCollection = $productType->getOptionsCollection($product);
        $selectionCollection = $productType->getSelectionsCollection(
            $productType->getOptionsIds($product),
            $product
        );
        $optionCollection->appendSelections($selectionCollection);

        $optionRawData = [];
        $selectionRawData = [];

        $i = 0;
        foreach ($optionCollection as $option) {
            $optionRawData[$i] = [
                    'required' => $option->getData('required'),
                    'position' => $option->getData('position'),
                    'type' => $option->getData('type'),
                    'title' => $option->getData('title') ? $option->getData('title') : $option->getData('default_title'),
                    'delete' => ''
            ];
            foreach ($option->getSelections() as $selection) {
                $selectionRawData[$i][] = [
                    'product_id' => $selection->getProductId(),
                    'position' => $selection->getPosition(),
                    'is_default' => $selection->getIsDefault(),
                    'selection_price_type' => $selection->getSelectionPriceType(),
                    'selection_price_value' => $selection->getSelectionPriceValue(),
                    'selection_qty' => $selection->getSelectionQty(),
                    'selection_can_change_qty' => $selection->getSelectionCanChangeQty(),
                    'delete' => ''
                ];
            }
            $i++;
        }

        $newProduct->setBundleOptionsData($optionRawData);
        $newProduct->setBundleSelectionsData($selectionRawData);
        return $this;
    }

    /**
     * Setting attribute tab block for bundle
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function setAttributeTabBlock($observer)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $observer->getEvent()->getProduct();
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            Mage::helper('adminhtml/catalog')
                ->setAttributeTabBlock('bundle/adminhtml_catalog_product_edit_tab_attributes');
        }
        return $this;
    }

    /**
     * Initialize product options renderer with bundle specific params
     *
     * @return $this
     */
    public function initOptionRenderer(Varien_Event_Observer $observer)
    {
        /** @var Mage_Wishlist_Block_Customer_Wishlist_Item_Options $block */
        $block = $observer->getBlock();
        $block->addOptionsRenderCfg('bundle', 'bundle/catalog_product_configuration');
        return $this;
    }

    /**
     * Add price index to bundle product after load
     *
     * @deprecated since 1.4.0.0
     *
     * @return $this
     */
    public function catalogProductLoadAfter(Varien_Event_Observer $observer)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $observer->getEvent()->getProduct();
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            Mage::getSingleton('bundle/price_index')
                ->addPriceIndexToProduct($product);
        }

        return $this;
    }

    /**
     * CatalogIndex Indexer after plain reindex process
     *
     * @deprecated since 1.4.0.0
     * @see Mage_Bundle_Model_Resource_Indexer_Price
     *
     * @return $this
     */
    public function catalogIndexPlainReindexAfter(Varien_Event_Observer $observer)
    {
        $products = $observer->getEvent()->getProducts();
        Mage::getSingleton('bundle/price_index')->reindex($products);

        return $this;
    }
}
