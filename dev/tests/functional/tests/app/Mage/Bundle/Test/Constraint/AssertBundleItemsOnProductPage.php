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

namespace Mage\Bundle\Test\Constraint;

use Mage\Bundle\Test\Fixture\BundleProduct;
use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Mtf\Client\Browser;
use Magento\Mtf\Constraint\AbstractAssertForm;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Assert that displayed product bundle items data on product page equals passed from fixture preset.
 */
class AssertBundleItemsOnProductPage extends AbstractAssertForm
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Fixture for bundle product.
     *
     * @var BundleProduct
     */
    protected $product;

    /**
     * Assert that displayed product bundle items data on product page equals passed from fixture preset.
     *
     * @param CatalogProductView $catalogProductView
     * @param BundleProduct $product
     * @param Browser $browser
     * @return void
     */
    public function processAssert(CatalogProductView $catalogProductView, BundleProduct $product, Browser $browser)
    {
        $this->product = $product;
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');

        $productOptions = $this->prepareBundleOptions();
        $formOptions = $catalogProductView->getViewBlock()->getOptions($product)['bundle_options'];
        $formOptions = $this->prepareFormData($formOptions);

        $error = $this->verifyData($productOptions, $formOptions);
        \PHPUnit_Framework_Assert::assertEmpty($error, $error);
    }

    /**
     * Prepare form data.
     *
     * @param array $formOptions
     * @return array
     */
    protected function prepareFormData(array $formOptions)
    {
        foreach ($formOptions as $key => $formOption) {
            $formOptions[$key] = $this->sortDataByPath($formOption, 'options::title');
        }

        return $this->sortDataByPath($formOptions, '::title');
    }

    /**
     * Prepare bundle options.
     *
     * @return array|null
     */
    protected function prepareBundleOptions()
    {
        $result = [];
        $bundleSelections = $this->product->getBundleSelections();
        $assignedProducts = $this->product->getDataFieldConfig('bundle_selections')['source']->getProducts();
        foreach ($bundleSelections as $optionKey => $bundleOption) {
            $options = $this->getOptions(
                $bundleOption['assigned_products'],
                $assignedProducts[$optionKey],
                $bundleOption['type']
            );
            $optionData = [
                'title' => $bundleOption['title'],
                'type' => $bundleOption['type'],
                'is_require' => $bundleOption['required'],
                'options' => $options
            ];

            $result[$optionKey] = $this->sortDataByPath($optionData, 'options::title');
        }

        return $this->sortDataByPath($result, '::title');
    }

    /**
     * Get item bundle selections option.
     *
     * @param array $dataAssignedProducts
     * @param array $fixturesAssignedProducts
     * @param string $optionType
     * @return array
     */
    protected function getOptions(array $dataAssignedProducts, array $fixturesAssignedProducts, $optionType)
    {
        $result = [];
        foreach ($fixturesAssignedProducts as $productKey => $fixtureProduct) {
            $assignedProductPrice = $this->getAssignedProductPrice(
                $dataAssignedProducts[$productKey],
                $fixtureProduct,
                $optionType
            );
            $result[$productKey] = [
                'title' => $fixtureProduct->getName(),
                'price' => $this->getOptionPrice($assignedProductPrice)
            ];
        }

        return $result;
    }

    /**
     * Get assigned product price.
     *
     * @param array $dataAssignedProduct
     * @param InjectableFixture $fixtureAssignedProduct
     * @param string $optionType
     * @return float
     */
    protected function getAssignedProductPrice(
        array $dataAssignedProduct,
        InjectableFixture $fixtureAssignedProduct,
        $optionType
    ) {
        $resultPrice = $fixtureAssignedProduct->getPrice();
        if (isset($dataAssignedProduct['selection_price_value'])) {
            $assignedItemPrice = ($dataAssignedProduct['selection_price_type'] == 'Fixed')
                ? $dataAssignedProduct['selection_price_value']
                : $this->product->getPrice() * $dataAssignedProduct['selection_price_value'] / 100;
            $resultPrice = ($optionType == 'Drop-down' || $optionType == 'Radio Buttons')
                ? $assignedItemPrice
                : $assignedItemPrice * $dataAssignedProduct['selection_qty'];
        }

        return $resultPrice;
    }

    /**
     * Get item option price for item bundle selection.
     *
     * @param float $assignedProductPrice
     * @param string $customerGroup [optional]
     * @return string
     */
    protected function getOptionPrice($assignedProductPrice, $customerGroup = 'NOT LOGGED IN')
    {
        if ($this->product->hasData('group_price')) {
            $groupPrice = $this->product->getGroupPrice();
            $groupPriceType = array_search($customerGroup, $groupPrice);
            $assignedProductPrice -= $assignedProductPrice / 100 * $groupPrice[$groupPriceType]['price'];
        }
        $assignedProductPrice *= $this->product->hasData('special_price') ? $this->product->getSpecialPrice() / 100 : 1;

        return number_format($assignedProductPrice, 2);
    }

    /**
     * Return Text if displayed on frontend equals with fixture.
     *
     * @return string
     */
    public function toString()
    {
        return 'Bundle options data on product page equals to passed from fixture preset.';
    }
}
