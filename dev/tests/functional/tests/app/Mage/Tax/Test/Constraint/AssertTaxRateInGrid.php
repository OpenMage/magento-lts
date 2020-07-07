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

use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Tax\Test\Fixture\TaxRate;
use Mage\Tax\Test\Page\Adminhtml\TaxRateIndex;

/**
 * Assert tax rule availability in Tax Rate grid.
 */
class AssertTaxRateInGrid extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    /**
     * Assert tax rule availability in Tax Rate grid.
     *
     * @param TaxRateIndex $taxRateIndexPage
     * @param TaxRate $taxRate
     * @return void
     */
    public function processAssert(TaxRateIndex $taxRateIndexPage, TaxRate $taxRate)
    {
        $data = $taxRate->getData();
        $filter = [
            'code' => $data['code'],
            'tax_country_id' => $data['tax_country_id'],
            'tax_postcode' => $data['zip_is_range'] === 'No'
                ? $data['tax_postcode']
                : $data['zip_from'] . '-' . $data['zip_to']
        ];

        $taxRateIndexPage->open();

        \PHPUnit_Framework_Assert::assertTrue(
            $taxRateIndexPage->getTaxRatesGrid()->isRowVisible($filter),
            "Tax Rate {$filter['code']} is absent in Tax Rate grid."
        );
    }

    /**
     * Returns string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Tax rate is present in grid.';
    }
}
