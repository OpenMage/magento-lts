<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Tests
 * @package    Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Admin\Test\Constraint;

use Mage\Admin\Test\Fixture\User;
use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Admin\Test\Page\Adminhtml\UserIndex;

/**
 * Asserts that user is present in User Grid.
 */
class AssertUserInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Asserts that user is present in User Grid.
     *
     * @param UserIndex $userIndex
     * @param User $user
     * @param User $customAdmin
     * @return void
     */
    public function processAssert(
        UserIndex $userIndex,
        User $user,
        User $customAdmin = null
    ) {
        $adminUser = ($user->hasData('password') || $user->hasData('username')) ? $user : $customAdmin;
        $filter = ['username' => $adminUser->getUsername()];
        $userIndex->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $userIndex->getUserGrid()->isRowVisible($filter),
            'User with name \'' . $adminUser->getUsername() . '\' is absent in User grid.'
        );
    }

    /**
     * Return string representation of object.
     *
     * @return string
     */
    public function toString()
    {
        return 'User is present in Users grid.';
    }
}
