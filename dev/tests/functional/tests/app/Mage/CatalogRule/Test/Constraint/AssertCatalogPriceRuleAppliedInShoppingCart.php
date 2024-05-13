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

namespace Mage\CatalogRule\Test\Constraint;

use Mage\Catalog\Test\Page\Category\CatalogCategoryView;
use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Mage\Checkout\Test\Page\CheckoutCart;
use Mage\Cms\Test\Page\CmsIndex;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Check that Catalog Price Rule is applied for product(s) in Shopping Cart
 * according to Priority(Priority/Stop Further Rules Processing).
 */
class AssertCatalogPriceRuleAppliedInShoppingCart extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    /**
     * Assert that Catalog Price Rule is applied for product(s) in Shopping Cart
     * according to Priority(Priority/Stop Further Rules Processing).
     *
     * @param InjectableFixture $product
     * @param CatalogProductView $pageCatalogProductView
     * @param CmsIndex $cmsIndex
     * @param CatalogCategoryView $catalogCategoryView
     * @param CheckoutCart $pageCheckoutCart
     * @param array $prices
     * @return void
     */
    public function processAssert(
        InjectableFixture $product,
        CatalogProductView $pageCatalogProductView,
        CmsIndex $cmsIndex,
        CatalogCategoryView $catalogCategoryView,
        CheckoutCart $pageCheckoutCart,
        array $prices
    ) {
        $cmsIndex->open();
        $cmsIndex->getTopmenu()->selectCategory($product->getCategoryIds()[0]);
        $catalogCategoryView->getListProductBlock()->openProductViewPage($product->getName());
        $pageCatalogProductView->getViewBlock()->addToCart($product);
        $actualGrandTotal = $pageCheckoutCart->getCartBlock()->getCartItem($product)->getCartItemTypePrice('price');
        \PHPUnit_Framework_Assert::assertEquals($prices['grand_total'], $actualGrandTotal);
    }

    /**
     * Returns a string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return 'Catalog Price Rule is applied for product(s) in Shopping Cart according to Priority.';
    }
}
