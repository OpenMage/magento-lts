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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\Constraint;

use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Mage\Catalog\Test\Fixture\GroupedProduct;
use Magento\Mtf\Client\Browser;
use Magento\Mtf\Constraint\AbstractAssertForm;

/**
 * Assert that default qty for sub products in grouped product displays according to dataset on product page.
 */
class AssertGroupedProductsDefaultQty extends AbstractAssertForm
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that default qty for sub products in grouped product displays according to dataset on product page.
     *
     * @param CatalogProductView $groupedProductView
     * @param GroupedProduct $product
     * @param Browser $browser
     * @return void
     */
    public function processAssert(CatalogProductView $groupedProductView, GroupedProduct $product, Browser $browser)
    {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $pageOptions = $groupedProductView->getViewBlock()->getOptions($product);

        $fixtureQtyData = $this->prepareFixtureQtyData($product);
        $pageQtyData = $this->prepareFormQtyData($pageOptions);
        $error = $this->verifyData($fixtureQtyData, $pageQtyData);
        \PHPUnit_Framework_Assert::assertEmpty($error, $error);
    }

    /**
     * Prepare form's qty data.
     *
     * @param array $options
     * @return array
     */
    protected function prepareFormQtyData(array $options)
    {
        $qtyData = [];
        foreach ($options as $option) {
            $qtyData[] = ['name' => $option['name'], 'qty' => $option['qty']];
        }

        return $this->sortDataByPath($qtyData, '::name');
    }

    /**
     * Prepare fixture's qty data.
     *
     * @param GroupedProduct $product
     * @return array
     */
    protected function prepareFixtureQtyData(GroupedProduct $product)
    {
        $qtyData = [];
        $associatedProducts = $product->getDataFieldConfig('associated')['source']->getProducts();
        foreach ($product->getAssociated() as $key => $option) {
            $qtyData[] = ['name' => $associatedProducts[$key]->getName(), 'qty' => $option['qty']];
        }

        return $this->sortDataByPath($qtyData, '::name');
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Default qty for sub products in grouped product displays according to dataset on product page.';
    }
}
