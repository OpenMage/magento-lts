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
use Mage\Tax\Test\Page\Adminhtml\TaxRuleNew;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that tax rate is absent in tax rule form.
 */
class AssertTaxRateNotInTaxRule extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    /**
     * Assert that tax rate is absent in tax rule form.
     *
     * @param TaxRate $taxRate
     * @param TaxRuleNew $taxRuleNew
     * @return void
     */
    public function processAssert(TaxRate $taxRate, TaxRuleNew $taxRuleNew) {
        $taxRuleNew->open();
        $taxCode = $taxRate->getCode();
        \PHPUnit_Framework_Assert::assertFalse(
            $taxRuleNew->getTaxRuleForm()->isTaxRateAvailable($taxCode),
            "Tax Rate '$taxCode' is present in Tax Rule form."
        );
    }

    /**
     * Returns string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Tax rate is absent in tax rule from.';
    }
}
