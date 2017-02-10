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

namespace Mage\Catalog\Test\TestCase\Product;

use Mage\Catalog\Test\Fixture\CatalogProductSimple;
use Mage\Catalog\Test\Fixture\CatalogCategory;
use Mage\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Mage\Catalog\Test\Page\Adminhtml\CatalogProduct;
use Magento\Mtf\TestCase\Injectable;

/**
 * Precondition:
 * 1. Category is created.
 * 2. Product is created and assigned to created category.
 *
 * Steps:
 * 1. Login to backend.
 * 2. Navigate to Catalog > Manage Products.
 * 3. Select a product in the grid.
 * 4. Edit test value(s) according to dataset.
 * 5. Click "Save".
 * 6. Perform asserts.
 *
 * @group Products_(MX)
 * @ZephyrId MPERF-7051
 */
class UpdateSimpleProductEntityTest extends Injectable
{
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
     * Injection pages.
     *
     * @param CatalogProduct $productGrid
     * @param CatalogProductEdit $editProductPage
     * @return void
     */
    public function __inject(CatalogProduct $productGrid, CatalogProductEdit $editProductPage)
    {
        $this->productGrid = $productGrid;
        $this->editProductPage = $editProductPage;
    }

    /**
     * Run update product simple entity test.
     *
     * @param CatalogProductSimple $initialProduct
     * @param CatalogProductSimple $product
     * @return array
     */
    public function test(CatalogProductSimple $initialProduct, CatalogProductSimple $product)
    {
        // Preconditions
        $initialProduct->persist();

        // Steps
        $this->productGrid->open();
        $this->productGrid->getProductGrid()->searchAndOpen(['sku' => $initialProduct->getSku()]);
        $this->editProductPage->getProductForm()->fill($product);
        $this->editProductPage->getFormPageActions()->save();

        return ['category' => $this->getCategory($initialProduct, $product)];
    }

    /**
     * Get category.
     *
     * @param CatalogProductSimple $initialProduct
     * @param CatalogProductSimple $product
     * @return CatalogCategory|null
     */
    protected function getCategory(CatalogProductSimple $initialProduct, CatalogProductSimple $product)
    {
        return $product->hasData('category_ids')
            ? $product->getDataFieldConfig('category_ids')['source']->getProductCategory()
            : ($initialProduct->hasData('category_ids')
                ? $initialProduct->getDataFieldConfig('category_ids')['source']->getProductCategory()
                : null);
    }
}
