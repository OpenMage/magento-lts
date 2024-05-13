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

namespace Mage\Adminhtml\Test\Block\Sales\Invoices;

use Mage\Adminhtml\Test\Block\Sales\AbstractGrid;

/**
 * Sales invoices grid.
 */
class Grid extends AbstractGrid
{
    /**
     * Products table identifier.
     *
     * @var string
     */
    protected $tableIdentifier = '@id="order_invoices_table"';

    /**
     * Name for id column.
     *
     * @var array
     */
    protected $idColumnName = '.="Invoice #"';

    /**
     * Filters array mapping.
     *
     * @var array
     */
    protected $filters = [
        'id' => [
            'selector' => 'input[name="increment_id"]',
        ],
        'order_id' => [
            'selector' => 'input[name="order_increment_id"]',
        ],
        'from' => [
            'selector' => 'input[name*="grand_total[from]"]',
        ],
        'to' => [
            'selector' => 'input[name*="grand_total[to]"]',
        ],
    ];
}
