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

namespace Mage\Catalog\Test\TestCase\Product;

use Mage\Catalog\Test\Fixture\CatalogCategory;
use Mage\Catalog\Test\Page\Adminhtml\CatalogProduct;
use Magento\Mtf\TestCase\Injectable;

/**
 * Preconditions:
 * 1. Create product according to dataSet.
 *
 * Steps:
 * 1. Login to backend.
 * 2. Navigate Catalog -> Manege Products.
 * 3. Select products created in preconditions.
 * 4. Select delete from mass-action.
 * 5. Submit form.
 * 6. Perform all asserts.
 *
 * @group Products_(MX)
 * @ZephyrId MPERF-7327
 */
class DeleteProductEntityTest extends Injectable
{
    /**
     * Product page with a grid.
     *
     * @var CatalogProduct
     */
    protected $catalogProductIndex;

    /**
     * Category fixture.
     *
     * @var CatalogCategory
     */
    protected $category;

    /**
     * Prepare data.
     *
     * @param CatalogCategory $category
     * @return array
     */
    public function __prepare(CatalogCategory $category)
    {
        $category->persist();
        $this->category = $category;

        return ['category' => $category];
    }

    /**
     * Injection data.
     *
     * @param CatalogProduct $catalogProductIndexPage
     * @return void
     */
    public function __inject(CatalogProduct $catalogProductIndexPage)
    {
        $this->catalogProductIndex = $catalogProductIndexPage;
    }

    /**
     * Run delete product test.
     *
     * @param string $products
     * @return array
     */
    public function test($products)
    {
        //Preconditions
        $products = $this->createProducts($products);
        $deleteProducts = $this->prepareFilter($products);
        //Steps
        $this->catalogProductIndex->open();
        $this->catalogProductIndex->getProductGrid()->massaction($deleteProducts, 'Delete', true);

        return ['products' => $products];
    }

    /**
     * Create products.
     *
     * @param string $products
     * @return array
     */
    protected function createProducts($products)
    {
        return $this->objectManager->create(
            'Mage\Catalog\Test\TestStep\CreateProductsStep',
            ['products' => $products, 'data' => ['category_ids' => ['category' => $this->category]]]
        )->run()['products'];
    }

    /**
     * Prepare filter.
     *
     * @param array $products
     * @return array
     */
    protected function prepareFilter(array $products)
    {
        $deleteProducts = [];
        foreach ($products as $product) {
            $deleteProducts[] = ['sku' => $product->getSku()];
        }

        return $deleteProducts;
    }
}
