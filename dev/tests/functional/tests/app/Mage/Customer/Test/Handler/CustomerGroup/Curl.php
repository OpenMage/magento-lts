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

namespace Mage\Customer\Test\Handler\CustomerGroup;

use Mage\Customer\Test\Fixture\CustomerGroup;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Handler\Curl as AbstractCurl;
use Magento\Mtf\Util\Protocol\CurlInterface;
use Magento\Mtf\Util\Protocol\CurlTransport;
use Magento\Mtf\Util\Protocol\CurlTransport\BackendDecorator;

/**
 * Curl handler for creating customer group.
 */
class Curl extends AbstractCurl implements CustomerGroupInterface
{
    /**
     * Url for saving data.
     *
     * @var string
     */
    protected $saveUrl = 'customer_group/save/';

    /**
     * POST request for creating Customer Group.
     *
     * @param FixtureInterface $fixture [optional]
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $this->prepareData($fixture);
        $url = $_ENV['app_backend_url'] . $this->saveUrl . "?" . http_build_query($data);
        $curl = new BackendDecorator(new CurlTransport(), $this->_configuration);
        $curl->write($url, [], CurlInterface::GET);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'class="success-msg"')) {
            throw new \Exception(
                "Customer Group entity creating by curl handler was not successful! Response: $response"
            );
        }

        return ['customer_group_id' => $this->getCustomerGroupId($data, $response)];
    }

    /**
     * Get id after creating Customer Group.
     *
     * @param array $data
     * @return string|null
     */
    public function getCustomerGroupId(array $data)
    {
        $regExp = '/.*id\/(\d+)\/.*' . $data['code'] . '/siu';

        $url = $_ENV['app_backend_url'] . 'customer_group/index/sort/time/dir/desc/';
        $curl = new BackendDecorator(new CurlTransport(), $this->_configuration);
        $curl->write($url, [], CurlInterface::GET);
        $response = $curl->read();
        $curl->close();
        preg_match($regExp, $response, $matches);

        return empty($matches[1]) ? null : $matches[1];
    }

    /**
     * Prepare fixture data.
     *
     * @param FixtureInterface $fixture
     * @return array
     */
    protected function prepareData(FixtureInterface $fixture)
    {
        /** @var CustomerGroup $fixture */
        return [
            'code' => $fixture->getCustomerGroupCode(),
            'tax_class' => $fixture->getDataFieldConfig('tax_class_id')['source']->getTaxClass()->getId()
        ];
    }
}
