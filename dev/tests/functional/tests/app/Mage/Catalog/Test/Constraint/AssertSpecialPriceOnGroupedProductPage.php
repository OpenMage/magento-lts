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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\Constraint;

use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Mage\Catalog\Test\Fixture\GroupedProduct;
use Magento\Mtf\Client\Browser;

/**
 * Assert that displayed special price on grouped product page equals passed from fixture.
 */
class AssertSpecialPriceOnGroupedProductPage extends AbstractAssertPriceOnGroupedProductPage
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Format error message.
     *
     * @var string
     */
    protected $errorMessage = 'This "%s" product\'s special price on product page NOT equals passed from fixture.';

    /**
     * Successful message.
     *
     * @var string
     */
    protected $successfulMessage = 'Special price on grouped product page equals passed from fixture.';

    /**
     * Assert that displayed special price on grouped product page equals passed from fixture.
     *
     * @param CatalogProductView $catalogProductView
     * @param GroupedProduct $product
     * @param AssertProductSpecialPriceOnProductPage $specialPrice
     * @param Browser $browser
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        GroupedProduct $product,
        AssertProductSpecialPriceOnProductPage $specialPrice,
        Browser $browser
    ) {
        $this->processAssertPrice($product, $catalogProductView, $specialPrice, $browser);
    }
}
