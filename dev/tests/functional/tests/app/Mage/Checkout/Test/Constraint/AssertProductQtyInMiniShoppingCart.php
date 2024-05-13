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

namespace Mage\Checkout\Test\Constraint;

use Mage\Catalog\Test\Fixture\CatalogProductSimple;
use Mage\Checkout\Test\Fixture\Cart;
use Mage\Checkout\Test\Fixture\Cart\Items;
use Mage\Cms\Test\Page\CmsIndex;
use Magento\Mtf\Constraint\AbstractAssertForm;
use Magento\Mtf\Fixture\FixtureInterface;

/**
 * Assert that product quantity in the mini shopping cart is equals to expected quantity from data set.
 */
class AssertProductQtyInMiniShoppingCart extends AbstractAssertForm
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that product quantity in the mini shopping cart is equals to expected quantity from data set.
     *
     * @param CmsIndex $cmsIndex
     * @param Cart $cart
     * @return void
     */
    public function processAssert(CmsIndex $cmsIndex, Cart $cart)
    {
        $cmsIndex->open()->getTopLinksBlock()->openMiniCart();
        /** @var Items $sourceProducts */
        $products = $cart->getDataFieldConfig('items')['source']->getProducts();
        $items = $cart->getItems();
        $productsData = [];
        $miniCartData = [];

        foreach ($items as $key => $item) {
            /** @var CatalogProductSimple $product */
            $product = $products[$key];
            $productName = $product->getName();
            /** @var FixtureInterface $item */
            $checkoutItem = $item->getData();

            $productsData[$productName] = ['qty' => $checkoutItem['qty']];
            $miniCartData[$productName] = ['qty' => $cmsIndex->getCartSidebarBlock()->getCartItem($product)->getQty()];
        }

        $error = $this->verifyData($productsData, $miniCartData, true);
        \PHPUnit_Framework_Assert::assertEmpty($error, $error);
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Quantity in the mini shopping cart equals to expected quantity from data set.';
    }
}
