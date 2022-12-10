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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog navigation
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Navigation extends Mage_Core_Block_Template
{
    /**
     * Category instance
     *
     * @var Mage_Catalog_Model_Category
     */
    protected $_categoryInstance;

    /**
     * Current category key
     *
     * @var string
     */
    protected $_currentCategoryKey;

    /**
     * Array of level position counters
     *
     * @var array
     */
    protected $_itemLevelPositions = [];

    /**
     * Current child categories collection
     *
     * @var Mage_Catalog_Model_Resource_Category_Collection
     */
    protected $_currentChildCategories;

    /**
     * Set cache data
     */
    protected function _construct()
    {
        $this->addData(['cache_lifetime' => false]);
        $this->addCacheTag([
            Mage_Catalog_Model_Category::CACHE_TAG,
            Mage_Core_Model_Store_Group::CACHE_TAG
        ]);
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $shortCacheId = [
            'CATALOG_NAVIGATION',
            Mage::app()->getStore()->getId(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            Mage::getSingleton('customer/session')->getCustomerGroupId(),
            'template' => $this->getTemplate(),
            'name' => $this->getNameInLayout(),
            $this->getCurrenCategoryKey()
        ];
        $cacheId = $shortCacheId;

        $shortCacheId = array_values($shortCacheId);
        $shortCacheId = implode('|', $shortCacheId);
        $shortCacheId = md5($shortCacheId);

        $cacheId['category_path'] = $this->getCurrenCategoryKey();
        $cacheId['short_cache_id'] = $shortCacheId;

        return $cacheId;
    }

    /**
     * Get current category key
     *
     * @return mixed
     */
    public function getCurrenCategoryKey()
    {
        if (!$this->_currentCategoryKey) {
            $category = Mage::registry('current_category');
            if ($category) {
                $this->_currentCategoryKey = $category->getPath();
            } else {
                $this->_currentCategoryKey = Mage::app()->getStore()->getRootCategoryId();
            }
        }

        return $this->_currentCategoryKey;
    }

    /**
     * Get categories of current store
     *
     * @return Varien_Data_Tree_Node_Collection
     */
    public function getStoreCategories()
    {
        $helper = Mage::helper('catalog/category');
        return $helper->getStoreCategories();
    }

    /**
     * Retrieve child categories of current category
     *
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    public function getCurrentChildCategories()
    {
        if ($this->_currentChildCategories === null) {
            $layer = Mage::getSingleton('catalog/layer');
            $category = $layer->getCurrentCategory();
            $this->_currentChildCategories = $category->getChildrenCategories();
            $productCollection = Mage::getResourceModel('catalog/product_collection');
            $layer->prepareProductCollection($productCollection);
            $productCollection->addCountToCategories($this->_currentChildCategories);
        }
        return $this->_currentChildCategories;
    }

    /**
     * Check whether specified category is active
     *
     * @param Varien_Object $category
     * @return bool
     */
    public function isCategoryActive($category)
    {
        return $this->getCurrentCategory()
            ? in_array($category->getId(), $this->getCurrentCategory()->getPathIds()) : false;
    }

    /**
     * Retrieve category instance
     *
     * @return Mage_Catalog_Model_Category
     */
    protected function _getCategoryInstance()
    {
        if (is_null($this->_categoryInstance)) {
            $this->_categoryInstance = Mage::getModel('catalog/category');
        }
        return $this->_categoryInstance;
    }

    /**
     * Get url for category data
     *
     * @param Mage_Catalog_Model_Category $category
     * @return string
     */
    public function getCategoryUrl($category)
    {
        if ($category instanceof Mage_Catalog_Model_Category) {
            $url = $category->getUrl();
        } else {
            $url = $this->_getCategoryInstance()
                ->setData($category->getData())
                ->getUrl();
        }

        return $url;
    }

    /**
     * Return item position representation in menu tree
     *
     * @param int $level
     * @return string
     */
    protected function _getItemPosition($level)
    {
        if ($level == 0) {
            $zeroLevelPosition = isset($this->_itemLevelPositions[$level]) ? $this->_itemLevelPositions[$level] + 1 : 1;
            $this->_itemLevelPositions = [];
            $this->_itemLevelPositions[$level] = $zeroLevelPosition;
        } elseif (isset($this->_itemLevelPositions[$level])) {
            $this->_itemLevelPositions[$level]++;
        } else {
            $this->_itemLevelPositions[$level] = 1;
        }

        $position = [];
        for ($i = 0; $i <= $level; $i++) {
            if (isset($this->_itemLevelPositions[$i])) {
                $position[] = $this->_itemLevelPositions[$i];
            }
        }
        return implode('-', $position);
    }

    /**
     * Render category to html
     *
     * @param Mage_Catalog_Model_Category $category
     * @param int $level Nesting level number
     * @param bool $isLast Whether ot not this item is last, affects list item class
     * @param bool $isFirst Whether ot not this item is first, affects list item class
     * @param bool $isOutermost Whether ot not this item is outermost, affects list item class
     * @param string $outermostItemClass Extra class of outermost list items
     * @param string $childrenWrapClass If specified wraps children list in div with this class
     * @param bool $noEventAttributes Whether ot not to add on* attributes to list item
     * @return string
     */
    protected function _renderCategoryMenuItemHtml(
        $category,
        $level = 0,
        $isLast = false,
        $isFirst = false,
        $isOutermost = false,
        $outermostItemClass = '',
        $childrenWrapClass = '',
        $noEventAttributes = false
    ) {
        if (!$category->getIsActive()) {
            return '';
        }
        $html = [];

        // get all children
        // If Flat Data enabled then use it but only on frontend
        $flatHelper = Mage::helper('catalog/category_flat');
        if ($flatHelper->isAvailable() && $flatHelper->isBuilt(true) && !Mage::app()->getStore()->isAdmin()) {
            $children = (array)$category->getChildrenNodes();
            $childrenCount = count($children);
        } else {
            $children = $category->getChildren();
            $childrenCount = $children->count();
        }
        $hasChildren = ($children && $childrenCount);

        // select active children
        $activeChildren = [];
        foreach ($children as $child) {
            if ($child->getIsActive()) {
                $activeChildren[] = $child;
            }
        }
        $activeChildrenCount = count($activeChildren);
        $hasActiveChildren = ($activeChildrenCount > 0);

        // prepare list item html classes
        $classes = [];
        $classes[] = 'level' . $level;
        $classes[] = 'nav-' . $this->_getItemPosition($level);
        if ($this->isCategoryActive($category)) {
            $classes[] = 'active';
        }
        $linkClass = '';
        if ($isOutermost && $outermostItemClass) {
            $classes[] = $outermostItemClass;
            $linkClass = ' class="' . $outermostItemClass . '"';
        }
        if ($isFirst) {
            $classes[] = 'first';
        }
        if ($isLast) {
            $classes[] = 'last';
        }
        if ($hasActiveChildren) {
            $classes[] = 'parent';
        }

        // prepare list item attributes
        $attributes = [];
        if (count($classes) > 0) {
            $attributes['class'] = implode(' ', $classes);
        }
        if ($hasActiveChildren && !$noEventAttributes) {
            $attributes['onmouseover'] = 'toggleMenu(this,1)';
            $attributes['onmouseout'] = 'toggleMenu(this,0)';
        }

        // assemble list item with attributes
        $htmlLi = '<li';
        foreach ($attributes as $attrName => $attrValue) {
            $htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
        }
        $htmlLi .= '>';
        $html[] = $htmlLi;

        $html[] = '<a href="' . $this->getCategoryUrl($category) . '"' . $linkClass . '>';
        $html[] = '<span>' . $this->escapeHtml($category->getName()) . '</span>';
        $html[] = '</a>';

        // render children
        $htmlChildren = '';
        $j = 0;
        foreach ($activeChildren as $child) {
            $htmlChildren .= $this->_renderCategoryMenuItemHtml(
                $child,
                ($level + 1),
                ($j == $activeChildrenCount - 1),
                ($j == 0),
                false,
                $outermostItemClass,
                $childrenWrapClass,
                $noEventAttributes
            );
            $j++;
        }
        if (!empty($htmlChildren)) {
            if ($childrenWrapClass) {
                $html[] = '<div class="' . $childrenWrapClass . '">';
            }
            $html[] = '<ul class="level' . $level . '">';
            $html[] = $htmlChildren;
            $html[] = '</ul>';
            if ($childrenWrapClass) {
                $html[] = '</div>';
            }
        }

        $html[] = '</li>';

        $html = implode("\n", $html);
        return $html;
    }

    /**
     * Render category to html
     *
     * @deprecated deprecated after 1.4
     * @param Mage_Catalog_Model_Category $category
     * @param int $level Nesting level number
     * @param bool $last Whether ot not this item is last, affects list item class
     * @return string
     */
    public function drawItem($category, $level = 0, $last = false)
    {
        return $this->_renderCategoryMenuItemHtml($category, $level, $last);
    }

    /**
     * @return Mage_Catalog_Model_Category|false
     */
    public function getCurrentCategory()
    {
        if (Mage::getSingleton('catalog/layer')) {
            return Mage::getSingleton('catalog/layer')->getCurrentCategory();
        }
        return false;
    }

    /**
     * @return array
     */
    public function getCurrentCategoryPath()
    {
        if ($this->getCurrentCategory()) {
            return explode(',', $this->getCurrentCategory()->getPathInStore());
        }
        return [];
    }

    /**
     * @param Mage_Catalog_Model_Category $category
     * @return string
     */
    public function drawOpenCategoryItem($category)
    {
        $html = '';
        if (!$category->getIsActive()) {
            return $html;
        }

        $html .= '<li';

        if ($this->isCategoryActive($category)) {
            $html .= ' class="active"';
        }

        $html .= '>' . "\n";
        $html .= '<a href="' . $this->getCategoryUrl($category) . '">'
            . '<span>' . $this->escapeHtml($category->getName()) . '</span></a>' . "\n";

        if (in_array($category->getId(), $this->getCurrentCategoryPath())) {
            $children = $category->getChildren();
            $hasChildren = $children && $children->count();

            if ($hasChildren) {
                $htmlChildren = '';
                foreach ($children as $child) {
                    $htmlChildren .= $this->drawOpenCategoryItem($child);
                }

                if (!empty($htmlChildren)) {
                    $html .= '<ul>' . "\n" . $htmlChildren . '</ul>';
                }
            }
        }
        $html .= '</li>' . "\n";

        return $html;
    }

    /**
     * Render categories menu in HTML
     *
     * @param int $level Level number for list item class to start from
     * @param string $outermostItemClass Extra class of outermost list items
     * @param string $childrenWrapClass If specified wraps children list in div with this class
     * @return string
     */
    public function renderCategoriesMenuHtml($level = 0, $outermostItemClass = '', $childrenWrapClass = '')
    {
        $activeCategories = [];
        foreach ($this->getStoreCategories() as $child) {
            if ($child->getIsActive()) {
                $activeCategories[] = $child;
            }
        }
        $activeCategoriesCount = count($activeCategories);
        $hasActiveCategoriesCount = ($activeCategoriesCount > 0);

        if (!$hasActiveCategoriesCount) {
            return '';
        }

        $html = '';
        $j = 0;
        foreach ($activeCategories as $category) {
            $html .= $this->_renderCategoryMenuItemHtml(
                $category,
                $level,
                ($j == $activeCategoriesCount - 1),
                ($j == 0),
                true,
                $outermostItemClass,
                $childrenWrapClass,
                true
            );
            $j++;
        }

        return $html;
    }
}
