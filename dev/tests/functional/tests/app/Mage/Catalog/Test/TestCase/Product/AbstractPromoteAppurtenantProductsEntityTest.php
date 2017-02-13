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

use Mage\Catalog\Test\Page\Adminhtml\CatalogProduct;
use Mage\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\TestCase\Injectable;

/**
 * Base class for add related, cross sell and up sell products entity test.
 */
abstract class AbstractPromoteAppurtenantProductsEntityTest extends Injectable
{
    /**
     * Fixture factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

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
     * Tab name.
     *
     * @var string
     */
    protected $tabName = '';

    /**
     * Appurtenant type.
     *
     * @var array
     */
    protected $appurtenantType = [];

    /**
     * Prepare data.
     *
     * @param FixtureFactory $fixtureFactory .
     * @return void
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $this->fixtureFactory = $fixtureFactory;
    }

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
     * Run test promote cross sell products entity.
     *
     * @param string $products
     * @param array $appurtenantProductsData
     * @return array
     */
    public function test($products, array $appurtenantProductsData)
    {
        $editProduct = null;
        $productsDataResult = [];
        $createdProducts = $this->objectManager->create(
            'Mage\Catalog\Test\TestStep\CreateProductsStep',
            ['products' => $products]
        )->run()['products'];
        foreach ($appurtenantProductsData as $productData) {
            $appurtenant = $this->prepareAppurtenantProducts($createdProducts, $productData, $this->appurtenantType);
            $editProduct = $createdProducts[$productData['productIndex']];
            if ($appurtenant !== false) {
                $this->addAppurtenantProducts($editProduct, $appurtenant);
                $productsDataResult[] = ['product' => $editProduct, $this->appurtenantType['arrayIndex'] => $appurtenant];
            } else {
                $productsDataResult[] = ['product' => $editProduct];
            }
        }

        return ['productsData' => $productsDataResult];
    }

    /**
     * Get appurtenant products.
     *
     * @param array $createdProducts
     * @param string $appurtenantProductsIndex
     * @return array|bool
     */
    protected function prepareAppurtenantProducts(
        array $createdProducts,
        $appurtenantProductsIndex
    ) {
        if (isset($appurtenantProductsIndex[$this->appurtenantType['arrayIndex']])) {
            $appurtenantIndex = explode(',', $appurtenantProductsIndex[$this->appurtenantType['arrayIndex']]);
            $result = [];
            foreach ($appurtenantIndex as $index) {
                $result[$this->appurtenantType['formIndex']]['value'][] = $createdProducts[$index];
            }
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Add appurtenant products and save product.
     *
     * @param InjectableFixture $product
     * @param array $appurtenantData
     * @return void
     */
    protected function addAppurtenantProducts(
        InjectableFixture $product,
        array $appurtenantData
    ) {
        $this->productGrid->open();
        $this->productGrid->getProductGrid()->searchAndOpen(['sku' => $product->getSku()]);
        $form = $this->editProductPage->getProductForm();
        $form->openTab($this->tabName);
        $appurtenantTab = $form->getTabElement($this->tabName);
        $appurtenantData[$this->appurtenantType['formIndex']]['value'] = $this->prepareData(
            $appurtenantData[$this->appurtenantType['formIndex']]['value']
        );
        $appurtenantTab->fillFormTab($appurtenantData);
        $this->editProductPage->getFormPageActions()->save();
    }

    /**
     * Prepare data.
     *
     * @param array $appurtenantProducts
     * @return array
     */
    protected function prepareData(array $appurtenantProducts)
    {
        $data = [];
        foreach ($appurtenantProducts as $product) {
            $data[] = $product->getData();
        }
        return $data;
    }
}
