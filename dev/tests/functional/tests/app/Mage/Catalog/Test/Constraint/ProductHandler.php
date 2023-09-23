<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Tests
 * @package    Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\Constraint;

use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Product handler.
 */
class ProductHandler
{
    /**
     * Get product sku.
     *
     * @param InjectableFixture $product
     * @return string
     */
    public function getProductSku(InjectableFixture $product)
    {
        return $product->getSku();
    }

    /**
     * Get product options.
     *
     * @param InjectableFixture $product
     * @return array
     */
    public function getProductOptions(InjectableFixture $product)
    {
        $options = $this->getCheckoutDataOptions($product, 'custom_options');
        $productOptions = $product->getCustomOptions();

        return (!empty($options) && !empty($productOptions)) ? $this->getOptions($options, $productOptions) : [];
    }

    /**
     * Get options.
     *
     * @param array $checkoutOptions
     * @param array $fixtureOptions
     * @param string $key [optional]
     * @param bool $prepareKeys [optional]
     * @return array
     */
    protected function getOptions(array $checkoutOptions, array $fixtureOptions, $key = 'title', $prepareKeys = true)
    {
        $result = [];
        foreach ($checkoutOptions as $option) {
            list($attributeKey, $optionKey) = $this->getOptionKeys($option, $prepareKeys);
            $result[] = [
                'title' => $fixtureOptions[$attributeKey][$key],
                'value' => $fixtureOptions[$attributeKey]['options'][$optionKey][$key]
            ];
        }

        return $result;
    }

    /**
     * Get option keys.
     *
     * @param array $option
     * @param bool $prepareKeys
     * @return array
     */
    protected function getOptionKeys(array $option, $prepareKeys)
    {
        return [
            $prepareKeys ? $this->getAttributeKey($option['title']) : $option['title'],
            $prepareKeys ? $this->getOptionKey($option['value']) : $option['value']
        ];
    }

    /**
     * Get attribute key.
     *
     * @param string $attributeKey
     * @return string
     */
    protected function getAttributeKey($attributeKey)
    {
        return $this->getKey($attributeKey, 'attribute_key');
    }

    /**
     * Get option key.
     *
     * @param string $optionKey
     * @return string
     */
    protected function getOptionKey($optionKey)
    {
        return $this->getKey($optionKey, 'option_key');
    }

    /**
     * Get key.
     *
     * @param string $key
     * @param string $replace
     * @return string
     */
    protected function getKey($key, $replace)
    {
        return str_replace($replace . '_', '', $key);
    }

    /**
     * Get checkout data options.
     *
     * @param InjectableFixture $product
     * @param string $optionKey
     * @return array
     */
    protected function getCheckoutDataOptions(InjectableFixture $product, $optionKey = '')
    {
        $checkoutData = $product->getCheckoutData();
        return ($optionKey == '')
            ? (isset($checkoutData['options']) ? $checkoutData['options'] : [])
            : (isset($checkoutData['options'][$optionKey]) ? $checkoutData['options'][$optionKey] : []);
    }
}
