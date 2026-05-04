<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Model\Resource\Order\Shipment\Attribute\Backend;

// use Mage;
// use Mage_Sales_Model_Resource_Order_Shipment_Attribute_Backend_Child as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Model\Resource\Order\Shipment\Attribute\Backend\ChildTrait;

final class ChildTest extends OpenMageTest
{
    use ChildTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('sales/resource_order_shipment_attribute_backend_child');
        self::markTestSkipped('');
    }
}
