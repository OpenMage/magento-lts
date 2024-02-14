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

use Mage\Sales\Test\Fixture\Order;
use Mage\Sales\Test\Page\Adminhtml\SalesOrderIndex;
use Mage\Sales\Test\Page\Adminhtml\SalesOrderView;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Abstract assert that 'Button name' button is absent order page.
 */
abstract class AbstractAssertNoButtonOnOrderPage extends AbstractConstraint
{
    /**
     * Button name for verify.
     *
     * @var string
     */
    protected $buttonName;

    /**
     * Assert that 'Button name' button is absent order page.
     *
     * @param SalesOrderView $salesOrderView
     * @param SalesOrderIndex $orderIndex
     * @param Order $order
     * @param string|null $orderId
     * @return void
     */
    public function processAssert(
        SalesOrderView $salesOrderView,
        SalesOrderIndex $orderIndex,
        Order $order = null,
        $orderId = null
    ) {
        $orderIndex->open();
        $orderId = ($orderId == null) ? $order->getId() : $orderId;
        $orderIndex->getSalesOrderGrid()->searchAndOpen(['id' => $orderId]);
        \PHPUnit_Framework_Assert::assertFalse(
            $salesOrderView->getPageActions()->isActionButtonVisible($this->buttonName),
            "'$this->buttonName' button is present on order view page."
        );
    }
}
