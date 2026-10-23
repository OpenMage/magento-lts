<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Block\Order\Info;

use Mage;
use Mage_Core_Model_Session;
use Mage_Customer_Model_Session;
use Mage_Sales_Block_Order_Info_Buttons as Subject;
use Mage_Sales_Model_Order;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Block\Order\Info\ButtonsTrait;
use Override;
use ReflectionClass;

final class ButtonsTest extends OpenMageTest
{
    use ButtonsTrait;

    private const FORM_KEY = 'TestFormKey123';

    #[Override]
    protected function setUp(): void
    {
        $coreSession = $this->createPartialMock(Mage_Core_Model_Session::class, ['getFormKey']);
        $coreSession->method('getFormKey')->willReturn(self::FORM_KEY);
        Mage::unregister('_singleton/core/session');
        Mage::register('_singleton/core/session', $coreSession);
        Mage::unregister('_singleton/customer/session');
    }

    #[Override]
    protected function tearDown(): void
    {
        Mage::unregister('_singleton/core/session');
        Mage::unregister('_singleton/customer/session');
    }

    /**
     * The reorder URL must carry the session form key for customers and guests alike, the
     * controller rejects requests without it (GHSA-hp24-hrjg-v7mm).
     *
     * @dataProvider provideGetReorderUrlCarriesFormKey
     * @group Block
     */
    public function testGetReorderUrlCarriesFormKey(bool $isLoggedIn, string $route): void
    {
        $customerSession = $this->createPartialMock(Mage_Customer_Model_Session::class, ['isLoggedIn']);
        $customerSession->method('isLoggedIn')->willReturn($isLoggedIn);
        Mage::register('_singleton/customer/session', $customerSession);

        $result = $this->getReorderUrl();

        self::assertStringContainsString($route, $result);
        self::assertStringContainsString('/order_id/42/', $result);
        self::assertStringContainsString('/form_key/' . self::FORM_KEY . '/', $result);
    }

    public static function provideGetReorderUrlCarriesFormKey(): \Generator
    {
        yield 'customer' => [true, '/sales/order/reorder/'];
        yield 'guest' => [false, '/sales/guest/reorder/'];
    }

    private function getReorderUrl(): string
    {
        // The constructor loads the customer order collection, which needs a database
        $subject = (new ReflectionClass(Subject::class))->newInstanceWithoutConstructor();
        $order = new Mage_Sales_Model_Order();
        $order->setId(42);

        return $subject->getReorderUrl($order);
    }
}
