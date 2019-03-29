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

namespace Mage\Catalog\Test\Constraint;

use Magento\Mtf\Fixture\InjectableFixture;
use Mage\Cms\Test\Page\CmsIndex;
use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\CatalogSearch\Test\Page\CatalogsearchResult;
use Mage\Catalog\Test\Fixture\CatalogProductAttribute;

/**
 * Assert that product can be found via Quick Search using searchable product attributes label.
 */
class AssertProductAttributeSearchableByLabel extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that product can be found via Quick Search using searchable product attributes label.
     *
     * @param CatalogsearchResult $catalogSearchResult
     * @param CmsIndex $cmsIndex
     * @param InjectableFixture $product
     * @param CatalogProductAttribute $attribute
     * @return void
     */
    public function processAssert(
        CatalogsearchResult $catalogSearchResult,
        CmsIndex $cmsIndex,
        InjectableFixture $product,
        CatalogProductAttribute $attribute
    ) {
        $cmsIndex->open();
        $filter = $this->prepareFilter($product, $attribute);
        $cmsIndex->getSearchBlock()->search($filter);
        $isProductVisible = $catalogSearchResult->getListProductBlock()->isProductVisible($product);
        while (!$isProductVisible && $catalogSearchResult->getBottomToolbar()->nextPage()) {
            $isProductVisible = $catalogSearchResult->getListProductBlock()->isProductVisible($product);
        }

        \PHPUnit_Framework_Assert::assertTrue($isProductVisible, 'Product was not found by option label.');
    }

    /**
     * Prepare filter for search.
     *
     * @param InjectableFixture $product
     * @param CatalogProductAttribute $attribute
     * @return string
     */
    protected function prepareFilter(InjectableFixture $product, CatalogProductAttribute $attribute)
    {
        $filter = '';
        $attributesFillData = $product->getAttributes();
        $attributeOptions = $attribute->getOptions();
        foreach ($attributesFillData['dataset'] as $optionsFillData) {
            foreach ($optionsFillData as $optionKey) {
                $optionKey = str_replace('option_key_', '', $optionKey);
                $filter .= isset($attributeOptions[$optionKey]['view'])
                    ? $attributeOptions[$optionKey]['view']
                    : $attributeOptions[$optionKey]['admin'];
            }
        }

        return $filter;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return "Product is searchable by attribute label.";
    }
}
