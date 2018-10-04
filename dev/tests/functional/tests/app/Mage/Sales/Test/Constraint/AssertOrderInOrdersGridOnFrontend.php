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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Sales\Test\Constraint;

use Mage\Customer\Test\Fixture\Customer;
use Mage\Customer\Test\Page\CustomerAccountIndex;
use Mage\Sales\Test\Fixture\Order;
use Mage\Sales\Test\Page\OrderHistory;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that Order is present in orders grid on frontend.
 */
class AssertOrderInOrdersGridOnFrontend extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that order is present in Orders grid on frontend
     *
     * @param Order $order
     * @param Customer $customer
     * @param CustomerAccountIndex $customerAccountIndex
     * @param OrderHistory $orderHistory
     * @return void
     */
    public function processAssert(
        Order $order,
        Customer $customer,
        CustomerAccountIndex $customerAccountIndex,
        OrderHistory $orderHistory
    ) {
        $this->objectManager->create(
            'Mage\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
            ['customer' => $customer]
        )->run();
        $customerAccountIndex->getAccountNavigationBlock()->openNavigationItem('My Orders');
        \PHPUnit_Framework_Assert::assertTrue(
            $orderHistory->getOrderHistoryBlock()->isOrderVisible($order),
            "Order with following id {$order->getId()} is absent in Orders block on frontend."
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Sales order is present in orders grid on frontend.';
    }
}
