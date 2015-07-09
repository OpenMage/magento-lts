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

namespace Mage\Bundle\Test\Fixture\BundleProduct;

use Magento\Mtf\ObjectManager;
use Magento\Mtf\Fixture\FixtureInterface;

/**
 * Bundle selections preset.
 */
class BundleSelections implements FixtureInterface
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
     * Array products' fixtures.
     *
     * @var array
     */
    protected $products;

    /**
     * @constructor
     * @param ObjectManager $objectManager
     * @param array $data
     * @param array $params [optional]
     */
    public function __construct(ObjectManager $objectManager, array $data, array $params = [])
    {
        $this->params = $params;
        $this->objectManager = $objectManager;
        $this->data = $this->prepareData($data);
    }

    /**
     * Prepare preset data.
     *
     * @param array $data
     * @return array
     */
    protected function prepareData(array $data)
    {
        $preset = isset($data['preset']) ? $this->getPreset($data['preset']) : [];
        $this->products = isset($preset['products']) ? $this->createProducts($preset['products']) : [];
        return $this->prepareBundleOptions($preset['bundle_options']);
    }

    /**
     * Prepare bundle options.
     *
     * @param array $data
     * @return array
     */
    protected function prepareBundleOptions(array $data)
    {
        foreach ($data as $optionKey => $bundleOption) {
            foreach ($bundleOption['assigned_products'] as $key => $assignedProduct) {
                $data[$optionKey]['assigned_products'][$key]['sku'] = $this->products[$optionKey][$key]->getSku();
                $data[$optionKey]['assigned_products'][$key]['name'] = $this->products[$optionKey][$key]->getName();
            }
        }

        return $data;
    }

    /**
     * Create products.
     *
     * @param array $products
     * @return array
     */
    protected function createProducts(array $products)
    {
        $resultProduct = [];
        foreach ($products as $key => $product) {
            $resultProduct[$key] = $this->objectManager
                ->create('Mage\Catalog\Test\TestStep\CreateProductsStep', ['products' => implode(',', $product)])
                ->run()['products'];
        }

        return $resultProduct;
    }

    /**
     * Persist bundle selections products.
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
     * @param string $key [optional]
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = null)
    {
        return $this->data;
    }

    /**
     * Return data set configuration settings.
     *
     * @return string
     */
    public function getDataConfig()
    {
        return $this->params;
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
     * Get preset from array of presets.
     *
     * @param string $name
     * @return array
     * @throws \InvalidArgumentException
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getPreset($name)
    {
        $presets = [
            'default_dynamic' => [
                'bundle_options' => [
                    [
                        'title' => 'Drop-down Option',
                        'type' => 'Drop-down',
                        'required' => 'Yes',
                        'assigned_products' => [
                            [
                                'sku' => '%product_sku%',
                                'selection_qty' => 2,
                                'selection_can_change_qty' => 'Yes'
                            ],
                            [
                                'sku' => '%product_sku%',
                                'selection_qty' => 3,
                                'selection_can_change_qty' => 'Yes'
                            ]
                        ]
                    ]
                ],
                'products' => [
                    [
                        'catalogProductSimple::default',
                        'catalogProductSimple::50_dollar_product'
                    ]
                ]
            ],
            'default_fixed' => [
                'bundle_options' => [
                    [
                        'title' => 'Drop-down Option',
                        'type' => 'Drop-down',
                        'required' => 'Yes',
                        'assigned_products' => [
                            [
                                'sku' => '%product_sku%',
                                'selection_price_value' => 5.00,
                                'selection_price_type' => 'Fixed',
                                'selection_qty' => 2,
                                'selection_can_change_qty' => 'Yes'
                            ],
                            [
                                'sku' => '%product_sku%',
                                'selection_price_value' => 6.00,
                                'selection_price_type' => 'Fixed',
                                'selection_qty' => 3,
                                'selection_can_change_qty' => 'Yes'
                            ]
                        ]
                    ]
                ],
                'products' => [
                    [
                        'catalogProductSimple::default',
                        'catalogProductSimple::50_dollar_product',
                    ]
                ]
            ],
            'all_types_fixed' => [
                'bundle_options' => [
                    [
                        'title' => 'Drop-down Option',
                        'type' => 'Drop-down',
                        'required' => 'Yes',
                        'assigned_products' => [
                            [
                                'sku' => '%product_sku%',
                                'selection_price_value' => 5.00,
                                'selection_price_type' => 'Fixed',
                                'selection_qty' => 2,
                                'selection_can_change_qty' => 'Yes'
                            ],
                            [
                                'sku' => '%product_sku%',
                                'selection_price_value' => 6.00,
                                'selection_price_type' => 'Fixed',
                                'selection_qty' => 3,
                                'selection_can_change_qty' => 'Yes'
                            ]
                        ]
                    ],
                    [
                        'title' => 'Radio Button Option',
                        'type' => 'Radio Buttons',
                        'required' => 'Yes',
                        'assigned_products' => [
                            [
                                'sku' => '%product_sku%',
                                'selection_price_value' => 5.00,
                                'selection_price_type' => 'Fixed',
                                'selection_qty' => 2,
                                'selection_can_change_qty' => 'Yes'
                            ],
                            [
                                'sku' => '%product_sku%',
                                'selection_price_value' => 6.00,
                                'selection_price_type' => 'Fixed',
                                'selection_qty' => 3,
                                'selection_can_change_qty' => 'Yes'

                            ]
                        ]
                    ],
                    [
                        'title' => 'Checkbox Option',
                        'type' => 'Checkbox',
                        'required' => 'Yes',
                        'assigned_products' => [
                            [
                                'sku' => '%product_sku%',
                                'selection_price_value' => 5.00,
                                'selection_price_type' => 'Fixed',
                                'selection_qty' => 2,
                                'selection_can_change_qty' => 'Yes'
                            ],
                            [
                                'sku' => '%product_sku%',
                                'selection_price_value' => 6.00,
                                'selection_price_type' => 'Fixed',
                                'selection_qty' => 3,
                                'selection_can_change_qty' => 'Yes'
                            ]
                        ]
                    ],
                    [
                        'title' => 'Multiple Select Option',
                        'type' => 'Multiple Select',
                        'required' => 'Yes',
                        'assigned_products' => [
                            [
                                'sku' => '%product_sku%',
                                'selection_price_value' => 5.00,
                                'selection_price_type' => 'Fixed',
                                'selection_qty' => 2,
                                'selection_can_change_qty' => 'Yes'
                            ],
                            [
                                'sku' => '%product_sku%',
                                'selection_price_value' => 6.00,
                                'selection_price_type' => 'Fixed',
                                'selection_qty' => 3,
                                'selection_can_change_qty' => 'Yes'
                            ]
                        ]
                    ]
                ],
                'products' => [
                    [
                        'catalogProductSimple::default',
                        'catalogProductSimple::50_dollar_product',
                    ],
                    [
                        'catalogProductSimple::default',
                        'catalogProductSimple::50_dollar_product'
                    ],
                    [
                        'catalogProductSimple::default',
                        'catalogProductSimple::50_dollar_product'
                    ],
                    [
                        'catalogProductSimple::default',
                        'catalogProductSimple::50_dollar_product'
                    ]
                ]
            ],
            'all_types_dynamic' => [
                'bundle_options' => [
                    [
                        'title' => 'Drop-down Option',
                        'type' => 'Drop-down',
                        'required' => 'Yes',
                        'assigned_products' => [
                            [
                                'sku' => '%product_sku%',
                                'selection_qty' => 2,
                                'selection_can_change_qty' => 'Yes'
                            ],
                            [
                                'sku' => '%product_sku%',
                                'selection_qty' => 3,
                                'selection_can_change_qty' => 'Yes'
                            ]
                        ]
                    ],
                    [
                        'title' => 'Radio Button Option',
                        'type' => 'Radio Buttons',
                        'required' => 'Yes',
                        'assigned_products' => [
                            [
                                'sku' => '%product_sku%',
                                'selection_qty' => 2,
                                'selection_can_change_qty' => 'Yes'
                            ],
                            [
                                'sku' => '%product_sku%',
                                'selection_qty' => 3,
                                'selection_can_change_qty' => 'Yes'
                            ]
                        ]
                    ],
                    [
                        'title' => 'Checkbox Option',
                        'type' => 'Checkbox',
                        'required' => 'Yes',
                        'assigned_products' => [
                            [
                                'sku' => '%product_sku%',
                                'selection_qty' => 2,
                                'selection_can_change_qty' => 'Yes'
                            ],
                            [
                                'sku' => '%product_sku%',
                                'selection_qty' => 3,
                                'selection_can_change_qty' => 'Yes'
                            ]
                        ]
                    ],
                    [
                        'title' => 'Multiple Select Option',
                        'type' => 'Multiple Select',
                        'required' => 'Yes',
                        'assigned_products' => [
                            [
                                'sku' => '%product_sku%',
                                'selection_qty' => 2,
                                'selection_can_change_qty' => 'Yes'
                            ],
                            [
                                'sku' => '%product_sku%',
                                'selection_qty' => 3,
                                'selection_can_change_qty' => 'Yes'
                            ]
                        ]
                    ]
                ],
                'products' => [
                    [
                        'catalogProductSimple::default',
                        'catalogProductSimple::50_dollar_product',
                    ],
                    [
                        'catalogProductSimple::default',
                        'catalogProductSimple::50_dollar_product'
                    ],
                    [
                        'catalogProductSimple::default',
                        'catalogProductSimple::50_dollar_product'
                    ],
                    [
                        'catalogProductSimple::default',
                        'catalogProductSimple::50_dollar_product'
                    ]
                ]
            ],
            'with_not_required_options' => [
                'bundle_options' => [
                    [
                        'title' => 'Drop-down Option',
                        'type' => 'Drop-down',
                        'required' => 'No',
                        'assigned_products' => [
                            [
                                'sku' => '%product_sku%',
                                'selection_qty' => 2,
                                'selection_price_value' => 45,
                                'selection_price_type' => 'Fixed',
                                'selection_can_change_qty' => 'Yes'
                            ],
                            [
                                'sku' => '%product_sku%',
                                'selection_qty' => 3,
                                'selection_price_value' => 43,
                                'selection_price_type' => 'Fixed',
                                'selection_can_change_qty' => 'Yes'
                            ]
                        ]
                    ],
                    [
                        'title' => 'Radio Button Option',
                        'type' => 'Radio Buttons',
                        'required' => 'No',
                        'assigned_products' => [
                            [
                                'sku' => '%product_sku%',
                                'selection_qty' => 2,
                                'selection_price_value' => 45,
                                'selection_price_type' => 'Fixed',
                                'selection_can_change_qty' => 'Yes'
                            ],
                            [
                                'sku' => '%product_sku%',
                                'selection_qty' => 3,
                                'selection_price_value' => 43,
                                'selection_price_type' => 'Fixed',
                                'selection_can_change_qty' => 'Yes'
                            ]
                        ]
                    ]
                ],
                'products' => [
                    [
                        'catalogProductSimple::default',
                        'catalogProductSimple::50_dollar_product',
                    ],
                    [
                        'catalogProductSimple::default',
                        'catalogProductSimple::50_dollar_product'
                    ]
                ]
            ]
        ];
        if (!isset($presets[$name])) {
            throw new \InvalidArgumentException(
                sprintf('Wrong Bundle Selections preset name: %s', $name)
            );
        }
        return $presets[$name];
    }
}
