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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Paypal\Test\Constraint;

use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Sales\Test\Page\Adminhtml\SalesOrderIndex;
use Mage\Sales\Test\Page\Adminhtml\SalesOrderView;
use Mage\Adminhtml\Test\Block\Sales\Order\View\Tab\Transactions;
use Mage\Adminhtml\Test\Block\Sales\Order\View\Tab\Info;

/**
 * Assert that message like this persist on Comments History section on order page in backend:
 * "transaction_type amount of some_amount. Transaction ID: "transaction_id"
 */
class AssertTransaction extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that message like this persist on Comments History section on order page in backend:
     * "transaction_type amount of some_amount. Transaction ID: "transaction_id"
     *
     * @param SalesOrderIndex $salesOrderIndex
     * @param SalesOrderView $salesOrderView
     * @param string $orderId
     * @param string $transactionType
     * @param string $paymentAction
     * @param string $grandTotal
     * @return void
     */
    public function processAssert(
        SalesOrderIndex $salesOrderIndex,
        SalesOrderView $salesOrderView,
        $orderId,
        $transactionType,
        $paymentAction,
        $grandTotal
    ) {
        $filter = ['order_id' => $orderId, 'transaction_type' => $transactionType];

        $salesOrderIndex->open()->getSalesOrderGrid()->searchAndOpen(['id' => $orderId]);
        $orderForm = $salesOrderView->getOrderForm();

        /** Check if transaction present in comments block. */
        /** @var Info $informationTab */
        $informationTab = $orderForm->getTabElement('information');
        $text = $this->prepareSearchedText($grandTotal);
        \PHPUnit_Framework_Assert::assertTrue(
            $informationTab->getCommentsBlock()->isCommentPresent($text),
            'Searched text is not present in order comments.'
        );

        \PHPUnit_Framework_Assert::assertTrue(
            $informationTab->getCommentsBlock()->isCommentPresent($paymentAction),
            'Order has a wrong payment action.'
        );

        /** Check if transaction present in transactions Grid. */
        $orderForm->openTab('transactions');
        /** @var Transactions $transactionsTab */
        $transactionsTab = $orderForm->getTabElement('transactions');
        \PHPUnit_Framework_Assert::assertTrue(
            $transactionsTab->getGrid()->isRowVisible($filter),
            'Searched transaction is not present in transaction grid.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return "Transaction is present in transaction grid and order's comments.";
    }

    /**
     * Prepare text for search.
     *
     * @param string $grandTotal
     * @param string $currency
     * @return string
     */
    protected function prepareSearchedText($grandTotal, $currency = '$')
    {
        $amount = number_format(is_array($grandTotal) ? array_sum($grandTotal) : $grandTotal, 2);
        return "amount of " . $currency . $amount;
    }
}
