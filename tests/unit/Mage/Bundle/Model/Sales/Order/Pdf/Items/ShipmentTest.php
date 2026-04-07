<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Bundle\Model\Sales\Order\Pdf\Items;

# use Mage;
# use Mage_Bundle_Model_Sales_Order_Pdf_Items_Shipment as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Bundle\Model\Sales\Order\Pdf\Items\ShipmentTrait;

final class ShipmentTest extends OpenMageTest
{
    use ShipmentTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('bundle/sales_order_pdf_items_shipment');
        self::markTestSkipped('');
    }
}
