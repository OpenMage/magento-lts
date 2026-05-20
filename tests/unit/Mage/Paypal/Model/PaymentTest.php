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
use Mage_Paypal_Model_Payment as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model\PaymentTrait;
use ReflectionMethod;

final class PaymentTest extends OpenMageTest
{
    use PaymentTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('paypal/payment');
    }

    /**
     * The refund guard must accept amounts up to the captured total minus prior
     * refunds, and reject everything else.
     *
     * @dataProvider provideRefundableScenarios
     * @group Model
     */
    public function testAssertRefundable(
        bool $shouldThrow,
        ?float $captured,
        ?float $refunded,
        float $totalPaid,
        float $amount
    ): void {
        $order = Mage::getModel('sales/order')->setTotalPaid($totalPaid);
        $payment = Mage::getModel('sales/order_payment')->setOrder($order);

        if ($captured !== null) {
            $payment->setAdditionalInformation(Subject::PAYPAL_CAPTURED_AMOUNT, $captured);
        }

        if ($refunded !== null) {
            $payment->setAdditionalInformation(Subject::PAYPAL_REFUNDED_AMOUNT, $refunded);
        }

        $method = new ReflectionMethod(Subject::class, '_assertRefundable');

        if ($shouldThrow) {
            $this->expectException(Mage_Paypal_Model_Exception::class);
            $method->invoke(self::$subject, $payment, $amount);

            return;
        }

        self::assertNull($method->invoke(self::$subject, $payment, $amount));
    }
}
