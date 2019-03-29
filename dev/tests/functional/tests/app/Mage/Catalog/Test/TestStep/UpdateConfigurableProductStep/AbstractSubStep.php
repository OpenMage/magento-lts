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

namespace Mage\Catalog\Test\TestStep\UpdateConfigurableProductStep;

use Magento\Mtf\Fixture\InjectableFixture;
use Mage\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\ObjectManager;
use Mage\Catalog\Test\Fixture\ConfigurableProduct;
use Mage\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Mage\Catalog\Test\TestStep\UpdateConfigurableProductStep;
use Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab\Configurable as ConfigurableTab;
use Mage\Catalog\Test\Fixture\ConfigurableProduct\ConfigurableOptions;

/**
 * Abstract class for sub steps.
 */
abstract class AbstractSubStep
{
    /**
     * Fixture factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Object manager.
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Catalog product edit page.
     *
     * @var CatalogProductEdit
     */
    protected $catalogProductEdit;

    /**
     * Fixture original ConfigurableProduct.
     *
     * @var ConfigurableProduct
     */
    protected $product;

    /**
     * Configurable options edit data.
     *
     * @var array
     */
    protected $configurableOptionsEditData;

    /**
     * Current attributes fixture.
     *
     * @var CatalogProductAttribute[]
     */
    protected $currentAttributes = [];

    /**
     * Current assigned fixtures products.
     *
     * @var InjectableFixture[]
     */
    protected $currentAssignedProducts = [];

    /**
     * Current configurable options data.
     *
     * @var array
     */
    protected $currentConfigurableOptionsData = [];

    /**
     * Checkout data for result product.
     *
     * @var array
     */
    protected $checkoutData = [];

    /**
     * Return arguments from sub step.
     *
     * @return array
     */
    public abstract function returnArguments();

    /**
     * Run step flow.
     *
     * @return void
     */
    public abstract function run();

    /**
     * @constructor
     * @param ObjectManager $objectManager
     * @param FixtureFactory $fixtureFactory
     * @param CatalogProductEdit $catalogProductEdit
     * @param ConfigurableProduct $product
     * @param array $currentAssignedProducts
     * @param array $currentAttributes
     * @param array $currentConfigurableOptionsData
     * @param array $configurableOptionsEditData
     * @param array $checkoutData [optional]
     */
    public function __construct(
        ObjectManager $objectManager,
        FixtureFactory $fixtureFactory,
        CatalogProductEdit $catalogProductEdit,
        ConfigurableProduct $product,
        array $currentAssignedProducts,
        array $currentAttributes,
        array $currentConfigurableOptionsData,
        array $configurableOptionsEditData,
        array $checkoutData = []
    ) {
        $this->objectManager = $objectManager;
        $this->fixtureFactory = $fixtureFactory;
        $this->catalogProductEdit = $catalogProductEdit;
        $this->product = $product;
        $this->configurableOptionsEditData = $configurableOptionsEditData;
        $this->currentAssignedProducts = $currentAssignedProducts;
        $this->currentAttributes = $currentAttributes;
        $this->currentConfigurableOptionsData = $currentConfigurableOptionsData;
        $this->checkoutData = $checkoutData;
    }

    /**
     * Fill attributes data.
     *
     * @param array $attributes
     * @return void
     */
    protected function fillAttributes(array $attributes)
    {
        $this->getConfigurableProductTab()->fillAttributes($attributes);
    }

    /**
     * Select products.
     *
     * @param array $products
     * @return void
     */
    protected function selectProducts(array $products)
    {
        $filter = [];
        foreach ($products as $product) {
            $filter[] = $product->getSku();
        }
        $this->getConfigurableProductTab()->selectProducts($filter);
    }

    /**
     * Update configurable options.
     *
     * @param array $newData
     * @return void
     */
    protected function updateConfigurableOptionsData(array $newData)
    {
        foreach ($newData as $attributeKey => $itemAttribute) {
            foreach ($itemAttribute['options'] as $optionKey => $option) {
                $originalData = isset($this->currentConfigurableOptionsData[$attributeKey]['options'][$optionKey])
                    ? $this->currentConfigurableOptionsData[$attributeKey]['options'][$optionKey]
                    : [];
                $this->currentConfigurableOptionsData[$attributeKey]['options'][$optionKey] = array_merge(
                    $originalData,
                    $option
                );
            }
        }
    }

    /**
     * Get special tab for configurable product.
     *
     * @return ConfigurableTab
     */
    protected function getConfigurableProductTab()
    {
        return $this->catalogProductEdit->getProductForm()->getTabElement('configurable');
    }

    /**
     * Get assigned products from original configurable product.
     *
     * @return InjectableFixture[]
     */
    protected function getOriginalProductAssignedProducts()
    {
        return $this->getOriginalProductOptionsSource()->getProducts();
    }

    /**
     * Get configurable options source for original product.
     *
     * @return ConfigurableOptions
     */
    protected function getOriginalProductOptionsSource()
    {
        return $this->product->getDataFieldConfig('configurable_options')['source'];
    }
}
