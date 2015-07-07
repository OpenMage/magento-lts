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

namespace Mage\Admin\Test\Handler\User;

use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Handler\Curl as AbstractCurl;
use Magento\Mtf\Util\Protocol\CurlInterface;
use Magento\Mtf\Util\Protocol\CurlTransport;
use Magento\Mtf\Util\Protocol\CurlTransport\BackendDecorator;

/**
 * Creates Admin User Entity.
 */
class Curl extends AbstractCurl implements UserInterface
{
    /**
     * Mapping values for data.
     *
     * @var array
     */
    protected $mappingData = [
        'is_active' => [
            'Active' => 1
        ]
    ];

    /**
     * Curl creation of Admin User.
     *
     * @param FixtureInterface $fixture
     * @return array|mixed
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        /** @var \Mage\Admin\Test\Fixture\User $fixture */
        $data = $this->replaceMappingData($fixture->getData());
        if ($fixture->hasData('role_id')) {
            $data['roles[]'] = $fixture->getDataFieldConfig('role_id')['source']->getRole()->getRoleId();
        }
        $url = $_ENV['app_backend_url'] . 'permissions_user/save/';
        $curl = new BackendDecorator(new CurlTransport(), $this->_configuration);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.1', [], $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'class="success-msg"')) {
            throw new \Exception("Admin user entity creating by curl handler was not successful! Response: $response");
        }

        return ['user_id' => $this->getId()];
    }

    /**
     * Get created user id.
     *
     * @return string
     */
    protected function getId()
    {
        $url = $_ENV['app_backend_url'] . 'permissions_user/roleGrid/sort/user_id/dir/desc/';
        $curl = new BackendDecorator(new CurlTransport(), $this->_configuration);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0');
        $response = $curl->read();
        $curl->close();

        preg_match('@edit/user_id/(\d+)/@siu', $response, $matches);
        return $matches[1];
    }
}
