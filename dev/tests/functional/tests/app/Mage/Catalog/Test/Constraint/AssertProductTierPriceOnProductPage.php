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

use Magento\Mtf\Client\Browser;
use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Catalog\Test\Block\Product\View;
use Mage\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Assert that displayed tier price on product page equals passed from fixture.
 */
class AssertProductTierPriceOnProductPage extends AbstractConstraint implements AssertPriceOnProductPageInterface
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Error message.
     *
     * @var string
     */
    protected $errorMessage = 'Product tier price on product page is not correct.';

    /**
     * Format price.
     *
     * @var int
     */
    protected $priceFormat = 2;

    /**
     * Regular expression pattern for parse tier price.
     *
     * @var string
     */
    protected $pattern = '#^[^\d]+(\d+)[^\d]+(\d+(?:(?:,\d+)*)+(?:.\d+)*).*save (\d+)%#i';

    /**
     * Count matches.
     *
     * @var int
     */
    protected $matchCount = 3;

    /**
     * Fields for verify.
     *
     * @var array
     */
    protected $verifyFields = [
        'price_qty',
        'price',
        'percent'
    ];

    /**
     * Assertion that tier prices are displayed correctly.
     *
     * @param Browser $browser
     * @param CatalogProductView $catalogProductView
     * @param InjectableFixture $product
     * @return void
     */
    public function processAssert(Browser $browser, CatalogProductView $catalogProductView, InjectableFixture $product)
    {
        //Open product view page
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');

        //Process assertions
        $this->assertPrice($product, $catalogProductView->getViewBlock());
    }

    /**
     * Set error message for tier price assert.
     *
     * @param string $errorMessage
     * @return void
     */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * Verify product tier price on product view page.
     *
     * @param InjectableFixture $product
     * @param View $productViewBlock
     * @return void
     */
    public function assertPrice(InjectableFixture $product, View $productViewBlock)
    {
        $errors = [];
        $tierPrices = $this->prepareTierPrices($product);
        foreach ($tierPrices as $key => $tierPrice) {
            $formTierPrice = $productViewBlock->getTierPrices($key + 1);
            preg_match($this->pattern, $formTierPrice, $match);
            $errors = $this->verifyItemTierPrice($tierPrice, $match);
        }

        \PHPUnit_Framework_Assert::assertEmpty($errors, $errors);
    }

    /**
     * Verify item tier price.
     *
     * @param array $fixtureTierPrice
     * @param array $formTierPrice
     * @return string
     */
    protected function verifyItemTierPrice(array $fixtureTierPrice, array $formTierPrice)
    {
        $errors = [];
        if (count($formTierPrice) < $this->matchCount) {
            $errors[] = "Not all data exist in product page.\n";
        }
        foreach ($this->verifyFields as $key => $field) {
            if ($formTierPrice[$key + 1] != $fixtureTierPrice[$field]) {
                $errors[] = "'$field' in form doesn't equal to fixture:\n"
                    . "{$formTierPrice[$key + 1]} != {$fixtureTierPrice[$field]}";
            }
        }

        return implode("\n", $errors);
    }

    /**
     * Prepare tier price data.
     *
     * @param InjectableFixture $product
     * @return array
     */
    protected function prepareTierPrices(InjectableFixture $product)
    {
        $tierPrices = $product->getTierPrice();
        $percents = $product->getDataFieldConfig('price')['source']->getPriceData();
        foreach ($tierPrices as $key => $tierPrice) {
            $tierPrices[$key]['price'] = number_format($tierPrices[$key]['price'], $this->priceFormat);
            $tierPrices[$key]['percent'] = $percents[$key]['percent'];
        }

        return $tierPrices;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Tier price is displayed on the product page.';
    }
}
