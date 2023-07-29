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

namespace Mage\Catalog\Test\TestCase\Product;

use Mage\Catalog\Test\Fixture\CatalogCategory;
use Mage\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Mage\Catalog\Test\Page\Adminhtml\CatalogProduct;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\TestCase\Injectable;

/**
 * Precondition:
 * 1. Product is created.
 *
 * Steps:
 * 1. Login to backend.
 * 2. Navigate to Catalog > Manage Products.
 * 3. Search and open product.
 * 4. Click "Duplicate".
 * 5. Perform asserts.
 *
 * @group Products_(MX)
 * @ZephyrId MPERF-7489
 */
class DuplicateProductEntityTest extends Injectable
{
    /**
     * Category fixture.
     *
     * @var CatalogCategory
     */
    protected $category;

    /**
     * Product page with a grid.
     *
     * @var CatalogProduct
     */
    protected $productGrid;

    /**
     * Page to update a product.
     *
     * @var CatalogProductEdit
     */
    protected $editProductPage;

    /**
     * Fixture factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Prepare data.
     *
     * @param CatalogCategory $category
     * @param CatalogProduct $productGrid
     * @param CatalogProductEdit $editProductPage
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __prepare(
        CatalogCategory $category,
        CatalogProduct $productGrid,
        CatalogProductEdit $editProductPage,
        FixtureFactory $fixtureFactory
    ) {
        $this->category = $category;
        $this->category->persist();
        $this->productGrid = $productGrid;
        $this->editProductPage = $editProductPage;
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Run test duplicate product entity.
     *
     * @param string $productType
     * @return array
     */
    public function test($productType)
    {
        // Precondition
        $product = $this->createProduct($productType);

        // Steps
        $this->productGrid->open();
        $this->productGrid->getProductGrid()->searchAndOpen(['sku' => $product->getSku()]);
        $this->editProductPage->getFormPageActions()->duplicate();

        return ['product' => $product];
    }

    /**
     * Creating a product according to the type of.
     *
     * @param string $productType
     * @return InjectableFixture
     */
    protected function createProduct($productType)
    {
        list($fixture, $dataset) = explode('::', $productType);
        $product = $this->fixtureFactory->createByCode(
            $fixture,
            [
                'dataset' => $dataset,
                'data' => [
                    'category_ids' => [
                        'category' => $this->category,
                    ],
                ]
            ]
        );
        $product->persist();

        return $product;
    }
}
