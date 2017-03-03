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

namespace Mage\Catalog\Test\Handler\CatalogProductSimple;

use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Util\Protocol\CurlTransport;
use Magento\Mtf\Handler\Curl as AbstractCurl;
use Magento\Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Create new simple product via curl.
 */
class Curl extends AbstractCurl implements CatalogProductSimpleInterface
{
    /**
     * Flag with category.
     *
     * @var bool
     */
    protected $withCategory = false;

    /**
     * Mapping values for data.
     *
     * @var array
     */
    protected $mappingData = [
        'links_purchased_separately' => [
            'Yes' => 1,
            'No' => 0
        ],
        'use_config_notify_stock_qty' => [
            'Yes' => 1,
            'No' => 0
        ],
        'is_shareable' => [
            'Yes' => 1,
            'No' => 0,
            'Use config' => 2
        ],
        'required' => [
            'Yes' => 1,
            'No' => 0
        ],
        'use_config_manage_stock' => [
            'Yes' => 1,
            'No' => 0
        ],
        'is_virtual' => [
            'Yes' => 1
        ],
        'use_config_enable_qty_increments' => [
            'Yes' => 1,
            'No' => 0
        ],
        'use_config_qty_increments' => [
            'Yes' => 1,
            'No' => 0
        ],
        'is_in_stock' => [
            'In Stock' => 1,
            'Out of Stock' => 0
        ],
        'visibility' => [
            'Not Visible Individually' => 1,
            'Catalog' => 2,
            'Search' => 3,
            'Catalog, Search' => 4
        ],
        'website_ids' => [
            'Main Website' => 1
        ],
        'status' => [
            'Disabled' => 2,
            'Enabled' => 1
        ],
        'is_require' => [
            'Yes' => 1,
            'No' => 0
        ],
        'is_recurring' => [
            'Yes' => 1,
            'No' => 0
        ],
        'msrp_display_actual_price_type' => [
            'Use config' => 4,
            'On Gesture' => 1,
            'In Cart' => 2,
            'Before Order Confirmation' => 3
        ],
        'enable_qty_increments' => [
            'Yes' => 1,
            'No' => 0,
        ],
        'msrp_enabled' => [
            'Yes' => 1,
            'No' => 0,
        ],
        'is_anchor' => [
            'Yes' => 1,
            'No' => 0,
        ],
    ];

    /**
     * Placeholder for price data sent Curl.
     *
     * @var array
     */
    protected $priceData = [
        'customer_group' => [
            'name' => 'cust_group',
            'data' => [
                'ALL GROUPS' => 32000,
                'NOT LOGGED IN' => 0,
                'General' => 1
            ]
        ]
    ];

    /**
     * Placeholder for fpt data sent Curl
     *
     * @var array
     */
    protected $fptData = [
        'website' => [
            'name' => 'website_id',
            'data' => [
                'All Websites [USD]' => 0
            ]
        ],
        'country_name' => [
            'name' => 'country',
            'data' => [
                'United States' => 'US'
            ]
        ],
        'state_name' => [
            'name' => 'state',
            'data' => [
                'California' => 12,
                '*' => 0
            ]
        ]
    ];

    /**
     * Select custom options.
     *
     * @var array
     */
    protected $selectOptions = ['Drop-down', 'Radio Buttons', 'Checkbox', 'Multiple Select'];

    /**
     * Post request for creating simple product.
     *
     * @param FixtureInterface|null $fixture [optional]
     * @return array
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function persist(FixtureInterface $fixture = null)
    {
        /** @var InjectableFixture $fixture */
        $config = $this->prepareConfig($fixture);
        $prefix = isset($config['input_prefix']) ? $config['input_prefix'] : null;
        $data = $this->prepareData($fixture, $prefix);
        return $this->createProduct($data, $config);
    }

    /**
     * Prepare config data.
     *
     * @param InjectableFixture $product
     * @return array
     */
    protected function prepareConfig(InjectableFixture $product)
    {
        $config = $product->getDataConfig();
        if ($product->hasData('attribute_set_id')) {
            $config['create_url_params']['set'] = $product->getDataFieldConfig('attribute_set_id')['source']
                ->getAttributeSet()
                ->getAttributeSetId();
        }

        return $config;
    }

    /**
     * Prepare POST data for creating product request.
     *
     * @param FixtureInterface $fixture
     * @param string|null $prefix [optional]
     * @return array
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function prepareData(FixtureInterface $fixture, $prefix = null)
    {
        $fields = $this->replaceMappingData($fixture->getData());

        // Getting Tax class id
        if ($fixture->hasData('tax_class_id')) {
            $fields['tax_class_id'] = $fixture->getDataFieldConfig('tax_class_id')['source']->getTaxClassId();
        }

        if (isset($fields['tier_price'])) {
            $fields['tier_price'] = $this->preparePriceData($fields['tier_price']);
        }
        if (isset($fields['group_price'])) {
            $fields['group_price'] = $this->preparePriceData($fields['group_price']);
        }
        if (isset($fields['fpt'])) {
            $attributeLabel = $fixture->getDataFieldConfig('attribute_set_id')['source']
                ->getAttributeSet()->getDataFieldConfig('assigned_attributes')['source']
                ->getAttributes()[0]->getFrontendLabel();
            $fields[$attributeLabel] = $this->prepareFptData($fields['fpt']);
        }
        if ($isCustomOptions = isset($fields['custom_options'])) {
            $fields = $this->prepareCustomOptionsData($fields);
        }

        if (!empty($fields['website_ids'])) {
            $result = [];
            foreach ($fields['website_ids'] as $key => $value) {
                $result[] = isset($this->mappingData['website_ids'][$value])
                    ? $this->mappingData['website_ids'][$value]
                    : $fixture->getDataFieldConfig('website_ids')['source']->getWebsites()[$key]->getWebsiteId();
            }
            $fields['website_ids'] = $result;
        }

        if (isset($fields['attribute_set_id'])) {
            unset($fields['attribute_set_id']);
        }

        // Prepare assigned attribute
        if (isset($fields['attributes'])) {
            $fields = $this->prepareAttributes($fields, $fixture);
        }

        if (!empty($fields['category_ids'])) {
            $this->withCategory = true;
            unset($fields['category_ids']);
        }

        $fields = $this->prepareStockData($fields);
        $fields = $prefix ? [$prefix => $fields] : $fields;

        if ($this->withCategory) {
            foreach ($fixture->getDataFieldConfig('category_ids')['source']->getCategories() as $category) {
                $fields['category_ids'][] = $category->getId();
            }
        }

        if ($isCustomOptions) {
            $fields['affect_product_custom_options'] = 1;
        }

        if (isset($fields['product']['weight'])) {
            unset($fields['product']['is_virtual']);
        }

        return $fields;
    }

    /**
     * Prepare attributes data.
     *
     * @param array $data
     * @param FixtureInterface $fixture
     * @return array
     */
    protected function prepareAttributes(array $data, FixtureInterface $fixture)
    {
        $result = isset($data['attributes']['value']) ? $data['attributes']['value'] : [];
        if (isset($data['attributes']['preset'])) {
            $attributes = $fixture->getDataFieldConfig('attribute_set_id')['source']
                ->getAttributeSet()->getDataFieldConfig('assigned_attributes')['source']
                ->getAttributes();
            foreach ($data['attributes']['preset'] as $key => $attribute) {
                $attributeKey = str_replace('attribute_key_', '', $key);
                $options = $attributes[$attributeKey]->getOptions();
                if ($options !== null) {
                    foreach ($attribute as $optionKey) {
                        $optionKey = str_replace('option_key_', '', $optionKey);
                        $option = $options[$optionKey];
                        $optionsIds = $attributes[$attributeKey]->getDataFieldConfig('options' )['source']
                            ->getOptionsIds();
                        if (empty($optionsIds)) {
                            $optionsData = $attributes[$attributeKey]->getOptions();
                            $optionsIds = $this->prepareOptionsIds($optionsData);
                        }
                        $result[$attributes[$attributeKey]->getAttributeCode()] = $optionsIds[$option['admin']];
                    }
                }
            }
        }
        unset($data['attributes']);

        return array_merge($data, $result);
    }

    /**
     * Prepare options ids.
     *
     * @param array $options
     * @return array
     */
    protected function prepareOptionsIds(array $options)
    {
        $result = [];
        foreach ($options as $option) {
            $result[$option['admin']] = $option['id'];
        }
        return $result;
    }

    /**
     * Preparation of stock data.
     *
     * @param array $fields
     * @return array
     */
    protected function prepareStockData(array $fields)
    {
        if (!isset($fields['stock_data']['manage_stock'])) {
            $fields['stock_data']['manage_stock'] = (int)(!empty($fields['stock_data']['qty'])
                || !empty($fields['stock_data']['is_in_stock']));
        }

        return $this->filter($fields);
    }

    /**
     * Preparation of custom options data.
     *
     * @param array $fields
     * @return array
     */
    protected function prepareCustomOptionsData(array $fields)
    {
        $options = [];
        foreach ($fields['custom_options'] as $key => $customOption) {
            $options[$key] = ['option_id' => 0, 'is_delete' => ''];
            foreach ($customOption['options'] as $index => $option) {
                $customOption['options'][$index]['is_delete'] = '';
                $customOption['options'][$index]['price_type'] = strtolower($option['price_type']);
            }
            $customOption['type'] = explode('/', $customOption['type'])[1];
            $options[$key] += in_array($customOption['type'], $this->selectOptions)
                ? ['values' => $customOption['options']]
                : $customOption['options'][0];
            unset($customOption['options']);
            $options[$key] += $customOption;
            $options[$key]['type'] = $this->optionNameConvert($customOption['type']);
        }
        $fields['options'] = $options;
        unset($fields['custom_options']);

        return $fields;
    }

    /**
     * Convert option name.
     *
     * @param string $optionName
     * @return string
     */
    protected function optionNameConvert($optionName)
    {
        $optionName = str_replace(['-', ' & '], "_", trim($optionName));
        $end = strpos($optionName, ' ');
        if ($end !== false) {
            $optionName = substr($optionName, 0, $end);
        }
        return strtolower($optionName);
    }

    /**
     * Preparation of tier price data.
     *
     * @param array $fields
     * @return array
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function preparePriceData(array $fields)
    {
        foreach ($fields as &$field) {
            foreach ($this->priceData as $key => $data) {
                $field[$data['name']] = $this->priceData[$key]['data'][$field[$key]];
                unset($field[$key]);
            }
            $field['delete'] = '';
        }
        return $fields;
    }

    /**
     * Preparation of fpt data.
     *
     * @param array $fields
     * @return array
     */
    protected function prepareFptData(array $fields)
    {
        foreach ($fields as &$field) {
            foreach ($this->fptData as $key => $data) {
                $field[$data['name']] = $this->fptData[$key]['data'][$field[$key]];
                unset($field[$key]);
            }
            $field['delete'] = '';
        }
        return $fields;
    }

    /**
     * Remove items from a null.
     *
     * @param array $data
     * @return array
     */
    protected function filter(array $data)
    {
        foreach ($data as $key => $value) {
            if ($value === null) {
                unset($data[$key]);
            } elseif (is_array($data[$key])) {
                $data[$key] = $this->filter($data[$key]);
            }
        }
        return $data;
    }

    /**
     * Create product via curl.
     *
     * @param array $data
     * @param array $config
     * @return array
     * @throws \Exception
     */
    protected function createProduct(array $data, array $config)
    {
        $url = $this->getUrl($config);
        $curl = new BackendDecorator(new CurlTransport(), $this->_configuration);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write($url, $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'class="success-msg"')) {
            throw new \Exception("Product creation by curl handler was not successful! Response: $response");
        }

        return $this->parseResponse($response);
    }

    /**
     * Parse data in response.
     *
     * @param string $response
     * @return array
     */
    protected function parseResponse($response)
    {
        preg_match('~a href=[^\s]*\/id\/(\d+)~', $response, $matches);
        $id = isset($matches[1]) ? $matches[1] : null;
        return ['id' => $id];
    }

    /**
     * Retrieve URL for request with all necessary parameters.
     *
     * @param array $config
     * @param string $type [optional]
     * @return string
     */
    protected function getUrl(array $config, $type = 'save')
    {
        $requestParams = isset($config['create_url_params']) ? $config['create_url_params'] : [];
        $params = '';
        foreach ($requestParams as $key => $value) {
            $params .= $key . '/' . $value . '/';
        }

        return $_ENV['app_backend_url'] . "catalog_product/$type/{$params}back/edit";
    }
}
