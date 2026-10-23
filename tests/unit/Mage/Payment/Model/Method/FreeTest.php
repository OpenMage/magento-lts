<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Payment\Model\Method;

use Generator;
use Mage_Core_Exception;
use Mage_Payment_Model_Method_Free as Subject;
use Mage_Sales_Model_Order;
use Mage_Sales_Model_Order_Payment;
use Mage_Sales_Model_Quote;
use Mage_Sales_Model_Quote_Payment;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Payment\Model\Method\FreeTrait;
use Varien_Object;

final class FreeTest extends OpenMageTest
{
    use FreeTrait;

    /**
     * GHSA-rmw8-7h79-p386: the free method is selected while the cart totals zero, the cart is
     * then raised to a real total and the order placed without re-importing payment data. The
     * order payment must be refused at placement.
     *
     * @dataProvider provideValidateOrderPayment
     * @group Model
     */
    public function testValidateOrderPayment(float $grandTotal, bool $expectException): void
    {
        $order = $this->getMockWithCalledMethods(Mage_Sales_Model_Order::class, [
            'getBillingAddress' => new Varien_Object(['country_id' => 'US']),
        ]);
        $order->setGrandTotal($grandTotal);
        $payment = $this->getMockWithCalledMethods(Mage_Sales_Model_Order_Payment::class, [
            'getOrder' => $order,
        ]);

        $subject = new Subject();
        $subject->setInfoInstance($payment);

        if ($expectException) {
            $this->expectException(Mage_Core_Exception::class);
            $this->expectExceptionMessage('The requested Payment Method is not available.');
        }

        self::assertSame($subject, $subject->validate());
    }

    public static function provideValidateOrderPayment(): Generator
    {
        yield 'zero total' => [0.0, false];
        yield 'rounds to zero' => [0.001, false];
        yield 'full price order' => [149.99, true];
        yield 'one cent' => [0.01, true];
    }

    /**
     * @dataProvider provideValidateOrderPayment
     * @group Model
     */
    public function testValidateQuotePayment(float $grandTotal, bool $expectException): void
    {
        $quote = $this->getMockWithCalledMethods(Mage_Sales_Model_Quote::class, [
            'getBillingAddress' => new Varien_Object(['country_id' => 'US']),
        ]);
        $quote->setGrandTotal($grandTotal);
        $payment = $this->getMockWithCalledMethods(Mage_Sales_Model_Quote_Payment::class, [
            'getQuote' => $quote,
        ]);

        $subject = new Subject();
        $subject->setInfoInstance($payment);

        if ($expectException) {
            $this->expectException(Mage_Core_Exception::class);
        }

        self::assertSame($subject, $subject->validate());
    }
}
