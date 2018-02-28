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
