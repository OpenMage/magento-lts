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

namespace Mage\Catalog\Test\TestCase\Category;

use Mage\Catalog\Test\Fixture\CatalogCategory;
use Mage\Catalog\Test\Fixture\CatalogProductSimple;
use Mage\Catalog\Test\Page\Adminhtml\CatalogCategoryIndex;
use Magento\Mtf\TestCase\Injectable;
use Magento\Mtf\Fixture\FixtureFactory;

/**
 * Preconditions:
 * 1. Create category.
 *
 * Steps:
 * 1. Login as admin.
 * 2. Navigate to the Catalog->Categories->Manage Categories.
 * 3. Open category created in preconditions.
 * 4. Update data according to data set.
 * 5. Save category.
 * 6. Perform all assertions.
 *
 * @group Category_Management_(MX)
 * @ZephyrId MPERF-7032
 */
class UpdateCategoryEntityTest extends Injectable
{
    /**
     * Catalog category index page.
     *
     * @var CatalogCategoryIndex
     */
    protected $catalogCategoryIndex;

    /**
     * Fixture factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Array unassigned products.
     *
     * @var array
     */
    protected $unassignedProducts = [];

    /**
     * Unassigned category products keys.
     *
     * @var mixed
     */
    protected $unassignedProductsKeys;

    /**
     * Injection data.
     *
     * @param CatalogCategoryIndex $catalogCategoryIndex
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __inject(CatalogCategoryIndex $catalogCategoryIndex, FixtureFactory $fixtureFactory)
    {
        $this->catalogCategoryIndex = $catalogCategoryIndex;
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Update category.
     *
     * @param CatalogCategory $category
     * @param CatalogCategory $initialCategory
     * @param string $unassignedProductsKeys [optional]
     * @return array
     */
    public function test(CatalogCategory $category, CatalogCategory $initialCategory, $unassignedProductsKeys = '')
    {
        $this->unassignedProductsKeys = $unassignedProductsKeys;
        $initialCategory->persist();
        $this->catalogCategoryIndex->open();
        $this->catalogCategoryIndex->getTreeCategories()->selectCategory($initialCategory);
        $category = $this->updateCategoryFixture($initialCategory, $category);
        $this->catalogCategoryIndex->getCategoryForm()->fill($category);
        $this->catalogCategoryIndex->getFormPageActions()->save();

        return ['category' => $category, 'unassignedProducts' => $this->unassignedProducts];
    }

    /**
     * Update category fixture.
     *
     * @param CatalogCategory $initialCategory
     * @param CatalogCategory $category
     * @return CatalogCategory
     */
    protected function updateCategoryFixture(CatalogCategory $initialCategory, CatalogCategory $category)
    {
        $categoryData = $this->prepareCategoryData($initialCategory, $category);
        return $this->fixtureFactory->createByCode(
            'catalogCategory',
            ['data' => $categoryData]
        );
    }

    /**
     * Prepare category date.
     *
     * @param CatalogCategory $initialCategory
     * @param CatalogCategory $category
     * @return array
     */
    protected function prepareCategoryData(CatalogCategory $initialCategory, CatalogCategory $category)
    {
        $categoryData = array_merge($initialCategory->getData(), $category->getData());
        if (isset($categoryData['category_products'])) {
            $categoryData['category_products'] = $this->prepareCategoryProducts($initialCategory, $category);
        }
        $categoryData['parent_id'] = $this->prepareParentCategory($initialCategory, $category);
        unset($categoryData['path']);

        return $categoryData;
    }

    /**
     * Prepare category products.
     *
     * @param CatalogCategory $initialCategory
     * @param CatalogCategory $category
     * @return array
     */
    protected function prepareCategoryProducts(CatalogCategory $initialCategory, CatalogCategory $category)
    {
        $data = [];
        $initialCategoryProducts = $this->getCategoryProducts($initialCategory);
        $categoryProducts = $this->getCategoryProducts($category);
        if ($this->unassignedProductsKeys !== '') {
            $this->unassignedProductsKeys = explode(',', $this->unassignedProductsKeys);
            foreach ($this->unassignedProductsKeys as $key) {
                $key = trim($key);
                $this->unassignedProducts[] = $initialCategoryProducts[$key];
                unset($initialCategoryProducts[$key]);
            }
        }
        $resultProducts = array_merge($initialCategoryProducts, $categoryProducts);
        foreach ($resultProducts as $product) {
            $data[] = $product->getSku();
        }

        return ['data' => $data, 'products' => $resultProducts];
    }

    /**
     * Get category products.
     *
     * @param CatalogCategory $category
     * @return array
     */
    protected function getCategoryProducts(CatalogCategory $category)
    {
        return  $category->hasData('category_products')
            ? $category->getDataFieldConfig('category_products')['source']->getProducts()
            : [];
    }

    /**
     * Prepare parent category data.
     *
     * @param CatalogCategory $initialCategory
     * @param CatalogCategory $category
     * @return array
     */
    protected function prepareParentCategory(CatalogCategory $initialCategory, CatalogCategory $category)
    {
        $category = $category->hasData('parent_id') ? $category : $initialCategory;
        return [
            'data' => $category->getParentId(),
            'parent_category' => $category->getDataFieldConfig('parent_id')['source']->getParentCategory()
        ];
    }
}
