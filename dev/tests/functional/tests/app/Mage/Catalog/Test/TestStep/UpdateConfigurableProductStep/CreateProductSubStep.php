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

namespace Mage\Catalog\Test\TestStep\UpdateConfigurableProductStep;

use Magento\Mtf\Fixture\InjectableFixture;
use Mage\Catalog\Test\Fixture\ConfigurableProduct\ConfigurableOptions;

/**
 * Create new product sub step.
 */
class CreateProductSubStep extends AbstractSubStep
{
    /**
     * Create new product.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->configurableOptionsEditData['createProduct'] as $createType => $itemCreate) {
            $data = $this->getCreationData($createType);
            $source = $this->createConfigurableOptionsSource($data);
            $newData = $source->getData('attributes_data');
            $newProducts = $source->getProducts();
            $this->$createType($newProducts);
            $this->fillAttributes($newData);
            $this->updateConfigurableOptionsData($newData);
            $this->updateAssignedProducts($newProducts);
        }
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
     * Create product via 'Create Empty' button.
     *
     * @param array $newProducts
     * @return void
     */
    protected function createEmpty(array $newProducts)
    {
        foreach ($newProducts as $product) {
            $this->getConfigurableProductTab()->getSimpleAssociatedProductBlock()->clickCreateEmpty();
            $this->fillNewProduct($product);
        }
    }

    /**
     * Create product via 'Copy From Configurable' button.
     *
     * @param array $newProducts
     * @return void
     */
    protected function copyFromConfigurable(array $newProducts)
    {
        foreach ($newProducts as $product) {
            $this->getConfigurableProductTab()->getSimpleAssociatedProductBlock()->clickCopyFromConfigurable();
            $this->fillNewProduct($product);
        }
    }

    /**
     * Quick creation products.
     *
     * @param array $newProducts
     * @return void
     */
    protected function quickCreation(array $newProducts)
    {
        foreach ($newProducts as $product) {
            $this->getConfigurableProductTab()->getQuickCreationBlock()->create($product);
        }
    }

    /**
     * Fill new product.
     *
     * @param InjectableFixture $product
     * @return void
     */
    protected function fillNewProduct(InjectableFixture $product)
    {
        $this->getConfigurableProductTab()->getNewProductPopup()->fill($product);
        $this->getConfigurableProductTab()->getNewProductPopup()->getFormPageActions()->save();
        $this->getConfigurableProductTab()->getNewProductPopup()->close();
    }

    /**
     * Get creation data.
     *
     * @param string $creationType
     * @return array
     */
    protected function getCreationData($creationType)
    {
        return [
            'dataset' => $this->configurableOptionsEditData['createProduct'][$creationType]['dataset'],
            'data' => [
                'attributes_data' => [
                    'attributes' => $this->currentAttributes,
                    'attributeSet' => $this->getOriginalProductOptionsSource()->getAttributeSet()
                ],
            ]
        ];
    }

    /**
     * Update assigned products.
     *
     * @param array $newProducts
     * @return void
     */
    protected function updateAssignedProducts(array $newProducts)
    {
        $this->currentAssignedProducts = array_merge($this->currentAssignedProducts, $newProducts);
    }

    /**
     * Create configurable options source.
     *
     * @param array $data
     * @return ConfigurableOptions
     */
    protected function createConfigurableOptionsSource(array $data)
    {
        return $this->objectManager->create(
            'Mage\Catalog\Test\Fixture\ConfigurableProduct\ConfigurableOptions',
            [
                'data' => $data,
                'params' => [
                    'is_required' => '0',
                    'group' => 'configurable',
                    'source' => 'Mage\Catalog\Test\Fixture\ConfigurableProduct\ConfigurableOptions',
                    'repository' => 'Mage\Catalog\Test\Repository\CatalogProductConfigurable\ConfigurableOptions'
                ]
            ]
        );
    }
}
