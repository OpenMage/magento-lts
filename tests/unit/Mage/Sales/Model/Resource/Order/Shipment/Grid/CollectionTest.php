<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Model\Resource\Order\Shipment\Grid;

# use Mage;
# use Mage_Sales_Model_Resource_Order_Shipment_Grid_Collection as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Model\Resource\Order\Shipment\Grid\CollectionTrait;

final class CollectionTest extends OpenMageTest
{
    use CollectionTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('sales/resource_order_shipment_grid_collection');
        self::markTestSkipped('');
    }
}
