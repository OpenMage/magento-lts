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

namespace Mage\Admin\Test\TestCase;

use Mage\Admin\Test\Page\Adminhtml\UserEdit;
use Mage\Admin\Test\Page\Adminhtml\UserIndex;
use Mage\Admin\Test\Fixture\User;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\TestCase\Injectable;
use Magento\Mtf\Fixture\FixtureInterface;

/**
 * Test Flow:
 * 1. Log in as default admin user.
 * 2. Go to System-Permissions-Users.
 * 3. Press "Add new user" button to start create new admin user.
 * 4. Fill in all data according to data set.
 * 5. Save user.
 * 6. Perform assertions.
 *
 * @group ACL_(MX)
 * @ZephyrId MPERF-6593
 */
class CreateAdminUserEntityTest extends Injectable
{
    /**
     * User grid page.
     *
     * @var UserIndex
     */
    protected $userIndexPage;

    /**
     * User new/edit page.
     *
     * @var UserEdit
     */
    protected $userEditPage;

    /**
     * Factory for Fixtures.
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
        $this->fixtureFactory = $fixtureFactory;
        $adminUser = $fixtureFactory->createByCode('user');
        $adminUser->persist();

        return ['adminUser' => $adminUser];
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
     * Run Create Admin User Entity Test.
     *
     * @param User $user
     * @param User $adminUser
     * @param null|string $duplicatedParam
     * @param bool $isCustomRole [optional]
     * @return void
     */
    public function test(User $user, User $adminUser, $duplicatedParam = null, $isCustomRole = false)
    {
        // Prepare data
        $user = $this->prepareUser($user, $adminUser, $duplicatedParam, $isCustomRole);

        // Steps:
        $this->userIndexPage->open();
        $this->userIndexPage->getPageActionsBlock()->addNew();
        $this->userEditPage->getUserForm()->fill($user);
        $this->userEditPage->getFormPageActions()->save();
    }

    /**
     * Prepare User fixture.
     *
     * @param User $user
     * @param User $adminUser
     * @param null|string $duplicatedParam
     * @param bool $isCustomRole
     * @return User
     */
    protected function prepareUser(User $user, User $adminUser, $duplicatedParam, $isCustomRole)
    {
        if (!empty($duplicatedParam)) {
            $data = $user->getData();
            $data[$duplicatedParam] = $adminUser->getData($duplicatedParam);
            $user = $this->prepareUserFixture($user, $data);
        } elseif ($isCustomRole) {
            $data = $user->getData();
            $user = $this->prepareUserFixture($user, $data);
        }

        return $user;
    }

    /**
     * Prepare user fixture.
     *
     * @param User $user
     * @param array $data
     * @return FixtureInterface
     */
    protected function prepareUserFixture(User $user, array $data)
    {
        $data['role_id'] = ['role' => $user->getDataFieldConfig('role_id')['source']->getRole()];
        return $this->fixtureFactory->createByCode('user', ['data' => $data]);
    }
}
