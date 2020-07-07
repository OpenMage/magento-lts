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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Sales\Test\Constraint;

use Mage\Sales\Test\Fixture\Order;
use Mage\Sales\Test\Page\Adminhtml\SalesOrderIndex;
use Mage\Sales\Test\Page\Adminhtml\SalesOrderView;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that status is correct on order page in backend.
 */
class AssertReorderedOrderStatusIsCorrect extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that status is correct on order page in backend (same with value of orderStatus variable).
     *
     * @param string $previousOrderStatus
     * @param Order $order
     * @param SalesOrderIndex $salesOrder
     * @param SalesOrderView $salesOrderView
     * @return void
     */
    public function processAssert(
        $previousOrderStatus,
        Order $order,
        SalesOrderIndex $salesOrder,
        SalesOrderView $salesOrderView
    ) {
        $salesOrder->open()->getSalesOrderGrid()->searchAndOpen(['id' => $order->getId()]);

        \PHPUnit_Framework_Assert::assertEquals(
            $previousOrderStatus,
            $salesOrderView->getOrderForm()->getTabElement('information')->getOrderInfoBlock()->getOrderStatus(),
            'Order status is incorrect on order page in backend.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Order status is correct.';
    }
}
