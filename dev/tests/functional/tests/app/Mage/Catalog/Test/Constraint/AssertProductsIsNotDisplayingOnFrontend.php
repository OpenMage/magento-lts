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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\Constraint;

use Magento\Mtf\Client\Browser;
use Mage\Cms\Test\Page\CmsIndex;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Mtf\Fixture\InjectableFixture;
use Mage\Catalog\Test\Fixture\CatalogCategory;
use Mage\CatalogSearch\Test\Page\CatalogsearchResult;
use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Mage\Catalog\Test\Page\Category\CatalogCategoryView;

/**
 * Assert that product is not displayed on frontend.
 */
class AssertProductsIsNotDisplayingOnFrontend extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    /**
     * Assert that product is not displayed on frontend.
     *
     * @param InjectableFixture[] $products
     * @param Browser $browser
     * @param CatalogProductView $catalogProductView
     * @param CmsIndex $cmsIndex
     * @param CatalogsearchResult $catalogSearchResult
     * @param CatalogCategoryView $catalogCategoryView
     * @param AssertProductIsNotDisplayingOnFrontend $assertProductIsNotDisplayingOnFrontend
     * @param CatalogCategory|null $category
     * @return void
     */
    public function processAssert(
        array $products,
        Browser $browser,
        CatalogProductView $catalogProductView,
        CmsIndex $cmsIndex,
        CatalogsearchResult $catalogSearchResult,
        CatalogCategoryView $catalogCategoryView,
        AssertProductIsNotDisplayingOnFrontend $assertProductIsNotDisplayingOnFrontend,
        CatalogCategory $category = null
    ) {
        foreach ($products as $product) {
            $assertProductIsNotDisplayingOnFrontend->processAssert(
                $product,
                $browser,
                $catalogProductView,
                $cmsIndex,
                $catalogSearchResult,
                $catalogCategoryView,
                $category
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
        return 'Products is not displayed on frontend.';
    }
}
