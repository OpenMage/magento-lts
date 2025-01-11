<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_ConfigurableSwatches
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_ConfigurableSwatches
 */
class Mage_ConfigurableSwatches_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * Attach children products after product list load
     * Observes: catalog_block_product_list_collection
     *
     * @return void
     */
    public function productListCollectionLoadAfter(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('configurableswatches')->isEnabled()) {
            return;
        }

        /** @var Mage_ConfigurableSwatches_Helper_Mediafallback $mediaHelper */
        $mediaHelper = Mage::helper('configurableswatches/mediafallback');

        /** @var Mage_ConfigurableSwatches_Helper_List_Price $priceHelper */
        $priceHelper = Mage::helper('configurableswatches/list_price');

        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = $observer->getCollection();

        if ($collection
            instanceof Mage_ConfigurableSwatches_Model_Resource_Catalog_Product_Type_Configurable_Product_Collection
        ) {
            // avoid recursion
            return;
        }

        $products = $collection->getItems();

        $mediaHelper->attachChildrenProducts($products, $collection->getStoreId());

        $mediaHelper->attachProductChildrenAttributeMapping($products, $collection->getStoreId());

        if ($priceHelper->isEnabled()) {
            $priceHelper->attachConfigurableProductChildrenPricesMapping($products, $collection->getStoreId());
        }

        $mediaHelper->attachGallerySetToCollection($products, $collection->getStoreId());

        foreach ($products as $product) {
            $mediaHelper->groupMediaGalleryImages($product);
            Mage::helper('configurableswatches/productimg')
                ->indexProductImages($product, $product->getListSwatchAttrValues());
        }
    }

    /**
     * Attach children products after product load
     * Observes: catalog_product_load_after
     */
    public function productLoadAfter(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('configurableswatches')->isEnabled()) { // functionality disabled
            return; // exit without loading swatch functionality
        }

        /** @var Mage_ConfigurableSwatches_Helper_Mediafallback $helper */
        $helper = Mage::helper('configurableswatches/mediafallback');

        /** @var Mage_Catalog_Model_Product $product */
        $product = $observer->getDataObject();

        if ($product->getTypeId() != Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
            return;
        }

        $helper->groupMediaGalleryImages($product);

        $helper->attachProductChildrenAttributeMapping([$product], $product->getStoreId(), false);
    }

    /**
     * Instruct media attribute to load images for product's children
     * if config swatches enabled.
     * Observes: catalog_product_attribute_backend_media_load_gallery_before
     */
    public function loadChildProductImagesOnMediaLoad(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('configurableswatches')->isEnabled()) { // functionality disabled
            return; // exit without loading swatch functionality
        }

        /** @var Varien_Object $eventWrapper */
        $eventWrapper = $observer->getEventObjectWrapper();
        /** @var Mage_Catalog_Model_Product $product */
        $product = $eventWrapper->getProduct();

        if ($product->getTypeId() != Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
            return;
        }

        /** @var Mage_Catalog_Model_Product_Type_Configurable $productType */
        $productType = Mage::getModel('catalog/product_type_configurable');

        $childrenProducts = $productType->getUsedProducts(null, $product);
        $product->setChildrenProducts($childrenProducts);

        $mediaProductIds = [];
        foreach ($childrenProducts as $childProduct) {
            $mediaProductIds[] = $childProduct->getId();
        }

        if (empty($mediaProductIds)) { // no children product IDs found
            return; // stop execution of method
        }

        $mediaProductIds[] = $product->getId(); // ensure original product's media images are still loaded

        $eventWrapper->setProductIdsOverride($mediaProductIds);
    }

    /**
     * Convert a catalog layer block with the right templates
     * Observes: controller_action_layout_generate_blocks_after
     */
    public function convertLayerBlock(Varien_Event_Observer $observer)
    {
        $front = Mage::app()->getRequest()->getRouteName();
        $controller = Mage::app()->getRequest()->getControllerName();
        $action = Mage::app()->getRequest()->getActionName();

        // Perform this operation if we're on a category view page or search results page
        if (($front == 'catalog' && $controller == 'category' && $action == 'view')
            || ($front == 'catalogsearch' && $controller == 'result' && $action == 'index')
        ) {
            // Block name for layered navigation differs depending on which Magento edition we're in
            $blockName = 'catalog.leftnav';
            if (Mage::getEdition() == Mage::EDITION_ENTERPRISE) {
                $blockName = ($front == 'catalogsearch') ? 'enterprisesearch.leftnav' : 'enterprisecatalog.leftnav';
            } elseif ($front == 'catalogsearch') {
                $blockName = 'catalogsearch.leftnav';
            }
            Mage::helper('configurableswatches/productlist')->convertLayerBlock($blockName);
        }
    }
}
