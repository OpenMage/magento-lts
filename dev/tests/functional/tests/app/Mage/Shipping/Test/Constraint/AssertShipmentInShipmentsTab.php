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

namespace Mage\Shipping\Test\Constraint;

use Mage\Sales\Test\Fixture\Order;
use Mage\Sales\Test\Constraint\AbstractAssertSalesEntityInSalesEntityTab;

/**
 * Assert that shipment is present in the shipments tab with correct shipped items quantity.
 */
class AssertShipmentInShipmentsTab extends AbstractAssertSalesEntityInSalesEntityTab
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Entity type.
     *
     * @var string
     */
    protected $entityType = 'shipment';

    /**
     * Error message.
     *
     * @var string
     */
    protected $errorMessage = 'Shipment is absent in shipments tab.';

    /**
     * Specials filter fields for shipment.
     *
     * @var array
     */
    protected $specialFilterFields = [
        'totalQtyOrdered' => [
            'total_qty_from',
            'total_qty_to'
        ]
    ];

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Shipment is present on shipments tab.';
    }
}
