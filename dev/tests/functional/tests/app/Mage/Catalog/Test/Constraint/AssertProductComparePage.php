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

use Mage\Catalog\Test\Page\Product\CatalogProductCompare;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Assert that "Compare Product" page contains product(s) that was added.
 */
class AssertProductComparePage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Product attribute on compare product page.
     *
     * @var array
     */
    protected $attributeProduct = [
        'name',
        'price',
        'sku' => 'SKU',
        'description' => 'Description',
        'short_description' => 'Short Description',
    ];

    /**
     * Catalog product compare page.
     *
     * @var CatalogProductCompare
     */
    protected $comparePage;

    /**
     * Assert that "Compare Product" page contains product(s) that was added:
     * - Product name
     * - Price
     * - SKU
     * - Description (if exists, else text "No")
     * - Short Description (if exists, else text "No")
     *
     * @param CatalogProductCompare $comparePage
     * @param array $products
     * @return void
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function processAssert(CatalogProductCompare $comparePage, array $products)
    {
        $this->comparePage = $comparePage;
        $comparePage->open();
        foreach ($products as $key => $product) {
            $attributeData = $this->prepareAttributeData($product, $key);
            \PHPUnit_Framework_Assert::assertEquals(
                $attributeData['attributeValues'],
                $attributeData['attributeValuesFromPage'],
                "Product {$product->getName()} is not equals with data from fixture."
            );
        }
    }

    /**
     * Prepare attribute data.
     *
     * @param InjectableFixture $product
     * @param int $key
     * @return array
     */
    protected function prepareAttributeData(InjectableFixture $product, $key)
    {
        $data = [];
        foreach ($this->attributeProduct as $attributeKey => $attribute) {
            $value = $attribute;
            $attribute = is_numeric($attributeKey) ? $attribute : $attributeKey;

            $attributeValue = $attribute != 'price'
                ? ($product->hasData($attribute)
                    ? $product->getData($attribute)
                    : 'N/A')
                : ($product->getDataFieldConfig('price')['source']->getPriceData() !== null
                    ? $product->getDataFieldConfig('price')['source']->getPriceData()
                    : number_format($product->getPrice(), 2));

            $data['attributeValues'][$attribute] = !is_array($attributeValue) ? strtolower(
                $attributeValue
            ) : $attributeValue;
            $attributeName = ($value === 'name' || $value === 'price') ? 'Info' : 'MetaData';
            $data['attributeValuesFromPage'][$attribute] = $this->comparePage->getCompareProductsBlock(
            )->{'getProduct' . $attributeName}(
                $key + 1,
                $value
            );
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
        return '"Compare Product" page has valid data for all products.';
    }
}
