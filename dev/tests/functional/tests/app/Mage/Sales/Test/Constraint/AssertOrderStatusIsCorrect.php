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

namespace Mage\Sales\Test\Constraint;

use Mage\Sales\Test\Page\Adminhtml\SalesOrderIndex;
use Mage\Sales\Test\Page\Adminhtml\SalesOrderView;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that status is correct on order page in backend (same with value of orderStatus variable).
 */
class AssertOrderStatusIsCorrect extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that status is correct on order page in backend (same with value of orderStatus variable).
     *
     * @param SalesOrderIndex $salesOrder
     * @param SalesOrderView $salesOrderView
     * @param string $status
     * @param string $orderId
     * @return void
     */
    public function processAssert(SalesOrderIndex $salesOrder, SalesOrderView $salesOrderView, $status, $orderId)
    {
        $salesOrder->open()->getSalesOrderGrid()->searchAndOpen(['id' => $orderId]);

        \PHPUnit_Framework_Assert::assertEquals(
            $status,
            $salesOrderView->getOrderForm()->getTabElement('information')->getOrderInfoBlock()->getOrderStatus()
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
