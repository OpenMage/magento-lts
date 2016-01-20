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

/**
 * Assert that displayed product data on product page(front-end) equals passed from fixture.
 */
class AssertConfigurableProductPage extends AssertProductPage
{
    /* tags */
    const SEVERITY = 'middle';
    /* end tags */

    /**
     * Verify displayed product data on product page(front-end) equals passed from fixture:
     * 1. Product Name
     * 2. Price
     * 3. SKU
     * 4. Description
     * 5. Short Description
     * 6. Attributes
     *
     * @return array
     */
    protected function verify()
    {
        $errors = parent::verify();
        $errors[] = $this->verifyAttributes();

        return array_filter($errors);
    }

    /**
     * Verify displayed product attributes on product page(front-end) equals passed from fixture.
     *
     * @return string|null
     */
    protected function verifyAttributes()
    {
        $formOptions = $this->sortOptions($this->productView->getOptions($this->product)['configurable_options']);
        $fixtureOptions = $this->prepareFixtureOptions();
        $errors = $this->verifyData($fixtureOptions, $formOptions, true, false);
        return empty($errors) ? null : $this->prepareErrors($errors, 'Error configurable options:');
    }

    /**
     * Prepare fixture options data.
     *
     * @return array
     */
    protected function prepareFixtureOptions()
    {
        $configurableOptions = [];
        $attributesData = $this->product->getConfigurableOptions()['attributes_data'];
        $countAttributes = count($attributesData);
        for ($i = 0; $i < $countAttributes; $i++) {
            $attributeKey = 'attribute_key_' . $i;
            $configurableOptions[$attributesData[$attributeKey]['frontend_label']] = [
                'title' => $attributesData[$attributeKey]['frontend_label'],
                'type' => $attributesData[$attributeKey]['frontend_input'],
                'is_require' => 'Yes',
                'options' => $this->getOptionsData($attributesData[$attributeKey]['options'])
            ];
        }

        return $this->sortOptions($configurableOptions);
    }

    /**
     * Get options data.
     *
     * @param array $options
     * @return array
     */
    protected function getOptionsData(array $options)
    {
        $optionsData = [];
        foreach ($options as $option) {
            $optionsData[] = [
                'title' => $option['label'],
                'price' => $this->getOptionPrice($option)
            ];
        }

        return $optionsData;
    }

    /**
     * Options sort.
     *
     * @param array $options
     * @return array
     */
    protected function sortOptions(array $options)
    {
        $options = $this->sortDataByPath($options, '::title');
        foreach ($options as $key => $option) {
            $options[$key] = $this->sortDataByPath($option, 'options::title');
        }

        return $options;
    }

    /**
     * Get price for option.
     *
     * @param array $optionData
     * @return string
     */
    protected function getOptionPrice(array $optionData)
    {
        $price = ('Percentage' == $optionData['price_type'])
            ? ($this->product->getPrice() * $optionData['price']) / 100
            : $optionData['price'];

        return number_format($price, 2);
    }
}
