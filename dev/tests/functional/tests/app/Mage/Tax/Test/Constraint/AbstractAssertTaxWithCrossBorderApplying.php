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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Tax\Test\Constraint;

use Mage\Catalog\Test\Fixture\CatalogProductSimple;
use Mage\Catalog\Test\Page\Category\CatalogCategoryView;
use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Mage\Checkout\Test\Page\CheckoutCart;
use Mage\Cms\Test\Page\CmsIndex;
use Mage\Customer\Test\Fixture\Customer;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Mtf\Client\Browser;
use Mage\Catalog\Test\Block\Product\Price;

/**
 * Abstract class for implementing assert cross border applying.
 */
abstract class AbstractAssertTaxWithCrossBorderApplying extends AbstractConstraint
{
    /**
     * Cms index page.
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Catalog product page.
     *
     * @var CatalogCategoryView
     */
    protected $catalogCategoryView;

    /**
     * Catalog product page.
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Catalog product page.
     *
     * @var CheckoutCart
     */
    protected $checkoutCart;

    /**
     * Implementation assert.
     *
     * @param array $actualPrices
     * @return void
     */
    abstract protected function assert(array $actualPrices);

    /**
     * 1. Login with each customer and get product price on category, product and cart pages.
     * 2. Implementation assert.
     *
     * @param CatalogProductSimple $product
     * @param CmsIndex $cmsIndex
     * @param CatalogCategoryView $catalogCategoryView
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @param array $customers
     * @param Browser $browser
     * @return void
     */
    public function processAssert(
        CatalogProductSimple $product,
        CmsIndex $cmsIndex,
        CatalogCategoryView $catalogCategoryView,
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart,
        array $customers,
        Browser $browser
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->catalogCategoryView = $catalogCategoryView;
        $this->catalogProductView = $catalogProductView;
        $this->checkoutCart = $checkoutCart;
        $actualPrices = $this->getPricesForCustomers($product, $customers, $browser);
        $this->assert($actualPrices);
    }

    /**
     * Login with each provided customer and get product prices.
     *
     * @param CatalogProductSimple $product
     * @param array $customers
     * @param Browser $browser
     * @return array
     */
    protected function getPricesForCustomers(CatalogProductSimple $product, array $customers, Browser $browser)
    {
        $prices = [];
        foreach ($customers as $customer) {
            $this->loginCustomer($customer);
            $this->openCategory($product);
            $actualPrices = [];
            $actualPrices = $this->getCategoryPrice($product->getName(), $actualPrices);
            $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
            $actualPrices = $this->addToCart($product, $actualPrices);
            $actualPrices = $this->getCartPrice($product, $actualPrices);
            $prices[] = $actualPrices;
            $this->clearShoppingCart();
        }
        return $prices;
    }

    /**
     * Open product category.
     *
     * @param CatalogProductSimple $product
     * @return void
     */
    protected function openCategory(CatalogProductSimple $product)
    {
        $this->cmsIndex->open();
        $this->cmsIndex->getTopmenu()->selectCategory($product->getCategoryIds()[0]);
    }

    /**
     * Get prices on category page.
     *
     * @param string $productName
     * @param array $actualPrices
     * @return array
     */
    protected function getCategoryPrice($productName, array $actualPrices)
    {
        $priceBlock = $this->catalogCategoryView->getListProductBlock()->getProductPriceBlock($productName);
        $actualPrices['category_price_incl_tax'] = $this->getPrice($priceBlock, 'category_price_incl_tax');
        return $actualPrices;
    }

    /**
     * Get price.
     *
     * @param Price $block
     * @param string $type
     * @param string $currency
     * @return string|null
     */
    public function getPrice(Price $block, $type, $currency = '$')
    {
        $price = $block->getTypePriceElement($type);
        return $price->isVisible() ? trim($price->getText(), $currency) : null;
    }

    /**
     * Get price after fill product options, and add product to cart.
     *
     * @param CatalogProductSimple $product
     * @param array $actualPrices
     * @return array
     */
    protected function addToCart(CatalogProductSimple $product, array $actualPrices)
    {
        $this->catalogProductView->getViewBlock()->fillOptions($product);
        $priceBlock = $this->catalogProductView->getViewBlock()->getPriceBlock();
        $actualPrices['product_view_price_incl_tax'] = $this->getPrice($priceBlock, 'product_view_price_incl_tax');
        $this->catalogProductView->getViewBlock()->clickAddToCart();
        return $actualPrices;
    }

    /**
     * Get cart prices.
     *
     * @param CatalogProductSimple $product
     * @param array $actualPrices
     * @return array
     */
    protected function getCartPrice(CatalogProductSimple $product, array $actualPrices)
    {
        $actualPrices['cart_item_price_incl_tax'] = $this->checkoutCart->getCartBlock()->getCartItem($product)
            ->getCartItemTypePrice('cart_item_price_incl_tax');
        $actualPrices['cart_item_subtotal_incl_tax'] = $this->checkoutCart->getCartBlock()->getCartItem($product)
            ->getCartItemTypePrice('cart_item_subtotal_incl_tax');
        $actualPrices['grand_total'] = $this->checkoutCart->getTotalsBlock()->getData('grand_total_incl_tax');
        return $actualPrices;
    }

    /**
     * Login customer.
     *
     * @param Customer $customer
     * @return void
     */
    protected function loginCustomer(Customer $customer)
    {
        $this->objectManager->create(
            '\Mage\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
            ['customer' => $customer]
        )->run();
    }

    /**
     * Clear shopping cart.
     *
     * @return void
     */
    protected function clearShoppingCart()
    {
        $this->checkoutCart->open();
        $this->checkoutCart->getCartBlock()->clearShoppingCart();
    }
}
