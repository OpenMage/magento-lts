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
use Mage_Paypal_Model_Payment;
use Mage_Paypal_Model_Transaction as Subject;
use Mage_Sales_Model_Order_Payment_Transaction;
use Mage_Sales_Model_Quote_Payment;
use OpenMage\Tests\Unit\OpenMageTest;
use PaypalServerSdkLib\Http\ApiResponse;
use PaypalServerSdkLib\Models\Builders\AuthorizationWithAdditionalDataBuilder;
use PaypalServerSdkLib\Models\Builders\OrderBuilder;
use PaypalServerSdkLib\Models\Builders\PaymentCollectionBuilder;
use PaypalServerSdkLib\Models\Builders\PaymentSourceResponseBuilder;
use PaypalServerSdkLib\Models\Builders\PurchaseUnitBuilder;

final class TransactionTest extends OpenMageTest
{
    /**
     * @group Model
     */
    public function testUpdatePaymentAfterAuthorizationKeepsCheckoutMarkers(): void
    {
        $payment = $this->getMockBuilder(Mage_Sales_Model_Quote_Payment::class)
            ->onlyMethods(['save'])
            ->getMock();
        $payment->setAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_ORDER_ID, '5O190127TN364715T')
            ->setAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_REQUEST_ID, 'REQ-1');

        $authorization = AuthorizationWithAdditionalDataBuilder::init()->id('AUTH-1')->status('CREATED')->build();
        $order = OrderBuilder::init()
            ->purchaseUnits([
                PurchaseUnitBuilder::init()
                    ->payments(PaymentCollectionBuilder::init()->authorizations([$authorization])->build())
                    ->build(),
            ])
            ->paymentSource(PaymentSourceResponseBuilder::init()->build())
            ->build();
        $response = $this->createMock(ApiResponse::class);
        $response->method('getResult')->willReturn($order);
        $response->method('getBody')->willReturn('{"id":"5O190127TN364715T","status":"COMPLETED"}');

        /** @var Subject $subject */
        $subject = Mage::getModel('paypal/transaction');
        $subject->updatePaymentAfterAuthorization($payment, $response);

        self::assertSame('AUTH-1', $payment->getAdditionalInformation(Subject::PAYPAL_PAYMENT_AUTHORIZATION_ID));
        self::assertSame('5O190127TN364715T', $payment->getAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_ORDER_ID));
        self::assertSame('REQ-1', $payment->getAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_REQUEST_ID));
        self::assertIsArray($payment->getAdditionalInformation(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS));
    }
}
