<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Model\Order;

use Generator;
use Mage;
use Mage_Core_Exception;
use Mage_Payment_Block_Info;
use Mage_Payment_Model_Method_Banktransfer;
use Mage_Payment_Model_Method_Unavailable;
use Mage_Sales_Model_Order;
use Mage_Sales_Model_Order_Payment as Subject;
use Mage_Sales_Model_Quote_Payment;
use OpenMage\Tests\Unit\OpenMageTest;
use PHPUnit\Framework\Attributes\DataProvider;

final class PaymentMethodTest extends OpenMageTest
{
    /** @var array<string, mixed> */
    private array $originalConfig = [];

    protected function setUp(): void
    {
        parent::setUp();
        foreach (['payment/banktransfer/model', 'payment/banktransfer/active'] as $path) {
            $this->originalConfig[$path] = Mage::getStoreConfig($path);
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->originalConfig as $path => $value) {
            Mage::app()->getStore()->setConfig($path, $value);
        }

        parent::tearDown();
    }

    #[DataProvider('provideMissingMethods')]
    public function testMissingOrderMethodCanBeDisplayed(string $code): void
    {
        if ($code === 'banktransfer') {
            Mage::app()->getStore()->setConfig('payment/banktransfer/model', 'Removed_Payment_Method');
        }

        $payment = new Subject(['method' => $code]);
        $methodsBefore = Mage::helper('payment')->getPaymentMethodList();
        $method = $payment->getMethodInstance();

        self::assertInstanceOf(Mage_Payment_Model_Method_Unavailable::class, $method);
        self::assertSame($code, $method->getCode());
        self::assertSame($code, $method->getTitle());
        self::assertSame($payment, $method->getInfoInstance());
        self::assertSame($method, $payment->getMethodInstance());
        self::assertSame($code, $payment->getMethod());
        self::assertSame($methodsBefore, Mage::helper('payment')->getPaymentMethodList());
        self::assertFalse($payment->canCapture());
        self::assertFalse($payment->canVoid($payment));
        self::assertFalse($payment->canReviewPayment());
        self::assertFalse($payment->canFetchTransactionInfo());

        $block = Mage::helper('payment')->getInfoBlock($payment);
        self::assertInstanceOf(Mage_Payment_Block_Info::class, $block);
        self::assertSame($method, $block->getMethod());
        self::assertSame('payment/info/default.phtml', $block->getTemplate());
        self::assertSame([], $block->getSpecificInformation());

        $block->setScriptPath(Mage::getBaseDir('design'));
        foreach ([
            'frontend/base/default/template/payment/info/default.phtml',
            'adminhtml/base/default/template/payment/info/default.phtml',
            'adminhtml/base/default/template/payment/info/pdf/default.phtml',
        ] as $template) {
            $html = $block->fetchView($template);
            self::assertStringContainsString(htmlspecialchars($code, ENT_COMPAT, 'UTF-8', false), (string) $html);
        }
    }

    public static function provideMissingMethods(): Generator
    {
        yield 'removed configuration' => ['removed_method'];
        yield 'removed model class' => ['banktransfer'];
        yield 'escaped historical code' => ['<removed&method>'];
    }

    #[DataProvider('provideActiveFlags')]
    public function testConfiguredMethodKeepsItsImplementation(string $active): void
    {
        Mage::app()->getStore()->setConfig('payment/banktransfer/active', $active);
        $title = Mage::getStoreConfig('payment/banktransfer/title');
        $payment = new Subject(['method' => 'banktransfer']);
        $method = $payment->getMethodInstance();

        self::assertInstanceOf(Mage_Payment_Model_Method_Banktransfer::class, $method);
        self::assertSame($title, $method->getTitle());
        self::assertSame($payment, $method->getInfoInstance());
        self::assertSame($method, $payment->getMethodInstance());
    }

    public static function provideActiveFlags(): Generator
    {
        yield 'disabled' => ['0'];
        yield 'enabled' => ['1'];
    }

    public function testExplicitMethodInstanceIsPreserved(): void
    {
        $method = new Mage_Payment_Model_Method_Banktransfer();
        $payment = new Subject(['method' => 'removed_method', 'method_instance' => $method]);
        self::assertSame($method, $payment->getMethodInstance());
    }

    #[DataProvider('provideMissingMethods')]
    public function testQuoteDoesNotUseTheOrderFallback(string $code): void
    {
        if ($code === 'banktransfer') {
            Mage::app()->getStore()->setConfig('payment/banktransfer/model', 'Removed_Payment_Method');
        }

        $payment = new Mage_Sales_Model_Quote_Payment(['method' => $code]);
        $this->expectException(Mage_Core_Exception::class);
        $payment->getMethodInstance();
    }

    public function testFallbackCannotPlaceAnOrder(): void
    {
        $payment = new Subject(['method' => 'removed_method']);
        $payment->setOrder(new Mage_Sales_Model_Order());
        $this->expectException(Mage_Core_Exception::class);
        $this->expectExceptionMessage('The requested Payment Method is not available.');
        $payment->place();
    }

    public function testEmptyOrderPaymentStillThrows(): void
    {
        $this->expectException(Mage_Core_Exception::class);
        (new Subject())->getMethodInstance();
    }

    public function testFallbackCannotBeSelectedOrUsedOnline(): void
    {
        $method = (new Subject(['method' => 'removed_method']))->getMethodInstance();
        foreach ([
            'checkout' => $method->canUseCheckout(),
            'admin order creation' => $method->canUseInternal(),
            'multishipping' => $method->canUseForMultishipping(),
            'recurring profiles' => $method->canManageRecurringProfiles(),
            'order' => $method->canOrder(),
            'authorize' => $method->canAuthorize(),
            'capture' => $method->canCapture(),
            'refund' => $method->canRefund(),
            'edit' => $method->canEdit(),
            'available' => $method->isAvailable(),
        ] as $capability => $available) {
            self::assertFalse($available, $capability);
        }

        $this->expectException(Mage_Core_Exception::class);
        $method->validate();
    }

    #[DataProvider('provideOnlineActions')]
    public function testFallbackRejectsOnlineActions(string $action): void
    {
        $payment = new Subject(['method' => 'removed_method']);
        $this->expectException(Mage_Core_Exception::class);
        $method = $payment->getMethodInstance();
        match ($action) {
            'order' => $method->order($payment, 10.0),
            'authorize' => $method->authorize($payment, 10.0),
            'capture' => $method->capture($payment, 10.0),
            'refund' => $method->refund($payment, 10.0),
            'void' => $method->void($payment),
            default => self::fail('Unknown action'),
        };
    }

    public static function provideOnlineActions(): Generator
    {
        yield 'order' => ['order'];
        yield 'authorize' => ['authorize'];
        yield 'capture' => ['capture'];
        yield 'refund' => ['refund'];
        yield 'void' => ['void'];
    }
}
