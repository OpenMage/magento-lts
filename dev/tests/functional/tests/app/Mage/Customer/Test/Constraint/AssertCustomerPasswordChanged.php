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

use Mage\Customer\Test\Fixture\Customer;
use Mage\Customer\Test\Page\CustomerAccountIndex;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Mtf\Fixture\FixtureFactory;

/**
 * Check that login again to frontend with new password was success.
 */
class AssertCustomerPasswordChanged extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that login again to frontend with new password was success.
     *
     * @param FixtureFactory $fixtureFactory
     * @param CustomerAccountIndex $customerAccountIndex
     * @param Customer $initialCustomer
     * @param Customer $customer
     * @return void
     */
    public function processAssert(
        FixtureFactory $fixtureFactory,
        CustomerAccountIndex $customerAccountIndex,
        Customer $initialCustomer,
        Customer $customer
    ) {
        $customer = $fixtureFactory->createByCode(
            'customer',
            [
                'data' => [
                    'email' => $initialCustomer->getEmail(),
                    'password' => $customer->getPassword(),
                    'password_confirmation' => $customer->getPassword(),
                ],
            ]
        );
        $this->objectManager->create(
            'Mage\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
            ['customer' => $customer]
        )->run();
        \PHPUnit_Framework_Assert::assertTrue(
            $customerAccountIndex->getAccountNavigationBlock()->isVisible(),
            'Customer Account Dashboard is not visible.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer password was changed.';
    }
}
