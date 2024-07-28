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
 * Checks that prices displayed excluding and including tax in order are correct on backend.
 */
class AssertOrderTaxOnBackendExcludingIncludingTax extends AbstractAssertOrderTaxOnBackend
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    /**
     * Get invoice new totals.
     *
     * @return array
     */
    public function getInvoiceNewTotals()
    {
        $totalsBlock = $this->orderInvoiceNew->getTotalsBlock();
        return $this->getTypeBlockData($totalsBlock);
    }

    /**
     * Get Credit Memo new totals.
     *
     * @return array
     */
    public function getCreditMemoNewTotals()
    {
        $totalsBlock = $this->orderCreditMemoNew->getTotalsBlock();
        return $this->getTypeBlockData($totalsBlock);
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
