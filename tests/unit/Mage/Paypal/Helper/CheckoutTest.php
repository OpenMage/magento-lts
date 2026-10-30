<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Paypal\Helper;

use Mage_Paypal_Helper_Checkout as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Helper\CheckoutTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Varien_Object;

final class CheckoutTest extends OpenMageTest
{
    use CheckoutTrait;

    private Varien_Object $session;

    private Subject $subject;

    protected function setUp(): void
    {
        $this->session = new Varien_Object();
        $this->subject = $this->getMockWithCalledMethods(Subject::class, ['_getCheckoutSession' => $this->session]);
    }

    /**
     * @group Mage_Paypal
     * @group Mage_Paypal_Helper
     */
    #[DataProvider('provideAppendNonceToUrlData')]
    #[Group('Mage_Paypal')]
    #[Group('Mage_Paypal_Helper')]
    public function testAppendNonceToUrl(string $expected, string $url): void
    {
        self::assertSame($expected, $this->subject->appendNonceToUrl($url, 'abc123'));
    }

    /**
     * @group Mage_Paypal
     * @group Mage_Paypal_Helper
     */
    #[Group('Mage_Paypal')]
    #[Group('Mage_Paypal_Helper')]
    public function testGetCancelNonceIsReusedForSameOrderOnly(): void
    {
        $nonce = $this->subject->getCancelNonce('100000001');

        self::assertMatchesRegularExpression('/^[0-9a-f]{32}$/', $nonce);
        self::assertSame($nonce, $this->subject->getCancelNonce('100000001'));

        $other = $this->subject->getCancelNonce('100000002');
        self::assertNotSame($nonce, $other);
        self::assertFalse($this->subject->validateCancelNonce($nonce, '100000002'));
    }

    /**
     * @group Mage_Paypal
     * @group Mage_Paypal_Helper
     */
    #[Group('Mage_Paypal')]
    #[Group('Mage_Paypal_Helper')]
    public function testAddCancelNonceToUrl(): void
    {
        $url = $this->subject->addCancelNonceToUrl('https://example.com/paypal/standard/cancel/', '100000001');
        $nonce = $this->subject->getCancelNonce('100000001');

        self::assertSame('https://example.com/paypal/standard/cancel/nonce/' . $nonce . '/', $url);
    }

    /**
     * @group Mage_Paypal
     * @group Mage_Paypal_Helper
     */
    #[Group('Mage_Paypal')]
    #[Group('Mage_Paypal_Helper')]
    public function testValidateCancelNonce(): void
    {
        $nonce = $this->subject->getCancelNonce('100000001');

        self::assertFalse($this->subject->validateCancelNonce(null, '100000001'));
        self::assertFalse($this->subject->validateCancelNonce('', '100000001'));
        self::assertFalse($this->subject->validateCancelNonce(str_repeat('0', 32), '100000001'));
        self::assertFalse($this->subject->validateCancelNonce($nonce, '100000009'));

        self::assertTrue($this->subject->validateCancelNonce($nonce, '100000001'));
        // consumed
        self::assertFalse($this->subject->validateCancelNonce($nonce, '100000001'));
    }

    /**
     * @group Mage_Paypal
     * @group Mage_Paypal_Helper
     */
    #[Group('Mage_Paypal')]
    #[Group('Mage_Paypal_Helper')]
    public function testCanCancelLastRealOrder(): void
    {
        $nonce = $this->subject->getCancelNonce('100000001');

        // no last order in session
        self::assertFalse($this->subject->canCancelLastRealOrder($nonce, 'paypal_standard'));

        $order = new Varien_Object(['id' => 1, 'payment' => new Varien_Object(['method' => 'checkmo'])]);
        $this->session->setLastRealOrderId('100000001')->setLastRealOrder($order);

        // the last order was not paid with the method of the calling controller
        self::assertFalse($this->subject->canCancelLastRealOrder($nonce, 'paypal_standard'));

        $order->getPayment()->setMethod('paypal_standard');

        // forged request without the nonce
        self::assertFalse($this->subject->canCancelLastRealOrder(null, 'paypal_standard'));
        self::assertFalse($this->subject->canCancelLastRealOrder('forged', 'paypal_standard'));

        self::assertTrue($this->subject->canCancelLastRealOrder($nonce, ['payflow_link', 'paypal_standard']));
        // consumed
        self::assertFalse($this->subject->canCancelLastRealOrder($nonce, 'paypal_standard'));
    }

    /**
     * @group Mage_Paypal
     * @group Mage_Paypal_Helper
     */
    #[Group('Mage_Paypal')]
    #[Group('Mage_Paypal_Helper')]
    public function testCanCancelLastRealOrderRejectsNonceOfOtherOrder(): void
    {
        $nonce = $this->subject->getCancelNonce('100000001');
        $order = new Varien_Object(['id' => 2, 'payment' => new Varien_Object(['method' => 'paypal_standard'])]);
        $this->session->setLastRealOrderId('100000002')->setLastRealOrder($order);

        self::assertFalse($this->subject->canCancelLastRealOrder($nonce, 'paypal_standard'));
    }
}
