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

use Mage\Cms\Test\Page\CmsIndex;
use Mage\Customer\Test\Fixture\Customer;
use Mage\Customer\Test\Fixture\Address;
use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Catalog\Test\Fixture\CatalogCategory;
use Mage\Catalog\Test\Page\Category\CatalogCategoryView;
use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Mage\Checkout\Test\Page\CheckoutCart;
use Mage\Checkout\Test\Page\CheckoutOnepage;

/**
 * Assert that products' MAP has been applied before checkout and visible only on onepage checkout page.
 */
class AssertProductMapAppliedBeforeCheckout extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that products' MAP has been applied before checkout.
     *
     * @param CatalogCategory $category
     * @param Customer $customer
     * @param Address $address
     * @param CatalogCategoryView $catalogCategoryView
     * @param CmsIndex $cmsIndex
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $cart
     * @param CheckoutOnepage $checkoutOnePage
     * @param array $products
     * @return void
     */
    public function processAssert(
        CatalogCategory $category,
        Customer $customer,
        Address $address,
        CatalogCategoryView $catalogCategoryView,
        CmsIndex $cmsIndex,
        CatalogProductView $catalogProductView,
        CheckoutCart $cart,
        CheckoutOnepage $checkoutOnePage,
        array $products
    ) {
        for ($i = 0; $i < count($products); $i++) {
            $cart->getCartBlock()->clearShoppingCart();
            $productName = $products[$i]->getName();
            $cmsIndex->open();
            $cmsIndex->getTopmenu()->selectCategory($category->getName());

            // Check that price is not present on category page.
            $listProductBlock = $catalogCategoryView->getListProductBlock();
            $productPriceBlock = $listProductBlock->getProductPriceBlock($productName);
            $productPriceBlock->clickForPrice();
            \PHPUnit_Framework_Assert::assertFalse(
                $productPriceBlock->getMapBlock()->isPriceVisible(),
                'Price is present in MSRP dialog on category page.'
            );

            // Check that price is not present on product page.
            $listProductBlock->openProductViewPage($productName);
            \PHPUnit_Framework_Assert::assertFalse(
                $catalogProductView->getViewBlock()->getPriceBlock()->isRegularPriceVisible(),
                'Price is present in View block on product page.'
            );

            // Check that price is not present on cart.
            $catalogProductView->getViewBlock()->addToCart($products[$i]);
            \PHPUnit_Framework_Assert::assertTrue(
                $cart->getCartBlock()->getCartItem($products[$i])->isMsrpVisible(),
                "MSRP link is not visible in cart."
            );

            // Check that price is present on review block in onepage checkout page.
            $cart->getCartBlock()->getProceedToCheckoutBlock()->proceedToCheckout();
            $checkoutMethodBlock = $checkoutOnePage->getLoginBlock();
            $billingBlock = $checkoutOnePage->getBillingBlock();
            $paymentMethodBlock = $checkoutOnePage->getPaymentMethodsBlock();
            $shippingBlock = $checkoutOnePage->getShippingMethodBlock();
            $checkoutMethodBlock->guestCheckout();
            $checkoutMethodBlock->clickContinue();
            $billingBlock->fillBilling($address, $customer);
            $billingBlock->clickContinue();
            $shippingBlock->selectShippingMethod(['shipping_service' => 'Flat Rate', 'shipping_method' => 'Fixed']);
            $shippingBlock->clickContinue();
            $paymentMethodBlock->selectPaymentMethod(['method' => 'checkmo']);
            $paymentMethodBlock->clickContinue();
            \PHPUnit_Framework_Assert::assertEquals(
                number_format($products[$i]->getPrice(), 2),
                $checkoutOnePage->getReviewBlock()->getTotalBlock()->getData('subtotal'),
                "Subtotal in checkout one page for $productName is not equal to expected."
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
        return "Products' MAP has been applied before checkout.";
    }
}
