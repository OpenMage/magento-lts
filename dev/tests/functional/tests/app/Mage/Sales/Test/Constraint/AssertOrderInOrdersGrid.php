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
