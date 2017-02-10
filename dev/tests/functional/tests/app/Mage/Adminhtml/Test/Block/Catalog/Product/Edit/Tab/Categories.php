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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab;

use Mage\Catalog\Test\Fixture\CatalogCategory;
use Mage\Adminhtml\Test\Block\Widget\Tab;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Client\Element\SimpleElement as Element;

/**
 * Categories tree block.
 */
class Categories extends Tab
{
    /**
     * Category tree.
     *
     * @var string
     */
    protected $treeElement = '.x-tree-root-ct';

    /**
     * Fill data for category tab.
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        foreach ($fields as $category) {
            $this->selectCategory($category);
        }

        return $this;
    }

    /**
     * Get value.
     *
     * @param null $fields
     * @param Element $element
     * @return array
     */
    public function getDataFormTab($fields = null, Element $element = null)
    {
        return [
            'category_ids' => $this->_rootElement->find($this->treeElement, Locator::SELECTOR_CSS, 'tree')->getValue()
        ];
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
        $path = implode('/', $parentPath);

        $this->_rootElement->find($this->treeElement, Locator::SELECTOR_CSS, 'tree')->setValue($path);
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
        $parentCategory = $category->getDataFieldConfig('parent_id')['source']->getParentCategory();

        if ($parentCategory != null) {
            $path = $this->prepareFullCategoryPath($parentCategory);
        }
        return array_filter(array_merge($path, [$category->getPath(), $category->getName()]));
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
        return $this->_rootElement->find($this->treeElement, Locator::SELECTOR_CSS, 'tree')
            ->isCategoryVisible($categoryPath);
    }
}
