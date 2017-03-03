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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Shipping\Test\Constraint;

use Mage\Sales\Test\Constraint\AbstractAssertSalesEntityInSalesEntityGrid;
use Mage\Shipping\Test\Page\Adminhtml\SalesShipment;
use Magento\Mtf\ObjectManager;
use Magento\Mtf\System\Event\EventManagerInterface;

/**
 * Assert shipment with corresponding shipment/order ID is present in 'Shipments' with correct 'Total Quantity' field.
 */
class AssertShipmentInShipmentsGrid extends AbstractAssertSalesEntityInSalesEntityGrid
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
    protected $errorMessage = 'Shipment is absent in shipment grid on shipment index page.';

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
     * @constructor
     * @param ObjectManager $objectManager
     * @param EventManagerInterface $eventManager
     * @param SalesShipment $salesShipment
     */
    public function __construct(
        ObjectManager $objectManager,
        EventManagerInterface $eventManager,
        SalesShipment $salesShipment
    )
    {
        parent::__construct($objectManager, $eventManager);
        $this->salesEntityIndexPage = $salesShipment;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Shipment is present in the shipment grid with correct total qty on shipment index page.';
    }
}
