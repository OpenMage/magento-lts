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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\Constraint;

use Mage\Catalog\Test\Fixture\CatalogCategory;
use Magento\Mtf\Constraint\AbstractAssertForm;
use Magento\Mtf\Fixture\InjectableFixture;
use Mage\Catalog\Test\Page\Adminhtml\CatalogProduct;
use Mage\Catalog\Test\Page\Adminhtml\CatalogProductEdit;

/**
 * Assert that displayed product data on edit page equals passed from fixture.
 */
class AssertProductForm extends AbstractAssertForm
{
    /**
     * List skipped fixture fields in verify.
     *
     * @var array
     */
    protected $skippedFixtureFields = [
        'id',
        'checkout_data',
        'attribute_set_id',
        'type_id',
    ];

    /**
     * Sort fields for fixture and form data.
     *
     * @var array
     */
    protected $sortFields = [
        'custom_options::title',
        'cross_sell_products::entity_id',
        'up_sell_products::entity_id',
        'related_products::entity_id'
    ];

    /**
     * Formatting options for array values.
     *
     * @var array
     */
    protected $specialArray = [];

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Catalog product edit page.
     *
     * @var CatalogProductEdit
     */
    protected $catalogProductEdit;

    /**
     * Product fixture.
     *
     * @var InjectableFixture
     */
    protected $product;

    /**
     * Assert form data equals fixture data.
     *
     * @param InjectableFixture $product
     * @param CatalogProduct $productGrid
     * @param CatalogProductEdit $productPage
     * @return void
     */
    public function processAssert(
        InjectableFixture $product,
        CatalogProduct $productGrid,
        CatalogProductEdit $productPage
    ) {
        $this->product = $product;
        $this->catalogProductEdit = $productPage;
        $filter = ['sku' => $product->getSku()];
        $productGrid->open();
        $productGrid->getProductGrid()->searchAndOpen($filter);

        $productData = $product->getData();
        $fixtureData = $this->prepareFixtureData($productData, $this->sortFields);
        $formData = $this->prepareFormData($productPage->getProductForm()->getData($product), $this->sortFields);
        $error = $this->verifyData($fixtureData, $formData);
        \PHPUnit_Framework_Assert::assertTrue(empty($error), $error);
    }

    /**
     * Prepares fixture data for comparison.
     *
     * @param array $data
     * @param array $sortFields [optional]
     * @return array
     */
    protected function prepareFixtureData(array $data, array $sortFields = [])
    {
        $data = array_diff_key($data, array_flip($this->skippedFixtureFields));

        if (!$this->catalogProductEdit->getProductForm()->getTabElement('websites')->isVisible()) {
            unset($data['website_ids']);
        }
        if (isset($data['website_ids']) && !is_array($data['website_ids'])) {
            $data['website_ids'] = [$data['website_ids']];
        }
        if (!empty($this->specialArray)) {
            $data = $this->prepareSpecialPriceArray($data);
        }
        if (isset($data['category_ids'])) {
            $data['category_ids'] = $this->getFullPathCategories();
        }

        foreach ($sortFields as $path) {
            $data = $this->sortDataByPath($data, $path);
        }
        return $data;
    }

    /**
     * Get full path for all categories.
     *
     * @return array
     */
    protected function getFullPathCategories()
    {
        $result = [];
        $categories = $this->product->getDataFieldConfig('category_ids')['source']->getCategories();
        foreach ($categories as $key => $itemCategory) {
            $fullPath = $this->prepareFullCategoryPath($itemCategory);
            $result[$key] = implode('/', $fullPath);
        }

        return $result;
    }

    /**
     * Prepare category path.
     *
     * @param CatalogCategory $category
     * @return array
     */
    protected function prepareFullCategoryPath(CatalogCategory $category)
    {
        $path = [];
        $parentCategory = $category->getDataFieldConfig('parent_id')['source']->getParentCategory();

        if ($parentCategory != null) {
            $path = $this->prepareFullCategoryPath($parentCategory);
        }
        return array_filter(array_merge($path, [$category->getPath(), $category->getName()]));
    }

    /**
     * Prepare special price array for product.
     *
     * @param array $fields
     * @return array
     */
    protected function prepareSpecialPriceArray(array $fields)
    {
        foreach ($this->specialArray as $key => $value) {
            if (array_key_exists($key, $fields)) {
                if (isset($value['type']) && $value['type'] == 'date') {
                    $fields[$key] = vsprintf('%d/%d/%d', explode('/', $fields[$key]));
                }
            }
        }
        return $fields;
    }

    /**
     * Prepares form data for comparison.
     *
     * @param array $data
     * @param array $sortFields [optional]
     * @return array
     */
    protected function prepareFormData(array $data, array $sortFields = [])
    {
        foreach ($sortFields as $path) {
            $data = $this->sortDataByPath($data, $path);
        }
        return $data;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Form data equal the fixture data.';
    }
}
