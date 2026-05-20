<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Paypal\Model\Express;

use Mage;
use Mage_Checkout_Model_Session;
use Mage_Paypal_Model_Express_ShortcutState as Subject;
use Mage_Paypal_Model_Payment;
use Mage_Sales_Model_Quote;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model\Express\ShortcutStateTrait;
use ReflectionClass;

final class ShortcutStateTest extends OpenMageTest
{
    use ShortcutStateTrait;

    private Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registerHeadlessCheckoutSession();
        $this->subject = Mage::getModel('paypal/express_shortcutState');
        $this->resetSessionKeys();
    }

    protected function tearDown(): void
    {
        $this->resetSessionKeys();
        parent::tearDown();
    }

    /**
     * Register a Mage_Checkout_Model_Session instance built without its constructor so getData/setData/
     * unsetData (inherited from Varien_Object) work without triggering PHP's session_start() — which
     * cannot run inside a PHPUnit process that has already emitted output.
     */
    private function registerHeadlessCheckoutSession(): void
    {
        if (Mage::registry('_singleton/checkout/session') !== null) {
            return;
        }

        $session = (new ReflectionClass(Mage_Checkout_Model_Session::class))->newInstanceWithoutConstructor();
        Mage::register('_singleton/checkout/session', $session);
    }

    /**
     * @group Model
     */
    public function testGetOrderIdReadsFromPaymentAdditionalInformation(): void
    {
        $quote = $this->buildQuote();
        $quote->getPayment()->setAdditionalInformation(
            Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_ORDER_ID,
            'PAYPAL-ORDER-1',
        );

        self::assertSame('PAYPAL-ORDER-1', $this->subject->getOrderId($quote));
    }

    /**
     * @group Model
     */
    public function testGetOrderIdFallsBackToCheckoutSession(): void
    {
        $quote = $this->buildQuote();
        $this->getCheckoutSession()->setData(
            Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_ORDER_ID,
            'PAYPAL-ORDER-FROM-SESSION',
        );

        self::assertSame('PAYPAL-ORDER-FROM-SESSION', $this->subject->getOrderId($quote));
    }

    /**
     * @group Model
     */
    public function testGetOrderIdReturnsEmptyWhenNeitherSet(): void
    {
        self::assertSame('', $this->subject->getOrderId($this->buildQuote()));
    }

    /**
     * Verifies the read-fallback that previously lived in five copy-pasted blocks: when the payment has no
     * value but the session does, the session copy wins. This is the duplication the extraction removed.
     *
     * @group Model
     */
    public function testGetReservedOrderIdAndLockedCurrencyAlsoFallBackToSession(): void
    {
        $quote = $this->buildQuote();
        $session = $this->getCheckoutSession();
        $session->setData(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_RESERVED_ORDER_ID, '100000007');
        $session->setData(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_CURRENCY, 'EUR');

        self::assertSame('100000007', $this->subject->getReservedOrderId($quote));
        self::assertSame('EUR', $this->subject->getLockedCurrency($quote));
    }

    /**
     * Verifies a payment-stored value takes precedence over a session value for the same key.
     *
     * @group Model
     */
    public function testGetLockedCurrencyPrefersPaymentOverSession(): void
    {
        $quote = $this->buildQuote();
        $quote->getPayment()->setAdditionalInformation(
            Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_CURRENCY,
            'USD',
        );
        $this->getCheckoutSession()->setData(
            Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_CURRENCY,
            'EUR',
        );

        self::assertSame('USD', $this->subject->getLockedCurrency($quote));
    }

    /**
     * `clear()` must drop the session copy of every shortcut key, even when the in-memory quote is
     * not persisted (so the payment->save() branch is skipped). Cancel/cleanup paths rely on this.
     *
     * @group Model
     */
    public function testClearDropsAllShortcutKeysFromSession(): void
    {
        $session = $this->getCheckoutSession();
        $session->setData(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_ORDER_ID, 'ORDER-9');
        $session->setData(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_REQUEST_ID, 'REQ-9');
        $session->setData(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_RESERVED_ORDER_ID, '100000009');
        $session->setData(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_CURRENCY, 'USD');

        $this->subject->clear($this->buildQuote());

        self::assertNull($session->getData(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_ORDER_ID));
        self::assertNull($session->getData(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_REQUEST_ID));
        self::assertNull($session->getData(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_RESERVED_ORDER_ID));
        self::assertNull($session->getData(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_CURRENCY));
    }

    /**
     * `clear()` must reset transient payment-transaction flags so a retry does not inherit a half-finished
     * transaction id or closed-transaction marker.
     *
     * @group Model
     */
    public function testClearResetsTransientPaymentTransactionFlags(): void
    {
        $quote = $this->buildQuote();
        $payment = $quote->getPayment();
        $payment->setPaypalCorrelationId('CORR-1')
            ->setTransactionId('TXN-1')
            ->setIsTransactionClosed(true);

        $this->subject->clear($quote);

        self::assertNull($payment->getPaypalCorrelationId());
        self::assertNull($payment->getTransactionId());
        self::assertFalse((bool) $payment->getIsTransactionClosed());
    }

    private function buildQuote(): Mage_Sales_Model_Quote
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = Mage::getModel('sales/quote');
        // Leave id unset so clear() doesn't try to persist the payment to the database.
        return $quote;
    }

    private function getCheckoutSession(): Mage_Checkout_Model_Session
    {
        /** @var Mage_Checkout_Model_Session $session */
        $session = Mage::getSingleton('checkout/session');
        return $session;
    }

    private function resetSessionKeys(): void
    {
        $session = $this->getCheckoutSession();
        foreach ([
            Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_ORDER_ID,
            Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_REQUEST_ID,
            Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_RESERVED_ORDER_ID,
            Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_CURRENCY,
        ] as $key) {
            $session->unsetData($key);
        }
    }
}
