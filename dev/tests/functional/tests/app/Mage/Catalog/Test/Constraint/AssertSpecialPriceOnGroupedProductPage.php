<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Tests
 * @package    Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
