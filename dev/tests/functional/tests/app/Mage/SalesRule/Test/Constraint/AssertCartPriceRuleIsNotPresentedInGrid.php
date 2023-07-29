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

use Mage\SalesRule\Test\Fixture\SalesRule;
use Mage\SalesRule\Test\Page\Adminhtml\PromoQuoteIndex;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that sales rule is not present in cart price rules grid.
 */
class AssertCartPriceRuleIsNotPresentedInGrid extends AbstractConstraint
{
    /**
     * Assert that sales rule is not present in cart price rules grid.
     *
     * @param PromoQuoteIndex $promoQuoteIndex
     * @param SalesRule $salesRule
     * @return void
     */
    public function processAssert(PromoQuoteIndex $promoQuoteIndex, SalesRule $salesRule)
    {
        $promoQuoteIndex->open();
        \PHPUnit_Framework_Assert::assertFalse(
            $promoQuoteIndex->getPromoQuoteGrid()->isRowVisible(['name' => $salesRule->getName()]),
            "Sales rule {$salesRule->getName()} is present in cart price rules grid."
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Sales rule is not present in cart price rules grid.';
    }
}
