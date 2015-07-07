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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\TestStep\UpdateConfigurableProductStep;

use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Add options sub step.
 */
class AddOptionsSubStep extends AbstractSubStep
{
    /**
     * Add configurable options.
     *
     * @return void
     */
    public function run()
    {
        $newProducts = $this->prepareNewProducts();
        $this->selectProducts($newProducts);
        $configurableOptionsData = $this->prepareOptions();
        $this->updateConfigurableOptionsData($configurableOptionsData);
        $this->fillAttributes($this->currentConfigurableOptionsData);
    }

    /**
     * Return arguments from sub step.
     *
     * @return array
     */
    public function returnArguments()
    {
        return [
            'currentAssignedProducts' => $this->currentAssignedProducts,
            'currentConfigurableOptionsData' => $this->currentConfigurableOptionsData
        ];
    }

    /**
     * Prepare new products.
     *
     * @return array
     */
    protected function prepareNewProducts()
    {
        $newProducts = [];
        foreach ($this->configurableOptionsEditData['addOptions'] as $addOption) {
            $productKey = $this->getProductKeyByOptionIndex($addOption['optionIndex']);
            $newProducts[$productKey] = $this->createNewProduct($addOption);
        }
        $this->currentAssignedProducts = array_merge($this->currentAssignedProducts, $newProducts);

        return $newProducts;
    }

    /**
     * Get product key by option index.
     *
     * @param int $optionIndex
     * @return string
     */
    protected function getProductKeyByOptionIndex($optionIndex)
    {
        return "attribute_key_0:option_key_$optionIndex " . "attribute_key_1:option_key_$optionIndex";
    }

    /**
     * Create new product.
     *
     * @param array $data
     * @return InjectableFixture
     */
    protected function createNewProduct(array $data)
    {
        list($fixtureClass, $dataSet) = explode('::', $data['product']);
        $productData = $this->prepareProductCreateData($data);
        $product = $this->fixtureFactory->createByCode($fixtureClass, ['dataSet' => $dataSet, 'data' => $productData]);
        $product->persist();

        return $product;
    }

    /**
     * Prepare product create data.
     *
     * @param array $data
     * @return array
     */
    protected function prepareProductCreateData(array $data)
    {
        $attributeSet = $this->getOriginalProductOptionsSource()->getAttributeSet();
        $value = [];
        foreach ($this->currentAttributes as $attribute) {
            $value[$attribute->getAttributeCode()] = $attribute->getOptions()[$data['optionIndex']]['id'];
        }

        return [
            'attribute_set_id' => ['attribute_set' => $attributeSet],
            'attributes' => [
                'value' => $value
            ]
        ];
    }

    /**
     * Prepare options data.
     *
     * @return array
     */
    protected function prepareOptions()
    {
        $result = [];
        $originalData = $this->product->getConfigurableOptions();
        foreach ($this->configurableOptionsEditData['addOptions'] as $option) {
            foreach ($option['data'] as $attributeKey => $itemData) {
                $optionKey = 'option_key_' . $option['optionIndex'];
                $originalOptionData = isset($originalData['attributes_data'][$attributeKey]['options'][$optionKey])
                    ? $originalData['attributes_data'][$attributeKey]['options'][$optionKey]
                    : [];
                $result[$attributeKey]['options'][$optionKey] = array_merge($originalOptionData, $itemData['value']);
            }
        }

        return $result;
    }
}
