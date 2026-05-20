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
use Mage_Core_Model_Session;
use Mage_Paypal_Model_Express_CurrencyLock as Subject;
use Mage_Paypal_Model_Payment;
use Mage_Sales_Model_Quote;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model\Express\CurrencyLockTrait;
use ReflectionClass;

final class CurrencyLockTest extends OpenMageTest
{
    use CurrencyLockTrait;

    private Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registerHeadlessCheckoutSession();
        $this->subject = Mage::getModel('paypal/express_currencyLock');
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
        $this->registerHeadlessSession('_singleton/checkout/session', Mage_Checkout_Model_Session::class);
        // Mage_Core_Model_Store::getCurrentCurrencyCode() resolves a Mage_Core_Model_Session for its locale
        // currency override — install a headless copy so the same session_start() warning doesn't trip it.
        $this->registerHeadlessSession('_singleton/core/session', Mage_Core_Model_Session::class);
    }

    /**
     * @param class-string $class
     */
    private function registerHeadlessSession(string $registryKey, string $class): void
    {
        if (Mage::registry($registryKey) !== null) {
            return;
        }
        Mage::register($registryKey, (new ReflectionClass($class))->newInstanceWithoutConstructor());
    }

    /**
     * Should silently no-op when no PayPal attempt is in progress — the controller can call applyTo()
     * unconditionally without an existence check.
     *
     * @group Model
     */
    public function testApplyToNoOpsWhenLockedCurrencyMissing(): void
    {
        $this->subject->applyTo($this->buildQuote());
        $this->expectNotToPerformAssertions();
    }

    /**
     * Should no-op when nothing was snapshotted — so cancel/success flows can call restore() blindly.
     *
     * @group Model
     */
    public function testRestoreNoOpsWhenNoPriorSnapshot(): void
    {
        $this->subject->restore($this->buildQuote());
        $this->expectNotToPerformAssertions();
    }

    /**
     * Drops the prior-currency snapshot so the next attempt's lock() takes a fresh one — used by the
     * stale-quote cleanup path in the controller.
     *
     * @group Model
     */
    public function testForgetPriorSnapshotClearsSessionKey(): void
    {
        $this->getCheckoutSession()->setData(
            Mage_Paypal_Model_Payment::PAYPAL_EXPRESS_PRIOR_CURRENCY,
            'EUR',
        );

        $this->subject->forgetPriorSnapshot();

        self::assertNull(
            $this->getCheckoutSession()->getData(Mage_Paypal_Model_Payment::PAYPAL_EXPRESS_PRIOR_CURRENCY),
        );
    }

    private function buildQuote(): Mage_Sales_Model_Quote
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = Mage::getModel('sales/quote');
        $quote->setStoreId((int) Mage::app()->getStore()->getId());
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
        $session->unsetData(Mage_Paypal_Model_Payment::PAYPAL_EXPRESS_PRIOR_CURRENCY);
        $session->unsetData(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_CURRENCY);
    }
}
