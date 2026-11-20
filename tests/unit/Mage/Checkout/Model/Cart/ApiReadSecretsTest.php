<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Checkout\Model\Cart;

use Mage_Checkout_Model_Cart_Api as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use ReflectionMethod;
use Varien_Object;

/**
 * cart.info must not return the quote's password_hash, which holds the encrypted password of a customer
 * who registers during checkout, nor stored card data and payment gateway secrets.
 */
final class ApiReadSecretsTest extends OpenMageTest
{
    public function testQuoteAttributesExcludePasswordHash(): void
    {
        $quote = new Varien_Object([
            'entity_id' => 7,
            'customer_email' => 'a@example.com',
            'checkout_method' => 'register',
            'password_hash' => 'encrypted-password',
        ]);

        $result = $this->getAttributes($quote, 'quote');

        self::assertArrayNotHasKey('password_hash', $result);
        self::assertSame(
            ['customer_email' => 'a@example.com', 'checkout_method' => 'register', 'quote_id' => 7],
            $result,
        );
    }

    public function testExplicitAttributeFilterCannotReadmitPasswordHash(): void
    {
        $quote = new Varien_Object(['entity_id' => 7, 'password_hash' => 'encrypted-password']);

        self::assertArrayNotHasKey('password_hash', $this->getAttributes($quote, 'quote', ['password_hash']));
    }

    public function testSecretsAreRemovedWhateverTheEntityType(): void
    {
        $address = new Varien_Object(['entity_id' => 3, 'password_hash' => 'x', 'cc_number_enc' => 'y', 'city' => 'Austin']);

        self::assertSame(
            ['city' => 'Austin', 'address_id' => 3],
            $this->getAttributes($address, 'quote_address'),
        );
    }

    public function testQuotePaymentExcludesCardSecrets(): void
    {
        $payment = new Varien_Object([
            'entity_id' => 9,
            'method' => 'ccsave',
            'cc_type' => 'VI',
            'cc_last4' => '1111',
            'cc_owner' => 'John Doe',
            'cc_exp_month' => 12,
            'cc_exp_year' => 2030,
            'cc_ss_issue' => '01',
            'cc_number_enc' => 'encrypted-pan',
            'cc_cid_enc' => 'encrypted-cvv',
            'cc_number' => '4111111111111111',
            'cc_cid' => '123',
            'additional_information' => [
                'secure_silent_post_hash' => 'hash',
                'secure_token' => 'token',
                'secure_token_id' => 'token-id',
                'paypal_avs_code' => 'Y',
            ],
        ]);

        self::assertSame(
            [
                'method' => 'ccsave',
                'cc_type' => 'VI',
                'cc_last4' => '1111',
                'cc_owner' => 'John Doe',
                'cc_exp_month' => 12,
                'cc_exp_year' => 2030,
                'cc_ss_issue' => '01',
                'additional_information' => ['paypal_avs_code' => 'Y'],
                'payment_id' => 9,
            ],
            $this->getAttributes($payment, 'quote_payment'),
        );
    }

    public function testExplicitAttributeFilterCannotReadmitCardSecrets(): void
    {
        $payment = new Varien_Object(['entity_id' => 9, 'cc_number_enc' => 'encrypted-pan', 'cc_cid_enc' => 'encrypted-cvv']);

        self::assertSame(
            ['payment_id' => 9],
            $this->getAttributes($payment, 'quote_payment', ['cc_number_enc', 'cc_cid_enc']),
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
