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

use Mage\Cms\Test\Page\CmsIndex;
use Mage\Catalog\Test\Page\Category\CatalogCategoryView;
use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Mage\Checkout\Test\Page\CheckoutCart;
use Mage\Customer\Test\Fixture\Address;
use Magento\Mtf\Fixture\InjectableFixture;
use Mage\Catalog\Test\Block\Product\Price;
use Mage\Adminhtml\Test\Page\Adminhtml\Cache;

/**
 * Checks that prices excl tax on category, product and cart pages are equal to specified in dataset.
 */
abstract class AbstractAssertTaxRuleIsAppliedToAllPrices extends AbstractAssertTax
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
     * Verify fields for category page.
     *
     * @var array
     */
    protected $categoryPrices = [
        'category_price_excl_tax',
        'category_price_incl_tax'
    ];

    /**
     * Verify fields for product page.
     *
     * @var array
     */
    protected $productPrices = [
        'product_view_price_excl_tax',
        'product_view_price_incl_tax'
    ];

    /**
     * Assert that specified prices are actual on category, product and cart pages.
     *
     * @param InjectableFixture $product
     * @param CmsIndex $cmsIndex
     * @param CatalogCategoryView $catalogCategoryView
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @param Address $address
     * @param Cache $cachePage
     * @param array $prices
     * @param array $arguments
     * @return void
     */
    public function processAssert(
        InjectableFixture $product,
        CmsIndex $cmsIndex,
        CatalogCategoryView $catalogCategoryView,
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart,
        Address $address,
        Cache $cachePage,
        array $prices,
        array $arguments
    ) {
        $cachePage->open()->getPageActions()->flushCacheStorage();
        $this->cmsIndex = $cmsIndex;
        $this->catalogCategoryView = $catalogCategoryView;
        $this->catalogProductView = $catalogProductView;
        $this->checkoutCart = $checkoutCart;
        $prices = $this->prepareVerifyFields($prices);

        //Assertion steps
        $productName = $product->getName();
        $this->cmsIndex->open()->getTopmenu()->selectCategory($product->getCategoryIds()[0]);
        $actualPrices = $this->getCategoryPrices($product);
        $catalogCategoryView->getListProductBlock()->openProductViewPage($productName);
        $this->fillCheckoutData($product);
        $actualPrices = array_merge($actualPrices, $this->getProductPagePrices());
        $catalogProductView->getViewBlock()->clickAddToCart();
        if (isset($arguments['shipping'])) {
            $this->fillEstimateBlock($address, $arguments['shipping']);
        }
        $actualPrices = array_merge($actualPrices, $this->getCartPrices($product), $this->getTotals($actualPrices));

        //Prices verification
        $error = $this->verifyData($prices, $actualPrices);
        \PHPUnit_Framework_Assert::assertTrue(empty($error), $error);
    }

    /**
     * Unset category and product page expected prices.
     *
     * @param array $prices
     * @return array
     */
    protected function preparePrices(array $prices)
    {
        $generalPrices = parent::preparePrices($prices);
        $categoryPrices = array_intersect_key($prices, array_flip($this->categoryPrices));
        $productPrices = array_intersect_key($prices, array_flip($this->productPrices));

        return array_merge($generalPrices, $categoryPrices, $productPrices);
    }

    /**
     * Get prices on category page.
     *
     * @param InjectableFixture $product
     * @return array
     */
    protected function getCategoryPrices(InjectableFixture $product)
    {
        $result = [];
        $priceBlock = $this->catalogCategoryView->getListProductBlock()->getProductPriceBlock($product->getName());
        foreach ($this->categoryPrices as $item) {
            $result[$item] = $this->getPrice($priceBlock, $item);
        }

        return $result;
    }

    /**
     * Get product view prices.
     *
     * @return array
     */
    protected function getProductPagePrices()
    {
        $result = [];
        $viewBlock = $this->catalogProductView->getViewBlock()->getPriceBlock();
        foreach ($this->productPrices as $item) {
            $result[$item] = (count($this->categoryPrices) == 1)
                ? $this->getPrice($viewBlock, 'special_price')
                : $this->getPrice($viewBlock, $item);
        }
        return $result;
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
     * Get totals.
     *
     * @return array
     */
    protected function getTotals()
    {
        $totalsBlock = $this->checkoutCart->getTotalsBlock();
        return $this->getTypeBlockData($totalsBlock);
    }

    /**
     * Fill checkout data.
     *
     * @param InjectableFixture $product
     * @return void
     */
    protected function fillCheckoutData(InjectableFixture $product)
    {
        $qty = $product->getCheckoutData()['qty'];
        $this->catalogProductView->getViewBlock()->fillOptions($product);
        $this->catalogProductView->getViewBlock()->setQty($qty);
    }

    /**
     * Get cart prices.
     *
     * @param InjectableFixture $product
     * @return array
     */
    protected function getCartPrices(InjectableFixture $product)
    {
        $cartItemData = $this->checkoutCart->getCartBlock()->getCartItem($product);
        return $this->getTypePrices($cartItemData);
    }

    /**
     * Fill estimate block.
     *
     * @param Address $address
     * @param array $shipping
     * @return void
     */
    protected function fillEstimateBlock(Address $address, array $shipping)
    {
        $this->checkoutCart->getShippingBlock()->fillEstimateShippingAndTax($address);
        $this->checkoutCart->getShippingBlock()->selectShippingMethod($shipping);
    }
}
