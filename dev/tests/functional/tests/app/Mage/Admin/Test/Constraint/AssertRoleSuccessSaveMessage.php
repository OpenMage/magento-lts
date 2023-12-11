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

use Mage\Admin\Test\Page\Adminhtml\UserRoleIndex;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Asserts that success message equals to expected message.
 */
class AssertRoleSuccessSaveMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    const SUCCESS_MESSAGE = 'The role has been successfully saved.';

    /**
     * Asserts that success message equals to expected message.
     *
     * @param UserRoleIndex $rolePage
     * @return void
     */
    public function processAssert(UserRoleIndex $rolePage)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $rolePage->getMessagesBlock()->getSuccessMessages()
        );
    }

    /**
     * Return string representation of object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Success message on roles page is correct.';
    }
}
