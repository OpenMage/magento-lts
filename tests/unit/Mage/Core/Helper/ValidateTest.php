<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Mage_Core_Helper_Validate as Subject;
use ErrorException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

final class ValidateTest extends TestCase
{
    /**
     * @dataProvider provideEmail
     */
    public function testValidateEmailUsesHtml5ByDefault(string $value, bool $valid): void
    {
        $subject = new Subject();
        set_error_handler(static function (int $severity, string $message, string $file, int $line): never {
            throw new ErrorException($message, 0, $severity, $file, $line);
        }, E_USER_DEPRECATED);
        try {
            self::assertSame($valid, $subject->validateEmail($value)->count() === 0);
        } finally {
            restore_error_handler();
        }
    }

    public static function provideEmail(): array
    {
        return [
            'ordinary address' => ['customer@example.com', true],
            'plus addressing' => ['customer+orders@example.com', true],
            'missing domain' => ['customer@', false],
            'missing at sign' => ['customer.example.com', false],
            'empty address' => ['', false],
            'spaces in local part' => ['first last@example.com', false],
        ];
    }

    public function testExplicitModeTakesPrecedenceOverOptions(): void
    {
        $subject = new Subject();
        self::assertCount(1, $subject->validateEmail(
            'customer@localhost',
            options: ['mode' => Email::VALIDATION_MODE_HTML5_ALLOW_NO_TLD],
            mode: Email::VALIDATION_MODE_HTML5,
        ));
    }

    public function testModeInOptionsIsPreserved(): void
    {
        $subject = new Subject();
        self::assertCount(0, $subject->validateEmail(
            'customer@localhost',
            options: ['mode' => Email::VALIDATION_MODE_HTML5_ALLOW_NO_TLD],
        ));
    }

    public function testOtherConstraintValidatorsRemainAvailable(): void
    {
        $subject = new Subject();
        self::assertCount(1, $subject->validate('abc', new Length(min: 4)));
        self::assertCount(0, $subject->validate('abcd', new Length(min: 4)));
    }
}
