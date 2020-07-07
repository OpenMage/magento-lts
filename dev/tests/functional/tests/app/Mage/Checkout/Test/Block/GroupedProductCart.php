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
