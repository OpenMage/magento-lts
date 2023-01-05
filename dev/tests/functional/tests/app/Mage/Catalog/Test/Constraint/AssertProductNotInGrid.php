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
 * Assert that product cannot be found by name and sku on product grid.
 */
class AssertProductNotInGrid extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that product cannot be found by name and sku on product grid.
     *
     * @param InjectableFixture $product
     * @param CatalogProduct $productGrid
     * @return void
     */
    public function processAssert(InjectableFixture $product, CatalogProduct $productGrid)
    {
        $productGrid->open();
        $this->assert($product, $productGrid);
    }

    /**
     * Check product on product grid.
     *
     * @param InjectableFixture $product
     * @param CatalogProduct $productGrid
     * @return void
     */
    public function assert(InjectableFixture $product, CatalogProduct $productGrid)
    {
        $filter = ['sku' => $product->getSku(), 'name' => $product->getName()];
        \PHPUnit_Framework_Assert::assertFalse(
            $productGrid->getProductGrid()->isRowVisible($filter),
            "Product with sku {$filter['sku']} and name {$filter['name']} is present in Products grid."
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Assertion that product is absent in products grid.';
    }
}
