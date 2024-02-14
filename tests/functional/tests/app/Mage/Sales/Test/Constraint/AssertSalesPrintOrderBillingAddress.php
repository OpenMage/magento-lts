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

use Mage\Customer\Test\Fixture\Address;
use Mage\Sales\Test\Page\SalesGuestPrint;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that BillingAddress printed correctly on sales guest print page.
 */
class AssertSalesPrintOrderBillingAddress extends AbstractConstraint
{
    /**
     * Assert that BillingAddress printed correctly on sales guest print page.
     *
     * @param SalesGuestPrint $salesGuestPrint
     * @param Address $billingAddress
     * @return void
     */
    public function processAssert(SalesGuestPrint $salesGuestPrint, Address $billingAddress)
    {
        $addressRenderer = $this->objectManager->create(
            'Mage\Customer\Test\Block\Address\Renderer',
            ['address' => $billingAddress, 'type' => 'html']
        );
        $expectedBillingAddress = $addressRenderer->render();
        \PHPUnit_Framework_Assert::assertEquals(
            $expectedBillingAddress,
            $salesGuestPrint->getViewBlock()->getBillingAddress(),
            "Billing address was printed incorrectly."
        );
    }

    /**
     * Returns a string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return "Billing address printed correctly.";
    }
}
