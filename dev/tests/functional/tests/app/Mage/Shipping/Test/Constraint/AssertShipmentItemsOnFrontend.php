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

use Mage\Sales\Test\Constraint\AbstractAssertSalesEntityItemsOnFrontend;
use Mage\Shipping\Test\Page\ShipmentView;
use Magento\Mtf\ObjectManager;
use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\System\Event\EventManagerInterface;

/**
 * Assert that shipped items is equal to data from fixture on 'My Account' page.
 */
class AssertShipmentItemsOnFrontend extends AbstractAssertSalesEntityItemsOnFrontend
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
     * @constructor
     * @param ObjectManager $objectManager
     * @param ShipmentView $shipmentView
     */
    public function __construct(
        ObjectManager $objectManager,
        EventManagerInterface $eventManager,
        ShipmentView $shipmentView
    )
    {
        parent::__construct($objectManager, $eventManager);
        $this->salesTypeViewPage = $shipmentView;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Shipped items quantity is equal to data from fixture on My Account page.';
    }
}
