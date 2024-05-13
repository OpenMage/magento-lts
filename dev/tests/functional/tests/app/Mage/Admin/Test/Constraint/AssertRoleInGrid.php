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

use Mage\Admin\Test\Fixture\Role;
use Mage\Admin\Test\Page\Adminhtml\UserRoleIndex;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Asserts that saved role is present in Role Grid.
 */
class AssertRoleInGrid extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Asserts that saved role is present in Role Grid.
     *
     * @param UserRoleIndex $rolePage
     * @param Role $role
     * @return void
     */
    public function processAssert(UserRoleIndex $rolePage, Role $role)
    {
        $filter = ['rolename' => $role->getRoleName()];
        $rolePage->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $rolePage->getRoleGrid()->isRowVisible($filter),
            'Role with name \'' . $filter['rolename'] . '\' is absent in Roles grid.'
        );
    }

    /**
     * Return string representation of object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Role is present in Roles grid.';
    }
}
