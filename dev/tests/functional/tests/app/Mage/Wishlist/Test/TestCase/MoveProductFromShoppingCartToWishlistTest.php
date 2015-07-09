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

namespace Mage\Wishlist\Test\TestCase;

use Mage\Checkout\Test\Page\CheckoutCart;
use Mage\Customer\Test\Fixture\Customer;
use Magento\Mtf\Fixture\FixtureInterface;

/**
 * Preconditions:
 * 1. Test products are created.
 * 2. Create customer and login on frontend.
 *
 * Steps:
 * 1. Add product to Shopping Cart.
 * 2. Click 'Move to Wishlist' button from Shopping Cart for added product.
 * 3. Perform asserts.
 *
 * @group Shopping_Cart_(CS)
 * @ZephyrId MPERF-7605
 */
class MoveProductFromShoppingCartToWishlistTest extends AbstractWishlistTest
{
    /**
     * Run Move from ShoppingCard to Wishlist test.
     *
     * @param Customer $customer
     * @param CheckoutCart $checkoutCart
     * @param string $product
     * @return array
     */
    public function test(Customer $customer, CheckoutCart $checkoutCart, $product) {
        // Preconditions:
        $product = $this->createProducts($product)[0];
        $this->loginCustomer($customer);

        // Steps:
        $this->addToCart($product);
        $checkoutCart->open()->getCartBlock()->getCartItem($product)->moveToWishlist();

        return ['product' => $product];
    }

    /**
     * Add product to cart.
     *
     * @param FixtureInterface $product
     * @return void
     */
    protected function addToCart(FixtureInterface $product)
    {
        $this->objectManager->create('Mage\Checkout\Test\TestStep\AddProductsToTheCartStep', ['products' => [$product]])
            ->run();
    }
}
