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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Sales\Test\Constraint;

use Mage\Sales\Test\Page\Adminhtml\SalesOrderIndex;
use Mage\Sales\Test\Page\Adminhtml\SalesOrderView;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that specified in data set buttons exist on order page in backend.
 */
class AssertOrderButtonsAvailable extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that specified in data set buttons exist on order page in backend.
     *
     * @param SalesOrderIndex $salesOrder
     * @param SalesOrderView $salesOrderView
     * @param string $orderButtonsAvailable
     * @param string $orderId
     * @return void
     */
    public function processAssert(
        SalesOrderIndex $salesOrder,
        SalesOrderView $salesOrderView,
        $orderButtonsAvailable,
        $orderId
    ) {
        $salesOrder->open()->getSalesOrderGrid()->searchAndOpen(['id' => $orderId]);
        $absentButtons = $this->getAbsentButtons($salesOrderView, $orderButtonsAvailable);

        \PHPUnit_Framework_Assert::assertEmpty(
            $absentButtons,
            "Next buttons was not found on page: \n" . implode(";\n", $absentButtons)
        );
    }

    /**
     * Get absent buttons.
     *
     * @param SalesOrderView $salesOrderView
     * @param string $orderButtonsAvailable
     * @return array
     */
    protected function getAbsentButtons(SalesOrderView $salesOrderView, $orderButtonsAvailable)
    {
        $buttons = explode(',', $orderButtonsAvailable);
        $absentButtons = [];
        $actionsBlock = $salesOrderView->getPageActions();
        foreach ($buttons as $button) {
            if (!$actionsBlock->isActionButtonVisible(trim($button))) {
                $absentButtons[] = $button;
            }
        }
        return $absentButtons;
    }

    /**
     * Returns string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return "All buttons are available on order page.";
    }
}
