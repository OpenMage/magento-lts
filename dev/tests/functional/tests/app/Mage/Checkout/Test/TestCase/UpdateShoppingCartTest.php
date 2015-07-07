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

namespace Mage\Checkout\Test\TestCase;

use Mage\Catalog\Test\Fixture\CatalogProductSimple;
use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Mage\Checkout\Test\Fixture\Cart;
use Mage\Checkout\Test\Page\CheckoutCart;
use Magento\Mtf\Client\BrowserInterface;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\TestCase\Injectable;

/**
 * Precondition:
 * 1. Simple product is created.
 * 2. Clear shopping cart.
 *
 * Steps:
 * 1. Go to frontend.
 * 2. Add product with qty from data set to shopping cart.
 * 3. Fill in all data according to data set.
 * 4. Click "Update Shopping Cart" button.
 * 5. Perform all assertion from dataset.
 *
 * @group Shopping_Cart_(CS)
 * @ZephyrId MPERF-7313
 */
class UpdateShoppingCartTest extends Injectable
{
    /**
     * Browser interface.
     *
     * @var BrowserInterface
     */
    protected $browser;

    /**
     * Fixture factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Page CatalogProductView.
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Page CheckoutCart.
     *
     * @var CheckoutCart
     */
    protected $checkoutCart;

    /**
     * Prepare test data.
     *
     * @param BrowserInterface $browser
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __prepare(BrowserInterface $browser, FixtureFactory $fixtureFactory)
    {
        $this->browser = $browser;
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Inject data.
     *
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @return void
     */
    public function __inject(CatalogProductView $catalogProductView, CheckoutCart $checkoutCart)
    {
        $this->catalogProductView = $catalogProductView;
        $this->checkoutCart = $checkoutCart;
    }

    /**
     * Update Shopping Cart.
     *
     * @param CatalogProductSimple $product
     * @param int $qty
     * @return array
     */
    public function test(CatalogProductSimple $product, $qty)
    {
        // Preconditions
        $product->persist();
        $this->checkoutCart->getCartBlock()->clearShoppingCart();

        // Steps
        $this->addProductToCart($product, $qty);
        $this->updateShoppingCart($product);

        $cart['data']['items'] = ['products' => [$product]];
        return ['cart' => $this->fixtureFactory->createByCode('cart', $cart)];
    }

    /**
     * Add product to cart.
     *
     * @param CatalogProductSimple $product
     * @param int $qty
     * @return void
     */
    protected function addProductToCart(CatalogProductSimple $product, $qty)
    {
        $this->browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $productView = $this->catalogProductView->getViewBlock();
        $productView->fillOptions($product);
        $productView->setQty($qty);
        $productView->clickAddToCart();
        $this->catalogProductView->getMessagesBlock()->waitSuccessMessage();
    }

    /**
     * Update shopping cart.
     *
     * @param CatalogProductSimple $product
     * @return void
     */
    protected function updateShoppingCart(CatalogProductSimple $product)
    {
        $qty = $product->getCheckoutData()['qty'];
        $this->checkoutCart->open();
        $this->checkoutCart->getCartBlock()->getCartItem($product)->setQty($qty);
        $this->checkoutCart->getCartBlock()->updateShoppingCart();
    }
}
