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

namespace Mage\Customer\Test\Handler\Customer;

use Mage\Customer\Test\Fixture\Customer;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Handler\Curl as AbstractCurl;
use Magento\Mtf\Util\Protocol\CurlInterface;
use Magento\Mtf\Util\Protocol\CurlTransport;
use Magento\Mtf\Util\Protocol\CurlTransport\BackendDecorator;

/**
 * Curl handler for creating customer through registration page.
 */
class Curl extends AbstractCurl implements CustomerInterface
{
    /**
     * Default customer group.
     */
    const GENERAL_GROUP = '1';

    /**
     * Mapping values for data.
     *
     * @var array
     */
    protected $mappingData = [
        'country_id' => [
            'United States' => 'US',
            'United Kingdom' => 'GB'
        ],
        'region_id' => [
            'California' => 12,
            'New York' => 43,
            'Texas' => 57,
        ],
        'gender' => [
          'Male' => 1,
          'Female' => 2
        ],
    ];

    /**
     * Curl mapping data.
     *
     * @var array
     */
    protected $curlMapping = [
        'account' => [
            'group_id',
            'firstname',
            'lastname',
            'email',
            'dob',
            'taxvat',
            'gender'
        ],
        'customerbalance' => [
          'amount_delta'
        ],
    ];

    /**
     * Array of fields are needing to be updated via updateCustomer() method.
     *
     * @var array
     */
    protected $updatingFields = [
        'address',
        'dob',
        'gender',
        'amount_delta'
    ];

    /**
     * Post request for creating customer in frontend.
     *
     * @param FixtureInterface|null $customer
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $customer = null)
    {
        $result = [];
        /** @var Customer $customer */
        $url = $_ENV['app_frontend_url'] . 'customer/account/createpost/?nocookie=true';
        $data = $customer->getData();
        $data['group_id'] = $this->getCustomerGroup($customer);

        if ($customer->hasData('address')) {
            $data['address'] = http_build_query($data['address']);
        }

        $curl = new CurlTransport();
        $curl->write($url, $data);
        $response = $curl->read();
        $curl->close();
        if (!strpos($response, 'class="success-msg"')) {
            throw new \Exception("Customer entity creating by curl handler was not successful! Response: $response");
        }

        $result['id'] = $this->getCustomerId($customer->getEmail());
        $data['customer_id'] = $result['id'];

        if ($this->checkForUpdateData($data)) {
            parse_str($data['address'], $data['address']);
            $this->updateCustomer($data);
        }

        return $result;
    }

    /**
     * Check if customer needs to update data during curl creation.
     *
     * @param array $data
     * @return bool
     */
    protected function checkForUpdateData(array $data)
    {
        foreach ($data as $key => $field) {
            if (in_array($key, $this->updatingFields)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get customer id by email.
     *
     * @param string $email
     * @return int|null
     */
    protected function getCustomerId($email)
    {
        $url = $_ENV['app_backend_url'] . 'customer/index/grid/filter/' . $this->encodeFilter(['email' => $email]);
        $curl = new BackendDecorator(new CurlTransport(), $this->_configuration);

        $curl->write($url, [], CurlInterface::GET);
        $response = $curl->read();
        $curl->close();

        preg_match('~a href=[^\s]*\/id\/(\d+)~', $response, $match);
        return empty($match[1]) ? null : $match[1];
    }

    /**
     * Prepare customer for curl.
     *
     * @param FixtureInterface $customer
     * @return string
     */
    protected function getCustomerGroup(FixtureInterface $customer)
    {
        return $customer->hasData('group_id')
            ? $customer->getDataFieldConfig('group_id')['source']->getCustomerGroup()->getCustomerGroupId()
            : self::GENERAL_GROUP;
    }

    /**
     * Update customer account.
     *
     * @param array $data
     * @return void
     * @throws \Exception
     */
    protected function updateCustomer(array $data)
    {
        $curlData = [];
        $url = $_ENV['app_backend_url'] . 'customer/save';
        foreach ($data as $key => $value) {
            foreach ($this->curlMapping as $prefix => $prefixValues) {
                if (in_array($key, $prefixValues)) {
                    $curlData[$prefix][$key] = $value;
                    unset($data[$key]);
                }
            }
        }
        unset($data['password'], $data['confirmation']);

        $curlData = $this->replaceMappingData(array_merge($curlData, $data));
        $curlData = $this->prepareAddressData($curlData);

        $curl = new BackendDecorator(new CurlTransport(), $this->_configuration);
        $curl->write($url, $curlData);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'class="success-msg"')) {
            throw new \Exception('Failed to assign an address to the customer!');
        }
    }

    /**
     * Preparing address data for curl.
     *
     * @param array $curlData
     * @return array
     */
    protected function prepareAddressData(array $curlData)
    {
        $address = [];
        foreach (array_keys($curlData['address']) as $key) {
            $curlData['address'][$key]['_deleted'] = '';
            $curlData['address'][$key]['region'] = '';
            if (!is_array($curlData['address'][$key]['street'])) {
                $street = $curlData['address'][$key]['street'];
                $curlData['address'][$key]['street'] = [];
                $curlData['address'][$key]['street'][] = $street;
            }
            $newKey = '_item' . ($key);
            if (isset($curlData['address'][$key]['default_billing'])) {
                $value = $curlData['address'][$key]['default_billing'] === 'Yes' ? $newKey : '';
                $curlData['account']['default_billing'] = $value;
            }
            if (isset($curlData['address'][$key]['default_shipping'])) {
                $value = $curlData['address'][$key]['default_shipping'] === 'Yes' ? $newKey : '';
                $curlData['account']['default_shipping'] = $value;
            }
            $address[$newKey] = $curlData['address'][$key];
        }
        $curlData['address'] = $address;

        return $curlData;
    }

    /**
     * Encoded filter parameters.
     *
     * @param array $filter
     * @return string
     */
    protected function encodeFilter(array $filter)
    {
        $result = [];
        foreach ($filter as $name => $value) {
            $result[] = "{$name}={$value}";
        }
        $result = implode('&', $result);

        return base64_encode($result);
    }
}
