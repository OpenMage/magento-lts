<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Tests
 * @package    Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\Constraint;

use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Catalog\Test\Page\Category\CatalogCategoryView;

/**
 * Assert that filtered product is present on category page by filter and another products are absent.
 */
abstract class AbstractAssertProductsVisibleOnCategoryPageShopByFilter extends AbstractConstraint
{
    /**
     * Check is visible product on category page.
     *
     * @param CatalogCategoryView $catalogCategoryView
     * @param InjectableFixture[] $products
     * @param string $searchProductsIndexes
     * @return void
     */
    protected function verify(CatalogCategoryView $catalogCategoryView, array $products, $searchProductsIndexes)
    {
        $searchProductsIndexes = explode(',', $searchProductsIndexes);
        foreach ($products as $key => $product) {
            $isProductVisible = $catalogCategoryView->getListProductBlock()->isProductVisible($product);
            while (!$isProductVisible && $catalogCategoryView->getBottomToolbar()->nextPage()) {
                $isProductVisible = $catalogCategoryView->getListProductBlock()->isProductVisible($product);
            }
            $expected = in_array($key, $searchProductsIndexes) ? true : false;
            \PHPUnit_Framework_Assert::assertEquals($expected, $isProductVisible);
        }
    }
}
