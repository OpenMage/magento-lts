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

namespace Mage\Checkout\Test\Constraint;

use Mage\Checkout\Test\Page\CheckoutMultishippingSuccess;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that success message is correct.
 */
class AssertOrderWithMultishippingSuccessPlacedMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Message of success checkout.
     */
    const SUCCESS_MESSAGE = 'ORDER SUCCESS';

    /**
     * Assert that success message is correct.
     *
     * @param CheckoutMultishippingSuccess $сheckoutMultishippingSuccess
     * @return void
     */
    public function processAssert(CheckoutMultishippingSuccess $сheckoutMultishippingSuccess)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $сheckoutMultishippingSuccess->getTitleBlock()->getTitle()
        );
    }

    /**
     * Returns string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return 'Success message on Checkout onepage success page is correct.';
    }
}
