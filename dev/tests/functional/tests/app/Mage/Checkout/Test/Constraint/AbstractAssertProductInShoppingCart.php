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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Checkout\Test\Constraint;

use Mage\Catalog\Test\Fixture\CatalogProductSimple;
use Mage\Checkout\Test\Fixture\Cart;
use Mage\Checkout\Test\Fixture\Cart\Items;
use Mage\Checkout\Test\Page\CheckoutCart;
use Magento\Mtf\Constraint\AbstractAssertForm;
use Magento\Mtf\Fixture\FixtureInterface;
use Mage\Checkout\Test\Block\Cart\CartItem;

/**
 * Abstract assert for check product in ShoppingCart.
 */
abstract class AbstractAssertProductInShoppingCart extends AbstractAssertForm
{
    /**
     * Data type.
     *
     * @var string
     */
    protected $dataType = '';

    /**
     * Assert that $dataType in the shopping cart equals to expected $dataType from data set.
     *
     * @param CheckoutCart $checkoutCart
     * @param Cart $cart
     * @param array $verifyData [optional]
     * @return void
     */
    public function processAssert(CheckoutCart $checkoutCart, Cart $cart, array $verifyData = [])
    {
        $checkoutCart->open();
        /** @var Items $sourceProducts */
        $sourceProducts = $cart->getDataFieldConfig('items')['source'];
        $products = $sourceProducts->getProducts();
        $items = $cart->getItems();
        $productsData = [];
        $cartData = [];

        foreach ($items as $key => $item) {
            /** @var CatalogProductSimple $product */
            $product = $products[$key];
            $data = isset($verifyData[$key]) ? $verifyData[$key] : [];
            $productName = $product->getName();
            /** @var FixtureInterface $item */
            $checkoutItem = $item->getData();
            $cartItem = $checkoutCart->getCartBlock()->getCartItem($product);

            $productsData[$productName] = $this->getProductData($checkoutItem, $data);
            $cartData[$productName] = $this->getCartData($cartItem);
        }

        $error = $this->verifyContainsData($productsData, $cartData);
        \PHPUnit_Framework_Assert::assertEmpty($error, $error);
    }

    /**
     * Verify product data.
     *
     * @param array $productsData
     * @param array $cartData
     * @return array|string
     */
    protected function verifyContainsData(array $productsData, array $cartData)
    {
        return $this->verifyData($productsData, $cartData, true);
    }

    /**
     * Get cart data.
     *
     * @param CartItem $cartItem
     * @return array
     */
    protected function getCartData(CartItem $cartItem)
    {
        return [$this->dataType => $cartItem->getCartItemTypePrice($this->dataType)];
    }

    /**
     * Get product data.
     *
     * @param array $checkoutItem
     * @param array $verifyData
     * @return array
     */
    protected function getProductData(array $checkoutItem, array $verifyData)
    {
        return [
            $this->dataType => isset($verifyData[$this->dataType])
                    ? $verifyData[$this->dataType]
                    : $checkoutItem[$this->dataType]
        ];
    }
}
