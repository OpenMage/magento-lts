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

namespace Mage\Checkout\Test\Block;

use Mage\Catalog\Test\Fixture\GroupedProduct;
use Magento\Mtf\Fixture\InjectableFixture;
use Mage\Checkout\Test\Block\Cart;
use Mage\Checkout\Test\Block\GroupedProductCart\CartItem;

/**
 * Shopping cart block for grouped product.
 */
class GroupedProductCart extends Cart
{
    /**
     * Get cart item block.
     *
     * @param InjectableFixture $product
     * @return CartItem
     */
    public function getCartItem(InjectableFixture $product)
    {
        return $this->blockFactory->create(
            'Mage\Checkout\Test\Block\GroupedProductCart\CartItem',
            [
                'element' => $this->_rootElement,
                'config' => [
                    'associated_cart_items' => $this->getAssociatedItems($product),
                ]
            ]
        );
    }

    /**
     * Get associated items for grouped product.
     *
     * @param InjectableFixture $product
     * @return array
     */
    protected function getAssociatedItems(InjectableFixture $product)
    {
        $cartItems = [];

        /** @var GroupedProduct $product */
        $associatedProducts = $product->getDataFieldConfig('associated')['source']->getProducts();
        foreach ($associatedProducts as $product) {
            $cartItems[$product->getSku()] = parent::getCartItem($product);
        }

        return $cartItems;
    }
}
