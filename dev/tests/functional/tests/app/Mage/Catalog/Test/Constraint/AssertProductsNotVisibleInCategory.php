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
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\Constraint;

use Mage\Cms\Test\Page\CmsIndex;
use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Catalog\Test\Fixture\CatalogCategory;
use Mage\Catalog\Test\Page\Category\CatalogCategoryView;

/**
 * Assert that products are absent in category frontend page.
 */
class AssertProductsNotVisibleInCategory extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that products are absent in category frontend page.
     *
     * @param CatalogCategoryView $catalogCategoryView
     * @param CmsIndex $cmsIndex
     * @param CatalogCategory $category
     * @param array $unassignedProducts
     * @return void
     */
    public function processAssert(
        CatalogCategoryView $catalogCategoryView,
        CmsIndex $cmsIndex,
        CatalogCategory $category,
        array $unassignedProducts
    ) {
        $cmsIndex->open();
        $cmsIndex->getTopmenu()->selectCategory($category->getName());
        foreach ($unassignedProducts as $product) {
            $isProductVisible = $catalogCategoryView->getListProductBlock()->isProductVisible($product);
            while (!$isProductVisible && $catalogCategoryView->getBottomToolbar()->nextPage()) {
                $isProductVisible = $catalogCategoryView->getListProductBlock()->isProductVisible($product);
            }
            \PHPUnit_Framework_Assert::assertFalse(
                $isProductVisible,
                "Product {$product->getName()} is present in category page."
            );
        }
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Products are absent in category page';
    }
}
