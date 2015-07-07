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

namespace Mage\Catalog\Test\TestCase\Layer;

use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\ObjectManager;
use Magento\Mtf\TestCase\Injectable;
use Mage\Catalog\Test\Fixture\CatalogCategory;

/**
 * Preconditions:
 * 1. Configure native search engine.
 * 2. Set up Layer Navigation for Price Navigation Step Calculation = Manual.
 * 3. Create anchored category.
 * 4. Create products and assigned to this category.
 *
 * Steps:
 * 1. Perform assert.
 *
 * @group layered-navigation_(CS)
 * @ZephyrId MPERF-6959
 */
class FilterProductListTest extends Injectable
{
    /**
     * Category fixture.
     *
     * @var CatalogCategory
     */
    protected $category;

    /**
     * Injection data.
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __inject(FixtureFactory $fixtureFactory)
    {
        $this->category = $fixtureFactory->createByCode('catalogCategory', ['dataSet' => 'anchor_category']);
        $this->category->persist();

        return ['category' => $this->category];
    }

    /**
     * Run filter product list test.
     *
     * @param string $products
     * @return array
     */
    public function test($products)
    {
        // Precondition
        $this->setupConfigData();
        return $this->createProducts($products);
    }

    /**
     * Create products.
     *
     * @param string $products
     * @return array
     */
    protected function createProducts($products)
    {
        return $this->objectManager->create(
            'Mage\Catalog\Test\TestStep\CreateProductsStep',
            ['products' => $products, 'data' => $this->prepareProductsData($products)]
        )->run();
    }

    /**
     * Prepare products data.
     *
     * @param string $products
     * @return array
     */
    protected function prepareProductsData($products)
    {
        $resultData = [];
        $productsCount = substr_count($products, ',');
        for ($i = 0; $i <= $productsCount; $i++) {
            $resultData[] = ['category_ids' => ['category' => $this->category]];
        }

        return $resultData;
    }

    /**
     * Setup configuration.
     *
     * @param bool $rollback [optional]
     * @return void
     */
    protected function setupConfigData($rollback = false)
    {
        $this->objectManager->create(
            'Mage\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => 'manual_layered_navigation_mysql', 'rollback' => $rollback]
        )->run();
    }

    /**
     * Rollback configuration after test.
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        ObjectManager::getInstance()->create(
            'Mage\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => 'manual_layered_navigation_mysql', 'rollback' => true]
        )->run();
    }
}
