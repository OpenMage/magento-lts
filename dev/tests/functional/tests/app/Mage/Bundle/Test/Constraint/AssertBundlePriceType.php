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

namespace Mage\Bundle\Test\Constraint;

use Mage\Bundle\Test\Fixture\BundleProduct;
use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Mage\Checkout\Test\Page\CheckoutCart;
use Magento\Mtf\Client\Browser;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that displayed price for bundle items on shopping cart page equals to passed from fixture.
 */
class AssertBundlePriceType extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Fixture for bundle product.
     *
     * @var BundleProduct
     */
    protected $product;

    /**
     * Assert that displayed price for bundle items on shopping cart page equals to passed from fixture.
     *   Price for bundle items has two options:
     *   1. Fixed (price of bundle product)
     *   2. Dynamic (price of bundle item)
     *
     * @param CatalogProductView $catalogProductView
     * @param BundleProduct $product
     * @param CheckoutCart $checkoutCartView
     * @param Browser $browser
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        BundleProduct $product,
        CheckoutCart $checkoutCartView,
        Browser $browser
    ) {
        $this->product = $product;
        $checkoutCartView->open()->getCartBlock()->clearShoppingCart();
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $catalogProductView->getViewBlock()->addToCart($product);

        //Process assertions
        $this->assertPrice($checkoutCartView);
    }

    /**
     * Assert prices on the product view page and shopping cart page.
     *
     * @param CheckoutCart $checkoutCartView
     * @return void
     */
    protected function assertPrice(CheckoutCart $checkoutCartView)
    {
        $formCartItem = $checkoutCartView->getCartBlock()->getCartItem($this->product);
        $fixtureCartItem = $this->product->getCheckoutData()['cartItem'];

        $this->assertCartOptions($fixtureCartItem['options']['bundle_options'], $formCartItem->getOptions());
        $this->assertCartPrice($fixtureCartItem['price'], $formCartItem->getCartItemTypePrice('price'));
    }

    /**
     * Assert cart bundle options.
     *
     * @param array $fixtureBundleOptions
     * @param array $formBundleOptions
     * @return void
     */
    protected function assertCartOptions(array $fixtureBundleOptions, array $formBundleOptions)
    {
        foreach ($fixtureBundleOptions as $key => $fixtureOption) {
            $cartIndex = str_replace('option_key_', '', $key);
            preg_match('`\d+ x .* \$*(.*)`', $formBundleOptions[$cartIndex]['value'], $matches);
            \PHPUnit_Framework_Assert::assertEquals($fixtureOption['price'], number_format($matches[1], 2));
        }
    }

    /**
     * Assert cart price.
     *
     * @param $fixtureCartPrice
     * @param $formCartPrice
     * @return void
     */
    protected function assertCartPrice($fixtureCartPrice, $formCartPrice)
    {
        \PHPUnit_Framework_Assert::assertEquals($fixtureCartPrice, number_format($formCartPrice, 2));
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Bundle price on shopping cart page is not correct.';
    }
}
