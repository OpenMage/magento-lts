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

namespace Mage\Customer\Test\Constraint;

use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Customer\Test\Fixture\Customer;
use Mage\Customer\Test\Page\Adminhtml\CustomerNew;

/**
 * Check that error message is displayed after customer with invalid email save.
 */
class AssertCustomerInvalidEmail extends AbstractConstraint
{
    /**
     * Text value to be checked.
     */
    const ERROR_EMAIL_MESSAGE = '"Email" is not a valid hostname.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert that error message is displayed after customer with invalid email save.
     *
     * @param Customer $customer
     * @param CustomerNew $pageCustomerNew
     * @return void
     */
    public function processAssert(Customer $customer, CustomerNew $pageCustomerNew)
    {
        $expectMessage = str_replace('%email%', $customer->getEmail(), self::ERROR_EMAIL_MESSAGE);
        $message = $pageCustomerNew->getMessagesBlock()->getErrorMessages();
        $actualMessage = explode("\n", $message[0]);

        \PHPUnit_Framework_Assert::assertEquals(
            $expectMessage,
            $actualMessage[0],
            'Wrong error message is displayed.'
        );
    }

    /**
     * Text success display error message
     *
     * @return string
     */
    public function toString()
    {
        return 'Assert that error message is displayed.';
    }
}
