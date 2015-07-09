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

namespace Mage\Catalog\Test\Handler\ConfigurableProduct;

use Mage\Catalog\Test\Handler\CatalogProductSimple\Curl as ProductCurl;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Config\DataInterface;
use Magento\Mtf\System\Event\EventManagerInterface;
use Magento\Mtf\Fixture\FixtureFactory;
use Mage\Catalog\Test\Fixture\ConfigurableProduct;
use Magento\Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Magento\Mtf\Util\Protocol\CurlInterface;
use Magento\Mtf\Util\Protocol\CurlTransport;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Create new configurable product via curl.
 */
class Curl extends ProductCurl implements ConfigurableProductInterface
{
    /**
     * Fixture factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * @constructor
     * @param DataInterface $configuration
     * @param EventManagerInterface $eventManager
     * @param FixtureFactory $fixtureFactory
     */
    public function __construct(
        DataInterface $configuration,
        EventManagerInterface $eventManager,
        FixtureFactory $fixtureFactory
    ) {
        parent::__construct($configuration, $eventManager);

        $this->fixtureFactory = $fixtureFactory;
        $this->mappingData += [
            'include' => [
                'Yes' => 1,
                'No' => 0,
            ]
        ];
    }

    /**
     * Prepare POST data for creating product request.
     *
     * @param FixtureInterface $product
     * @param string|null $prefix [optional]
     * @return array
     */
    protected function prepareData(FixtureInterface $product, $prefix = null)
    {
        /** @var ConfigurableProduct $product */
        $valuesIndexes = $this->getIndexValues($product);
        $data = parent::prepareData($product, $prefix);
        $data['configurable_products_data'] = $this->prepareConfigurableProductData($product, $valuesIndexes);
        $data['configurable_attributes_data'] = $this->prepareConfigurableAttributeData($product, $valuesIndexes);
        $data['affect_configurable_product_attributes'] = 1;

        return $this->replaceMappingData($data);
    }

    /**
     * Get configurable product data for curl.
     *
     * @param ConfigurableProduct $product
     * @param array $valuesIndexes
     * @return string
     */
    protected function prepareConfigurableAttributeData(ConfigurableProduct $product, array $valuesIndexes)
    {
        $result = [];
        $configurableOptions = $product->getConfigurableOptions();

        foreach ($configurableOptions['attributes_data'] as $attributeKey => $attribute) {
            $key = str_replace('attribute_key_', '', $attributeKey);
            $result[$key] = [
                'label' => $attribute['label'],
                'attribute_id' => $attribute['attribute_id'],
                'attribute_code' => $attribute['attribute_code'],
                'frontend_label' => $attribute['frontend_label'],
                'store_label' => $attribute['frontend_label'],
            ];
            foreach ($attribute['options'] as $optionKey => $option) {
                $result[$key]['values'][] = [
                    'label' => $option['label'],
                    'value_index' => $valuesIndexes[$attributeKey . ':' . $optionKey],
                    'pricing_value' => $option['price'],
                    'is_percent' => ($option['price_type'] == 'Percentage') ? 1 : 0
                ];
            }
        }

        return json_encode($result, true);
    }

    /**
     * Get params of configurable items attributes.
     *
     * @param ConfigurableProduct $product
     * @return array
     */
    protected function getIndexValues(ConfigurableProduct $product)
    {
        $config = $this->prepareConfigDataForNew($product);
        $url = $this->getUrl($config, 'new');
        $curl = new BackendDecorator(new CurlTransport(), $this->_configuration);
        $curl->write(CurlInterface::GET, $url);
        $response = $curl->read();
        $curl->close();

        preg_match_all('/class="value-json" value="(.*)"/', $response, $tempResult);
        $valueIndexes = [];
        krsort($tempResult[1]);
        $optionIndex = 0;
        foreach ($tempResult[1] as $value) {
            $arrayResult = json_decode(str_replace('&quot;', '"', $value), true);
            foreach ($arrayResult as $key => $item) {
                $valueIndexes['attribute_key_' . $key . ':' . 'option_key_' . $optionIndex] = $item['value_index'];
            }
            $optionIndex++;
        }

        return $valueIndexes;
    }

    /**
     * Prepare config data.
     *
     * @param InjectableFixture $product
     * @return array
     */
    protected function prepareConfig(InjectableFixture $product)
    {
        $config = parent::prepareConfig($product);
        $config['create_url_params']['set'] = $product->getDataFieldConfig('configurable_options')['source']
            ->getAttributeSet()
            ->getAttributeSetId();

        return $config;
    }

    /**
     * Prepare config data for product.
     *
     * @param ConfigurableProduct $product
     * @return array
     */
    protected function prepareConfigDataForNew(ConfigurableProduct $product)
    {
        $attributeSet = $product->getDataFieldConfig('configurable_options')['source']->getAttributeSet();
        $attributes = $attributeSet->getDataFieldConfig('assigned_attributes')['source']->getAttributes();
        $attributesIds = $this->getAttributesIds($attributes);

        $customUrlParam['create_url_params'] = [
            'attributes' => $this->codeAttribute($attributesIds),
            'set' => $attributeSet->getAttributeSetId()
        ];

        return array_replace_recursive($product->getDataConfig(), $customUrlParam);
    }

    /**
     * Prepare attributes ids.
     *
     * @param array $attributes
     * @return string
     */
    protected function getAttributesIds(array $attributes)
    {
        $attributesIds = "";
        foreach ($attributes as $attribute) {
            $attributesIds .= $attribute->getAttributeId() . ",";
        }
        return substr($attributesIds, 0, strlen($attributesIds) - 1);
    }

    /**
     * Prepare configurable attribute data to GET request.
     *
     * @param string $attributes
     * @return string
     */
    protected function codeAttribute($attributes)
    {
        $res = base64_encode($attributes);
        $res = str_replace('/', '%2F', $res);
        $res = str_replace('=', '%3D', $res);

        return $res;
    }

    /**
     * Prepare configurable product data.
     *
     * @param ConfigurableProduct $product
     * @param array $valuesIndexes
     * @return string
     */
    protected function prepareConfigurableProductData(ConfigurableProduct $product, array $valuesIndexes)
    {
        $result = [];
        $configurableOptions = $product->getConfigurableOptions();
        $configurableOptionsSource = $product->getDataFieldConfig('configurable_options')['source'];
        $associatedProducts = $this->splitProducts($configurableOptionsSource->getProducts());

        foreach ($configurableOptions['attributes_data'] as $attributeKey => $attribute) {
            foreach ($attribute['options'] as $optionKey => $option) {
                $productId = $associatedProducts[$attributeKey . ':' . $optionKey]->getId();
                $result[$productId][] = [
                    'label' => $option['label'],
                    'attribute_id' => $attribute['attribute_id'],
                    'value_index' => $valuesIndexes[$attributeKey . ':' . $optionKey],
                    'is_percent' => ($option['price_type'] == 'Percentage') ? 1 : 0,
                    'pricing_value' => $option['price']
                ];
            }
        }

        return json_encode($result, true);
    }

    /**
     * Split products.
     *
     * @param array $products
     * @return array
     */
    protected function splitProducts(array $products)
    {
        $result = [];
        foreach ($products as $key => $product) {
            $indexes = explode(' ', $key);
            foreach ($indexes as $index) {
                $result[$index] = $product;
            }
        }

        return $result;
    }
}
