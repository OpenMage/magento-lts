<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Block\Order;

use Mage;
use Mage_Core_Model_Session;
use Mage_Sales_Block_Order_Recent as Subject;
use Mage_Sales_Model_Order;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Block\Order\RecentTrait;
use Override;
use ReflectionClass;

final class RecentTest extends OpenMageTest
{
    use RecentTrait;

    private const FORM_KEY = 'TestFormKey123';

    #[Override]
    protected function setUp(): void
    {
        $coreSession = $this->createPartialMock(Mage_Core_Model_Session::class, ['getFormKey']);
        $coreSession->method('getFormKey')->willReturn(self::FORM_KEY);
        Mage::unregister('_singleton/core/session');
        Mage::register('_singleton/core/session', $coreSession);
    }

    #[Override]
    protected function tearDown(): void
    {
        Mage::unregister('_singleton/core/session');
    }

    /**
     * The reorder URL must carry the session form key, the controller rejects requests without
     * it (GHSA-hp24-hrjg-v7mm). Themes still rendering the control as a plain link rely on it.
     *
     * @group Block
     */
    public function testGetReorderUrlCarriesFormKey(): void
    {
        $result = $this->getReorderUrl();

        self::assertStringContainsString('/order/reorder/', $result);
        self::assertStringContainsString('/order_id/42/', $result);
        self::assertStringContainsString('/form_key/' . self::FORM_KEY . '/', $result);
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
