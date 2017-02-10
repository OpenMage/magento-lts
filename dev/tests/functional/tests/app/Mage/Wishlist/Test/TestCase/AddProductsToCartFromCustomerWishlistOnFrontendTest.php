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

namespace Mage\Wishlist\Test\TestCase;

use Mage\Checkout\Test\Fixture\Cart;
use Mage\Customer\Test\Fixture\Customer;

/**
 * Preconditions:
 * 1. Create customer and login to frontend.
 * 2. Create products.
 * 3. Add products to customer's wishlist.
 *
 * Steps:
 * 1. Navigate to My Account -> My Wishlist.
 * 2. Fill qty and update wish list.
 * 3. Click "Add to Cart".
 * 4. Perform asserts.
 *
 * @group Wishlist_(CS)
 * @ZephyrId MPERF-7293
 */
class AddProductsToCartFromCustomerWishlistOnFrontendTest extends AbstractWishlistTest
{
    /**
     * Run add products to cart from customer wishlist on frontend test.
     *
     * @param Customer $customer
     * @param string $products
     * @param int|null $qty
     * @return array
     */
    public function test(Customer $customer, $products, $qty = null)
    {
        // Preconditions
        $this->loginCustomer($customer);
        $products = $this->createProducts($products);
        $this->addToWishlist($products);

        // Steps
        $this->addToCart($products, $qty);

        // Prepare data for asserts
        $cart = $this->createCart($products, $qty);

        return ['products' => $products, 'customer' => $customer, 'cart' => $cart];
    }

    /**
     * Add products from wish list to cart.
     *
     * @param array $products
     * @param int|null $qty
     * @return void
     */
    protected function addToCart(array $products, $qty)
    {
        foreach ($products as $product) {
            $this->cmsIndex->getTopLinksBlock()->openAccountLink("My Wishlist");
            $itemProductBlock = $this->wishlistIndex->getItemsBlock()->getItemProductBlock($product);
            if ($qty !== null) {
                $itemProductBlock->fillProduct(['qty' => $qty]);
                $this->wishlistIndex->getWishlistBlock()->clickUpdateWishlist();
            }
            $itemProductBlock->clickAddToCart();
            $productViewBlock = $this->catalogProductView->getViewBlock();
            if ($productViewBlock->isVisible()) {
                $productViewBlock->fillOptions($product);
                $productViewBlock->clickAddToCart();
                $this->catalogProductView->getMessagesBlock()->waitSuccessMessage();
            }
        }
    }

    /**
     * Create cart fixture.
     *
     * @param array $products
     * @return Cart
     */
    protected function createCart(array $products)
    {
        return $this->fixtureFactory->createByCode('cart', ['data' => ['items' => ['products' => $products]]]);
    }
}
