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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Bundle\Test\Handler;

use Mage\Bundle\Test\Fixture\BundleProduct;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Config\DataInterface;
use Magento\Mtf\System\Event\EventManagerInterface;
use Magento\Mtf\Util\Protocol\CurlInterface;
use Magento\Mtf\Util\Protocol\CurlTransport;
use Magento\Mtf\Util\Protocol\CurlTransport\BackendDecorator;

/**
 * Create new bundle product via curl.
 */
class Curl extends \Mage\Catalog\Test\Handler\CatalogProductSimple\Curl implements BundleProductInterface
{
    /**
     * Fixture product.
     *
     * @var BundleProduct
     */
    protected $fixture;

    /**
     * Options fields.
     *
     * @var array
     */
    protected $optionsFields = [
        'title',
        'type',
        'required'
    ];

    /**
     * @constructor
     * @param DataInterface $configuration
     * @param EventManagerInterface $eventManager
     */
    public function __construct(DataInterface $configuration, EventManagerInterface $eventManager)
    {
        parent::__construct($configuration, $eventManager);

        $this->mappingData += [
            'selection_can_change_qty' => [
                'Yes' => 1,
                'No' => 0,
            ],
            'sku_type' => [
                'Dynamic' => 0,
                'Fixed' => 1,
            ],
            'price_type' => [
                'Dynamic' => 0,
                'Fixed' => 1,
            ],
            'weight_type' => [
                'Dynamic' => 0,
                'Fixed' => 1,
            ],
            'shipment_type' => [
                'Together' => 0,
                'Separately' => 1,
            ],
            'type' => [
                'Drop-down' => 'select',
                'Radio Buttons' => 'radio',
                'Checkbox' => 'checkbox',
                'Multiple Select' => 'multi',
            ],
            'selection_price_type' => [
                'Fixed' => 0,
                'Percent' => 1,
            ]
        ];
    }

    /**
     * Post request for creating bundle product product.
     *
     * @param FixtureInterface|null $fixture [optional]
     * @return array
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $this->fixture = $fixture;
        return parent::persist($fixture);
    }

    /**
     * Prepare POST data for creating product request.
     *
     * @param FixtureInterface $fixture
     * @param string|null $prefix [optional]
     * @return array
     */
    protected function prepareData(FixtureInterface $fixture, $prefix = null)
    {
        $data = parent::prepareData($fixture, null);
        $bundleOptions = $this->prepareBundleOptions($data['bundle_selections']);
        $bundleSelections = $this->prepareBundleSelections($data['bundle_selections']);
        $data = $prefix ? [$prefix => $data] : $data;
        $data['bundle_selections'] = $bundleSelections;
        $data['bundle_options'] = $bundleOptions;

        return $this->replaceMappingData($data);
    }

    /**
     * Prepare bundle selections data.
     *
     * @param array $bundleData
     * @return array
     */
    protected function prepareBundleSelections(array $bundleData)
    {
        $result = [];
        $products = $this->fixture->getDataFieldConfig('bundle_selections')['source']->getProducts();
        foreach ($bundleData as $optionKey => $option) {
            $result[$optionKey] = $this->prepareItemSelectionData($option['assigned_products'], $products[$optionKey]);
        }

        return $result;
    }

    /**
     * Prepare item selection data.
     *
     * @param array $selections
     * @param array $products
     * @return array
     */
    protected function prepareItemSelectionData(array $selections, array $products)
    {
        foreach ($selections as $key => $selection) {
            $selections[$key]['product_id'] = $products[$key]->getId();
            unset($selections[$key]['sku']);
            $selections[$key]['delete'] = '';
        }

        return $selections;
    }

    /**
     * Prepare bundle options data.
     *
     * @param array $bundleData
     * @return array
     */
    protected function prepareBundleOptions(array $bundleData)
    {
        foreach ($bundleData as $key => $option) {
            $bundleData[$key] = array_intersect_key($bundleData[$key], array_flip($this->optionsFields));
            $bundleData[$key]['delete'] = '';
        }

        return $bundleData;
    }

    /**
     * Parse response.
     *
     * @param string $response
     * @return array
     */
    protected function parseResponse($response)
    {
        $result = parent::parseResponse($response);
        return array_replace_recursive($result, $this->parseResponseSelections($result['id']));
    }

    /**
     * Parse bundle selections in response.
     *
     * @param string $id
     * @return array
     */
    protected function parseResponseSelections($id)
    {
        $url = $_ENV['app_backend_url'] . "bundle_product_edit/form/id/{$id}/back/edit/tab/product_info_tabs_group_7/";
        $curl = new BackendDecorator(new CurlTransport(), $this->_configuration);
        $curl->write($url);
        $response = $curl->read();
        $curl->close();

        $selectionIdKey = 1;
        $optionIdKey = 2;
        $productNameKey = 3;
        $responseSelections = [];
        $bundleSelections = $this->fixture->getBundleSelections();

        preg_match_all(
            '/{.*"selection_id":"(\d+)".*"option_id":"(\d+)".*"name":"([^"]+)".*}/',
            $response,
            $matches,
            PREG_SET_ORDER
        );
        foreach ($matches as $match) {
            $productName = $match[$productNameKey];
            $responseSelections[$productName] = [
                'selection_id' => $match[$selectionIdKey],
                'option_id' => $match[$optionIdKey],
            ];
        }

        foreach ($bundleSelections as $optionKey => $option) {
            foreach ($option['assigned_products'] as $assignedKey => $optionValue) {
                $productName = $optionValue['name'];
                $bundleSelections[$optionKey]['assigned_products'][$assignedKey] += $responseSelections[$productName];
            }
        }

        return ['bundle_selections' => $bundleSelections];
    }
}
