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

namespace Mage\Catalog\Test\Constraint;

use Magento\Mtf\Fixture\InjectableFixture;
use Mage\Cms\Test\Page\CmsIndex;
use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Catalog\Test\Fixture\CatalogCategory;
use Mage\Catalog\Test\Page\Category\CatalogCategoryView;

/**
 * Assert that product is visible in the assigned category.
 */
class AssertProductVisibleInCategory extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Displays an error message.
     *
     * @var string
     */
    protected $errorMessage = 'Product is absent on category page.';

    /**
     * Message for passing test.
     *
     * @var string
     */
    protected $successfulMessage = 'Product is visible in the assigned category.';

    /**
     * Assert that product is visible in the assigned category.
     *
     * @param CatalogCategoryView $catalogCategoryView
     * @param CmsIndex $cmsIndex
     * @param InjectableFixture $product
     * @param CatalogCategory|null $category
     * @return void
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function processAssert(
        CatalogCategoryView $catalogCategoryView,
        CmsIndex $cmsIndex,
        InjectableFixture $product,
        CatalogCategory $category = null
    ) {
        $categoryName = $product->hasData('category_ids') ? $product->getCategoryIds()[0] : $category->getName();
        $cmsIndex->open();
        $cmsIndex->getTopmenu()->selectCategory($categoryName);

        $isProductVisible = $catalogCategoryView->getListProductBlock()->isProductVisible($product);
        while (!$isProductVisible && $catalogCategoryView->getBottomToolbar()->nextPage()) {
            $isProductVisible = $catalogCategoryView->getListProductBlock()->isProductVisible($product);
        }

        if (($product->getVisibility() === 'Search') || ($this->getStockStatus($product) === 'Out of Stock')) {
            $isProductVisible = !$isProductVisible;
            $this->errorMessage = 'Product found in this category.';
            $this->successfulMessage = 'Asserts that the product could not be found in this category.';
        }

        \PHPUnit_Framework_Assert::assertTrue(
            $isProductVisible,
            $this->errorMessage
        );
    }

    /**
     * Getting is in stock status.
     *
     * @param InjectableFixture $product
     * @return string|null
     */
    protected function getStockStatus(InjectableFixture $product)
    {
        $quantityAndStockStatus = $product->getStockData();
        return isset($quantityAndStockStatus['is_in_stock']) ? $quantityAndStockStatus['is_in_stock'] : null;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return $this->successfulMessage;
    }
}
