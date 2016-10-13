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

namespace Mage\Admin\Test\Handler\Role;

use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Handler\Curl as AbstractCurl;
use Magento\Mtf\Util\Protocol\CurlInterface;
use Magento\Mtf\Util\Protocol\CurlTransport;
use Magento\Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mage\Admin\Test\Fixture\Role;

/**
 * Creates User Role Entity.
 */
class Curl extends AbstractCurl implements RoleInterface
{
    /**
     * Mapping values for data.
     *
     * @var array
     */
    protected $mappingData = [
        'gws_is_all' => [
            'Custom' => 0,
            'All' => 1
        ],
        'resource_access' => [
            'Custom' => 0,
            'All' => 1
        ]
    ];

    /**
     * Curl creation of User Role.
     *
     * @param FixtureInterface $fixture [optional]
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $this->prepareData($fixture);
        $url = $_ENV['app_backend_url'] . 'permissions_role/saverole/';
        $curl = new BackendDecorator(new CurlTransport(), $this->_configuration);
        $curl->write($url, $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'class="success-msg"')) {
            throw new \Exception("Role entity creating by curl handler was not successful! Response: $response");
        }

        return ['role_id' => $this->getRoleId($response)];
    }

    /**
     * Prepare data.
     *
     * @param FixtureInterface $fixture
     * @return array
     */
    protected function prepareData(FixtureInterface $fixture){
        /** @var Role $fixture */
        $data = $this->replaceMappingData($fixture->getData());
        if (isset($data['roles_resources'])) {
            $data['resource'] = $this->prepareRolesResources($data['roles_resources']);
            unset($data['roles_resources']);
        }

        return $data;
    }

    /**
     * Prepare roles resources.
     *
     * @param array $resources
     * @return string
     */
    protected function prepareRolesResources(array $resources)
    {
        $data = '__root__,';
        $data .= implode(',', $resources);
        $result = str_replace(',', ',admin/', $data);

        return $result;
    }

    /**
     * Get role id.
     *
     * @param string $response
     * @return int
     * @throws \Exception
     */
    protected function getRoleId($response)
    {
        preg_match_all('~title="http[^\s]*\/rid\/(\d+)~', $response, $matches);
        if (empty($matches)) {
            throw new \Exception('Cannot find user role id');
        }

        return max($matches[1]);
    }
}
