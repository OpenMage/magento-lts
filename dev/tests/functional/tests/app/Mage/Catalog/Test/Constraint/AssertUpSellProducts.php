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

namespace Mage\Catalog\Test\Constraint;

use Mage\Catalog\Test\Fixture\CatalogProductSimple;
use Mage\Catalog\Test\Fixture\ConfigurableProduct;
use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Mage\Checkout\Test\Page\CheckoutCart;
use Magento\Mtf\Client\Browser;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Assert that products are displayed in upSell section.
 */
class AssertUpSellProducts extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'middle';
    /* end tags */

    /**
     * Assert that products are displayed in upSell section.
     *
     * @param Browser $browser
     * @param CheckoutCart $checkoutCart
     * @param CatalogProductView $catalogProductView
     * @param array $productsData
     * @param array $upSellProductsData
     * @return void
     */
    public function processAssert(
        Browser $browser,
        CheckoutCart $checkoutCart,
        CatalogProductView $catalogProductView,
        array $productsData,
        array $upSellProductsData
    ) {
        $checkoutCart->open()->getCartBlock()->clearShoppingCart();

        $index = $upSellProductsData['firstProduct']['productIndex'];
        $productCheck = $productsData[$index]['product'];
        $upSellProducts = $productsData[$index]['upSellProducts']['up_sell_products']['value'];
        $browser->open($_ENV['app_frontend_url'] . $productCheck->getUrlKey() . '.html');
        $this->assertUpSellSection($catalogProductView, $upSellProducts);

        $index = $upSellProductsData['secondProduct']['productIndex'];
        $productCheck = $productsData[$index]['product'];
        $upSellProducts = $productsData[$index]['upSellProducts']['up_sell_products']['value'];
        $this->openUpSellProduct($catalogProductView, $productCheck);
        unset($upSellProducts[$upSellProductsData['firstProduct']['productIndex']]);
        $this->assertUpSellSection($catalogProductView, $upSellProducts);

        $index = $upSellProductsData['thirdProduct']['productIndex'];
        $productCheck = $productsData[$index]['product'];
        $this->openUpSellProduct($catalogProductView, $productCheck);
        $this->assertUpSellSectionAbsent($catalogProductView);
    }

    /**
     * Open UpSell product.
     *
     * @param CatalogProductView $catalogProductView
     * @param InjectableFixture $productCheck
     * @return void
     */
    protected function openUpSellProduct(CatalogProductView $catalogProductView, InjectableFixture $productCheck)
    {
        $catalogProductView->getUpsellBlock()->getItemBlock($productCheck)->openProduct();
    }

    /**
     * Check products on upSell section.
     *
     * @param CatalogProductView $catalogProductView
     * @param array $upSellProducts
     * @return void
     */
    protected function assertUpSellSection(CatalogProductView $catalogProductView, array $upSellProducts)
    {
        $errors = [];
        $upSellBlock = $catalogProductView->getUpsellBlock();
        foreach ($upSellProducts as $upSellProduct) {
            if (!$upSellBlock->getItemBlock($upSellProduct)->isVisible()) {
                $errors[] = "Product {$upSellProduct->getName()} is absent in up-sell section.";
            }
        }

        \PHPUnit_Framework_Assert::assertEmpty($errors, implode("\n", $errors));
    }

    /**
     * Check that upSell section is absent.
     *
     * @param CatalogProductView $catalogProductView
     * @return void
     */
    protected function assertUpSellSectionAbsent(CatalogProductView $catalogProductView)
    {
        \PHPUnit_Framework_Assert::assertFalse($catalogProductView->getUpsellBlock()->isVisible());
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Products are displayed in upSell section.';
    }
}
