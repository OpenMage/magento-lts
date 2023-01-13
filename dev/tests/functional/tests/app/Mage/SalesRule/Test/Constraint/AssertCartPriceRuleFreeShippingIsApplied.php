<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\SalesRule\Test\Constraint;

/**
 * Assert that free shipping is applied in shopping cart.
 */
class AssertCartPriceRuleFreeShippingIsApplied extends AbstractCartPriceRuleApplying
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Free shipping price.
     */
    const FREE_SHIPPING_PRICE = '0.00';

    /**
     * Assert that free shipping is applied in shopping cart.
     *
     * @return void
     */
    protected function assert()
    {
        \PHPUnit_Framework_Assert::assertEquals(
            $this->checkoutCart->getTotalsBlock()->getData('shipping_price'),
            self::FREE_SHIPPING_PRICE,
            "Free shipping hasn't been applied."
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Free shipping is applied.';
    }
}
