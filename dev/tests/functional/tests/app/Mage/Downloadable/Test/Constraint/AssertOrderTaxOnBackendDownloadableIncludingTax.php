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

namespace Mage\Downloadable\Test\Constraint;

use Mage\Tax\Test\Constraint\AssertOrderTaxOnBackendIncludingTax;

/**
 * Checks that prices displayed including tax in order are correct on backend.
 */
class AssertOrderTaxOnBackendDownloadableIncludingTax extends AssertOrderTaxOnBackendIncludingTax
{
    /**
     * Verify fields for assert.
     *
     * @var array
     */
    protected $verifyFields = [
        'subtotal',
        'discount',
        'grand_total_excl_tax',
        'grand_total_incl_tax',
        'tax'
    ];

    /**
     * Prepare prices for credit memo.
     *
     * @param array $prices
     * @return array
     */
    protected function preparePricesCreditMemo(array $prices)
    {
        return $prices;
    }
}
