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

namespace Mage\Catalog\Test\TestCase\Product;

use Mage\Catalog\Test\Fixture\CatalogCategory;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\ObjectManager;
use Magento\Mtf\TestCase\Injectable;

/**
* Preconditions:
 * 1. Create category.
 * 2. Create products according to dataset for with category created before.
 * 3. Apply configuration specified in dataset.
 *
 * Steps:
 * 1. Perform assertions.
 *
 * @group Products_(CS)
 * @ZephyrId MPERF-6965
 */
class ApplyMapTest extends Injectable
{
    /**
     * Create category for test.
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $category = $fixtureFactory->createByCode('catalogCategory', ['dataset' => 'default_subcategory']);
        $category->persist();

        return ['category' => $category];
    }

    /**
     * Run Apply MAP test.
     *
     * @param CatalogCategory $category
     * @param string $products
     * @param string $configData
     * @return array
     */
    public function test(CatalogCategory $category, $products, $configData)
    {
        // Preconditions:
        // Creating products:
        $products = $this->objectManager->create('Mage\Catalog\Test\TestStep\CreateProductsStep',
            [
                'products' => $products,
                'data' => $this->prepareProductData($category, $products)
            ]
        )->run()['products'];

        // Setup configuration:
        $this->objectManager->create('Mage\Core\Test\TestStep\SetupConfigurationStep',['configData' => $configData])
            ->run();

        return ['products' => $products, 'category' => $category];
    }

    /**
     * Prepare products data for persist.
     *
     * @param CatalogCategory $category
     * @param $products
     * @return array
     */
    protected function prepareProductData(CatalogCategory $category, $products)
    {
        $data = [];
        $productsCount = count(explode(',', $products));
        while ($productsCount > 0) {
            $data[] = ['category_ids' => ['category' => $category]];
            $productsCount--;
        }

        return $data;
    }

    /**
     * Rollback configuration after test variation.
     *
     * @return void
     */
    public function tearDown()
    {
        $this->objectManager->create(
            'Mage\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => $this->currentVariation['arguments']['configData'], 'rollback' => true]
        )->run();
    }
}
