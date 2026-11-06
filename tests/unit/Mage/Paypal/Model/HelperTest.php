<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Paypal\Model;

use Mage;
use Mage_Core_Exception;
use Mage_Paypal_Model_Exception;
use Mage_Paypal_Model_Payment;
use Mage_Paypal_Model_Helper as Subject;
use Mage_Paypal_Model_Transaction;
use Mage_Sales_Model_Order_Payment_Transaction;
use Mage_Sales_Model_Quote;
use Mage_Sales_Model_Quote_Address;
use Mage_Sales_Model_Quote_Address_Rate;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model\HelperTrait;
use PaypalServerSdkLib\Http\ApiResponse;
use PaypalServerSdkLib\Models\Builders\MoneyBuilder;
use PaypalServerSdkLib\Models\Builders\OrderBuilder;
use PaypalServerSdkLib\Models\Builders\OrdersCaptureBuilder;
use PaypalServerSdkLib\Models\Builders\PaymentCollectionBuilder;
use PaypalServerSdkLib\Models\Builders\PurchaseUnitBuilder;
use Varien_Object;

final class HelperTest extends OpenMageTest
{
    use HelperTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('paypal/helper');
    }

    /**
     * Build a PayPal order API result with the requested shape.
     */
    private function buildResult(string $kind, ?string $amount): mixed
    {
        if ($kind === 'null') {
            return null;
        }

        if ($kind === 'string') {
            return 'not-an-object';
        }

        if ($kind === 'plain') {
            return new Varien_Object();
        }

        if ($kind === 'emptyUnits') {
            return OrderBuilder::init()->purchaseUnits([])->build();
        }

        if ($kind === 'noPayments') {
            return OrderBuilder::init()->purchaseUnits([PurchaseUnitBuilder::init()->build()])->build();
        }

        $captures = [];
        if ($kind !== 'emptyCaptures') {
            $capture = OrdersCaptureBuilder::init()->id('CAP-1')->status('COMPLETED');
            if ($amount !== null) {
                $capture->amount(MoneyBuilder::init('USD', $amount)->build());
            }

            $captures[] = $capture->build();
        }

        $purchaseUnit = PurchaseUnitBuilder::init()
            ->payments(PaymentCollectionBuilder::init()->captures($captures)->build())
            ->build();

        return OrderBuilder::init()->purchaseUnits([$purchaseUnit])->build();
    }

    /**
     * Build a quote with processed PayPal details already stored on payment.
     *
     * @param array<string, string> $rawDetails
     */
    private function buildProcessedPaymentQuote(array $rawDetails, float $grandTotal = 42.17): Mage_Sales_Model_Quote
    {
        $quote = Mage::getModel('sales/quote');
        $quote->setId(10)
            ->setReservedOrderId('100000001')
            ->setGrandTotal($grandTotal)
            ->setQuoteCurrencyCode('USD')
            ->setOrderCurrencyCode('USD');

        $payment = $quote->getPayment();
        $payment->setMethod('paypal')
            ->setPaypalCorrelationId($rawDetails['capture_id'] ?? 'CAP-1')
            ->setAdditionalInformation(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS, $rawDetails);

        if (isset($rawDetails['authorization_id'])) {
            $payment->setAdditionalInformation(
                Mage_Paypal_Model_Transaction::PAYPAL_PAYMENT_AUTHORIZATION_ID,
                $rawDetails['authorization_id'],
            );
        }

        return $quote;
    }

    /**
     * @dataProvider provideCaptureResultShapes
     * @group Model
     */
    public function testExtractCaptureAmount(string $kind, ?string $amount): void
    {
        $result = $this->buildResult($kind, $amount);
        $extracted = self::$subject->extractCaptureAmount($result);

        if ($kind === 'ok') {
            self::assertSame((float) $amount, $extracted);
        } else {
            self::assertNull($extracted);
        }
    }

    /**
     * @dataProvider provideCaptureResultShapes
     * @group Model
     */
    public function testExtractCaptureId(string $kind, ?string $amount): void
    {
        $result = $this->buildResult($kind, $amount);
        $extracted = self::$subject->extractCaptureId($result);

        if ($kind === 'ok' || $kind === 'noAmount') {
            // Both shapes carry a capture with an ID.
            self::assertSame('CAP-1', $extracted);
        } else {
            self::assertNull($extracted);
        }
    }

    /**
     * @group Model
     */
    public function testValidateProcessedPaymentForQuoteAcceptsMatchingCapture(): void
    {
        $quote = $this->buildProcessedPaymentQuote([
            'id' => 'ORDER-1',
            'invoice_id' => '100000001',
            'capture_id' => 'CAP-1',
            'capture_amount' => 'USD 42.17',
        ]);

        self::$subject->validateProcessedPaymentForQuote($quote, false, 'ORDER-1');

        self::assertSame('CAP-1', $quote->getPayment()->getPaypalCorrelationId());
    }

    /**
     * @group Model
     */
    public function testValidateProcessedPaymentForQuoteAcceptsMatchingAuthorization(): void
    {
        $quote = $this->buildProcessedPaymentQuote([
            'id' => 'ORDER-1',
            'invoice_id' => '100000001',
            'authorization_id' => 'AUTH-1',
            'authorization_amount' => 'USD 42.17',
        ]);

        self::$subject->validateProcessedPaymentForQuote($quote, true, 'ORDER-1');

        self::assertSame(
            'AUTH-1',
            $quote->getPayment()->getAdditionalInformation(Mage_Paypal_Model_Transaction::PAYPAL_PAYMENT_AUTHORIZATION_ID),
        );
    }

    /**
     * @group Model
     */
    public function testValidateProcessedPaymentForQuoteRejectsChangedQuoteTotal(): void
    {
        $quote = $this->buildProcessedPaymentQuote([
            'id' => 'ORDER-1',
            'invoice_id' => '100000001',
            'capture_id' => 'CAP-1',
            'capture_amount' => 'USD 42.17',
        ], 50.00);

        $this->expectException(Mage_Paypal_Model_Exception::class);

        self::$subject->validateProcessedPaymentForQuote($quote, false, 'ORDER-1');
    }

    /**
     * @group Model
     */
    public function testValidateProcessedPaymentForQuoteRejectsDifferentQuoteInvoice(): void
    {
        $quote = $this->buildProcessedPaymentQuote([
            'id' => 'ORDER-1',
            'invoice_id' => '100000002',
            'capture_id' => 'CAP-1',
            'capture_amount' => 'USD 42.17',
        ]);

        $this->expectException(Mage_Paypal_Model_Exception::class);

        self::$subject->validateProcessedPaymentForQuote($quote, false, 'ORDER-1');
    }

    /**
     * @group Model
     */
    public function testValidateProcessedPaymentForQuoteRejectsDifferentPaypalOrder(): void
    {
        $quote = $this->buildProcessedPaymentQuote([
            'id' => 'ORDER-1',
            'invoice_id' => '100000001',
            'capture_id' => 'CAP-1',
            'capture_amount' => 'USD 42.17',
        ]);

        $this->expectException(Mage_Paypal_Model_Exception::class);

        self::$subject->validateProcessedPaymentForQuote($quote, false, 'ORDER-2');
    }

    /**
     * @dataProvider provideRawDetails
     * @param array<string, string> $expected
     * @group Model
     */
    public function testPrepareRawDetails(string $json, array $expected): void
    {
        self::assertSame($expected, self::$subject->prepareRawDetails($json));
    }

    /**
     * Build a non-virtual quote whose shipping address answers with the given method and rate.
     */
    private function buildShippingQuote(string $shippingMethod, ?Mage_Sales_Model_Quote_Address_Rate $rate): Mage_Sales_Model_Quote
    {
        // Stub only getShippingRateByCode(), which would otherwise query the rates table. Leaving
        // the rest of the class intact keeps Varien_Object::__call working, so the magic
        // set/getShippingMethod() pair behaves exactly as it does in production.
        $address = $this->getMockBuilder(Mage_Sales_Model_Quote_Address::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getShippingRateByCode'])
            ->getMock();
        $address->method('getShippingRateByCode')->willReturn($rate ?? false);
        $address->setShippingMethod($shippingMethod);

        $quote = $this->createMock(Mage_Sales_Model_Quote::class);
        $quote->method('isVirtual')->willReturn(false);
        $quote->method('getShippingAddress')->willReturn($address);

        return $quote;
    }

    /**
     * @group Model
     */
    public function testValidateShippingMethodForQuoteSkipsVirtualQuotes(): void
    {
        $quote = $this->createMock(Mage_Sales_Model_Quote::class);
        $quote->method('isVirtual')->willReturn(true);
        $quote->expects(self::never())->method('getShippingAddress');

        self::$subject->validateShippingMethodForQuote($quote, false);
    }

    /**
     * The reported FireCheckout defect: the shopper reaches PayPal having never picked a method.
     *
     * @group Model
     */
    public function testValidateShippingMethodForQuoteRejectsUnselectedMethod(): void
    {
        $quote = $this->buildShippingQuote('', null);

        $this->expectException(Mage_Core_Exception::class);
        $this->expectExceptionMessage('Please specify a shipping method.');

        self::$subject->validateShippingMethodForQuote($quote, false);
    }

    /**
     * A stale method code that no longer resolves to a rate — e.g. a MatrixRate row dropped by a
     * CSV re-import — must not survive to submit time either.
     *
     * @group Model
     */
    public function testValidateShippingMethodForQuoteRejectsMethodWithoutRate(): void
    {
        $quote = $this->buildShippingQuote('matrixrate_matrixrate_51919', null);

        $this->expectException(Mage_Core_Exception::class);
        $this->expectExceptionMessage('Please specify a valid shipping method.');

        self::$subject->validateShippingMethodForQuote($quote, false);
    }

    /**
     * @group Model
     */
    public function testValidateShippingMethodForQuoteRejectsErroredRate(): void
    {
        $rate = Mage::getModel('sales/quote_address_rate');
        $rate->setCode('matrixrate_matrixrate_51919')
            ->setErrorMessage('No rate available for this destination.');

        $quote = $this->buildShippingQuote('matrixrate_matrixrate_51919', $rate);

        $this->expectException(Mage_Core_Exception::class);
        $this->expectExceptionMessage('Please specify a valid shipping method.');

        self::$subject->validateShippingMethodForQuote($quote, false);
    }

    /**
     * @group Model
     */
    public function testValidateShippingMethodForQuoteAcceptsSelectedRate(): void
    {
        $rate = Mage::getModel('sales/quote_address_rate');
        $rate->setCode('matrixrate_matrixrate_51919');

        $quote = $this->buildShippingQuote('matrixrate_matrixrate_51919', $rate);

        self::$subject->validateShippingMethodForQuote($quote, false);

        self::assertSame('matrixrate_matrixrate_51919', $quote->getShippingAddress()->getShippingMethod());
    }

    /**
     * @dataProvider provideUnplacedPaymentQuotes
     * @param null|array<string, string> $rawDetails
     * @group Model
     */
    public function testHasUnplacedPaypalPayment(?int $quoteId, bool $isActive, ?array $rawDetails, bool $expected): void
    {
        $quote = Mage::getModel('sales/quote');
        $quote->setId($quoteId)->setIsActive($isActive ? 1 : 0);
        if ($rawDetails !== null) {
            $quote->getPayment()->setAdditionalInformation(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS, $rawDetails);
        }

        self::assertSame($expected, self::$subject->hasUnplacedPaypalPayment($quote));
    }

    /**
     * @group Model
     */
    public function testHandleApiErrorKeepsThePayloadOfObjectResults(): void
    {
        $body = [
            'name' => 'UNPROCESSABLE_ENTITY',
            'details' => [['issue' => 'PAYER_ACTION_REQUIRED']],
            'links' => [['rel' => 'payer-action', 'href' => 'https://www.paypal.com/checkoutnow?token=5O190127TN364715T']],
        ];
        $response = $this->createMock(ApiResponse::class);
        $response->method('getResult')->willReturn(new Varien_Object(['message' => 'The requested action could not be performed.']));
        $response->method('getBody')->willReturn((string) json_encode($body));

        try {
            self::$subject->handleApiError($response, 'Unable to capture PayPal order.');
        } catch (Mage_Paypal_Model_Exception $magePaypalModelException) {
            self::assertTrue($magePaypalModelException->isPayerActionRequired());
            self::assertSame($body['links'], $magePaypalModelException->getDebugData()['links'] ?? null);
        }
    }

    /**
     * @group Model
     */
    public function testGetQuotePaypalOrderIdPrefersPaymentColumn(): void
    {
        $quote = Mage::getModel('sales/quote');
        $quote->getPayment()
            ->setData(Mage_Paypal_Model_Payment::PAYPAL_ORDER_ID, 'ORDER-COLUMN')
            ->setAdditionalInformation(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS, ['id' => 'ORDER-RAW']);

        self::assertSame('ORDER-COLUMN', self::$subject->getQuotePaypalOrderId($quote));
    }

    /**
     * Attempts started before the column existed only have the id in the stored create response.
     *
     * @group Model
     */
    public function testGetQuotePaypalOrderIdFallsBackToRawDetails(): void
    {
        $quote = Mage::getModel('sales/quote');
        $quote->getPayment()
            ->setAdditionalInformation(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS, ['id' => 'ORDER-RAW']);

        self::assertSame('ORDER-RAW', self::$subject->getQuotePaypalOrderId($quote));
        self::assertSame('', self::$subject->getQuotePaypalOrderId(Mage::getModel('sales/quote')));
    }

    /**
     * @group Model
     */
    public function testAssertPaypalOrderMatchesQuoteAcceptsMatchingOrder(): void
    {
        $quote = $this->buildProcessedPaymentQuote([]);

        self::$subject->assertPaypalOrderMatchesQuote($this->buildPaypalOrderDetails(), $quote, 'ORDER-1');

        self::assertSame('100000001', $quote->getReservedOrderId());
    }

    /**
     * @dataProvider provideMismatchedPaypalOrders
     * @param array<string, mixed> $overrides
     * @group Model
     */
    public function testAssertPaypalOrderMatchesQuoteRejectsMismatch(array $overrides, string $paypalOrderId): void
    {
        $quote = $this->buildProcessedPaymentQuote([]);

        $this->expectException(Mage_Paypal_Model_Exception::class);

        self::$subject->assertPaypalOrderMatchesQuote($this->buildPaypalOrderDetails($overrides), $quote, $paypalOrderId);
    }

    /**
     * @param  array<string, mixed> $overrides invoice_id, currency_code or value
     * @return array<string, mixed>
     */
    private function buildPaypalOrderDetails(array $overrides = []): array
    {
        return [
            'id' => 'ORDER-1',
            'status' => 'APPROVED',
            'purchase_units' => [[
                'invoice_id' => $overrides['invoice_id'] ?? '100000001',
                'amount' => [
                    'currency_code' => $overrides['currency_code'] ?? 'USD',
                    'value' => $overrides['value'] ?? '42.17',
                ],
            ]],
        ];
    }

    /**
     * @dataProvider providePendingChargeQuotes
     * @param null|array<string, string> $rawDetails
     * @group Model
     */
    public function testGetPendingChargePaypalOrderId(bool $isActive, ?array $rawDetails, ?string $attemptOrderId, string $expected): void
    {
        $quote = Mage::getModel('sales/quote');
        $quote->setId(10)->setIsActive($isActive ? 1 : 0);
        $payment = $quote->getPayment()->setData(Mage_Paypal_Model_Payment::PAYPAL_ORDER_ID, 'ORDER-1');
        if ($rawDetails !== null) {
            $payment->setAdditionalInformation(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS, $rawDetails);
        }

        if ($attemptOrderId !== null) {
            $payment->setAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_CHECKOUT_ATTEMPT_ORDER_ID, $attemptOrderId);
        }

        self::assertSame($expected, self::$subject->getPendingChargePaypalOrderId($quote));
        self::assertSame($expected !== '', self::$subject->hasPendingPaypalCharge($quote));
    }
}
