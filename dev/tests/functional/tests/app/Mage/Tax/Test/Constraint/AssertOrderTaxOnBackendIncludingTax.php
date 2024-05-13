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

/**
 * Checks that prices displayed including tax in order are correct on backend.
 */
class AssertOrderTaxOnBackendIncludingTax extends AssertOrderTaxOnBackendExcludingIncludingTax
{
    /**
     * Verify fields for assert.
     *
     * @var array
     */
    protected $verifyFields = [
        'subtotal',
        'discount',
        'shipping',
        'grand_total_excl_tax',
        'grand_total_incl_tax',
        'tax'
    ];

    /**
     * Verifiable fields for cart items.
     *
     * @var array
     */
    protected $cartItemVerifiableFields = [
        'cart_item_price',
        'cart_item_subtotal'
    ];

    /**
     * Prepare prices for credit memo.
     *
     * @param array $prices
     * @return array
     */
    protected function preparePricesCreditMemo(array $prices)
    {
        unset($prices['shipping']);
        return $prices;
    }

    /**
     * Returns string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Prices on backend after order creation is correct.';
    }
}
