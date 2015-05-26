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
 * @package     Mage_ConfigurableSwatches
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_ConfigurableSwatches_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * Attach children products after product list load
     * Observes: catalog_block_product_list_collection
     *
     * @param Varien_Event_Observer $observer
     */
    public function productListCollectionLoadAfter(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('configurableswatches')->isEnabled()) { // check if functionality disabled
            return; // exit without loading swatch functionality
        }

        /* @var $helper Mage_ConfigurableSwatches_Helper_Mediafallback */
        $helper = Mage::helper('configurableswatches/mediafallback');

        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = $observer->getCollection();

        if ($collection
            instanceof Mage_ConfigurableSwatches_Model_Resource_Catalog_Product_Type_Configurable_Product_Collection) {
            // avoid recursion
            return;
        }

        $products = $collection->getItems();

        $helper->attachChildrenProducts($products, $collection->getStoreId());

        $helper->attachConfigurableProductChildrenAttributeMapping($products, $collection->getStoreId());

        $helper->attachGallerySetToCollection($products, $collection->getStoreId());

        /* @var $product Mage_Catalog_Model_Product */
        foreach ($products as $product) {
            $helper->groupMediaGalleryImages($product);
            Mage::helper('configurableswatches/productimg')
                ->indexProductImages($product, $product->getListSwatchAttrValues());
        }

    }

    /**
     * Attach children products after product load
     * Observes: catalog_product_load_after
     *
     * @param Varien_Event_Observer $observer
     */
    public function productLoadAfter(Varien_Event_Observer $observer) {

        if (!Mage::helper('configurableswatches')->isEnabled()) { // functionality disabled
            return; // exit without loading swatch functionality
        }

        /* @var $helper Mage_ConfigurableSwatches_Helper_Mediafallback */
        $helper = Mage::helper('configurableswatches/mediafallback');

        /* @var $product Mage_Catalog_Model_Product */
        $product = $observer->getDataObject();

        if ($product->getTypeId() != Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
            return;
        }

        $helper->groupMediaGalleryImages($product);

        $helper->attachConfigurableProductChildrenAttributeMapping(array($product), $product->getStoreId());
    }

    /**
     * Instruct media attribute to load images for product's children
     * if config swatches enabled.
     * Observes: catalog_product_attribute_backend_media_load_gallery_before
     *
     * @param Varien_Event_Observer $observer
     */
    public function loadChildProductImagesOnMediaLoad(Varien_Event_Observer $observer) {

        if (!Mage::helper('configurableswatches')->isEnabled()) { // functionality disabled
            return; // exit without loading swatch functionality
        }

        /* @var $eventWrapper Varien_Object */
        $eventWrapper = $observer->getEventObjectWrapper();
        /* @var $product Mage_Catalog_Model_Product */
        $product = $eventWrapper->getProduct();

        if ($product->getTypeId() != Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
            return;
        }

        /* @var $productType Mage_Catalog_Model_Product_Type_Configurable */
        $productType = Mage::getModel('catalog/product_type_configurable');

        $childrenProducts = $productType->getUsedProducts(null, $product);
        $product->setChildrenProducts($childrenProducts);

        $mediaProductIds = array();
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
     *
     * @param Varien_Event_Observer $observer
     */
    public function convertLayerBlock(Varien_Event_Observer $observer)
    {
        $front = Mage::app()->getRequest()->getRouteName();
        $controller = Mage::app()->getRequest()->getControllerName();
        $action = Mage::app()->getRequest()->getActionName();

        // Perform this operation if we're on a category view page or search results page
        if (($front == 'catalog' && $controller == 'category' && $action == 'view')
            || ($front == 'catalogsearch' && $controller == 'result' && $action == 'index')) {

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
