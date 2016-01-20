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

namespace Mage\Admin\Test\TestCase;

use Mage\Admin\Test\Fixture\Role;
use Mage\Admin\Test\Page\Adminhtml\UserEdit;
use Mage\Admin\Test\Page\Adminhtml\UserIndex;
use Mage\Admin\Test\Fixture\User;
use Mage\Adminhtml\Test\Fixture\StoreGroup;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\TestCase\Injectable;

/**
 * Preconditions:
 * 1. Custom admin user is created.
 * 2. Custom Store is created.
 * 3. Custom ACL role is created with role scope - store from preconditions and role scope - Sales.
 *
 * Steps:
 * 1. Login as Admin User.
 * 2. Go to System -> Permissions -> Users. Find user and open to edit it.
 * 3. Go to User Role tab and assign user role from preconditions. Save changes.
 * 4. Perform assertions.
 *
 * @group ACL_(MX)
 * @ZephyrId MPERF-7602
 */
class UseAclRoleWithRestrictedGwsScopeTest extends Injectable
{
    /**
     * User index page.
     *
     * @var UserIndex
     */
    protected $userIndexPage;

    /**
     * User edit page.
     *
     * @var UserEdit
     */
    protected $userEditPage;

    /**
     * Factory of fixtures.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Preconditions for test.
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        /** @var Role $role */
        $role = $fixtureFactory->createByCode('role', ['dataSet' => 'custom_with_gws_scope']);
        $role->persist();
        $user = $fixtureFactory->createByCode('user', ['dataSet' => 'admin_without_role']);
        $user->persist();

        $this->fixtureFactory = $fixtureFactory;

        return ['user' => $user, 'role' => $role];
    }

    /**
     * Injection data.
     *
     * @param UserIndex $userIndex
     * @param UserEdit $userEdit
     * @return void
     */
    public function __inject(UserIndex $userIndex, UserEdit $userEdit)
    {
        $this->userIndexPage = $userIndex;
        $this->userEditPage = $userEdit;
    }

    /**
     * Run Use ACL Role with Restricted GWS Scope test.
     *
     * @param User $user
     * @param Role $role
     * @return array
     */
    public function test(User $user, Role $role)
    {
        // Steps:
        $this->userIndexPage->open();
        $this->userIndexPage->getUserGrid()->searchAndOpen(['email' => $user->getEmail()]);
        $this->userEditPage->getUserForm()->fill($this->prepareUser($user, $role));
        $this->userEditPage->getFormPageActions()->save();

    }

    /**
     * Prepare user for test.
     *
     * @param User $user
     * @param Role $role
     * @return User
     */
    protected function prepareUser(User $user, Role $role)
    {
        $userData = $user->getData();
        $userData['role_id'] = ['role' => $role];
        unset($userData['user_id']);
        unset($userData['password_confirmation']);

        return $this->fixtureFactory->createByCode('user', ['data' => $userData]);
    }

}
