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

use Mage\Tax\Test\Fixture\TaxRate;
use Mage\Tax\Test\Page\Adminhtml\TaxRuleIndex;
use Mage\Tax\Test\Page\Adminhtml\TaxRuleNew;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that necessary tax rate is present in "Tax Rule Information" on TaxRuleEdit page.
 */
class AssertTaxRateInTaxRule extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that necessary tax rate is present in "Tax Rule Information" on TaxRuleEdit page.
     *
     * @param TaxRuleIndex $taxRuleIndex
     * @param TaxRuleNew $taxRuleNew
     * @param TaxRate $taxRate
     * @return void
     */
    public function processAssert(TaxRuleIndex $taxRuleIndex, TaxRuleNew $taxRuleNew, TaxRate $taxRate)
    {
        $taxRateCode = $taxRate->getCode();
        $taxRuleIndex->open()->getPageActionsBlock()->addNew();

        \PHPUnit_Framework_Assert::assertTrue(
            $taxRuleNew->getTaxRuleForm()->isTaxRateAvailable($taxRateCode),
            "$taxRateCode is not present in Tax Rates multiselect on TaxRuleEdit page."
        );
    }

    /**
     * Returns string representation of object.
     *
     * @return string
     */
    public function toString()
    {
        return "Necessary tax rate is present in Tax Rule Information on TaxRuleEdit page.";
    }
}
