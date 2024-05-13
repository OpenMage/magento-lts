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

namespace Mage\Shipping\Test\Constraint;

use Mage\Sales\Test\Page\SalesGuestPrint;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that shipping method was printed correctly on sales guest print page.
 */
class AssertShippingMethodOnPrintOrder extends AbstractConstraint
{
    /**
     * Assert that shipping method was printed correctly on sales guest print page.
     *
     * @param SalesGuestPrint $salesGuestPrint
     * @param array $shipping
     * @return void
     */
    public function processAssert(SalesGuestPrint $salesGuestPrint, array $shipping)
    {
        \PHPUnit_Framework_Assert::assertTrue(
            $salesGuestPrint->getViewBlock()->isShippingMethodVisible($shipping),
            "Shipping method was printed incorrectly."
        );
    }

    /**
     * Returns a string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return "Shipping method was printed correctly.";
    }
}
