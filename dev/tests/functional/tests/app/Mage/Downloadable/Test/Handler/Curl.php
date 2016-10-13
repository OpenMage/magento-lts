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

namespace Mage\Downloadable\Test\Handler;

use Magento\Mtf\Config\DataInterface;
use Magento\Mtf\System\Event\EventManagerInterface;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Util\Protocol\CurlInterface;
use Magento\Mtf\Util\Protocol\CurlTransport;
use Magento\Mtf\Util\Protocol\CurlTransport\BackendDecorator;

/**
 * Create new downloadable product via curl.
 */
class Curl extends \Mage\Catalog\Test\Handler\CatalogProductSimple\Curl implements DownloadableProductInterface
{
    /**
     * Downloadable types.
     *
     * @var array
     */
    protected $downloadableTypes = [
        'links' => [
            'links_title',
            'links_purchased_separately'
        ],
        'samples' => [
            'samples_title'
        ]
    ];

    /**
     * Fields for link.
     *
     * @var array
     */
    protected $linkFields = [
        'title',
        'type',
        'link_url' => 'file_link_url',
        'price',
        'number_of_downloads',
        'is_shareable',
        'sort_order',
        'sample' => [
            'type',
            'url' => 'sample_url'
        ]
    ];

    /**
     * Fields for sample.
     *
     * @var array
     */
    protected $sampleFields = [
        'title',
        'type',
        'sample_url',
        'sort_order'
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
            'links_purchased_separately' => [
                'Yes' => 1,
                'No' => 0,
            ],
            'is_shareable' => [
                'Yes' => 1,
                'No' => 0,
                'Use config' => 2,
            ],
        ];
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
        $originalData = parent::prepareData($fixture, $prefix);
        $downloadableData = [];
        $data = ($prefix == null) ? $originalData : $originalData[$prefix];

        foreach ($this->downloadableTypes as $type => $fields) {
            if (!empty($data['downloadable_' . $type])) {
                $downloadableTypeData = $this->{'prepare' . ucfirst($type) . 'Data'}($data['downloadable_' . $type]);
                $data = array_merge_recursive($data, array_intersect_key($downloadableTypeData, array_flip($fields)));
                $downloadableData = array_merge_recursive($downloadableData, $downloadableTypeData);
                unset($downloadableTypeData['downloadable']);
                if ($prefix == null) {
                    $originalData = array_merge($originalData, $downloadableTypeData);
                } else {
                    $originalData[$prefix] = array_merge($originalData[$prefix], $downloadableTypeData);
                }
            }
        }

        $data = array_merge_recursive($originalData, $downloadableData);

        return $this->replaceMappingData($data);
    }

    /**
     * Prepare links data.
     *
     * @param array $linksData
     * @return array
     */
    protected function prepareLinksData(array $linksData)
    {
        return [
            'links_title' => $linksData['title'],
            'links_purchased_separately' => $linksData['links_purchased_separately'],
            'downloadable' => [
                'link' => $this->prepareLinksItemsData($linksData['downloadable']['link'])
            ]
        ];
    }

    /**
     * Prepare samples data.
     *
     * @param array $samplesData
     * @return array
     */
    protected function prepareSamplesData(array $samplesData)
    {
        return [
            'samples_title' => $samplesData['title'],
            'downloadable' => [
                'sample' => $this->prepareSamplesItemsData($samplesData['downloadable']['sample'])
            ]
        ];
    }

    /**
     * Prepare samples items data.
     *
     * @param array $samples
     * @return array
     */
    protected function prepareSamplesItemsData(array $samples)
    {
        $resultSamples = [];
        foreach ($samples as $key => $sample) {
            $resultLinks[$key] = $this->prepareItem($sample, 'sample');
        }

        return $resultSamples;
    }

    /**
     * Prepare links items data.
     *
     * @param array $links
     * @return array
     */
    protected function prepareLinksItemsData(array $links)
    {
        $resultLinks = [];
        foreach ($links as $key => $link) {
            $resultLinks[$key] = $this->prepareItem($link, 'link');
        }

        return $resultLinks;
    }

    /**
     * Prepare item data.
     *
     * @param array $link
     * @param string $type
     * @param string|null $key
     * @return array
     */
    protected function prepareItem(array $link, $type, $key = null)
    {
        $result = [];
        $fields = ($key == null) ? $this->{$type . 'Fields'} : $this->{$type . 'Fields'}[$key];
        foreach ($fields as $key => $value) {
            if ($key === 'type' || $value === 'type') {
                $result['type'] = 'url';
            } else {
                if (is_string($key)) {
                    if (is_array($value)) {
                        $result[$key] = $this->prepareItem($link[$key], $type, $key);
                    } else {
                        $result[$key] = $link[$value];
                    }
                } else {
                    $result[$value] = $link[$value];
                }
            }
        }

        return $result;
    }

    /**
     * Create product via curl
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
        $id = $this->parseResponse($response);
        $checkoutData = isset($data['product']['checkout_data']) ? $data['product']['checkout_data'] : null;
        foreach ($data['downloadable']['link'] as $key => $link) {
            preg_match('`"link_id":"(\d*?)","title":"' . $link['title'] . '"`', $response, $linkId);
            if (isset($checkoutData['options']['links'][$key]['label'])) {
                $checkoutData['options']['links'][$key]['id'] = $linkId[1];
            }
        }

        return ['id' => $id['id'], 'checkout_data' => $checkoutData];
    }
}
