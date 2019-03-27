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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Admin\Test\Fixture\AdminUserRole;

use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Util\Protocol\CurlTransport;
use Mage\Admin\Test\Fixture\User;

/**
 * In role users data source.
 *
 * Data keys:
 *  - dataset
 */
class InRoleUsers implements FixtureInterface
{
    /**
     * Array with data set configuration settings.
     *
     * @var array
     */
    protected $params;

    /**
     * Array with Admin Users.
     *
     * @var array
     */
    protected $adminUsers;

    /**
     * Array with user names.
     *
     * @var array
     */
    protected $data;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data [optional]
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['dataset']) && $data['dataset'] !== '-') {
            $datasets = explode(',', $data['dataset']);
            foreach ($datasets as $dataset) {
                $adminUser = $fixtureFactory->createByCode('user', ['dataset' => trim($dataset)]);
                if (!$adminUser->hasData('user_id')) {
                    $adminUser->persist();
                }
                $this->adminUsers[] = $adminUser;
                $this->data[] = $adminUser->getUsername();
            }
        }
    }

    /**
     * Persist user role.
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Return array with user names.
     *
     * @param string $key [optional]
     * @return array|null
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = null)
    {
        return $this->data;
    }

    /**
     * Return data set configuration settings.
     *
     * @return array
     */
    public function getDataConfig()
    {
        return $this->params;
    }

    /**
     * Return array with admin user fixtures.
     *
     * @return array
     */
    public function getAdminUsers()
    {
        return $this->adminUsers;
    }
}
