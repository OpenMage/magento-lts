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

use Mage\Catalog\Test\Page\Adminhtml\CatalogProduct;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Assert that duplicated product can be found by filter.
 */
class AssertProductDuplicatedInGrid extends AbstractConstraint
{
    /**
     * Assert that duplicated product can be found in grid by type, template, status and stock status.
     *
     * @param InjectableFixture $product
     * @param CatalogProduct $productGrid
     * @return void
     */
    public function processAssert(InjectableFixture $product, CatalogProduct $productGrid)
    {
        $config = $product->getDataConfig();
        $filter = [
            'name' => $product->getName(),
            'visibility' => $product->getVisibility(),
            'status' => 'Disabled',
            'type' => ucfirst($config['create_url_params']['type']) . ' Product',
            'price_to' => number_format($product->getPrice(), 2),
        ];
        $productGrid->open()->getProductGrid()->search($filter);

        \PHPUnit_Framework_Assert::assertTrue(
            $productGrid->getProductGrid()->isRowVisible($filter, false, false),
            'Product duplicate is absent in Products grid.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'The product has been successfully found, according to the filters.';
    }
}
