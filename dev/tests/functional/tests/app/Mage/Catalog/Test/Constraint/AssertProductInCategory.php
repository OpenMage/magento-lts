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

namespace Mage\Catalog\Test\Constraint;

use Magento\Mtf\Fixture\InjectableFixture;
use Mage\Cms\Test\Page\CmsIndex;
use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Catalog\Test\Fixture\CatalogCategory;
use Mage\Catalog\Test\Page\Category\CatalogCategoryView;

/**
 * Assert that product is visible in the assigned category and checks the price of the product.
 */
class AssertProductInCategory extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Checking the product in the page of its price.
     *
     * @param CatalogCategoryView $catalogCategoryView
     * @param CmsIndex $cmsIndex
     * @param InjectableFixture $product
     * @param CatalogCategory $category
     * @return void
     */
    public function processAssert(
        CatalogCategoryView $catalogCategoryView,
        CmsIndex $cmsIndex,
        InjectableFixture $product,
        CatalogCategory $category
    ) {
        // Open category view page and check visible product
        $categoryName = $category->getName();
        if ($product->hasData('category_ids')) {
            $categoryIds = $product->getCategoryIds();
            $categoryName = is_array($categoryIds) ? reset($categoryIds) : $categoryName;
        }
        $cmsIndex->open();
        $cmsIndex->getTopmenu()->selectCategory($categoryName);

        $isProductVisible = $catalogCategoryView->getListProductBlock()->isProductVisible($product);
        while (!$isProductVisible && $catalogCategoryView->getBottomToolbar()->nextPage()) {
            $isProductVisible = $catalogCategoryView->getListProductBlock()->isProductVisible($product);
        }

        \PHPUnit_Framework_Assert::assertTrue(
            $isProductVisible,
            'Product is absent on category page.'
        );

        //Process price asserts
        $this->assertPrice($product, $catalogCategoryView);
    }

    /**
     * Verify product price on category view page.
     *
     * @param InjectableFixture $product
     * @param CatalogCategoryView $catalogCategoryView
     * @return void
     */
    protected function assertPrice(InjectableFixture $product, CatalogCategoryView $catalogCategoryView)
    {
        $price = $catalogCategoryView->getListProductBlock()->getProductPriceBlock($product->getName())
            ->getRegularPrice();

        \PHPUnit_Framework_Assert::assertEquals(
            number_format($product->getPrice(), 2),
            $price,
            'Product regular price on category page is not correct.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product price on category page correct.';
    }
}
