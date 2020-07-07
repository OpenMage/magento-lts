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

use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Catalog\Test\Page\Adminhtml\CatalogProduct;

/**
 * Assert that product is present in products grid.
 */
class AssertProductInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Product fixture.
     *
     * @var InjectableFixture $product
     */
    protected $product;

    /**
     * Assert that product is present in products grid and can be found by sku, type, status and attribute set.
     *
     * @param InjectableFixture $product
     * @param CatalogProduct $productGrid
     * @return void
     */
    public function processAssert(InjectableFixture $product, CatalogProduct $productGrid)
    {
        $this->product = $product;
        $productGrid->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $productGrid->getProductGrid()->isRowVisible($this->prepareFilter()),
            'Product \'' . $this->product->getName() . '\' is absent in Products grid.'
        );
    }

    /**
     * Prepare filter for product grid.
     *
     * @return array
     */
    protected function prepareFilter()
    {
        $filter = [
            'type' => $this->getProductType(),
            'sku' => $this->product->getSku(),
            'status' => $this->product->getStatus(),
        ];

        return $filter;
    }

    /**
     * Get product type
     *
     * @return string
     */
    protected function getProductType()
    {
        $config = $this->product->getDataConfig();

        return ucfirst($config['type_id']) . ' Product';
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product is present in products grid.';
    }
}
