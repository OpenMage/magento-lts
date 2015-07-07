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
use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Checkout\Test\Page\CheckoutCart;
use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Mage\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Assertion that the product is correctly displayed in cart.
 */
class AssertProductInCart extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

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
        // Add product to cart
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $catalogProductView->getViewBlock()->addToCart($product);

        // Check price
        $this->assertOnShoppingCart($product, $checkoutCart);
    }

    /**
     * Assert prices on the shopping cart.
     *
     * @param InjectableFixture $product
     * @param CheckoutCart $checkoutCart
     * @return void
     */
    protected function assertOnShoppingCart(InjectableFixture $product, CheckoutCart $checkoutCart)
    {
        $cartItem = $checkoutCart->getCartBlock()->getCartItem($product);
        $formPrice = $cartItem->getCartItemTypePrice('price');
        $fixturePrice = number_format($this->prepareFixturePrice($product), 2);

        \PHPUnit_Framework_Assert::assertEquals(
            $fixturePrice,
            $formPrice,
            'Product price in shopping cart is not correct.'
        );
    }

    /**
     * Prepare product price from fixture.
     *
     * @param InjectableFixture $product
     * @return float
     */
    protected function prepareFixturePrice(InjectableFixture $product)
    {
        /** @var CatalogProductSimple $product */
        $customOptions = $product->getCustomOptions();
        $checkoutData = $product->getCheckoutData();
        $checkoutCustomOptions = isset($checkoutData['options']['custom_options'])
            ? $checkoutData['options']['custom_options']
            : [];

        if (isset($checkoutData['cartItem'])) {
            $fixturePrice = $checkoutData['cartItem']['price'];
        } else {
            $fixturePrice = $product->getPrice();
            $groupPrice = $product->getGroupPrice();
            $specialPrice = $product->getSpecialPrice();
            if ($groupPrice) {
                $groupPrice = reset($groupPrice);
                $fixturePrice = $groupPrice['price'];
            }
            if ($specialPrice) {
                $fixturePrice = $specialPrice;
            }
            foreach ($checkoutCustomOptions as $checkoutOption) {
                $attributeKey = str_replace('attribute_key_', '', $checkoutOption['title']);
                $optionKey = str_replace('option_key_', '', $checkoutOption['value']);
                $option = $customOptions[$attributeKey]['options'][$optionKey];

                if ('Fixed' == $option['price_type']) {
                    $fixturePrice += $option['price'];
                } else {
                    $fixturePrice += ($fixturePrice / 100) * $option['price'];
                }
            }
        }

        return $fixturePrice;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product is correctly displayed in cart.';
    }
}
