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
use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\Constraint\AbstractAssertForm;
use Mage\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Assert that displayed product custom options data on product page equals passed from fixture.
 */
class AssertProductCustomOptionsOnProductPage extends AbstractAssertForm
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Skipped field for custom options.
     *
     * @var array
     */
    protected $skippedFieldOptions = [
        'price_type',
        'sku',
    ];

    /**
     * Assertion that commodity options are displayed correctly.
     *
     * @param CatalogProductView $catalogProductView
     * @param InjectableFixture $product
     * @param Browser $browser
     * @return void
     */
    public function processAssert(CatalogProductView $catalogProductView, InjectableFixture $product, Browser $browser)
    {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $fixtureCustomOptions = $this->prepareOptions($product);
        $formCustomOptions = $catalogProductView->getViewBlock()->getOptions($product)['custom_options'];
        $error = $this->verifyData($fixtureCustomOptions, $formCustomOptions);
        \PHPUnit_Framework_Assert::assertEmpty($error, $error);
    }

    /**
     * Preparation options before comparing.
     *
     * @param InjectableFixture $product
     * @return array
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function prepareOptions(InjectableFixture $product)
    {
        $result = [];
        $customOptions = $product->getCustomOptions();
        $actualPrice = $this->getProductActualPrice($product);
        foreach ($customOptions as $customOption) {
            $customOptionsPriceType = isset($customOption['options']['price_type'])
                ? $customOption['options']['price_type']
                : null;
            if ($customOptionsPriceType) {
                if ($customOptionsPriceType === 'Percent') {
                    $customOptionPrice = $customOption['options']['price'];
                    $customOption['options']['price'] = $this->calculatePrice($actualPrice, $customOptionPrice);
                }
                $customOption['options'] = array_diff_key(
                    $customOption['options'],
                    array_flip($this->skippedFieldOptions)
                );
            } else {
                foreach ($customOption['options'] as &$option) {
                    if ('Percent' === $option['price_type']) {
                        $option['price'] = $this->calculatePrice($actualPrice, $option['price']);
                    }
                    $option = array_diff_key($option, array_flip($this->skippedFieldOptions));
                }
            }
            $customOption['type'] = explode('/', $customOption['type'])[1];
            $result[$customOption['title']] = $customOption;
        }

        return $result;
    }

    /**
     * Calculate price.
     *
     * @param int $actualPrice
     * @param int $customOptionPrice
     * @return int
     */
    protected function calculatePrice($actualPrice, $customOptionPrice)
    {
        return round((($actualPrice * $customOptionPrice) / 100), 2);
    }

    /**
     * Get product actual price.
     *
     * @param InjectableFixture $product
     * @return int
     */
    protected function getProductActualPrice(InjectableFixture $product)
    {
        return $product->hasData('special_price') ? $product->getSpecialPrice() : $product->getPrice();
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Value of custom option on the page is correct.';
    }
}
