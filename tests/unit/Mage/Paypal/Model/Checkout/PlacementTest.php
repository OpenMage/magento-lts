<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Paypal\Model\Checkout;

use Iterator;
use Mage;
use Mage_Core_Exception;
use Mage_Core_Model_App_Emulation;
use Mage_Paypal_Model_Checkout_Finalizer;
use Mage_Paypal_Model_Checkout_Placement as Subject;
use Mage_Paypal_Model_Checkout_UnplacedPaymentException;
use Mage_Paypal_Model_Exception;
use Mage_Paypal_Model_Helper;
use Mage_Paypal_Model_Payment;
use Mage_Paypal_Model_Transaction;
use Mage_Sales_Model_Order;
use Mage_Sales_Model_Quote;
use Mage_Sales_Model_Quote_Payment;
use Mage_Sales_Model_Service_Quote;
use OpenMage\Tests\Unit\OpenMageTest;
use PaypalServerSdkLib\Http\ApiResponse;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;
use Varien_Object;

final class PlacementTest extends OpenMageTest
{
    private const PAYPAL_ORDER_ID = '5O190127TN364715T';

    private const CHECKOUT_SESSION_SINGLETON = '_singleton/checkout/session';

    private const EMULATION_SINGLETON = '_singleton/core/app_emulation';

    /** @var list<string> */
    private array $calls = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->calls = [];
        Mage::unregister(self::CHECKOUT_SESSION_SINGLETON);
        Mage::register(self::CHECKOUT_SESSION_SINGLETON, new Varien_Object());
    }

    protected function tearDown(): void
    {
        Mage::unregister(self::CHECKOUT_SESSION_SINGLETON);
        Mage::unregister(self::EMULATION_SINGLETON);
        parent::tearDown();
    }

    /**
     * A repeated request (double submit, webhook after the browser) gets the existing order, charges nothing,
     * and deactivates a quote that an interrupted placement left active.
     *
     * @group Model
     */
    public function testReturnsExistingOrderWithoutCharging(): void
    {
        $order = Mage::getModel('sales/order')->setData(['entity_id' => 5, 'quote_id' => 10, 'increment_id' => 'yes2609140001']);
        $storedQuote = $this->buildQuote();
        $storedQuote->expects(self::once())->method('save')->willReturnSelf();

        $subject = $this->buildSubject($this->buildHelper(), [$order]);
        $subject->method('loadQuote')->willReturn($storedQuote);
        $subject->expects(self::never())->method('fetchPaypalOrder');
        $subject->expects(self::never())->method('chargeApprovedOrder');

        self::assertSame($order, $subject->placeApprovedOrder($this->buildQuote(), self::PAYPAL_ORDER_ID));
        self::assertSame(0, (int) $storedQuote->getIsActive());
        self::assertSame('yes2609140001', Mage::registry(self::CHECKOUT_SESSION_SINGLETON)->getLastRealOrderId());
    }

    /**
     * @group Model
     */
    public function testRefusesExistingOrderOfAnotherQuote(): void
    {
        $order = Mage::getModel('sales/order')->setData(['entity_id' => 5, 'quote_id' => 99]);
        $subject = $this->buildSubject($this->buildHelper(), [$order]);
        $subject->expects(self::never())->method('loadQuote');
        $subject->expects(self::never())->method('chargeApprovedOrder');

        $this->expectException(Mage_Paypal_Model_Exception::class);

        $subject->placeApprovedOrder($this->buildQuote(), self::PAYPAL_ORDER_ID);
    }

    /**
     * @group Model
     */
    public function testRefusesWhileAnotherProcessHoldsTheLock(): void
    {
        $helper = $this->createMock(Mage_Paypal_Model_Helper::class);
        $helper->method('acquirePaypalOrderLock')->willReturn(false);
        $helper->expects(self::never())->method('releasePaypalOrderLock');

        $subject = $this->buildSubject($helper);
        $subject->expects(self::never())->method('findOrderByPaypalOrderId');
        $subject->expects(self::never())->method('chargeApprovedOrder');

        $this->expectException(Mage_Core_Exception::class);

        $subject->placeApprovedOrder($this->buildQuote(), self::PAYPAL_ORDER_ID);
    }

    /**
     * The stored quote is checked, not the caller's copy: another request may have placed it meanwhile.
     *
     * @group Model
     */
    public function testRefusesQuoteNoLongerActive(): void
    {
        $subject = $this->buildSubject($this->buildHelper());
        $subject->method('loadQuote')->willReturn($this->buildQuote(isActive: false));
        $subject->expects(self::never())->method('fetchPaypalOrder');

        $this->expectException(Mage_Paypal_Model_Exception::class);

        $subject->placeApprovedOrder($this->buildQuote(), self::PAYPAL_ORDER_ID);
    }

    /**
     * The buyer started another PayPal attempt for this cart; the older approval must not be charged.
     *
     * @group Model
     */
    public function testRefusesPaypalOrderTheQuoteNoLongerPointsAt(): void
    {
        $subject = $this->buildSubject($this->buildHelper(quotePaypalOrderId: '8MC585209K746392H'));
        $subject->method('loadQuote')->willReturn($this->buildQuote());
        $subject->expects(self::never())->method('fetchPaypalOrder');

        $this->expectException(Mage_Paypal_Model_Exception::class);

        $subject->placeApprovedOrder($this->buildQuote(), self::PAYPAL_ORDER_ID);
    }

    /**
     * The customer is prepared, then the quote checked, before anything is charged; a failing check charges nothing.
     *
     * @group Model
     */
    public function testPreparesAndChecksQuoteBeforeCharging(): void
    {
        $helper = $this->buildHelper();
        $helper->method('validateShippingMethodForQuote')->willReturnCallback(function (): void {
            $this->calls[] = 'validate';
        });
        $helper->method('assertPaypalOrderMatchesQuote')
            ->willThrowException(new Mage_Paypal_Model_Exception('The PayPal payment amount no longer matches your quote total.'));

        $quote = $this->buildQuote();
        /** @var Mage_Sales_Model_Quote_Payment&MockObject $payment */
        $payment = $quote->getPayment();
        $payment->expects(self::never())->method('save');

        $subject = $this->buildSubject($helper);
        $subject->method('loadQuote')->willReturn($this->buildQuote());
        $subject->method('fetchPaypalOrder')->willReturn($this->buildResponse('APPROVED'));
        $subject->method('getFinalizer')->willReturn($this->buildFinalizer());
        $subject->expects(self::never())->method('chargeApprovedOrder');

        try {
            $subject->placeApprovedOrder($quote, self::PAYPAL_ORDER_ID);
            self::fail('The mismatching quote was charged.');
        } catch (Mage_Paypal_Model_Exception) {
            self::assertSame(['prepare', 'validate'], $this->calls);
        }
    }

    /**
     * @dataProvider provideUnchargeableStates
     * @group Model
     */
    public function testDoesNotChargeWhenNotAllowedOrNotApproved(string $status, bool $allowCharge): void
    {
        $subject = $this->buildSubject($this->buildHelper());
        $subject->method('loadQuote')->willReturn($this->buildQuote());
        $subject->method('fetchPaypalOrder')->willReturn($this->buildResponse($status));
        $subject->expects(self::never())->method('chargeApprovedOrder');

        $this->expectException(Mage_Core_Exception::class);

        $subject->placeApprovedOrder($this->buildQuote(), self::PAYPAL_ORDER_ID, $allowCharge, false);
    }

    /**
     * @return Iterator<string, array{string, bool}>
     */
    public static function provideUnchargeableStates(): Iterator
    {
        yield 'approved, but only charged orders may be placed' => ['APPROVED', false];
        yield 'created, never approved by the buyer' => ['CREATED', true];
        yield 'voided' => ['VOIDED', true];
    }

    /**
     * The attempt is recorded before the charge call, so a lost response still leaves a trace on the quote. A decline
     * that PayPal confirms is reported as it is.
     *
     * @group Model
     */
    public function testRecordsTheChargeAttemptBeforeCharging(): void
    {
        $quote = $this->buildQuote();
        /** @var Mage_Sales_Model_Quote_Payment&MockObject $payment */
        $payment = $quote->getPayment();
        $payment->method('save')->willReturnCallback(function () use ($payment) {
            $this->calls[] = 'attempt saved:' . $payment->getAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_CHECKOUT_ATTEMPT_ORDER_ID);
            return $payment;
        });

        $subject = $this->buildChargingSubject($this->buildHelper(), [null], ['APPROVED', 'APPROVED']);
        $subject->method('getFinalizer')->willReturn($this->buildFinalizer());
        $subject->method('chargeApprovedOrder')->willReturnCallback(function (): void {
            $this->calls[] = 'charge';
            throw new Mage_Paypal_Model_Exception('INSTRUMENT_DECLINED');
        });

        try {
            $subject->placeApprovedOrder($quote, self::PAYPAL_ORDER_ID);
            self::fail('A declined charge was reported as success.');
        } catch (Mage_Paypal_Model_Exception $magePaypalModelException) {
            self::assertSame('INSTRUMENT_DECLINED', $magePaypalModelException->getMessage());
            self::assertSame(['prepare', 'attempt saved:' . self::PAYPAL_ORDER_ID, 'charge'], $this->calls);
        }
    }

    /**
     * A charge call that fails after PayPal captured (a timeout, a failure recording the result) is not reported as a
     * failure the buyer could answer by paying again: PayPal is asked, and the order is placed.
     *
     * @group Model
     */
    public function testChargeErrorAfterPaypalCapturedStillPlacesTheOrder(): void
    {
        $order = Mage::getModel('sales/order')->setData(['entity_id' => 7, 'quote_id' => 10]);

        $transactions = $this->createMock(Mage_Paypal_Model_Transaction::class);
        $transactions->expects(self::once())->method('updatePaymentAfterCapture')->with(self::anything(), self::anything(), 'CAP-1');

        $finalizer = $this->buildFinalizer();
        $finalizer->method('finalizeSubmittedOrder')->willReturn($order);

        $subject = $this->buildChargingSubject($this->buildHelper(), [null], ['APPROVED', 'COMPLETED'], $transactions);
        $subject->method('getFinalizer')->willReturn($finalizer);
        $subject->method('chargeApprovedOrder')->willThrowException(new RuntimeException('Operation timed out'));
        $subject->method('createQuoteService')->willReturn($this->createMock(Mage_Sales_Model_Service_Quote::class));

        self::assertSame($order, $subject->placeApprovedOrder($this->buildQuote(), self::PAYPAL_ORDER_ID));
    }

    /**
     * Once the buyer is charged, a placement that fails without committing an order raises the dedicated exception.
     *
     * @group Model
     */
    public function testChargedPaymentThatCannotBePlacedRaisesUnplacedPayment(): void
    {
        $service = $this->createMock(Mage_Sales_Model_Service_Quote::class);
        $service->method('submitAll')->willThrowException(new RuntimeException('Not all products are available'));

        $finalizer = $this->buildFinalizer();
        $finalizer->expects(self::never())->method('finalizeSubmittedOrder');

        $subject = $this->buildChargingSubject($this->buildHelper(), [null, null]);
        $subject->method('getFinalizer')->willReturn($finalizer);
        $subject->expects(self::once())->method('chargeApprovedOrder');
        $subject->method('createQuoteService')->willReturn($service);

        $this->expectException(Mage_Paypal_Model_Checkout_UnplacedPaymentException::class);
        $this->expectExceptionMessage('Please do not pay again');

        $subject->placeApprovedOrder($this->buildQuote(), self::PAYPAL_ORDER_ID, true, false);
    }

    /**
     * An order committed before a later step failed is a real order: it is returned, not reported as lost.
     *
     * @group Model
     */
    public function testOrderCommittedBeforeFinalizingFailedIsReturned(): void
    {
        $order = Mage::getModel('sales/order')->setData(['entity_id' => 7, 'quote_id' => 10, 'increment_id' => 'yes2609150001']);

        $finalizer = $this->buildFinalizer();
        $finalizer->method('finalizeSubmittedOrder')->willThrowException(new RuntimeException('Invoice save failed'));

        $subject = $this->buildChargingSubject($this->buildHelper(), [null, $order]);
        $subject->method('getFinalizer')->willReturn($finalizer);
        $subject->method('createQuoteService')->willReturn($this->createMock(Mage_Sales_Model_Service_Quote::class));

        self::assertSame($order, $subject->placeApprovedOrder($this->buildQuote(), self::PAYPAL_ORDER_ID, true, false));
    }

    /**
     * A capture PayPal still holds as pending is not placed from reconciliation; its completion webhook does that.
     *
     * @group Model
     */
    public function testPendingCaptureIsNotPlacedFromReconciliation(): void
    {
        $transactions = $this->createMock(Mage_Paypal_Model_Transaction::class);
        $transactions->expects(self::never())->method('updatePaymentAfterCapture');

        $subject = $this->buildSubject($this->buildHelper());
        $subject->method('loadQuote')->willReturn($this->buildQuote());
        $subject->method('fetchPaypalOrder')->willReturn($this->buildResponse('COMPLETED', 'PENDING'));
        $subject->method('getFinalizer')->willReturn($this->buildFinalizer());
        $subject->method('getTransactionManager')->willReturn($transactions);

        $this->expectException(Mage_Core_Exception::class);
        $this->expectExceptionMessage('still being processed');

        $subject->placeApprovedOrder($this->buildQuote(), self::PAYPAL_ORDER_ID, false, false);
    }

    /**
     * @group Model
     */
    public function testResolvePendingChargeForgetsAnAttemptPaypalDidNotCharge(): void
    {
        $helper = $this->buildHelper(pendingPaypalOrderId: self::PAYPAL_ORDER_ID);

        $quote = $this->buildQuote();
        /** @var Mage_Sales_Model_Quote_Payment&MockObject $payment */
        $payment = $quote->getPayment();
        $payment->setAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_CHECKOUT_ATTEMPT_ORDER_ID, self::PAYPAL_ORDER_ID);
        $payment->expects(self::once())->method('save');

        $subject = $this->buildSubject($helper, [null], ['placeApprovedOrder']);
        $subject->method('fetchPaypalOrder')->willReturn($this->buildResponse('APPROVED'));
        $subject->expects(self::never())->method('placeApprovedOrder');

        self::assertNull($subject->resolvePendingCharge($quote));
        self::assertNull($quote->getPayment()->getAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_CHECKOUT_ATTEMPT_ORDER_ID));
    }

    /**
     * @dataProvider provideChargedPendingQuotes
     * @group Model
     */
    public function testResolvePendingChargePlacesAChargedPayment(bool $hasRecordedCapture): void
    {
        $order = Mage::getModel('sales/order')->setId(7);
        $helper = $this->createMock(Mage_Paypal_Model_Helper::class);
        $helper->method('getPendingChargePaypalOrderId')->willReturn(self::PAYPAL_ORDER_ID);
        $helper->method('hasUnplacedPaypalPayment')->willReturn($hasRecordedCapture);
        $helper->method('acquirePaypalOrderLock')->willReturn(true);

        $subject = $this->buildSubject($helper, [null], ['placeApprovedOrder']);
        $subject->expects($hasRecordedCapture ? self::never() : self::once())
            ->method('fetchPaypalOrder')
            ->willReturn($this->buildResponse('COMPLETED'));
        $subject->expects(self::once())
            ->method('placeApprovedOrder')
            ->with(self::isInstanceOf(Mage_Sales_Model_Quote::class), self::PAYPAL_ORDER_ID, false, false)
            ->willReturn($order);

        self::assertSame($order, $subject->resolvePendingCharge($this->buildQuote()));
    }

    /**
     * @return Iterator<string, array{bool}>
     */
    public static function provideChargedPendingQuotes(): Iterator
    {
        yield 'capture recorded on the quote' => [true];
        yield 'attempt only, PayPal reports completed' => [false];
    }

    /**
     * @dataProvider provideSkippedRecoveries
     * @group Model
     */
    public function testRecoverOrderLeavesWhatTheWebhookMustNotPlace(bool $orderExists, bool $hasQuote, ?string $shortcutOrderId, ?string $attemptOrderId): void
    {
        $subject = $this->buildRecoverySubject(
            $orderExists ? Mage::getModel('sales/order')->setId(5) : null,
            $hasQuote ? $this->buildRecoveryQuote($shortcutOrderId, $attemptOrderId) : null,
        );
        $subject->expects(self::never())->method('placeApprovedOrder');

        self::assertNull($subject->recoverOrder(self::PAYPAL_ORDER_ID, true, false));
    }

    /**
     * @return Iterator<string, array{bool, bool, (null|string), (null|string)}>
     */
    public static function provideSkippedRecoveries(): Iterator
    {
        yield 'order already placed' => [true, true, null, null];
        yield 'no open cart for the PayPal order' => [false, false, null, null];
        yield 'shortcut approval awaits the review page' => [false, true, self::PAYPAL_ORDER_ID, null];
        yield 'checkout already handled this approval' => [false, true, null, self::PAYPAL_ORDER_ID];
    }

    /**
     * @dataProvider provideRecoverableEvents
     * @group Model
     */
    public function testRecoverOrderPlacesMissingOrder(bool $isApproval, ?string $shortcutOrderId, ?string $attemptOrderId, bool $saveCustomerAddresses): void
    {
        $this->useEmulation();
        $quote = $this->buildRecoveryQuote($shortcutOrderId, $attemptOrderId);
        $order = Mage::getModel('sales/order')->setId(7);

        $subject = $this->buildRecoverySubject(null, $quote);
        $subject->expects(self::once())
            ->method('placeApprovedOrder')
            ->with($quote, self::PAYPAL_ORDER_ID, $isApproval, true, null, $saveCustomerAddresses)
            ->willReturn($order);

        self::assertSame($order, $subject->recoverOrder(self::PAYPAL_ORDER_ID, $isApproval, true));
    }

    /**
     * @return Iterator<string, array{bool, (null|string), (null|string), bool}>
     */
    public static function provideRecoverableEvents(): Iterator
    {
        yield 'untouched in-checkout approval is charged' => [true, null, null, true];
        yield 'shortcut capture is placed' => [false, self::PAYPAL_ORDER_ID, null, false];
        yield 'capture after a checkout attempt' => [false, null, self::PAYPAL_ORDER_ID, true];
    }

    /**
     * @group Model
     */
    public function testMarkCheckoutAttemptOnlyForTheQuotesCurrentPaypalOrder(): void
    {
        $quote = $this->buildQuote();
        /** @var Mage_Sales_Model_Quote_Payment&MockObject $payment */
        $payment = $quote->getPayment();
        $payment->expects(self::once())->method('save');

        $helper = $this->createMock(Mage_Paypal_Model_Helper::class);
        $helper->method('getQuotePaypalOrderId')->willReturn(self::PAYPAL_ORDER_ID);
        $subject = $this->buildSubject($helper);

        $subject->markCheckoutAttempt($quote, '8MC585209K746392H');
        $subject->markCheckoutAttempt($quote, self::PAYPAL_ORDER_ID);

        self::assertSame(
            self::PAYPAL_ORDER_ID,
            $quote->getPayment()->getAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_CHECKOUT_ATTEMPT_ORDER_ID),
        );
    }

    /**
     * @param  list<null|Mage_Sales_Model_Order> $foundOrders  successive results of findOrderByPaypalOrderId()
     * @param  list<non-empty-string>            $extraMethods
     * @return MockObject&Subject
     */
    private function buildSubject(Mage_Paypal_Model_Helper $helper, array $foundOrders = [null], array $extraMethods = []): Subject
    {
        $subject = $this->getMockBuilder(Subject::class)
            ->onlyMethods(array_merge([
                'findOrderByPaypalOrderId',
                'loadQuote',
                'fetchPaypalOrder',
                'chargeApprovedOrder',
                'createQuoteService',
                'getTransactionManager',
                'getFinalizer',
                'getHelper',
            ], $extraMethods))
            ->getMock();
        $subject->method('getHelper')->willReturn($helper);
        $subject->method('findOrderByPaypalOrderId')->willReturnOnConsecutiveCalls(...$foundOrders);

        return $subject;
    }

    /**
     * A subject that reaches the charge: an active stored quote pointing at the PayPal order, checks that pass, and
     * PayPal answering with the given statuses in turn. The caller configures getFinalizer().
     *
     * @param  list<null|Mage_Sales_Model_Order> $foundOrders
     * @param  list<string>                      $statuses
     * @return MockObject&Subject
     */
    private function buildChargingSubject(
        Mage_Paypal_Model_Helper $helper,
        array $foundOrders = [null],
        array $statuses = ['APPROVED'],
        ?Mage_Paypal_Model_Transaction $transactions = null
    ): Subject {
        $subject = $this->buildSubject($helper, $foundOrders);
        $subject->method('loadQuote')->willReturn($this->buildQuote());
        $subject->method('fetchPaypalOrder')->willReturnOnConsecutiveCalls(
            ...array_map(fn(string $status): ApiResponse => $this->buildResponse($status), $statuses),
        );
        $subject->method('getTransactionManager')->willReturn($transactions ?? $this->createMock(Mage_Paypal_Model_Transaction::class));

        return $subject;
    }

    /**
     * @return MockObject&Subject
     */
    private function buildRecoverySubject(?Mage_Sales_Model_Order $existingOrder, ?Mage_Sales_Model_Quote $quote): Subject
    {
        $subject = $this->getMockBuilder(Subject::class)
            ->onlyMethods(['findOrderByPaypalOrderId', 'findQuoteByPaypalOrderId', 'placeApprovedOrder'])
            ->getMock();
        $subject->method('findOrderByPaypalOrderId')->willReturn($existingOrder);
        $subject->method('findQuoteByPaypalOrderId')->willReturn($quote);

        return $subject;
    }

    /**
     * @return Mage_Paypal_Model_Helper&MockObject
     */
    private function buildHelper(string $quotePaypalOrderId = self::PAYPAL_ORDER_ID, string $pendingPaypalOrderId = ''): Mage_Paypal_Model_Helper
    {
        $helper = $this->createMock(Mage_Paypal_Model_Helper::class);
        $helper->method('acquirePaypalOrderLock')->willReturn(true);
        $helper->expects(self::atLeastOnce())->method('releasePaypalOrderLock')->with(self::PAYPAL_ORDER_ID);
        $helper->method('getQuotePaypalOrderId')->willReturn($quotePaypalOrderId);
        $helper->method('getPendingChargePaypalOrderId')->willReturn($pendingPaypalOrderId);

        return $helper;
    }

    /**
     * @return Mage_Paypal_Model_Checkout_Finalizer&MockObject
     */
    private function buildFinalizer(): Mage_Paypal_Model_Checkout_Finalizer
    {
        $finalizer = $this->createMock(Mage_Paypal_Model_Checkout_Finalizer::class);
        $finalizer->method('prepareQuoteForCheckout')->willReturnCallback(function (): bool {
            $this->calls[] = 'prepare';
            return false;
        });

        return $finalizer;
    }

    /**
     * A quote whose persistence and totals are stubbed; its data accessors and payment behave normally.
     *
     * @return Mage_Sales_Model_Quote&MockObject
     */
    private function buildQuote(bool $isActive = true): Mage_Sales_Model_Quote
    {
        $payment = $this->getMockBuilder(Mage_Sales_Model_Quote_Payment::class)
            ->onlyMethods(['save'])
            ->getMock();

        $quote = $this->getMockBuilder(Mage_Sales_Model_Quote::class)
            ->onlyMethods(['save', 'hasItems', 'collectTotals', 'getPayment'])
            ->getMock();
        $quote->setId(10)->setIsActive($isActive ? 1 : 0)->setStoreId(0);
        $quote->method('hasItems')->willReturn(true);
        $quote->method('collectTotals')->willReturnSelf();
        $quote->method('getPayment')->willReturn($payment);

        return $quote;
    }

    private function buildRecoveryQuote(?string $shortcutOrderId, ?string $attemptOrderId): Mage_Sales_Model_Quote
    {
        $quote = Mage::getModel('sales/quote')->setId(10)->setIsActive(1)->setStoreId(0);
        if ($shortcutOrderId !== null) {
            $quote->getPayment()->setAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_ORDER_ID, $shortcutOrderId);
        }

        if ($attemptOrderId !== null) {
            $quote->getPayment()->setAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_CHECKOUT_ATTEMPT_ORDER_ID, $attemptOrderId);
        }

        return $quote;
    }

    /**
     * @return ApiResponse&MockObject
     */
    private function buildResponse(string $status, string $captureStatus = 'COMPLETED'): ApiResponse
    {
        $order = [
            'id' => self::PAYPAL_ORDER_ID,
            'intent' => 'CAPTURE',
            'status' => $status,
        ];
        if ($status === 'COMPLETED') {
            $order['purchase_units'] = [['payments' => ['captures' => [['id' => 'CAP-1', 'status' => $captureStatus]]]]];
        }

        $response = $this->createMock(ApiResponse::class);
        $response->method('getBody')->willReturn((string) json_encode($order));

        return $response;
    }

    private function useEmulation(): void
    {
        $emulation = $this->createMock(Mage_Core_Model_App_Emulation::class);
        $emulation->method('startEnvironmentEmulation')->willReturn(new Varien_Object());
        $emulation->expects(self::once())->method('stopEnvironmentEmulation');
        Mage::unregister(self::EMULATION_SINGLETON);
        Mage::register(self::EMULATION_SINGLETON, $emulation);
    }
}
