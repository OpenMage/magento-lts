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

namespace Mage\Tax\Test\Constraint;

use Mage\Tax\Test\Fixture\TaxRule;
use Mage\Tax\Test\Page\Adminhtml\TaxRuleIndex;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Check that tax rule availability in Tax Rule grid.
 */
class AssertTaxRuleInGrid extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    /**
     * Assert tax rule availability in Tax Rule grid.
     *
     * @param TaxRuleIndex $taxRuleIndex
     * @param TaxRule $taxRule
     * @return void
     */
    public function processAssert(TaxRuleIndex $taxRuleIndex, TaxRule $taxRule)
    {
        $filter = ['code' => $taxRule->getCode()];

        $taxRuleIndex->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $taxRuleIndex->getTaxRuleGrid()->isRowVisible($filter),
            "Tax Rule '{$filter['code']}' is absent in Tax Rule grid."
        );
    }

    /**
     * Returns string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Tax rule is present in grid.';
    }
}
