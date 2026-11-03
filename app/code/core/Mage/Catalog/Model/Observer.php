<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog Observer
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Observer
{
    /**
     * Catalog Product Compare Items Clean
     *
     * @return $this
     */
    public function catalogProductCompareClean(Varien_Event_Observer $observer)
    {
        Mage::getModel('catalog/product_compare_item')->clean();
        return $this;
    }

    /**
     * Checking whether the using static urls in WYSIWYG allowed event
     */
    public function catalogCheckIsUsingStaticUrlsAllowed(Varien_Event_Observer $observer)
    {
        $storeId = $observer->getEvent()->getDataByKey('store_id');
        $result  = $observer->getEvent()->getDataByKey('result');
        $result->isAllowed = Mage::helper('catalog')->setStoreId($storeId)->isUsingStaticUrlsAllowed();
    }

    /**
     * Cron job method for product prices to reindex
     */
    public function reindexProductPrices(Mage_Cron_Model_Schedule $schedule)
    {
        $indexProcess = Mage::getSingleton('index/indexer')->getProcessByCode('catalog_product_price');
        if ($indexProcess) {
            $indexProcess->reindexAll();
        }
    }

    /**
     * Adds catalog categories to top menu
     */
    public function addCatalogToTopmenuItems(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        $block->addCacheTag(Mage_Catalog_Model_Category::CACHE_TAG);
        $this->_addCategoriesToMenu(
            Mage::helper('catalog/category')->getStoreCategories(),
            $observer->getMenu(),
            $block,
        );
    }

    /**
     * Recursively adds categories to top menu
     *
     * @param array|Varien_Data_Tree_Node_Collection $categories
     * @param Varien_Data_Tree_Node                  $parentCategoryNode
     * @param Mage_Page_Block_Html_Topmenu           $menuBlock
     * @param bool                                   $addTags
     */
    protected function _addCategoriesToMenu($categories, $parentCategoryNode, $menuBlock, $addTags = false)
    {
        $categoryModel = Mage::getModel('catalog/category');
        foreach ($categories as $category) {
            if (!$category->getIsActive()) {
                continue;
            }

            $nodeId = 'category-node-' . $category->getId();

            $categoryModel->setId($category->getId());
            if ($addTags) {
                $menuBlock->addModelTags($categoryModel);
            }

            $tree = $parentCategoryNode->getTree();
            $categoryData = [
                'name' => $category->getName(),
                'id' => $nodeId,
                'url' => Mage::helper('catalog/category')->getCategoryUrl($category),
                'is_active' => $this->_isActiveMenuCategory($category),
            ];
            $categoryNode = new Varien_Data_Tree_Node($categoryData, 'id', $tree, $parentCategoryNode);
            $parentCategoryNode->addChild($categoryNode);

            $flatHelper = Mage::helper('catalog/category_flat');
            if ($flatHelper->isEnabled() && $flatHelper->isBuilt(true)) {
                $subcategories = (array) $category->getChildrenNodes();
            } else {
                $subcategories = $category->getChildren();
            }

            $this->_addCategoriesToMenu($subcategories, $categoryNode, $menuBlock, $addTags);
        }
    }

    /**
     * Checks whether category belongs to active category's path
     *
     * @param  Varien_Data_Tree_Node $category
     * @return bool
     */
    protected function _isActiveMenuCategory($category)
    {
        $catalogLayer = Mage::getSingleton('catalog/layer');
        if (!$catalogLayer) {
            return false;
        }

        $currentCategory = $catalogLayer->getCurrentCategory();
        if (!$currentCategory) {
            return false;
        }

        $categoryPathIds = explode(',', $currentCategory->getPathInStore());
        return in_array($category->getId(), $categoryPathIds);
    }

    /**
     * Checks whether attribute_code by current module is reserved
     *
     * @throws Mage_Core_Exception
     */
    public function checkReservedAttributeCodes(Varien_Event_Observer $observer)
    {
        /** @var Mage_Catalog_Model_Entity_Attribute $attribute */
        $attribute = $observer->getEvent()->getAttribute();
        if (!is_object($attribute)) {
            return;
        }

        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product');
        if ($product->isReservedAttribute($attribute)) {
            throw new Mage_Core_Exception(
                Mage::helper('catalog')->__("The attribute code '%s' is reserved by system. Please try another attribute code", $attribute->getAttributeCode()),
            );
        }
    }
}
