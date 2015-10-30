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

namespace Mage\Admin\Test\Constraint;

use Mage\Admin\Test\Fixture\User;
use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Adminhtml\Test\Page\AdminAuthLogin;

/**
 * Verify incorrect credentials message while login to admin.
 */
class AssertUserWrongCredentialsMessage extends AbstractConstraint
{
    /**
     * Credentials error message.
     */
    const INVALID_CREDENTIALS_MESSAGE = 'Invalid User Name or Password.';

    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Verify incorrect credentials message while login to admin.
     *
     * @param AdminAuthLogin $adminAuth
     * @param User $customAdmin
     * @return void
     */
    public function processAssert(AdminAuthLogin $adminAuth, User $customAdmin)
    {
        $adminAuth->open();
        $adminAuth->getLoginBlock()->loginToAdminPanel($customAdmin->getData());

        \PHPUnit_Framework_Assert::assertEquals(
            self::INVALID_CREDENTIALS_MESSAGE,
            $adminAuth->getMessagesBlock()->getErrorMessages(),
            'Message "' . self::INVALID_CREDENTIALS_MESSAGE . '" is not visible.'
        );
    }

    /**
     * Returns success message if equals to expected message.
     *
     * @return string
     */
    public function toString()
    {
        return 'Invalid credentials message was displayed.';
    }
}
