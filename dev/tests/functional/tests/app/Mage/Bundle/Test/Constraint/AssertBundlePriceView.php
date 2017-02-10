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

namespace Mage\Bundle\Test\Constraint;

use Mage\Bundle\Test\Fixture\BundleProduct;
use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Mtf\Client\Browser;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that displayed price view for bundle product on product page equals passed from fixture.
 */
class AssertBundlePriceView extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that displayed price view for bundle product on product page equals passed from fixture.
     *
     * @param CatalogProductView $catalogProductView
     * @param Browser $browser
     * @param BundleProduct $product
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        Browser $browser,
        BundleProduct $product
    ) {
        //Open product view page
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');

        //Process assertions
        $this->assertPrice($product, $catalogProductView);
    }

    /**
     * Assert prices on the product view Page
     *
     * @param BundleProduct $product
     * @param CatalogProductView $catalogProductView
     * @return void
     */
    protected function assertPrice(BundleProduct $product, CatalogProductView $catalogProductView)
    {
        $priceData = $product->getDataFieldConfig('price')['source']->getPriceData();
        $priceBlock = $catalogProductView->getBundleViewBlock()->getPriceBlock();
        $priceLow = ($product->getPriceView() == 'Price Range')
            ? $priceBlock->getPriceFrom()
            : $priceBlock->getRegularPrice();

        \PHPUnit_Framework_Assert::assertEquals($priceData['price_from'], $priceLow);

        if ($product->getPriceView() == 'Price Range') {
            \PHPUnit_Framework_Assert::assertEquals($priceData['price_to'], $priceBlock->getPriceTo());
        }
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Bundle price on product view page is not correct.';
    }
}
