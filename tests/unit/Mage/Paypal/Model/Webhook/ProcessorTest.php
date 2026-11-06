<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Paypal\Model\Webhook;

use Carbon\Carbon;
use Mage;
use Mage_Core_Exception;
use Mage_Paypal_Model_Checkout_Placement;
use Mage_Paypal_Model_Config;
use Mage_Paypal_Model_Webhook_Event;
use Mage_Paypal_Model_Webhook_Event_Resolver;
use Mage_Paypal_Model_Webhook_Processor as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model\Webhook\ProcessorTrait;

final class ProcessorTest extends OpenMageTest
{
    use ProcessorTrait;

    private const CONFIG_SINGLETON = '_singleton/paypal/config';

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        Mage::unregister(self::CONFIG_SINGLETON);
        parent::tearDown();
    }

    /**
     * @dataProvider provideEventActionData
     */
    public function testGetActionForEventType(string $eventType, string $expectedResult): void
    {
        $subject = new Subject(self::createStub(Mage_Paypal_Model_Webhook_Event_Resolver::class));

        self::assertSame($expectedResult, $subject->getActionForEventType($eventType));
    }

    /**
     * Mage::getModel() hands every model an (empty) arguments array; a typed constructor turned each
     * webhook delivery and cron run into a TypeError.
     */
    public function testModelFactoryCreatesProcessor(): void
    {
        self::assertInstanceOf(Subject::class, Mage::getModel('paypal/webhook_processor'));
    }

    /**
     * @dataProvider provideRecoveryData
     * @param null|array{0: bool, 1: bool} $expectedRecovery [isApproval, notifyOnFailure], or null when nothing may run
     */
    public function testRecoverMissingOrder(string $eventType, int $attempts, string $eventTime, ?array $expectedRecovery): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-14 12:00:00', 'UTC'));
        $this->useRetryLimit(3);

        $placement = $this->createMock(Mage_Paypal_Model_Checkout_Placement::class);
        if ($expectedRecovery === null) {
            $placement->expects(self::never())->method('recoverOrder');
        } else {
            $placement->expects(self::once())
                ->method('recoverOrder')
                ->with('5O190127TN364715T', $expectedRecovery[0], $expectedRecovery[1]);
        }

        $subject = new Subject(self::createStub(Mage_Paypal_Model_Webhook_Event_Resolver::class));
        $subject->setPlacement($placement);

        $subject->recoverMissingOrder($this->buildEvent($eventType, $attempts, $eventTime));
    }

    public function testRecoverMissingOrderNeedsPaypalOrderId(): void
    {
        $this->useRetryLimit(3);

        $placement = $this->createMock(Mage_Paypal_Model_Checkout_Placement::class);
        $placement->expects(self::never())->method('recoverOrder');

        $subject = new Subject(self::createStub(Mage_Paypal_Model_Webhook_Event_Resolver::class));
        $subject->setPlacement($placement);

        $subject->recoverMissingOrder(
            $this->buildEvent('PAYMENT.CAPTURE.COMPLETED', 0, '2026-09-14 11:00:00')->setData('paypal_order_id'),
        );
    }

    /**
     * A failed placement must not stop the event itself from being processed and retried.
     */
    public function testRecoverMissingOrderSwallowsPlacementFailure(): void
    {
        $this->useRetryLimit(3);

        $placement = $this->createMock(Mage_Paypal_Model_Checkout_Placement::class);
        $placement->expects(self::once())
            ->method('recoverOrder')
            ->willThrowException(new Mage_Core_Exception('Out of stock'));

        $subject = new Subject(self::createStub(Mage_Paypal_Model_Webhook_Event_Resolver::class));
        $subject->setPlacement($placement);

        $subject->recoverMissingOrder($this->buildEvent('PAYMENT.CAPTURE.COMPLETED', 0, '2026-09-14 11:00:00'));
    }

    public function testApprovalWithoutTimestampIsNeverPastGrace(): void
    {
        $subject = new Subject(self::createStub(Mage_Paypal_Model_Webhook_Event_Resolver::class));
        $event = Mage::getModel('paypal/webhook_event');

        self::assertFalse($subject->isPastApprovalGrace($event));
    }

    private function buildEvent(string $eventType, int $attempts, string $eventTime): Mage_Paypal_Model_Webhook_Event
    {
        return Mage::getModel('paypal/webhook_event')->setData([
            'event_type' => $eventType,
            'paypal_order_id' => '5O190127TN364715T',
            'processing_attempts' => $attempts,
            'event_time' => $eventTime,
        ]);
    }

    private function useRetryLimit(int $retryLimit): void
    {
        $config = $this->createMock(Mage_Paypal_Model_Config::class);
        $config->method('getWebhookRetryLimit')->willReturn($retryLimit);
        Mage::unregister(self::CONFIG_SINGLETON);
        Mage::register(self::CONFIG_SINGLETON, $config);
    }
}
