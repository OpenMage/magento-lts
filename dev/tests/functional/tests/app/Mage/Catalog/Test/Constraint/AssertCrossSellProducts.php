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

namespace Mage\Catalog\Test\Constraint;

use Mage\Catalog\Test\Fixture\CatalogProductSimple;
use Mage\Catalog\Test\Fixture\ConfigurableProduct;
use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Mage\Checkout\Test\Page\CheckoutCart;
use Magento\Mtf\Client\Browser;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Assert that products are displayed in crossSell section.
 */
class AssertCrossSellProducts extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'middle';
    /* end tags */

    /**
     * Assert that products are displayed in crossSell section.
     *
     * @param Browser $browser
     * @param CheckoutCart $checkoutCart
     * @param CatalogProductView $catalogProductView
     * @param array $productsData
     * @param array $crossSellProductsData
     * @return void
     */
    public function processAssert(
        Browser $browser,
        CheckoutCart $checkoutCart,
        CatalogProductView $catalogProductView,
        array $productsData,
        array $crossSellProductsData

    ) {
        $checkoutCart->open()->getCartBlock()->clearShoppingCart();

        $index = $crossSellProductsData['firstProduct']['productIndex'];
        $productCheck = $productsData[$index]['product'];
        $crossSellProducts = $productsData[$index]['crossSellProducts']['cross_sell_products']['value'];
        $browser->open($_ENV['app_frontend_url'] . $productCheck->getUrlKey() . '.html');
        $catalogProductView->getViewBlock()->addToCart($productCheck);
        $this->assertCrossSellSection($checkoutCart, $crossSellProducts);

        $index = $crossSellProductsData['secondProduct']['productIndex'];
        $productCheck = $productsData[$index]['product'];
        $crossSellProducts = $productsData[$index]['crossSellProducts']['cross_sell_products']['value'];
        $this->addToCartFromCrossSell($catalogProductView, $checkoutCart, $productCheck);
        unset($crossSellProducts[$crossSellProductsData['firstProduct']['productIndex']]);
        $this->assertCrossSellSection($checkoutCart, $crossSellProducts);

        $index = $crossSellProductsData['thirdProduct']['productIndex'];
        $productCheck = $productsData[$index]['product'];
        $this->addToCartFromCrossSell($catalogProductView, $checkoutCart, $productCheck);
        $this->assertCrossSellSectionAbsent($checkoutCart);
    }

    /**
     * Add to cart.
     *
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @param InjectableFixture $productCheck
     * @return void
     */
    protected function addToCartFromCrossSell(
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart,
        InjectableFixture $productCheck
    ) {
        $crossSellBlock = $checkoutCart->getCrosssellBlock();
        $crossSellBlock->getItemBlock($productCheck)->addToCart();
        if ($catalogProductView->getViewBlock()->isVisible()) {
            $catalogProductView->getViewBlock()->addToCart($productCheck);
        }
    }

    /**
     * Check products on crossSell section.
     *
     * @param CheckoutCart $checkoutCart
     * @param array $crossSellProducts
     * @return void
     */
    protected function assertCrossSellSection(CheckoutCart $checkoutCart, array $crossSellProducts)
    {
        $errors = [];
        $crossSellBlock = $checkoutCart->getCrosssellBlock();
        foreach ($crossSellProducts as $crossSellProduct) {
            if (!$crossSellBlock->getItemBlock($crossSellProduct)->isVisible()) {
                $errors[] = "Product {$crossSellProduct->getName()} is absent in cross-sell section.";
            }
        }

        \PHPUnit_Framework_Assert::assertEmpty($errors, implode("\n", $errors));
    }

    /**
     * Check that crossSell section is absent.
     *
     * @param CheckoutCart $checkoutCart
     * @return void
     */
    protected function assertCrossSellSectionAbsent(CheckoutCart $checkoutCart)
    {
        \PHPUnit_Framework_Assert::assertFalse($checkoutCart->getCrosssellBlock()->isVisible());
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Products are displayed in crossSell section.';
    }
}
