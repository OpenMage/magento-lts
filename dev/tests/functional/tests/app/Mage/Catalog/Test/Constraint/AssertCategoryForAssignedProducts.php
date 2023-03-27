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

use Magento\Mtf\Client\Browser;
use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Catalog\Test\Fixture\CatalogCategory;
use Mage\Catalog\Test\Page\Category\CatalogCategoryView;

/**
 * Assert that displayed assigned products on category page equals passed from fixture.
 */
class AssertCategoryForAssignedProducts extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that displayed assigned products on category page equals passed from fixture.
     *
     * @param CatalogCategory $category
     * @param CatalogCategoryView $categoryView
     * @param Browser $browser
     * @return void
     */
    public function processAssert(CatalogCategory $category, CatalogCategoryView $categoryView, Browser $browser)
    {
        $browser->open($_ENV['app_frontend_url'] . strtolower($category->getUrlKey()) . '.html');
        $products = $category->getDataFieldConfig('category_products')['source']->getProducts();
        foreach ($products as $productFixture) {
            \PHPUnit_Framework_Assert::assertTrue(
                $categoryView->getListProductBlock()->isProductVisible($productFixture),
                "Products '{$productFixture->getName()}' is not find."
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
        return 'Displayed assigned products on category page equal to passed from fixture.';
    }
}
