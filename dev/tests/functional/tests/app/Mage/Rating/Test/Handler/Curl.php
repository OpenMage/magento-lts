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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Rating\Test\Handler;

use Mage\Adminhtml\Test\Handler\Extractor;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Handler\Curl as AbstractCurl;
use Magento\Mtf\Util\Protocol\CurlInterface;
use Magento\Mtf\Util\Protocol\CurlTransport;
use Magento\Mtf\Util\Protocol\CurlTransport\BackendDecorator;

/**
 * Curl handler for creating product Rating through backend.
 */
class Curl extends AbstractCurl implements RatingInterface
{
    /**
     * Mapping stores value.
     *
     * @var array
     */
    protected $mappingStores = [
        'Main Website/Main Website Store/Default Store View' => 1,
    ];

    /**
     * Rating options.
     *
     * @var array
     */
    protected $options = [
        'add_1' => 1,
        'add_2' => 2,
        'add_3' => 3,
        'add_4' => 4,
        'add_5' => 5,
    ];

    /**
     * Post request for creating product Rating in backend.
     *
     * @param FixtureInterface|null $rating
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $rating = null)
    {
        $url = $_ENV['app_backend_url'] . 'rating/save';
        $curl = new BackendDecorator(new CurlTransport(), $this->_configuration);
        $data = $this->replaceMappingData($this->prepareData($rating->getData()));
        $curl->write($url, $data);
        $response = $curl->read();
        $curl->close();
        if (!strpos($response, 'class="success-msg"')) {
            throw new \Exception(
                'Product Rating entity creating by curl handler was not successful! Response:' . $response
            );
        }

        return ['rating_id' => $this->getProductRatingId()];
    }

    /**
     * Prepare POST data for creating rating request.
     *
     * @param array $data
     * @return array
     */
    protected function prepareData(array $data)
    {
        if (isset($data['stores'])) {
            foreach ($data['stores'] as $key => $store) {
                if (isset($this->mappingStores[$store])) {
                    $data['stores'][$key] = $this->mappingStores[$store];
                }
            }
        }
        $data['option_title'] = $this->options;

        return $data;
    }

    /**
     * Get product Rating id.
     *
     * @return mixed
     */
    protected function getProductRatingId()
    {
        $url = 'rating/index/sort/rating_id/dir/desc/';
        $regex = '`rating\/edit\/id\/(\d+)`';
        $extractor = new Extractor($url, $regex);
        $match = $extractor->getData();

        return empty($match[1]) ? null : $match[1];
    }
}
