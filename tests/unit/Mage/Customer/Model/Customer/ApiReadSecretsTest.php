<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Customer\Model\Customer;

use Generator;
use Mage_Customer_Model_Customer;
use Mage_Customer_Model_Customer_Api as Subject;
use Mage_Eav_Model_Entity_Abstract;
use Mage_Eav_Model_Entity_Attribute;
use OpenMage\Tests\Unit\OpenMageTest;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionMethod;

/**
 * customer.info and customer.list must not hand out the password hash, the password-reset token or the
 * confirmation key, and must not let a filter recover them. customer.create and customer.update treat a
 * written confirmation as the status the reads return, never as the key.
 */
final class ApiReadSecretsTest extends OpenMageTest
{
    private const SECRET_CODES = ['password_hash', 'rp_token', 'rp_token_created_at', 'rp_customer_id'];

    public function testReadableAttributeCodesExcludeSecrets(): void
    {
        $customer = $this->createMock(Mage_Customer_Model_Customer::class);
        $allowed = array_fill_keys(['email', 'firstname', 'confirmation', ...self::SECRET_CODES], true);

        $subject = $this->createPartialMock(Subject::class, ['getAllowedAttributes']);
        $subject->method('getAllowedAttributes')->willReturn($allowed);

        $codes = $this->invoke($subject, '_getReadableAttributeCodes', $customer);

        self::assertSame(['email', 'firstname', 'confirmation'], $codes);
    }

    public function testExplicitAttributeFilterCannotReadmitSecrets(): void
    {
        $customer = $this->createMock(Mage_Customer_Model_Customer::class);
        $filter = ['email', ...self::SECRET_CODES];

        $subject = $this->createPartialMock(Subject::class, ['getAllowedAttributes']);
        $subject->expects(self::once())
            ->method('getAllowedAttributes')
            ->with($customer, $filter)
            ->willReturn(array_fill_keys($filter, true));

        self::assertSame(['email'], $this->invoke($subject, '_getReadableAttributeCodes', $customer, $filter));
    }

    #[DataProvider('provideReadValues')]
    public function testPrepareAttributeValueForRead(string $code, mixed $value, mixed $expected): void
    {
        self::assertSame($expected, $this->invoke(new Subject(), '_prepareAttributeValueForRead', $code, $value));
    }

    public static function provideReadValues(): Generator
    {
        yield 'pending confirmation key becomes true' => ['confirmation', 'b7a1c0d2e3f4a5b6c7d8e9f0a1b2c3d4', true];
        yield 'confirmed (null) becomes false' => ['confirmation', null, false];
        yield 'confirmed (empty) becomes false' => ['confirmation', '', false];
        yield 'other attributes are untouched' => ['email', 'john@example.com', 'john@example.com'];
    }

    #[DataProvider('provideFilters')]
    public function testIsAllowedFilter(bool $expected, int|string $field, mixed $condition, ?string $resolvedCode): void
    {
        $entity = $this->createMock(Mage_Eav_Model_Entity_Abstract::class);
        if ($resolvedCode === null) {
            $entity->method('getAttribute')->willReturn(false);
        } else {
            $attribute = $this->createPartialMock(Mage_Eav_Model_Entity_Attribute::class, []);
            $attribute->setAttributeCode($resolvedCode);
            $entity->method('getAttribute')->willReturn($attribute);
        }

        self::assertSame($expected, $this->invoke(new Subject(), '_isAllowedFilter', $entity, $field, $condition));
    }

    public static function provideFilters(): Generator
    {
        yield 'email like' => [true, 'email', ['like' => '%@example.com'], 'email'];
        yield 'entity_id eq' => [true, 'entity_id', ['eq' => 5], 'entity_id'];
        yield 'unknown field passes through' => [true, 'foo', 'bar', null];
        yield 'rp_token like' => [false, 'rp_token', ['like' => 'a%'], 'rp_token'];
        yield 'rp_token plain value' => [false, 'rp_token', 'abc', 'rp_token'];
        yield 'rp_token_created_at' => [false, 'rp_token_created_at', ['gt' => '2026-01-01'], 'rp_token_created_at'];
        yield 'rp_customer_id' => [false, 'rp_customer_id', ['like' => 'a%'], 'rp_customer_id'];
        yield 'password_hash like' => [false, 'password_hash', ['like' => 'a%'], 'password_hash'];
        yield 'case and padding variant' => [false, ' RP_Token ', ['like' => 'a%'], null];
        yield 'attribute id resolving to rp_token' => [false, '123', ['like' => 'a%'], 'rp_token'];
        yield 'collation variant resolving to password_hash' => [false, 'pässword_hash', ['like' => 'a%'], 'password_hash'];
        yield 'confirmation notnull' => [true, 'confirmation', ['notnull' => true], 'confirmation'];
        yield 'confirmation null' => [true, 'confirmation', ['null' => true], 'confirmation'];
        yield 'confirmation like' => [false, 'confirmation', ['like' => 'a%'], 'confirmation'];
        yield 'confirmation eq' => [false, 'confirmation', 'abc', 'confirmation'];
        yield 'confirmation notnull plus like' => [false, 'confirmation', ['notnull' => true, 'like' => 'a%'], 'confirmation'];
        yield 'attribute id resolving to confirmation' => [false, '45', ['like' => 'a%'], 'confirmation'];
    }

    #[DataProvider('provideConfirmationWrites')]
    public function testPrepareConfirmationForWrite(?string $storedKey, mixed $value, ?string $expected): void
    {
        $customer = $this->createPartialMock(Mage_Customer_Model_Customer::class, ['getRandomConfirmationKey']);
        $customer->method('getRandomConfirmationKey')->willReturn('new-random-key');
        $customer->setConfirmation($storedKey);

        self::assertSame($expected, $this->invoke(new Subject(), '_prepareConfirmationForWrite', $customer, $value));
    }

    public static function provideConfirmationWrites(): Generator
    {
        $key = 'b7a1c0d2e3f4a5b6c7d8e9f0a1b2c3d4';

        yield 'pending read back (true) keeps the key' => [$key, true, $key];
        yield 'pending read back over XML-RPC ("1") keeps the key' => [$key, '1', $key];
        yield 'pending read back as int keeps the key' => [$key, 1, $key];
        yield 'a key chosen by the caller is not stored' => [$key, 'caller-chosen-key', $key];
        yield 'pending without a stored key gets a random key' => [null, true, 'new-random-key'];
        yield 'chosen key without a stored key gets a random key' => [null, 'caller-chosen-key', 'new-random-key'];
        yield 'confirmed (false) clears the key' => [$key, false, null];
        yield 'confirmed ("0") clears the key' => [$key, '0', null];
        yield 'confirmed ("false") clears the key' => [$key, 'false', null];
        yield 'confirmed (empty) clears the key' => [$key, '', null];
        yield 'confirmed stays confirmed' => [null, false, null];
    }

    private function invoke(object $subject, string $method, mixed ...$args): mixed
    {
        return (new ReflectionMethod($subject, $method))->invoke($subject, ...$args);
    }
}
