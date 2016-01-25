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

namespace Mage\Catalog\Test\Fixture\ConfigurableProduct;

use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\Fixture\FixtureInterface;
use Mage\Catalog\Test\Fixture\CatalogAttributeSet;
use Mage\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Source configurable options of the configurable products.
 */
class ConfigurableOptions implements FixtureInterface
{
    /**
     * Fixture factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Data set configuration settings.
     *
     * @var array
     */
    protected $params;

    /**
     * Prepared dataSet data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Prepared products.
     *
     * @var array
     */
    protected $products = [];

    /**
     * Attributes data array.
     *
     * @var array
     */
    protected $attributesData = [];

    /**
     * Presets data.
     *
     * @var array
     */
    protected $presets = [
        'default' => [
            'attributeSet' => ['dataSet' => 'custom_attribute_set'],
            'attributes_data' => [
                'attribute_key_0' => [
                    'options' => [
                        'option_key_0' => [
                            'price' => 12,
                            'price_type' => 'Percentage'
                        ],
                        'option_key_1' => [
                            'price' => 20,
                            'price_type' => 'Percentage'
                        ],
                        'option_key_2' => [
                            'price' => 18,
                            'price_type' => 'Percentage'
                        ],
                    ]
                ],
                'attribute_key_1' => [
                    'options' => [
                        'option_key_0' => [
                            'price' => 42.00,
                            'price_type' => 'Fixed'
                        ],
                        'option_key_1' => [
                            'price' => 40.00,
                            'price_type' => 'Fixed'
                        ],
                        'option_key_2' => [
                            'price' => 48.00,
                            'price_type' => 'Fixed'
                        ],
                    ]
                ]
            ],
            'products' => [
                'attribute_key_0:option_key_0 attribute_key_1:option_key_0' => 'catalogProductSimple::default',
                'attribute_key_0:option_key_1 attribute_key_1:option_key_1' => 'catalogProductSimple::default',
                'attribute_key_0:option_key_2 attribute_key_1:option_key_2' => 'catalogProductSimple::default',
            ]
        ],
        'with_filterable_options' => [
            'attributeSet' => ['dataSet' => 'with_filterable_options'],
            'attributes_data' => [
                'attribute_key_0' => [
                    'options' => [
                        'option_key_0' => [
                            'price' => 12,
                            'price_type' => 'Percentage'
                        ],
                        'option_key_1' => [
                            'price' => 20,
                            'price_type' => 'Percentage'
                        ],
                        'option_key_2' => [
                            'price' => 18,
                            'price_type' => 'Percentage'
                        ],
                    ]
                ],
                'attribute_key_1' => [
                    'options' => [
                        'option_key_0' => [
                            'price' => 42.00,
                            'price_type' => 'Fixed'
                        ],
                        'option_key_1' => [
                            'price' => 40.00,
                            'price_type' => 'Fixed'
                        ],
                        'option_key_2' => [
                            'price' => 48.00,
                            'price_type' => 'Fixed'
                        ],
                    ]
                ]
            ],
            'products' => [
                'attribute_key_0:option_key_0 attribute_key_1:option_key_0' => 'catalogProductSimple::default',
                'attribute_key_0:option_key_1 attribute_key_1:option_key_1' => 'catalogProductSimple::default',
                'attribute_key_0:option_key_2 attribute_key_1:option_key_2' => 'catalogProductSimple::default',
            ]
        ],
        'quickCreation' => [
            'attributes_data' => [
                'attribute_key_0' => [
                    'options' => [
                        'option_key_0' => [
                            'price' => 121,
                            'price_type' => 'Percentage'
                        ]
                    ]
                ],
                'attribute_key_1' => [
                    'options' => [
                        'option_key_0' => [
                            'price' => 412.00,
                            'price_type' => 'Fixed'
                        ]
                    ]
                ]
            ],
            'products' => [
                'attribute_key_0:option_key_0 attribute_key_1:option_key_0' => 'catalogProductSimple::quickCreation'
            ]
        ],
        'createEmpty' => [
            'attributes_data' => [
                'attribute_key_0' => [
                    'options' => [
                        'option_key_1' => [
                            'price' => 11,
                            'price_type' => 'Percentage'
                        ]
                    ]
                ],
                'attribute_key_1' => [
                    'options' => [
                        'option_key_1' => [
                            'price' => 76.00,
                            'price_type' => 'Fixed'
                        ]
                    ]
                ]
            ],
            'products' => [
                'attribute_key_0:option_key_1 attribute_key_1:option_key_1' => 'catalogProductSimple::withoutPersist'
            ]
        ],
        'copyFromConfigurable' => [
            'attributes_data' => [
                'attribute_key_0' => [
                    'options' => [
                        'option_key_2' => [
                            'price' => 33,
                            'price_type' => 'Percentage'
                        ]
                    ]
                ],
                'attribute_key_1' => [
                    'options' => [
                        'option_key_2' => [
                            'price' => 62.00,
                            'price_type' => 'Fixed'
                        ]
                    ]
                ]
            ],
            'products' => [
                'attribute_key_0:option_key_2 attribute_key_1:option_key_2' => 'catalogProductSimple::withoutPersist'
            ]
        ],
        'with_out_of_stock_items' => [
            'attributeSet' => ['dataSet' => 'custom_attribute_set'],
            'attributes_data' => [
                'attribute_key_0' => [
                    'options' => [
                        'option_key_0' => [
                            'price' => 12,
                            'price_type' => 'Percentage'
                        ],
                        'option_key_1' => [
                            'price' => 20,
                            'price_type' => 'Percentage'
                        ],
                        'option_key_2' => [
                            'price' => 18,
                            'price_type' => 'Percentage'
                        ],
                    ]
                ],
                'attribute_key_1' => [
                    'options' => [
                        'option_key_0' => [
                            'price' => 42.00,
                            'price_type' => 'Fixed'
                        ],
                        'option_key_1' => [
                            'price' => 40.00,
                            'price_type' => 'Fixed'
                        ],
                        'option_key_2' => [
                            'price' => 48.00,
                            'price_type' => 'Fixed'
                        ],
                    ]
                ]
            ],
            'products' => [
                'attribute_key_0:option_key_0 attribute_key_1:option_key_0' => 'catalogProductSimple::out_of_stock',
                'attribute_key_0:option_key_1 attribute_key_1:option_key_1' => 'catalogProductSimple::out_of_stock',
                'attribute_key_0:option_key_2 attribute_key_1:option_key_2' => 'catalogProductSimple::out_of_stock',
            ]
        ]
    ];

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $data
     * @param array $params [optional]
     */
    public function __construct(FixtureFactory $fixtureFactory, array $data, array $params = [])
    {
        $this->fixtureFactory = $fixtureFactory;
        $this->params = $params;
        $preset = [];
        $isSetData = false;
        if (isset($data['preset'])) {
            $preset = $this->getPreset($data['preset']);
            unset($data['preset']);
        }
        if (isset($data['data'])) {
            $this->setData($data['data']);
            if (isset($data['data']['data'])) {
                $data = $data['data']['data'];
                $isSetData = true;
            }
            unset($data['data']);
        }
        $this->data = array_replace_recursive($data, $preset);
        if (!empty($this->data)) {
            $this->prepareProducts($this->data);
            if (!$isSetData) {
                $this->prepareData();
            }
        }
    }

    /**
     * Set data to source properties.
     *
     * @param array $data
     * @return void
     */
    protected function setData(array $data)
    {
        $this->products = isset($data['assigned_product']) ? $data['assigned_product'] : [];
        $this->attributesData = isset($data['attributes_data'])
            ? $data['attributes_data']
            : [];
    }

    /**
     * Persist configurable attribute data.
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Prepare products.
     *
     * @param array $data
     * @return void
     */
    protected function prepareProducts(array $data)
    {
        if (empty($this->products)) {
            $attributeSetData = $this->prepareAttributesData($data);
            foreach ($data['products'] as $key => $product) {
                if (is_string($product)) {
                    list($fixture, $dataSet) = explode('::', $product);
                    $attributeData = ['attributes' => $this->getProductAttributeData($key)];
                    $product = $this->fixtureFactory->createByCode(
                        $fixture,
                        ['dataSet' => $dataSet, 'data' => array_merge($attributeSetData, $attributeData)]
                    );
                }
                if (!$product->hasData('id') && $product->getData('isPersist') !== 'No') {
                    $product->persist();
                }

                $this->products[$key] = $product;
            }
        }
        foreach ($this->products as $key => $product) {
            $this->data['products'][$key] = $product->getSku();
        }
    }

    /**
     * Prepare attributes data.
     *
     * @param array $data
     * @return array
     */
    protected function prepareAttributesData(array $data)
    {
        $attributeSetData = [];
        if (isset($data['attributeSet'])) {
            if (!isset($this->attributesData['attributeSet'])) {
                $this->attributesData['attributeSet'] = $this->createAttributeSet($data['attributeSet']);
            }
            if ($this->isSetAttributes()) {
                $this->attributesData['attributes'] = $this->attributesData['attributeSet']
                    ->getDataFieldConfig('assigned_attributes')['source']->getAttributes();
            }
            $attributeSetData['attribute_set_id'] = ['attribute_set' => $this->attributesData['attributeSet']];
        }

        return $attributeSetData;
    }

    /**
     * Check attributes data in source.
     *
     * @return bool
     */
    protected function isSetAttributes()
    {
        return $this->attributesData['attributeSet']->hasData('assigned_attributes')
        && !isset($this->attributesData['attributes']);
    }

    /**
     * Create attribute set.
     *
     * @param array $attributeSet
     * @return CatalogAttributeSet
     */
    protected function createAttributeSet(array $attributeSet)
    {
        $attributeSet = $this->fixtureFactory->createByCode('catalogAttributeSet', $attributeSet);
        $attributeSet->persist();

        return $attributeSet;
    }

    /**
     * Get prepared attribute data for persist product.
     *
     * @param string $key
     * @return array
     */
    protected function getProductAttributeData($key)
    {
        $compositeKeys = explode(' ', $key);
        $data = [];

        foreach ($compositeKeys as $compositeKey) {
            $attributeId = $this->getAttributeOptionId($compositeKey);
            if ($attributeId) {
                $compositeKey = explode(':', $compositeKey);
                $attributeKey = $this->getKey($compositeKey[0]);
                $data[$this->attributesData['attributes'][$attributeKey]->getAttributeCode()] = $attributeId;
            }
        }

        return ['value' => $data];
    }

    /**
     * Get id of attribute option by composite key.
     *
     * @param string $compositeKey
     * @return int|null
     */
    protected function getAttributeOptionId($compositeKey)
    {
        $compositeKey = explode(':', $compositeKey);
        $attributeKey = $this->getKey($compositeKey[0]);
        $optionKey = $this->getKey($compositeKey[1]);

        $attributeOptions = $this->attributesData['attributes'][$attributeKey]->getOptions();
        return isset($attributeOptions[$optionKey]['id'])
            ? $attributeOptions[$optionKey]['id']
            : null;
    }

    /**
     * Prepare data from source.
     *
     * @return void
     */
    protected function prepareData()
    {
        $attributeFields = [
            'frontend_label',
            'label',
            'frontend_input',
            'attribute_code',
            'attribute_id',
            'is_required',
            'options',
        ];
        $optionFields = [
            'admin',
            'label',
            'price',
            'price_type',
            'include',
        ];
        $resultData = [
            'attributes_data',
            'products'
        ];

        foreach ($this->attributesData['attributes'] as $attributeIndex => $attribute) {
            $attribute = $attribute->getData();
            $attributeKey = 'attribute_key_' . $attributeIndex;
            $options = [];
            foreach ($attribute['options'] as $optionIndex => $option) {
                $option['label'] = isset($option['view']) ? $option['view'] : $option['label'];
                $optionKey = 'option_key_' . $optionIndex;
                if (isset($this->data['attributes_data'][$attributeKey]['options'][$optionKey])) {
                    $options[$optionKey] = array_intersect_key($option, array_flip($optionFields));
                }
            }
            $attribute['options'] = $options;
            $attribute['label'] = isset($attribute['label'])
                ? $attribute['label']
                : (isset($attribute['frontend_label']) ? $attribute['frontend_label'] : null);
            $attribute = array_intersect_key($attribute, array_flip($attributeFields));

            $this->data['attributes_data'][$attributeKey] = array_merge_recursive(
                $this->data['attributes_data'][$attributeKey],
                $attribute
            );
        }

        $this->data = array_intersect_key($this->data, array_flip($resultData));
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
     * Return prepared data set.
     *
     * @param string|null $key
     * @return array
     */
    public function getData($key = null)
    {
        return isset($this->data[$key]) ? $this->data[$key] : $this->data;
    }

    /**
     * Get prepared products.
     *
     * @return array
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Get attribute set.
     *
     * @return CatalogAttributeSet
     */
    public function getAttributeSet()
    {
        return $this->attributesData['attributeSet'];
    }

    /**
     * Preset array.
     *
     * @param string $name
     * @return mixed|null
     */
    protected function getPreset($name)
    {
        return isset($this->presets[$name]) ? $this->presets[$name] : null;
    }

    /**
     * Prepare key for array.
     *
     * @param string $key
     * @return int
     */
    protected function getKey($key)
    {
        return str_replace(['attribute_key_', 'option_key_'], '', $key);
    }

    /**
     * Get attribute set.
     *
     * @return array
     */
    public function getAttributesData()
    {
        return $this->attributesData['attributes'];
    }
}
