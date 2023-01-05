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

namespace Mage\Wishlist\Test\Constraint;

use Mage\Wishlist\Test\Page\WishlistIndex;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Assert that success message appears on My Wish List page after adding products to wishlist.
 */
class AssertAddProductsToWishlistSuccessMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Success add message.
     */
    const SUCCESS_MESSAGE = "%s has been added to your wishlist. Click here to continue shopping.";

    /**
     * Assert that success message appears on My Wish List page after adding products to wishlist.
     *
     * @param WishlistIndex $wishlistIndex
     * @param AssertAddProductToWishlistSuccessMessage $addProductToWishlistSuccessMessage
     * @param InjectableFixture[] $products
     * @return void
     */
    public function processAssert(
        WishlistIndex $wishlistIndex,
        AssertAddProductToWishlistSuccessMessage $addProductToWishlistSuccessMessage,
        array $products
    ) {
        foreach ($products as $product) {
            $addProductToWishlistSuccessMessage->processAssert($wishlistIndex, $product);
        }
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Success message appears on My Wish List page after adding products to wishlist.';
    }
}
