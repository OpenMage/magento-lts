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

namespace Mage\Bundle\Test\Block\Catalog\Product;

use Mage\Bundle\Test\Block\Catalog\Product\View\Type\Bundle;
use Mage\Bundle\Test\Fixture\BundleProduct;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Bundle product view block on the product page.
 */
class View extends \Mage\Catalog\Test\Block\Product\View
{
    /**
     * Bundle options block
     *
     * @var string
     */
    protected $bundleBlock = '//*[@id="product-options-wrapper"]';

    /**
     * Get block price.
     *
     * @return Price
     */
    public function getPriceBlock()
    {
        return $this->blockFactory->create(
            'Mage\Bundle\Test\Block\Catalog\Product\Price',
            ['element' => $this->_rootElement->find($this->priceBlock, Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Get bundle options block.
     *
     * @return Bundle
     */
    public function getBundleBlock()
    {
        return $this->blockFactory->create(
            'Mage\Bundle\Test\Block\Catalog\Product\View\Type\Bundle',
            ['element' => $this->_rootElement->find($this->bundleBlock, Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Return product options.
     *
     * @param InjectableFixture $product [optional]
     * @return array
     */
    public function getOptions(InjectableFixture $product = null)
    {
        $options['bundle_options'] = $this->getBundleBlock()->getOptions($product);
        $options += parent::getOptions($product);

        return $options;
    }

    /**
     * Fill in the option specified for the product.
     *
     * @param InjectableFixture $product
     * @return void
     */
    public function fillOptions(InjectableFixture $product)
    {
        /** @var BundleProduct $product */
        $bundleCheckoutData = $this->prepareBundleCheckoutData($product);
        $this->getBundleBlock()->fillBundleOptions($bundleCheckoutData);
    }

    /**
     * Prepare checkout data for fill bundle options.
     *
     * @param BundleProduct $product
     * @return array
     */
    protected function prepareBundleCheckoutData(BundleProduct $product)
    {
        $assignedProducts = $product->getDataFieldConfig('bundle_selections')['source']->getProducts();
        $bundleOptions = $product->getBundleSelections();
        $checkoutData = $product->getCheckoutData();
        $checkoutData = isset($checkoutData['options']['bundle_options'])
            ? $checkoutData['options']['bundle_options']
            : [];

        foreach ($checkoutData as $optionKey => $option) {
            $optionIndex = str_replace('option_key_', '', $optionKey);
            $names = explode(',', $checkoutData[$optionKey]['value']['name']);
            $checkoutData[$optionKey]['title'] = $bundleOptions[$optionIndex]['title'];
            $checkoutData[$optionKey]['type'] = $bundleOptions[$optionIndex]['type'];
            $checkoutData[$optionKey]['value']['name'] = $this->prepareOptionValue(
                $names,
                $assignedProducts[$optionIndex]
            );
        }

        return $checkoutData;
    }

    /**
     * Prepare option value.
     *
     * @param array $values
     * @param array $assignedProducts
     * @return mixed
     */
    protected function prepareOptionValue(array $values, array $assignedProducts)
    {
        return (count($values) > 1)
            ? $this->prepareOptionMultiValue($values, $assignedProducts)
            : $this->prepareOptionSimpleValue($values[0], $assignedProducts);
    }

    /**
     * Prepare option simple value.
     *
     * @param string $value
     * @param array $assignedProducts
     * @return string
     */
    protected function prepareOptionSimpleValue($value, array $assignedProducts)
    {
        $productIndex = str_replace('product_key_', '', $value);
        return $assignedProducts[$productIndex]->getName();
    }

    /**
     * Prepare option multilpe value.
     *
     * @param array $values
     * @param array $assignedProducts
     * @return array
     */
    protected function prepareOptionMultiValue(array $values, array $assignedProducts)
    {
        $optionValues = [];
        foreach ($values as $key => $value) {
            $optionValues[$key] = $this->prepareOptionSimpleValue($value, $assignedProducts);
        }

        return $optionValues;
    }

    /**
     * Get text of Stock Availability control.
     *
     * @return string
     */
    public function getBundleStockAvailability()
    {
        return strtolower($this->_rootElement->find($this->stockAvailability)->getText());
    }
}
