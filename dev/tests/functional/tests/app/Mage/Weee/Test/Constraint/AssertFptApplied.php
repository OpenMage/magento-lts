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

namespace Mage\Weee\Test\Constraint;

use Mage\Adminhtml\Test\Page\Adminhtml\Cache;
use Mage\Catalog\Test\Page\Category\CatalogCategoryView;
use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Mage\Checkout\Test\Page\CheckoutCart;
use Mage\Cms\Test\Page\CmsIndex;
use Magento\Mtf\Constraint\AbstractAssertForm;
use Magento\Mtf\Fixture\InjectableFixture;
use Mage\Catalog\Test\Block\Product\Price;

/**
 * Checks that prices with fpt on category, product and cart pages are equal to specified in dataset.
 */
class AssertFptApplied extends AbstractAssertForm
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
     * @var catalogCategoryView
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
     * Fpt label.
     *
     * @var string
     */
    protected $fptLabel;

    /**
     * Product fixture.
     *
     * @var InjectableFixture
     */
    protected $product;

    /**
     * Expected prices.
     *
     * @var array
     */
    protected $expectedPrices;

    /**
     * Assert that specified prices with fpt are actual on category, product and cart pages.
     *
     * @param InjectableFixture $product
     * @param CmsIndex $cmsIndex
     * @param CatalogCategoryView $catalogCategoryView
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @param Cache $cache
     * @param array $prices
     * @return void
     */
    public function processAssert(
        InjectableFixture $product,
        CmsIndex $cmsIndex,
        CatalogCategoryView $catalogCategoryView,
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart,
        Cache $cache,
        array $prices
    ) {
        $cache->open()->getPageActions()->flushCacheStorage();
        $this->product = $product;
        $this->expectedPrices = $prices;
        $this->cmsIndex = $cmsIndex;
        $this->catalogCategoryView = $catalogCategoryView;
        $this->catalogProductView = $catalogProductView;
        $this->checkoutCart = $checkoutCart;
        $this->fptLabel = $this->getFptLabel($product);
        $this->clearShoppingCart();

        $error = $this->verifyData($prices, $this->getPrices());
        \PHPUnit_Framework_Assert::assertEmpty($error, $error);
    }

    /**
     * Get fpt label.
     *
     * @param InjectableFixture $product
     * @return string
     */
    protected function getFptLabel(InjectableFixture $product)
    {
        return $product->getDataFieldConfig('attribute_set_id')['source']
            ->getAttributeSet()->getDataFieldConfig('assigned_attributes')['source']
            ->getAttributes()[0]->getFrontendLabel();
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

    /**
     * Get prices with fpt on category, product and cart pages.
     *
     * @return array
     */
    protected function getPrices()
    {
        $actualPrices = [];
        foreach ($this->expectedPrices as $priceType => $prices) {
            $actualPrices[$priceType] = $this->{'get' . ucfirst($priceType) . 'Price'}();
        }

        return $actualPrices;
    }

    /**
     * Get prices on category page.
     *
     * @return array
     */
    protected function getCategoryPrice()
    {
        $prices = [];
        $this->cmsIndex->open();
        $this->cmsIndex->getTopmenu()->selectCategory($this->product->getCategoryIds()[0]);
        $priceBlock = $this->catalogCategoryView->getWeeeListProductBlock()->getProductItem($this->product)
            ->getPriceBlock();
        foreach ($this->expectedPrices['category'] as $key => $type) {
            $prices[$key] = $this->getPrice($priceBlock, $key);
        }

        return $prices;
    }

    /**
     * Get price.
     *
     * @param Price $block
     * @param string $type
     * @param string $currency
     * @return string|null
     */
    protected function getPrice(Price $block, $type, $currency = '$')
    {
        $price = $block->getTypePriceElement($type);
        return $price->isVisible() ? trim($price->getText(), $currency) : null;
    }

    /**
     * Fill options, get price and add to cart.
     *
     * @return array
     */
    protected function getProductPrice()
    {
        $prices = [];
        $this->catalogCategoryView->getListProductBlock()->openProductViewPage($this->product->getName());
        $priceBlock = $this->catalogProductView->getWeeeViewBlock()->getPriceBlock();
        $this->catalogProductView->getViewBlock()->fillOptions($this->product);
        foreach ($this->expectedPrices['product'] as $key => $type) {
            $prices[$key] = $this->getPrice($priceBlock, $key);
        }

        return $prices;
    }

    /**
     * Get cart prices.
     *
     * @return array
     */
    protected function getCartItemPrice()
    {
        $prices = [];
        $this->catalogProductView->getViewBlock()->clickAddToCart();
        $productWeeeItem = $this->checkoutCart->getWeeeCartBlock()->getCartItem($this->product);
        $productWeeeItem->openFpt();
        foreach ($this->expectedPrices['cartItem'] as $key => $type) {
            $prices[$key] = $productWeeeItem->getCartItemTypePrice($key);
        }

        return $prices;
    }

    /**
     * Get grand total.
     *
     * @return array
     */
    protected function getTotalPrice()
    {
        $prices = [];
        foreach ($this->expectedPrices['total'] as $key => $type) {
            $prices[$key] = $this->checkoutCart->getTotalsBlock()->getData($key);
        }

        return $prices;
    }

    /**
     * Text of fpt is applied.
     *
     * @return string
     */
    public function toString()
    {
        return 'FPT is applied to product.';
    }
}
