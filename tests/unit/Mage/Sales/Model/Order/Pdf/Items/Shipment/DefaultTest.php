<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Model\Order\Pdf\Items\Shipment;

use Mage;
use Mage_Sales_Model_Order_Pdf_Items_Shipment_Default as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class DefaultTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('sales/order_pdf_items_shipment_default');
    }
}
