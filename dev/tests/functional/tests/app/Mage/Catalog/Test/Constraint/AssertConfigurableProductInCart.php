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

use Magento\Mtf\Client\Browser;
use Mage\Catalog\Test\Fixture\CatalogProductSimple;
use Mage\Checkout\Test\Page\CheckoutCart;
use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Mage\Catalog\Test\Fixture\ConfigurableProduct;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Assertion that the product is correctly displayed in cart.
 */
class AssertConfigurableProductInCart extends AssertProductInCart
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assertion that the product is correctly displayed in cart.
     *
     * @param CatalogProductView $catalogProductView
     * @param InjectableFixture $product
     * @param Browser $browser
     * @param CheckoutCart $checkoutCart
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        InjectableFixture $product,
        Browser $browser,
        CheckoutCart $checkoutCart
    ) {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $catalogProductView->getViewBlock()->addToCart($product);
        $checkoutData = $product->getCheckoutData();
        $price = $checkoutCart->getCartBlock()->getCartItem($product)->getCartItemTypePrice('price');
        \PHPUnit_Framework_Assert::assertEquals(
            $checkoutData['cartItem']['price'],
            $price,
            'Product price in shopping cart is not correct.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product price in shopping cart is correct.';
    }
}
