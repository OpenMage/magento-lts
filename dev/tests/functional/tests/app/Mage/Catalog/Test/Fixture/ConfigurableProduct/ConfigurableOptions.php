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

use Magento\Mtf\Fixture\DataSource;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\Fixture\InjectableFixture;
use Mage\Catalog\Test\Fixture\CatalogAttributeSet;
use Mage\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Mtf\Repository\RepositoryFactory;

/**
 * Source configurable options of the configurable products.
 */
class ConfigurableOptions extends DataSource
{
    /**
     * Fixture factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

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
     * Prepared attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * @constructor
     * @param RepositoryFactory $repositoryFactory
     * @param FixtureFactory $fixtureFactory
     * @param array $data
     * @param array $params [optional]
     */
    public function __construct(
        RepositoryFactory $repositoryFactory,
        FixtureFactory $fixtureFactory,
        array $data,
        array $params = [])
    {
        $this->fixtureFactory = $fixtureFactory;
        $this->params = $params;
        $dataset = [];
        $isSetData = false;
        if (isset($data['dataset']) && isset($this->params['repository'])) {
            $dataset = $repositoryFactory->get($this->params['repository'])->get($data['dataset']);
            unset($data['dataset']);
        }

        if (isset($data['data'])) {
            $this->setData($data['data']);
            if (isset($data['data']['data'])) {
                $data = $data['data']['data'];
                $isSetData = true;
            }
            unset($data['data']);
        }
        $this->data = array_replace_recursive($data, $dataset);
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
                    list($fixture, $dataset) = explode('::', $product);
                    $attributeData = ['attributes' => $this->getProductAttributeData($key)];
                    $product = $this->fixtureFactory->createByCode(
                        $fixture,
                        ['dataset' => $dataset, 'data' => array_merge($attributeSetData, $attributeData)]
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
     * Get prepared products.
     *
     * @return array
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Get data by key.
     *
     * @param null $key
     * @return array
     */
    public function getData($key = null)
    {
        if ($key === null) {
            return $this->data;
        } else {
            return $this->data[$key];
        }
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
