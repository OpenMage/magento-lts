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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Checkout\Test\Block;

use Mage\PayPal\Test\Block\Express\Shortcut;
use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Fixture\InjectableFixture;
use Mage\Checkout\Test\Block\Cart\CartItem;
use Mage\Checkout\Test\Block\Onepage\Link;

/**
 * Shopping cart block.
 */
class Cart extends Block
{
    /**
     * Path for cart item class.
     *
     * @var string
     */
    protected $cartItemClass = 'Mage\Checkout\Test\Block\Cart\CartItem';

    /**
     * Selector for cart item block.
     *
     * @var string
     */
    protected $cartItemByProductName = '//tr[*[@class="product-cart-info"] and (.//a[text()="%s"])]';

    /**
     * Empty cart block selector.
     *
     * @var string
     */
    protected $emptyShoppingCart = '.cart-empty';

    /**
     * Clear shopping cart button selector.
     *
     * @var string
     */
    protected $clearShoppingCart = '#empty_cart_button';

    /**
     * Proceed To Checkout Button.
     *
     * @var string
     */
    protected $proceedToCheckoutButton = '.btn-proceed-checkout';

    /**
     * Pay Pal express checkout block selector.
     *
     * @var string
     */
    protected $payPalExpressCheckout = '.title-buttons [data-action="checkout-form-submit"]';

    /**
     * 'Update Shopping Cart' button.
     *
     * @var string
     */
    protected $updateShoppingCart = '.button2.btn-update:not([style="visibility:hidden;"])';

    /**
     * Checkout with multi shipping link selector.
     *
     * @var string
     */
    protected $checkoutWithMultiAddress = '.method-checkout-cart-methods-multishipping a';

    /**
     * Get cart item block.
     *
     * @param InjectableFixture $product
     * @return CartItem
     */
    public function getCartItem(InjectableFixture $product)
    {
        $dataConfig = $product->getDataConfig();
        $typeId = isset($dataConfig['type_id']) ? $dataConfig['type_id'] : null;
        $cartItem = null;

        if ($this->hasRender($typeId)) {
            $cartItem = $this->callRender($typeId, 'getCartItem', ['product' => $product]);
        } else {
            $cartItemBlock = $this->_rootElement->find(
                sprintf($this->cartItemByProductName, $product->getName()),
                Locator::SELECTOR_XPATH
            );
            $cartItem = $this->blockFactory->create(
                $this->cartItemClass,
                ['element' => $cartItemBlock]
            );
        }

        return $cartItem;
    }

    /**
     * Check if a product has been successfully added to the cart.
     *
     * @param InjectableFixture $product
     * @return boolean
     */
    public function isProductInShoppingCart(InjectableFixture $product)
    {
        return $this->getCartItem($product)->isVisible();
    }

    /**
     * Clear shopping cart.
     *
     * @return void
     */
    public function clearShoppingCart()
    {
        if (!$this->_rootElement->find($this->emptyShoppingCart)->isVisible()){
            $clearShoppingCart = $this->_rootElement->find($this->clearShoppingCart);
            if ($clearShoppingCart->isVisible()) {
                $clearShoppingCart->click();
            }
        }
    }

    /**
     * Get Proceed to checkout block.
     *
     * @return Link
     */
    public function getProceedToCheckoutBlock()
    {
        return $this->blockFactory->create(
            'Mage\Checkout\Test\Block\Onepage\Link',
            ['element' => $this->_rootElement->find($this->proceedToCheckoutButton)]
        );
    }

    /**
     * Get express shortcut block.
     *
     * @return Shortcut
     */
    public function getExpressShortcutBlock()
    {
        return $this->blockFactory->create(
            'Mage\Paypal\Test\Block\Express\Shortcut',
            ['element' => $this->_rootElement->find($this->payPalExpressCheckout)]
        );
    }

    /**
     * Update shopping cart.
     *
     * @return void
     */
    public function updateShoppingCart()
    {
        $this->_rootElement->find($this->updateShoppingCart)->click();
    }

    /**
     * Click on 'Checkout with Multiple Addresses' link.
     *
     * @return void
     */
    public function clickCheckoutWithMultiAddress()
    {
        $this->_rootElement->find($this->checkoutWithMultiAddress)->click();
    }

    /**
     * Check that empty cart block is visible.
     *
     * @return bool
     */
    public function emptyCartBlockIsVisible()
    {
        return $this->_rootElement->find($this->emptyShoppingCart)->isVisible();
    }
}
