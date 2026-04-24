<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

namespace OpenMage\Tests\Functional;

use Mage;
use PHPUnit\Framework\TestCase;

abstract class FunctionalTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        Mage::app();

        self::skipIfMissingEnv(static::requiredEnv());
    }

    /**
     * Env vars that must be populated for every test in the class. Override
     * in subclasses; the base `setUpBeforeClass()` skips the whole class if
     * any are missing from the loaded `.env` file.
     *
     * @return list<string>
     */
    protected static function requiredEnv(): array
    {
        return [];
    }

    protected static function env(string $key, ?string $default = null): ?string
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        if (in_array($value, [false, null, ''], true)) {
            return $default;
        }

        return (string) $value;
    }

    /**
     * @param list<string> $keys
     */
    protected static function skipIfMissingEnv(array $keys): void
    {
        $missing = array_values(array_filter(
            $keys,
            static fn(string $key): bool => self::env($key) === null,
        ));

        if ($missing !== []) {
            self::markTestSkipped(sprintf(
                'Set %s in the functional-suite env file to run this test.',
                implode(', ', $missing),
            ));
        }
    }
}
