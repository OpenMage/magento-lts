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

namespace Mage\Adminhtml\Test\Block\Sales\Shipment;

use Mage\Adminhtml\Test\Block\Sales\AbstractGrid;

/**
 * Sales shipment grid.
 */
class Grid extends AbstractGrid
{
    /**
     * Products table identifier.
     *
     * @var string
     */
    protected $tableIdentifier = '@id="order_shipments_table"';

    /**
     * Name for id column.
     *
     * @var array
     */
    protected $idColumnName = '.="Shipment #"';

    /**
     * Filters array mapping.
     *
     * @var array
     */
    protected $filters = [
        'id' => [
            'selector' => 'input[name="increment_id"]'
        ],
        'total_qty_from' => [
            'selector' => 'input[name="total_qty[from]"]'
        ],
        'total_qty_to' => [
            'selector' => 'input[name="total_qty[to]"]'
        ]
    ];
}
