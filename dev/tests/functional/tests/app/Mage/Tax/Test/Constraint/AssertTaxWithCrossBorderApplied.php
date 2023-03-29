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

namespace Mage\Tax\Test\Constraint;

/**
 * Checks that prices on category, product and cart pages are equal for both customers.
 */
class AssertTaxWithCrossBorderApplied extends AbstractAssertTaxWithCrossBorderApplying
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert prices on category, product and cart pages are equal for both customers.
     *
     * @param array $actualPrices
     * @return void
     */
    public function assert(array $actualPrices)
    {
        //Prices verification
        \PHPUnit_Framework_Assert::assertEmpty(
            array_diff($actualPrices[0], $actualPrices[1]),
            'Prices for customers should be equal. Cross border is not applied.'
        );
    }

    /**
     * Returns string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Cross border trading is applied on front.';
    }
}
