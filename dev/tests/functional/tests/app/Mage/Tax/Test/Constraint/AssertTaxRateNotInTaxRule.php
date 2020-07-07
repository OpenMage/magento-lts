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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
