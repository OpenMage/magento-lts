<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Model\Order;

use Mage_Sales_Model_Order_Api as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use ReflectionMethod;
use Varien_Object;

/**
 * sales_order.info must not return stored card data or payment gateway secrets.
 */
final class ApiReadSecretsTest extends OpenMageTest
{
    public function testOrderPaymentExcludesCardSecrets(): void
    {
        $payment = new Varien_Object([
            'entity_id' => 4,
            'method' => 'ccsave',
            'cc_type' => 'VI',
            'cc_last4' => '1111',
            'cc_owner' => 'John Doe',
            'cc_exp_month' => '12',
            'cc_exp_year' => '2030',
            'cc_trans_id' => 'txn-1',
            'last_trans_id' => 'txn-1',
            'cc_avs_status' => 'Y',
            'cc_number_enc' => 'encrypted-pan',
            'cc_debug_request_body' => 'x_card_num=4111111111111111',
            'cc_debug_response_body' => 'raw response',
            'cc_debug_response_serialized' => 'a:0:{}',
            'additional_information' => [
                'secure_silent_post_hash' => 'hash',
                'secure_token' => 'token',
                'secure_token_id' => 'token-id',
                'paypal_cvv2_match' => 'Y',
            ],
        ]);

        self::assertSame(
            [
                'method' => 'ccsave',
                'cc_type' => 'VI',
                'cc_last4' => '1111',
                'cc_owner' => 'John Doe',
                'cc_exp_month' => '12',
                'cc_exp_year' => '2030',
                'cc_trans_id' => 'txn-1',
                'last_trans_id' => 'txn-1',
                'cc_avs_status' => 'Y',
                'additional_information' => ['paypal_cvv2_match' => 'Y'],
                'payment_id' => 4,
            ],
            $this->getAttributes($payment, 'order_payment'),
        );
    }

    public function testExplicitAttributeFilterCannotReadmitCardSecrets(): void
    {
        $payment = new Varien_Object(['entity_id' => 4, 'cc_number_enc' => 'encrypted-pan', 'method' => 'ccsave']);

        self::assertSame(
            ['method' => 'ccsave', 'payment_id' => 4],
            $this->getAttributes($payment, 'order_payment', ['cc_number_enc', 'method']),
        );
    }

    public function testSecretsAreRemovedWhateverTheEntityType(): void
    {
        $order = new Varien_Object(['entity_id' => 2, 'increment_id' => '100000002', 'cc_number_enc' => 'x']);

        self::assertSame(
            ['increment_id' => '100000002', 'order_id' => 2],
            $this->getAttributes($order, 'order'),
        );
    }

    /**
     * @param  null|string[]        $attributes
     * @return array<string, mixed>
     */
    private function getAttributes(Varien_Object $object, string $type, ?array $attributes = null): array
    {
        $subject = new Subject();

        return (new ReflectionMethod($subject, '_getAttributes'))->invoke($subject, $object, $type, $attributes);
    }
}
