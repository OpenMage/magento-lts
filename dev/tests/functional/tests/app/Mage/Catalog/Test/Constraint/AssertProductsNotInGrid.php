<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\Constraint;

use Mage\Catalog\Test\Page\Adminhtml\CatalogProduct;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Assert that products cannot be found by name and sku on product grid.
 */
class AssertProductsNotInGrid extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that products cannot be found by name and sku on product grid.
     *
     * @param InjectableFixture[] $products
     * @param CatalogProduct $productGrid
     * @param AssertProductNotInGrid $assertProductNotInGrid
     * @return void
     */
    public function processAssert(
        array $products,
        CatalogProduct $productGrid,
        AssertProductNotInGrid $assertProductNotInGrid
    ) {
        $productGrid->open();
        foreach ($products as $product) {
            $assertProductNotInGrid->assert($product, $productGrid);
        }
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Assertion that products is absent in products grid.';
    }
}
