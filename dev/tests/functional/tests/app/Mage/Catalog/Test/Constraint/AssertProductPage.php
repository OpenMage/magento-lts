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

namespace Mage\Catalog\Test\Constraint;

use Magento\Mtf\ObjectManager;
use Magento\Mtf\Client\Browser;
use Magento\Mtf\Constraint\AbstractAssertForm;
use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Mage\Catalog\Test\Block\Product\View;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Assert that displayed product data on product page(front-end) equals passed from fixture.
 */
class AssertProductPage extends AbstractAssertForm
{
    /**
     * Product view block on frontend page.
     *
     * @var View
     */
    protected $productView;

    /**
     * Catalog product view page.
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Product fixture.
     *
     * @var InjectableFixture
     */
    protected $product;

    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert that displayed product data on product page(front-end) equals passed from fixture:
     * 1. Product Name
     * 2. Price
     * 3. Special price
     * 4. SKU
     * 5. Description
     * 6. Short Description
     *
     * @param Browser $browser
     * @param CatalogProductView $catalogProductView
     * @param InjectableFixture $product
     * @return void
     */
    public function processAssert(Browser $browser, CatalogProductView $catalogProductView, InjectableFixture $product)
    {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');

        $this->product = $product;
        $this->productView = $catalogProductView->getViewBlock();
        $this->catalogProductView = $catalogProductView;

        $errors = $this->verify();
        \PHPUnit_Framework_Assert::assertEmpty(
            $errors,
            "\nFound the following errors:\n" . implode(" \n", $errors)
        );
    }

    /**
     * Verify displayed product data on product page(front-end) equals passed from fixture.
     *
     * @return array
     */
    protected function verify()
    {
        $errors = [];

        $errors[] = $this->verifyName();
        $errors[] = $this->verifyPrice();
        $errors[] = $this->verifyDescription();
        $errors[] = $this->verifyShortDescription();

        return array_filter($errors);
    }

    /**
     * Verify displayed product name on product page(front-end) equals passed from fixture.
     *
     * @return string|null
     */
    protected function verifyName()
    {
        $fixtureProductName = strtoupper($this->product->getName());
        $formProductName = $this->productView->getProductName();

        if ($fixtureProductName == $formProductName) {
            return null;
        }
        return "Displayed product name on product page(front-end) not equals passed from fixture. "
        . "Actual: {$formProductName}, expected: {$fixtureProductName}.";
    }

    /**
     * Verify displayed product price on product page(front-end) equals passed from fixture.
     *
     * @return string|null
     */
    protected function verifyPrice()
    {
        if ($this->product->hasData('price') == false) {
            return null;
        }
        $fixtureProductPrice = number_format($this->product->getPrice(), 2);
        $formProductPrice = $this->productView->getPriceBlock()->getRegularPrice();
        if ($fixtureProductPrice == $formProductPrice) {
            return null;
        }
        $error = "Displayed product price on product page(front-end) not equals passed from fixture. "
            . "Actual: {$formProductPrice}, expected: {$fixtureProductPrice}.";

        $verifySpecialPriceResult = $this->verifySpecialPrice();
        if ($verifySpecialPriceResult !== null) {
            $error .= $verifySpecialPriceResult;
        }

        return $error;
    }

    /**
     * Verify displayed product special price on product page(front-end) equals passed from fixture.
     *
     * @return string|null
     */
    protected function verifySpecialPrice()
    {
        $fixtureProductSpecialPrice = $this->product->getSpecialPrice();
        if (!$fixtureProductSpecialPrice) {
            return null;
        }

        $fixtureProductSpecialPrice = number_format($fixtureProductSpecialPrice, 2);
        $formProductSpecialPrice = $this->productView->getPriceBlock()->getSpecialPrice();
        if ($fixtureProductSpecialPrice == $formProductSpecialPrice) {
            return null;
        }
        return "\nDisplayed product special price on product page(front-end) not equals passed from fixture. "
            . "Actual: {$formProductSpecialPrice}, expected: {$fixtureProductSpecialPrice}.";
    }

    /**
     * Verify displayed product description on product page(front-end) equals passed from fixture.
     *
     * @return string|null
     */
    protected function verifyDescription()
    {
        $fixtureProductDescription = $this->product->getDescription();
        $formProductDescription = $this->productView->getProductDescription();

        if ($fixtureProductDescription == $formProductDescription) {
            return null;
        }
        return "Displayed product description on product page(front-end) not equals passed from fixture. "
            . "Actual: {$formProductDescription}, expected: {$fixtureProductDescription}.";
    }

    /**
     * Verify displayed product short description on product page(front-end) equals passed from fixture.
     *
     * @return string|null
     */
    protected function verifyShortDescription()
    {
        $fixtureProductShortDescription = $this->product->getShortDescription();
        $formProductShortDescription = $this->productView->getProductShortDescription();

        if ($fixtureProductShortDescription == $formProductShortDescription) {
            return null;
        }
        return "Displayed product short description on product page(front-end) not equals passed from fixture. "
            . "Actual: {$formProductShortDescription}, expected: {$fixtureProductShortDescription}.";
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product on product view page is correct.';
    }
}
