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

namespace Mage\SalesRule\Test\Constraint;

/**
 * Assert that price rule is applied to shipping amount.
 */
class AssertCartPriceRuleIsAppliedToShipping extends AbstractCartPriceRuleApplying
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that calculated grand total(including shipping) equals with grand total.
     *
     * @return void
     */
    public function assert()
    {
        $subTotal = $this->checkoutCart->getTotalsBlock()->getData('subtotal');
        $grandTotal = $this->checkoutCart->getTotalsBlock()->getData('grand_total');
        $shippingPrice = $this->checkoutCart->getTotalsBlock()->getData('shipping_price');
        $discount = $this->checkoutCart->getTotalsBlock()->getData('discount');
        $calculatedGrandTotal = number_format(((float)$subTotal + (float)$shippingPrice - (float)$discount), 2);
        \PHPUnit_Framework_Assert::assertEquals(
            $calculatedGrandTotal,
            $grandTotal,
            "Calculated grand total: '$calculatedGrandTotal' not equals with grand total: '$grandTotal' \n"
            . "Price rule hasn't been applied to shipping amount."
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return "Price rule is applied to shipping amount.";
    }
}
