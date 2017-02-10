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

use Mage\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Mage\Catalog\Test\Page\Adminhtml\CatalogProduct;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Assert that duplicate product form data equals data from fixture.
 */
class AssertProductDuplicateForm extends AssertProductForm
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Formatting options for numeric values.
     *
     * @var array
     */
    protected $formattingOptions = [
        'price' => [
            'decimals' => 2,
            'dec_point' => '.',
            'thousands_sep' => '',
        ],
        'qty' => [
            'decimals' => 4,
            'dec_point' => '.',
            'thousands_sep' => '',
        ],
        'weight' => [
            'decimals' => 4,
            'dec_point' => '.',
            'thousands_sep' => '',
        ],
    ];

    /**
     * Format for url key field.
     *
     * @var string
     */
    protected $urlKeyFormat;

    /**
     * Assert form data equals fixture data.
     *
     * @param InjectableFixture $product
     * @param CatalogProduct $productGrid
     * @param CatalogProductEdit $productPage
     * @param string $urlKeyFormat [optional]
     * @return void
     */
    public function processAssert(
        InjectableFixture $product,
        CatalogProduct $productGrid,
        CatalogProductEdit $productPage,
        $urlKeyFormat = '%s'
    ) {
        $this->product = $product;
        $this->urlKeyFormat = $urlKeyFormat;
        $this->catalogProductEdit = $productPage;
        $filter = ['name' => $product->getName(), 'status' => 'Disabled'];
        $productGrid->open()->getProductGrid()->searchAndOpen($filter);

        $formData = $productPage->getProductForm()->getData($product);
        $fixtureData = $this->prepareFixtureData($product->getData());

        $errors = $this->verifyData($fixtureData, $formData);
        \PHPUnit_Framework_Assert::assertEmpty($errors, $errors);
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
        $compareData = array_filter($data);

        array_walk_recursive(
            $compareData,
            function (&$item, $key, $formattingOptions) {
                if (isset($formattingOptions[$key])) {
                    $item = number_format(
                        $item,
                        $formattingOptions[$key]['decimals'],
                        $formattingOptions[$key]['dec_point'],
                        $formattingOptions[$key]['thousands_sep']
                    );
                }
            },
            $this->formattingOptions
        );

        if (isset($compareData['status'])) {
            $compareData['status'] = 'Disabled';
        }
        if (isset($compareData['stock_data']['qty'])) {
            $compareData['stock_data'] = ['qty' => 0, 'is_in_stock' => 'Out of Stock'];
        }
        $compareData['sku'] = '';
        $compareData['url_key'] = sprintf($this->urlKeyFormat, $compareData['url_key']);

        return parent::prepareFixtureData($compareData, $sortFields);
    }

    /**
     * Prepare url key.
     *
     * @param string $urlKey
     * @return string
     */
    protected function prepareUrlKey($urlKey)
    {
        return $urlKey;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Form data equals to fixture data of duplicated product.';
    }
}
