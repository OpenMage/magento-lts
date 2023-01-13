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
