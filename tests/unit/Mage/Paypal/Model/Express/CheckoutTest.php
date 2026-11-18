<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Paypal\Model\Express;

use Iterator;
use Mage;
use Mage_Core_Exception;
use Mage_Customer_Model_Session;
use Mage_Paypal_Model_Config;
use Mage_Paypal_Model_Express_Checkout as Subject;
use Mage_PaypalUk_Model_Express_Checkout;
use Mage_Sales_Model_Quote;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model\Express\CheckoutTrait;
use Varien_Object;

final class CheckoutTest extends OpenMageTest
{
    use CheckoutTrait;

    /**
     * @return Iterator<string, array{class-string<Subject>, (null|string)}>
     */
    public static function provideNonExpressPaymentMethods(): Iterator
    {
        yield 'PayPal Express, no method' => [Subject::class, null];
        yield 'PayPal Express, check/money order' => [Subject::class, 'checkmo'];
        yield 'PayPal Express, Payflow Express method' => [Subject::class, Mage_Paypal_Model_Config::METHOD_WPP_PE_EXPRESS];
        yield 'Payflow Express, PayPal Express method' => [Mage_PaypalUk_Model_Express_Checkout::class, Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS];
        yield 'Payflow Express, check/money order' => [Mage_PaypalUk_Model_Express_Checkout::class, 'checkmo'];
    }

    /**
     * GHSA-5xfj-mgf7-27gm: place() submitted the quote with whatever payment method it carried.
     *
     * @param class-string<Subject> $class
     * @dataProvider provideNonExpressPaymentMethods
     * @group Model
     */
    public function testPlaceRefusesQuoteNotPaidWithThisExpressMethod(string $class, ?string $method): void
    {
        $quote = $this->createMock(Mage_Sales_Model_Quote::class);
        $quote->method('getPayment')->willReturn(new Varien_Object(['method' => $method]));
        $quote->expects(self::never())->method('collectTotals');

        $subject = new $class([
            'quote' => $quote,
            'config' => Mage::getModel('paypal/config'),
            'session' => $this->createMock(Mage_Customer_Model_Session::class),
        ]);

        $this->expectException(Mage_Core_Exception::class);
        $this->expectExceptionMessage('Unable to place the order: the payment method was not approved by PayPal.');
        $subject->place('EC-TOKEN');
    }
}
