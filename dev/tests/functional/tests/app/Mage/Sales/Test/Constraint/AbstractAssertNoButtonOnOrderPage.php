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
