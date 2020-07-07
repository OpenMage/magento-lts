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
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Catalog\Category;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;
use Mage\Catalog\Test\Fixture\CatalogCategory;
use Mage\Adminhtml\Test\Block\Template;

/**
 * Categories tree block.
 */
class Tree extends Block
{
    /**
     * 'Add Subcategory' button.
     *
     * @var string
     */
    protected $addSubcategory = '#add_subcategory_button';

    /**
     * 'Add Root Category' button.
     *
     * @var string
     */
    protected $addRootCategory = '#add_root_category_button';

    /**
     * 'Expand All' link.
     *
     * @var string
     */
    protected $expandAll = 'a[onclick*=expandTree]';

    /**
     * Backend abstract block.
     *
     * @var string
     */
    protected $templateBlock = './ancestor::body';

    /**
     * Category tree.
     *
     * @var string
     */
    protected $treeElement = '.tree-holder';

    /**
     * Get backend abstract block.
     *
     * @return Template
     */
    protected function getTemplateBlock()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Template',
            ['element' => $this->_rootElement->find($this->templateBlock, Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Press 'Add Subcategory' button.
     *
     * @return void
     */
    public function addSubcategory()
    {
        $this->_rootElement->find($this->addSubcategory, Locator::SELECTOR_CSS)->click();
        $this->getTemplateBlock()->waitLoader();
    }

    /**
     * Press 'Add Root Category' button.
     *
     * @return void
     */
    public function addRootCategory()
    {
        $this->_rootElement->find($this->addRootCategory, Locator::SELECTOR_CSS)->click();
        $this->getTemplateBlock()->waitLoader();
    }

    /**
     * Select category.
     *
     * @param CatalogCategory $category
     * @param bool $fullPath
     * @return void
     */
    public function selectCategory(CatalogCategory $category, $fullPath = true)
    {
        $parentPath = $this->prepareFullCategoryPath($category);
        if (!$fullPath) {
            array_pop($parentPath);
        }
        if (empty($parentPath)) {
            return;
        }
        $path = implode('/', $parentPath);
        $this->expandAllCategories();
        $this->_rootElement->find($this->treeElement, Locator::SELECTOR_CSS, 'tree')->setValue($path);
        $this->getTemplateBlock()->waitLoader();
    }

    /**
     * Prepare category path.
     *
     * @param CatalogCategory $category
     * @return array
     */
    protected function prepareFullCategoryPath(CatalogCategory $category)
    {
        $path = [];
        if ($category->hasData('parent_id')) {
            $parentCategory = $category->getDataFieldConfig('parent_id')['source']->getParentCategory();
            if ($parentCategory != null) {
                $path = $this->prepareFullCategoryPath($parentCategory);
            }
        }
        return array_filter(array_merge($path, [$category->getPath(), $category->getName()]));
    }

    /**
     * Expand all categories tree.
     *
     * @return void
     */
    protected function expandAllCategories()
    {
        $this->browser->find($this->addRootCategory)->hover();
        $this->_rootElement->find($this->expandAll)->click();
    }

    /**
     * Check category in category tree.
     *
     * @param CatalogCategory $category
     * @return bool
     */
    public function isCategoryVisible(CatalogCategory $category)
    {
        $categoryPath = $this->prepareFullCategoryPath($category);
        $categoryPath = implode('/', $categoryPath);
        return $this->_rootElement->find($this->treeElement, Locator::SELECTOR_CSS, 'tree')
            ->isElementVisible($categoryPath);
    }
}
