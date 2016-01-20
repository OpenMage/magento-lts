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

namespace Mage\Catalog\Test\Fixture\GroupedProduct;

use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\ObjectManager;

/**
 * Grouped associated products preset.
 */
class Associated implements FixtureInterface
{
    /**
     * Prepared dataSet data.
     *
     * @var array
     */
    protected $data;

    /**
     * Data set configuration settings.
     *
     * @var array
     */
    protected $params;

    /**
     * Object manager.
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @constructor
     * @param ObjectManager $objectManager
     * @param array $data
     * @param array $params [optional]
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function __construct(ObjectManager $objectManager, array $data, array $params = [])
    {
        $this->objectManager = $objectManager;
        $this->params = $params;
        $preset = $this->getPreset($data['preset']);
        if ($preset) {
            $this->products = $this->createProducts($preset['products'])['products'];
            foreach ($this->products as $key => $product) {
                $this->data[] =
                    [
                        'id' => $product->getId(),
                        'name' => $product->getName(),
                        'qty' => $preset['assigned_products'][$key]['qty'],
                        'position' => $key + 1
                    ];
            }
        }
    }

    /**
     * Create products.
     *
     * @param string $products
     * @return array
     */
    protected function createProducts($products)
    {
        return $this->objectManager->create('Mage\Catalog\Test\TestStep\CreateProductsStep', ['products' => $products])
            ->run();
    }

    /**
     * Persists associated products preset.
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Return prepared data set.
     *
     * @param string|null $key
     * @return array|null
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = null)
    {
        return $this->data;
    }

    /**
     * Return products' fixtures.
     *
     * @return array
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Return data set configuration settings.
     *
     * @return array
     */
    public function getDataConfig()
    {
        return $this->params;
    }

    /**
     * Preset array.
     *
     * @param string $name
     * @return array|null
     */
    protected function getPreset($name)
    {
        $presets = [
            'defaultSimpleProducts' => [
                'assigned_products' => [
                    [
                        'qty' => 3
                    ],
                    [
                        'qty' => 2
                    ]
                ],
                'products' => 'catalogProductSimple::default, catalogProductSimple::100_dollar_product',
            ],
            'defaultSimpleProduct_with_specialPrice' => [
                'assigned_products' => [
                    [
                        'qty' => 2
                    ],
                    [
                        'qty' => 4
                    ]
                ],
                'products' => 'catalogProductSimple::product_with_special_price_and_category,'
                    . 'catalogProductSimple::product_with_special_price_and_category',
            ],
            'defaultSimpleProduct_with_groupPrice' => [
                'assigned_products' => [
                    [
                        'qty' => 3
                    ],
                    [
                        'qty' => 4
                    ]
                ],
                'products' =>
                    'catalogProductSimple::simple_with_group_price, catalogProductSimple::simple_with_group_price',
            ],
            'defaultSimpleProduct_with_tierPrice' => [
                'assigned_products' => [
                    [
                        'qty' => 4
                    ],
                    [
                        'qty' => 3
                    ]
                ],
                'products' =>
                    'catalogProductSimple::simple_with_tier_price, catalogProductSimple::simple_with_tier_price',
            ],
            'defaultVirtualProducts' => [
                'assigned_products' => [
                    [
                        'qty' => 4
                    ],
                    [
                        'qty' => 2
                    ]
                ],
                'products' => 'catalogProductVirtual::order_default, catalogProductVirtual::order_default',
            ],
            'three_simple_products' => [
                'assigned_products' => [
                    [
                        'qty' => 17
                    ],
                    [
                        'qty' => 36
                    ],
                    [
                        'qty' => 20
                    ],
                ],
                'products' => 'catalogProductSimple::default, catalogProductSimple::default,'
                    . 'catalogProductSimple::100_dollar_product',
            ],
        ];
        if (!isset($presets[$name])) {
            return null;
        }
        return $presets[$name];
    }
}
