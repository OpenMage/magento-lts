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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\TestCase\Product;

use Mage\Catalog\Test\Fixture\GroupedProduct;
use Magento\Mtf\TestCase\Injectable;
use Mage\Catalog\Test\Fixture\CatalogProductSimple;
use Mage\Catalog\Test\Fixture\CatalogCategory;
use Mage\Catalog\Test\Page\Adminhtml\CatalogProductNew;
use Mage\Catalog\Test\Page\Adminhtml\CatalogProduct;

/**
 * Steps:
 * 1. Login to the backend.
 * 2. Navigate to Catalog > Manage Products.
 * 3. Click "Add Product" button.
 * 4. Select "Grouped Product" in product type field and click "Continue".
 * 5. Fill in data according to attached data set.
 * 6. Save Product.
 * 7. Perform all assertions.
 *
 * @group Products_(CS)
 * @ZephyrId MPERF-6919
 */
class CreateGroupedProductEntityTest extends Injectable
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
     * Page to create a product.
     *
     * @var CatalogProductNew
     */
    protected $newProductPage;

    /**
     * Create category.
     *
     * @param CatalogCategory $category
     * @return array
     */
    public function __prepare(CatalogCategory $category)
    {
        $category->persist();

        return ['category' => $category];
    }

    /**
     * Injection pages.
     *
     * @param CatalogProduct $productGrid
     * @param CatalogProductNew $newProductPage
     * @return void
     */
    public function __inject(CatalogProduct $productGrid, CatalogProductNew $newProductPage)
    {
        $this->productGrid = $productGrid;
        $this->newProductPage = $newProductPage;
    }

    /**
     * Test create grouped product.
     *
     * @param GroupedProduct $product
     * @param CatalogCategory $category
     * @return void
     */
    public function test(GroupedProduct $product, CatalogCategory $category)
    {
        //Steps
        $this->productGrid->open();
        $this->productGrid->getGridPageActionBlock()->addNew();
        $this->newProductPage->getProductForm()->fill($product, null, $category);
        $this->newProductPage->getFormPageActions()->save();
    }
}
