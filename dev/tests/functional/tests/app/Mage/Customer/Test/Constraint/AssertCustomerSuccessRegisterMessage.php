<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Customer\Test\Constraint;

use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Customer\Test\Page\CustomerAccountCreate;

/**
 * Check that success message is displayed after customer registered on frontend.
 */
class AssertCustomerSuccessRegisterMessage extends AbstractConstraint
{
    /**
     * Text value to be checked.
     */
    const SUCCESS_MESSAGE = 'Thank you for registering with Main Website Store.';

    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that success message is displayed after customer registered on frontend.
     *
     * @param CustomerAccountCreate $registerPage
     * @return void
     */
    public function processAssert(CustomerAccountCreate $registerPage)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $registerPage->getMessagesBlock()->getSuccessMessages(),
            'Wrong success message is displayed.'
        );
    }

    /**
     * Text of success register message is displayed.
     *
     * @return string
     */
    public function toString()
    {
        return "Customer is successfully registered.";
    }
}
