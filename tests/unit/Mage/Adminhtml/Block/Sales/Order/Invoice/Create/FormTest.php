<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Sales\Order\Invoice\Create;

use Mage;
use Mage_Admin_Model_Session;
use Mage_Adminhtml_Block_Sales_Order_Invoice_Create_Form as Subject;
use Mage_Sales_Model_Order;
use Mage_Sales_Model_Order_Invoice;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Block\Sales\Order\Invoice\Create\FormTrait;
use Varien_Object;

final class FormTest extends OpenMageTest
{
    use FormTrait;

    private const SESSION_KEY = '_singleton/admin/session';

    private mixed $previousSession = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = Mage::registry(self::SESSION_KEY);
    }

    protected function tearDown(): void
    {
        Mage::unregister(self::SESSION_KEY);
        if ($this->previousSession !== null) {
            Mage::register(self::SESSION_KEY, $this->previousSession);
        }

        parent::tearDown();
    }

    /**
     * The Create Shipment option needs the Ship permission, unless the order ships with its invoice.
     *
     * @dataProvider provideCanCreateShipmentData
     * @group Block
     */
    public function testCanCreateShipment(bool $mayShip, bool $forcedShipment, float $qtyToShip, bool $expected): void
    {
        $session = $this->createPartialMock(Mage_Admin_Model_Session::class, ['isAllowed']);
        $session->method('isAllowed')->willReturnCallback(
            static fn(string $resource): bool => $mayShip || $resource !== 'sales/order/actions/ship',
        );
        Mage::unregister(self::SESSION_KEY);
        Mage::register(self::SESSION_KEY, $session);

        $order = new Mage_Sales_Model_Order();
        $order->setForcedDoShipmentWithInvoice($forcedShipment ? 1 : 0);

        $item = new Varien_Object(['order_item' => new Varien_Object(['qty_to_ship' => $qtyToShip])]);
        $invoice = $this->createPartialMock(Mage_Sales_Model_Order_Invoice::class, ['getAllItems', 'getOrder']);
        $invoice->method('getAllItems')->willReturn([$item]);
        $invoice->method('getOrder')->willReturn($order);

        $subject = $this->createPartialMock(Subject::class, ['getInvoice']);
        $subject->method('getInvoice')->willReturn($invoice);

        self::assertSame($expected, $subject->canCreateShipment());
    }
}
