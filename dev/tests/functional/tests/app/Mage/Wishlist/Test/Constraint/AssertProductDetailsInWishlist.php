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

namespace Mage\Wishlist\Test\Constraint;

use Mage\Checkout\Test\Fixture\Cart;
use Mage\Cms\Test\Page\CmsIndex;
use Mage\Wishlist\Test\Page\WishlistIndex;
use Magento\Mtf\Constraint\AbstractAssertForm;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Assert that the correct option details are displayed on the "View Details" tool tip.
 */
class AssertProductDetailsInWishlist extends AbstractAssertForm
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that the correct option details are displayed on the "View Details" tool tip.
     *
     * @param CmsIndex $cmsIndex
     * @param WishlistIndex $wishlistIndex
     * @param InjectableFixture $product
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        WishlistIndex $wishlistIndex,
        InjectableFixture $product,
        FixtureFactory $fixtureFactory
    ) {
        $cmsIndex->getTopLinksBlock()->openAccount();
        $cmsIndex->getLinksBlock()->openLink("My Wishlist");
        $actualOptions = $wishlistIndex->getItemsBlock()->getItemProductBlock($product)->getOptions();
        $cartFixture = $fixtureFactory->createByCode('cart', ['data' => ['items' => ['products' => [$product]]]]);
        $expectedOptions = $this->prepareOptions($cartFixture);

        $errors = $this->verifyData($expectedOptions, $this->sortDataByPath($actualOptions, '::title'));
        \PHPUnit_Framework_Assert::assertEmpty($errors, $errors);
    }

    /**
     * Prepare options.
     *
     * @param Cart $cart
     * @return array
     */
    protected function prepareOptions(Cart $cart)
    {
        $data = [];
        $options = $cart->getItems()[0]->getData()['options'];
        foreach ($options as $key => $option) {
            foreach ($option as $index => $value){
                $data[$key][$index] = strtoupper($value);
            }
        }

        return  $this->sortDataByPath($data, '::title');
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return "Expected product options are equal to actual.";
    }
}
