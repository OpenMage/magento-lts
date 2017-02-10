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

namespace Mage\Catalog\Test\Constraint;

use Magento\Mtf\Fixture\InjectableFixture;
use Mage\Catalog\Test\Fixture\ConfigurableProduct;
use Mage\Catalog\Test\Page\Category\CatalogCategoryView;
use Mage\Adminhtml\Test\Page\Adminhtml\ProcessList;
use Mage\Catalog\Test\Fixture\CatalogCategory;
use Magento\Mtf\Client\Browser;

/**
 * Assert that filtered product is present on category page by attribute and another products are absent.
 */
class AssertProductsVisibleOnCategoryPageShopByAttribute extends AbstractAssertProductsVisibleOnCategoryPageShopByFilter
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that filtered product is present on category page by attribute and another products are absent.
     *
     * @param CatalogCategoryView $catalogCategoryView
     * @param ProcessList $processList
     * @param Browser $browser
     * @param CatalogCategory $category
     * @param InjectableFixture[] $products
     * @param string $searchProductsIndexes
     * @param string $filterLink
     * @return void
     */
    public function processAssert(
        CatalogCategoryView $catalogCategoryView,
        ProcessList $processList,
        Browser $browser,
        CatalogCategory $category,
        array $products,
        $searchProductsIndexes,
        $filterLink
    ) {
        $processList->open()->getIndexManagementGrid()->massactionForAll('Reindex Data');
        $browser->open($_ENV['app_frontend_url'] . strtolower($category->getUrlKey()) . '.html');
        $filter = $this->prepareFilter($products[$searchProductsIndexes], $filterLink);
        $catalogCategoryView->getLayeredNavigationBlock()->selectAttribute($filter);

        $this->verify($catalogCategoryView, $products, $searchProductsIndexes);
    }

    /**
     * Prepare filter for attribute.
     *
     * @param ConfigurableProduct $product
     * @param string $filterLink
     * @return array
     */
    protected function prepareFilter(ConfigurableProduct $product, $filterLink)
    {
        list($attributeKey, $optionKey) = explode('::', $filterLink);
        $attributesData = $product->getConfigurableOptions()['attributes_data'];
        return [
            'attribute' => $attributesData[$attributeKey]['frontend_label'],
            'option' => $attributesData[$attributeKey]['options'][$optionKey]['label']
        ];
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product is present in category after filter by attribute.';
    }
}
