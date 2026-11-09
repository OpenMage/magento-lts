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
use Mage_Core_Block_Template;
use Mage_Core_Helper_Data;
use Mage_Core_Model_App;
use Mage_Core_Model_Config;
use Mage_Core_Model_Store;
use Mage_Payment_Block_Info;
use Mage_Payment_Helper_Data;
use Mage_Payment_Model_Method_Banktransfer;
use Mage_Payment_Model_Method_Unavailable;
use Mage_Sales_Model_Order_Payment as Subject;
use Mage_Sales_Model_Quote_Payment;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Varien_Object;

/** Exercise the real model factories and payment rendering without a database. */
final class PaymentMethodTest extends TestCase
{
    /** @var array<array{ReflectionProperty, mixed}> */
    private array $staticState = [];

    #[Override]
    protected function setUp(): void
    {
        foreach ([
            Mage::class => ['_config', '_app', '_registry', '_appRoot'],
            Mage_Core_Block_Template::class => ['_showTemplateHints', '_showTemplateHintsBlocks'],
        ] as $class => $properties) {
            foreach ($properties as $property) {
                $reflection = new ReflectionProperty($class, $property);
                $this->staticState[] = [$reflection, $reflection->getValue()];
            }
        }

        Mage::setRoot(BP . '/app');
        $config = new Mage_Core_Model_Config();
        $config->loadString('<config><global><models>'
            . '<payment><class>Mage_Payment_Model</class></payment>'
            . '<sales><class>Mage_Sales_Model</class></sales>'
            . '</models><blocks><payment><class>Mage_Payment_Block</class></payment></blocks></global></config>');
        (new ReflectionProperty(Mage::class, '_config'))->setValue(null, $config);

        $methods = [
            'banktransfer' => ['model' => 'payment/method_banktransfer', 'title' => 'Bank Transfer', 'active' => '0'],
            'missing_model' => ['model' => 'removed/method', 'title' => 'Old Method', 'active' => '0'],
        ];
        $store = $this->createMock(Mage_Core_Model_Store::class);
        $store->method('getConfig')->willReturnCallback(static function (string $path) use ($methods): mixed {
            $value = ['payment' => $methods];
            foreach (explode('/', $path) as $part) {
                $value = is_array($value) ? ($value[$part] ?? null) : null;
            }
            return $value;
        });
        $app = $this->createMock(Mage_Core_Model_App::class);
        $app->method('getStore')->willReturn($store);
        (new ReflectionProperty(Mage::class, '_app'))->setValue(null, $app);

        $helper = $this->createPartialMock(Mage_Payment_Helper_Data::class, ['__']);
        $helper->method('__')->willReturnCallback(static fn(string $message, mixed ...$args): string => vsprintf($message, $args));
        (new ReflectionProperty(Mage::class, '_registry'))->setValue(null, [
            '_helper/payment' => $helper,
            '_helper/core' => new Mage_Core_Helper_Data(),
            '_singleton/core/logger' => new Varien_Object(),
        ]);
    }

    #[Override]
    protected function tearDown(): void
    {
        foreach ($this->staticState as [$property, $value]) {
            $property->setValue(null, $value);
        }
    }

    #[DataProvider('provideMissingMethods')]
    public function testMissingOrderMethodCanBeDisplayed(string $code): void
    {
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
        $html = $block->fetchView('frontend/base/default/template/payment/info/default.phtml');
        self::assertStringContainsString('<strong>' . htmlspecialchars($code, ENT_COMPAT, 'UTF-8', false) . '</strong>', $html);
    }

    public static function provideMissingMethods(): Generator
    {
        yield 'removed configuration' => ['removed_method'];
        yield 'removed model class' => ['missing_model'];
        yield 'escaped historical code' => ['<removed&method>'];
    }

    public function testDisabledConfiguredMethodKeepsItsImplementation(): void
    {
        $payment = new Subject(['method' => 'banktransfer']);
        $method = $payment->getMethodInstance();

        self::assertInstanceOf(Mage_Payment_Model_Method_Banktransfer::class, $method);
        self::assertSame('Bank Transfer', $method->getTitle());
        self::assertSame($payment, $method->getInfoInstance());
        self::assertSame($method, $payment->getMethodInstance());
    }

    public function testExplicitMethodInstanceIsPreserved(): void
    {
        $method = new Mage_Payment_Model_Method_Banktransfer();
        $payment = new Subject(['method' => 'removed_method', 'method_instance' => $method]);
        self::assertSame($method, $payment->getMethodInstance());
    }

    public function testQuoteDoesNotUseTheOrderFallback(): void
    {
        $payment = new Mage_Sales_Model_Quote_Payment(['method' => 'removed_method']);
        $this->expectException(Mage_Core_Exception::class);
        $payment->getMethodInstance();
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
            default => self::fail('Unknown action'),
        };
    }

    public static function provideOnlineActions(): Generator
    {
        yield 'order' => ['order'];
        yield 'authorize' => ['authorize'];
        yield 'capture' => ['capture'];
        yield 'refund' => ['refund'];
    }
}
