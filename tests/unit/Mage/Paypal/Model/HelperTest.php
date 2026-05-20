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
use Mage_Paypal_Model_Exception;
use Mage_Paypal_Model_Helper as Subject;
use Mage_Paypal_Model_Transaction;
use Mage_Sales_Model_Order_Payment_Transaction;
use Mage_Sales_Model_Quote;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model\HelperTrait;
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
}
