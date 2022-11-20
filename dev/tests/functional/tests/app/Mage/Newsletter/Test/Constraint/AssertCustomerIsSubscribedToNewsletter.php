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

namespace Mage\Newsletter\Test\Constraint;

use Mage\Customer\Test\Fixture\Customer;
use Mage\Newsletter\Test\Page\Adminhtml\SubscriberIndex;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Check that customer is subscribed to newsletter.
 */
class AssertCustomerIsSubscribedToNewsletter extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert customer is subscribed to newsletter.
     *
     * @param Customer $customer
     * @param SubscriberIndex $subscriberIndex
     * @return void
     */
    public function processAssert(Customer $customer, SubscriberIndex $subscriberIndex)
    {
        $filter = [
            'email' => $customer->getEmail(),
            'firstname' => $customer->getFirstname(),
            'lastname' => $customer->getLastname(),
            'status' => 'Subscribed'
        ];

        $subscriberIndex->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $subscriberIndex->getSubscriberGrid()->isRowVisible($filter),
            "Customer with email " . $customer->getEmail() . " is absent in Newsletter Subscribers grid."
        );
    }

    /**
     * Text of successful customer's subscription to newsletter.
     *
     * @return string
     */
    public function toString()
    {
        return "Customer is subscribed to newsletter.";
    }
}
