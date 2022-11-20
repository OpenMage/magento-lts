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

use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Tax\Test\Fixture\TaxRate;
use Mage\Tax\Test\Page\Adminhtml\TaxRateIndex;
use Mage\Tax\Test\Page\Adminhtml\TaxRateEdit;

/**
 * Assert that necessary tax rate percent is in correct range between 0 and 100.
 */
class AssertTaxRateIsInCorrectRange extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that necessary tax rate percent is in correct range between 0 and 100.
     *
     * @param TaxRateIndex $taxRateIndex
     * @param TaxRateEdit $taxRateEdit
     * @param TaxRate $taxRate
     * @return void
     */
    public function processAssert(TaxRateIndex $taxRateIndex, TaxRateEdit $taxRateEdit, TaxRate $taxRate)
    {
        $taxRateCode = $taxRate->getCode();
        $taxRateIndex->open()->getTaxRatesGrid()->searchAndOpen(['code' => $taxRateCode]);
        $ratePercentage = $taxRateEdit->getTaxRateForm()->getTaxRatePercentage();

        \PHPUnit_Framework_Assert::assertTrue(
            $ratePercentage >= 0 && $ratePercentage <= 100,
            "$taxRateCode rate percent $ratePercentage is not in correct range between 0 and 100."
        );
    }

    /**
     * Returns string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Necessary tax rate percent is in correct range between 0 and 100.';
    }
}
