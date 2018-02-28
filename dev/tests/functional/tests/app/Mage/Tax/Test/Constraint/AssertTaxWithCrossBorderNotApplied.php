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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Tax\Test\Constraint;

/**
 * Checks that prices on category, product and cart pages are equal for each customer.
 */
class AssertTaxWithCrossBorderNotApplied extends AbstractAssertTaxWithCrossBorderApplying
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert prices on category, product and cart pages are equal for each customer.
     *
     * @param array $actualPrices
     * @return void
     */
    public function assert(array $actualPrices)
    {
        //Prices verification
        \PHPUnit_Framework_Assert::assertNotEmpty(
            array_diff($actualPrices[0], $actualPrices[1]),
            'Prices for customers should be different.'
        );
    }

    /**
     * Returns string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Cross border trading is not applied on front.';
    }
}
