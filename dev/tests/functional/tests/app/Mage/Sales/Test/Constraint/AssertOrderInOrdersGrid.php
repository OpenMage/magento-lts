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

namespace Mage\Sales\Test\Constraint;

use Mage\Sales\Test\Page\Adminhtml\SalesOrderIndex;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that Order is present in orders grid on backend.
 */
class AssertOrderInOrdersGrid extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that Order is present in orders grid on backend.
     *
     * @param SalesOrderIndex $salesOrder
     * @param string $orderId
     * @return void
     */
    public function processAssert(SalesOrderIndex $salesOrder, $orderId)
    {
        \PHPUnit_Framework_Assert::assertTrue(
            $salesOrder->open()->getSalesOrderGrid()->isRowVisible(['id' => $orderId]),
            "Order with id $orderId is absent in orders grid on backend."
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Order is present in orders grid on backend.';
    }
}
