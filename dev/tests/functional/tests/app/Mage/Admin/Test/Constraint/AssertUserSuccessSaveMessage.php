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

namespace Mage\Admin\Test\Constraint;

use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Admin\Test\Page\Adminhtml\UserIndex;

/**
 * Assert that correct success message appears after click on 'Save User' button.
 */
class AssertUserSuccessSaveMessage extends AbstractConstraint
{
    /**
     * User success save message.
     */
    const SUCCESS_SAVE_MESSAGE = 'The user has been saved.';

    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Asserts that success message equals to expected message.
     *
     * @param UserIndex $userIndex
     * @return void
     */
    public function processAssert(UserIndex $userIndex)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_SAVE_MESSAGE,
            $userIndex->getMessagesBlock()->getSuccessMessages(),
            'Wrong success save message is displayed.'
        );
    }

    /**
     * Returns success message if equals to expected message.
     *
     * @return string
     */
    public function toString()
    {
        return 'Success message on User Index page is correct.';
    }
}
