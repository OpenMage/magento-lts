<?php
/**
 * OpenMage
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

namespace Mage\Downloadable\Test\Constraint;

use Mage\Tax\Test\Constraint\AssertTaxCalculationAfterCheckoutExcludingIncludingTax;

/**
 * Checks that prices excl and incl tax on order review and customer order pages are equal to specified in dataset.
 */
class AssertTaxCalculationAfterCheckoutDownloadableExcludingIncludingTax
    extends AssertTaxCalculationAfterCheckoutExcludingIncludingTax
{
    /**
     * Verify fields for assert.
     *
     * @var array
     */
    protected $verifyFields = [
        'subtotal_excl_tax',
        'subtotal_incl_tax',
        'discount',
        'grand_total_excl_tax',
        'grand_total_incl_tax',
        'tax'
    ];

    /**
     * Product type for review items block.
     *
     * @var string
     */
    protected $productType = 'downloadable';
}
