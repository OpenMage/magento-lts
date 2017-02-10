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

namespace Mage\Checkout\Test\TestCase;

use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Mage\Checkout\Test\Page\CheckoutCart;
use Magento\Mtf\Client\Browser;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\ObjectManager;
use Magento\Mtf\TestCase\Injectable;

/**
 * Preconditions
 * 1. Test products are created.
 *
 * Steps:
 * 1. Add product(s) to Shopping Cart.
 * 2. Click 'Remove item' button from Shopping Cart for each product(s).
 * 3. Perform all asserts.
 *
 * @group Shopping_Cart_(CS)
 * @ZephyrId MPERF-7659
 */
class DeleteProductsFromShoppingCartTest extends Injectable
{
    /**
     * Browser.
     *
     * @var Browser
     */
    protected $browser;

    /**
     * Fixture factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Catalog product view page.
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Checkout cart page.
     *
     * @var CheckoutCart
     */
    protected $cartPage;

    /**
     * Prepare test data.
     *
     * @param Browser$browser
     * @param FixtureFactory $fixtureFactory
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $cartPage
     * @return void
     */
    public function __prepare(
        Browser $browser,
        FixtureFactory $fixtureFactory,
        CatalogProductView $catalogProductView,
        CheckoutCart $cartPage
    ) {
        $this->browser = $browser;
        $this->fixtureFactory = $fixtureFactory;
        $this->catalogProductView = $catalogProductView;
        $this->cartPage = $cartPage;
    }

    /**
     * Run test add products to shopping cart.
     *
     * @param string $productsData
     * @return void
     */
    public function test($productsData)
    {
        // Preconditions
        $products = $this->prepareProducts($productsData);

        // Steps
        $this->addToCart($products);
        $this->removeProducts($products);
    }

    /**
     * Create products.
     *
     * @param string $productList
     * @return InjectableFixture[]
     */
    protected function prepareProducts($productList)
    {
        $createProductsStep = ObjectManager::getInstance()->create(
            'Mage\Catalog\Test\TestStep\CreateProductsStep',
            ['products' => $productList]
        );
        $result = $createProductsStep->run();

        return $result['products'];
    }

    /**
     * Add products to cart.
     *
     * @param array $products
     * @return void
     */
    protected function addToCart(array $products)
    {
        $addToCartStep = ObjectManager::getInstance()->create(
            'Mage\Checkout\Test\TestStep\AddProductsToTheCartStep',
            ['products' => $products]
        );
        $addToCartStep->run();
    }

    /**
     * Remove products form cart.
     *
     * @param array $products
     * @return void
     */
    protected function removeProducts(array $products)
    {
        $this->cartPage->open();
        foreach ($products as $product) {
            $this->cartPage->getCartBlock()->getCartItem($product)->removeItem();
        }
    }
}
