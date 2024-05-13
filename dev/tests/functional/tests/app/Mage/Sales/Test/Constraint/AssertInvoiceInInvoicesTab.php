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

namespace Mage\Sales\Test\Constraint;

use Mage\Adminhtml\Test\Block\Widget\Grid;

/**
 * Assert that invoice is present in the invoices tab of the order with corresponding amount(Grand Total).
 */
class AssertInvoiceInInvoicesTab extends AbstractAssertSalesEntityInSalesEntityTab
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Entity type.
     *
     * @var string
     */
    protected $entityType = 'invoice';

    /**
     * Error message.
     *
     * @var string
     */
    protected $errorMessage = 'Invoice is absent in invoices tab.';

    /**
     * Specials filter fields for invoice.
     *
     * @var array
     */
    protected $specialFilterFields = [
        'grandTotal' => [
            'from',
            'to'
        ]
    ];

    /**
     * Check visible item in grid.
     *
     * @param Grid $grid
     * @param array $filter
     * @return bool
     */
    protected function isItemInGridVisible($grid, array $filter)
    {
        return $grid->isRowVisible($filter, true, false);
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Invoice is present on invoices tab.';
    }
}
