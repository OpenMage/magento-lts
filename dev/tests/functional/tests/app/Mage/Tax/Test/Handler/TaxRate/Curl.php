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

namespace Mage\Tax\Test\Handler\TaxRate;

use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Handler\Curl as AbstractCurl;
use Magento\Mtf\Util\Protocol\CurlInterface;
use Magento\Mtf\Util\Protocol\CurlTransport;
use Magento\Mtf\Util\Protocol\CurlTransport\BackendDecorator;

/**
 * Curl handler for creating Tax Rate.
 */
class Curl extends AbstractCurl implements TaxRateInterface
{
    /**
     * Mapping for countries.
     *
     * @var array
     */
    protected $countryId = [
        'AU' => 'Australia',
        'US' => 'United States',
        'GB' => 'United Kingdom',
    ];

    /**
     * Mapping for regions.
     *
     * @var array
     */
    protected $regionId = [
        '0' => '*',
        '12' => 'California',
        '43' => 'New York',
        '57' => 'Texas',
    ];

    /**
     * Post request for creating tax rate.
     *
     * @param FixtureInterface $fixture [optional]
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $fixture->getData();
        $data['tax_country_id'] = array_search($data['tax_country_id'], $this->countryId);
        if (isset($data['tax_region_id'])) {
            $data['tax_region_id'] = array_search($data['tax_region_id'], $this->regionId);
        }

        $url = $_ENV['app_backend_url'] . 'tax_rate/save';
        $curl = new BackendDecorator(new CurlTransport(), $this->_configuration);
        $curl->write($url, $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'success-msg')) {
            throw new \Exception('Tax rate creation by curl handler was not successful!');
        }

        $url = $_ENV['app_backend_url'] . 'tax_rate/index/limit/200';
        $curl = new BackendDecorator(new CurlTransport(), $this->_configuration);
        $curl->write($url, $data);
        $response = $curl->read();
        $curl->close();

        $id = $this->getTaxRateId($response);
        return ['id' => $id];
    }

    /**
     * Return saved tax rate id.
     *
     * @param $response
     * @return int|null
     */
    protected function getTaxRateId($response)
    {
        preg_match_all('~tax_rate/edit[^\s]*\/rate\/(\d+)~', $response, $match);

        return max(empty($match[1]) ? null : $match[1]);
    }
}
