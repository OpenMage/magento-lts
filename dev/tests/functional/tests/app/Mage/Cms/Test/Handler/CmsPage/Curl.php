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

namespace Mage\Cms\Test\Handler\CmsPage;

use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Util\Protocol\CurlInterface;
use Magento\Mtf\Util\Protocol\CurlTransport;
use Magento\Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Magento\Mtf\Handler\Curl as AbstractCurl;

/**
 * Curl handler for creating Cms page.
 */
class Curl extends AbstractCurl implements CmsPageInterface
{
    /**
     * Mapping values for data.
     *
     * @var array
     */
    protected $mappingData = [
        'is_active' => [
            'Published' => 1,
            'Disabled' => 0,
            'Enabled' => 1
        ],
        'store_id' => [
            'Main Website/Main Website Store/Default Store View' => 1
        ],
        'page_layout' => [
            '1 column' => '1column',
            '2 columns with left bar' => '2columns-left',
            '2 columns with right bar' => '2columns-right',
            '3 columns' => '3columns',
        ],
        'under_version_control' => [
            'Yes' => 1,
            'No' => 0,
        ],
    ];

    /**
     * Url for save cms page.
     *
     * @var string
     */
    protected $url = 'cms_page/save';

    /**
     * Post request for creating a cms page.
     *
     * @param FixtureInterface $fixture
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $url = $_ENV['app_backend_url'] . $this->url;
        $data = $this->replaceMappingData($fixture->getData());
        $data = $this->prepareData($data);
        $curl = new BackendDecorator(new CurlTransport(), $this->_configuration);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write($url, $data);
        $response = $curl->read();
        $curl->close();
        if (!strpos($response, 'class="success-msg"')) {
            throw new \Exception("Cms page entity creating by curl handler was not successful! Response: $response");
        }
        $id = $this->getCmsPageId($response);

        return ['page_id' => $id];
    }

    /**
     * Prepare data.
     *
     * @param array $data
     * @return array
     */
    protected function prepareData(array $data)
    {
        $resultStore = [];
        foreach ($data['store_id'] as $key => $store) {
            $resultStore['store_id'][$key] = $this->mappingData['store_id'][$store];
        }
        $data['stores'] = $resultStore['store_id'];
        unset($data['store_id']);
        $data['content'] = $data['content']['content'];
        if (!isset($data['is_active'])) {
            $data['is_active'] = 1;
        }
        return $data;
    }

    /**
     * Return saved cms page id.
     *
     * @param string $response
     * @return int|null
     * @throws \Exception
     */
    protected function getCmsPageId($response)
    {
        preg_match_all('~tr title=[^\s]*\/page_id\/(\d+)~', $response, $matches);
        if (empty($matches[1])) {
            throw new \Exception('Cannot find Cms Page id');
        }

        return max($matches[1]);
    }
}
