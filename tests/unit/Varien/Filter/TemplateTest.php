<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Varien\Filter;

use Generator;
use OpenMage\Tests\Unit\OpenMageTest;
use PHPUnit\Framework\Attributes\DataProvider;
use Varien_Filter_Template as Subject;
use Varien_Object;

final class TemplateTest extends OpenMageTest
{
    /**
     * GHSA-94p5-6j66-c9j6: getConfig() on a template variable must not return encrypted
     * configuration, whatever the case of the method name (PHP method names are
     * case-insensitive), whether the path names the field or one of its parents, and whether
     * it uses the `node@attribute=value` segment syntax that the config reader accepts.
     *
     * @group Filter
     */
    #[DataProvider('provideGetConfigDirectives')]
    public function testGetConfigDoesNotDiscloseEncryptedPaths(string $directive, string $expected): void
    {
        $store = new class extends Varien_Object {
            public function getConfig(?string $path): string
            {
                return $path === null ? '' : 'value-of:' . $path;
            }
        };

        $subject = new Subject();
        $subject->setVariables(['store' => $store]);

        self::assertSame($expected, $subject->filter($directive));
    }

    public static function provideGetConfigDirectives(): Generator
    {
        yield 'plain field' => ['{{var store.getConfig("general/store_information/name")}}', 'value-of:general/store_information/name'];
        yield 'encrypted field' => ['{{var store.getConfig("paypal/wpp/api_password")}}', ''];
        yield 'lower case method' => ['{{var store.getconfig("paypal/wpp/api_password")}}', ''];
        yield 'upper case method' => ['{{var store.GETCONFIG("paypal/wpp/api_password")}}', ''];
        yield 'mixed case method' => ['{{var store.GetConfig("paypal/wpp/api_signature")}}', ''];
        yield 'encrypted parent group' => ['{{var store.getConfig("paypal/wpp")}}', ''];
        yield 'attribute selector on the field' => ['{{var store.getConfig("paypal/wpp/api_password@x=")}}', ''];
        yield 'attribute selector on the group' => ['{{var store.getConfig("paypal/wpp@x=/api_password")}}', ''];
        yield 'attribute selector in a condition' => ['{{depend store.getConfig("paypal/wpp/api_password@x=")}}set{{/depend}}', ''];
    }

    /**
     * Values like 0 (int, e.g. Quote::getIsVirtual()) and "0" (string, loaded from the database)
     * must be treated as false by {{depend}} and {{if}}; since PHP 8, 0 == '' is false.
     *
     * @group Filter
     */
    #[DataProvider('provideConditionValues')]
    public function testDependDirective(mixed $value, bool $isTrue): void
    {
        $subject = new Subject();
        $subject->setVariables(['order' => new Varien_Object(['is_virtual' => $value])]);

        self::assertSame(
            $isTrue ? 'yes' : '',
            $subject->filter('{{depend order.getIsVirtual()}}yes{{/depend}}'),
        );
    }

    /**
     * @group Filter
     */
    #[DataProvider('provideConditionValues')]
    public function testIfDirective(mixed $value, bool $isTrue): void
    {
        $subject = new Subject();
        $subject->setVariables(['order' => new Varien_Object(['is_virtual' => $value])]);

        self::assertSame(
            $isTrue ? 'yes' : 'no',
            $subject->filter('{{if order.getIsVirtual()}}yes{{else}}no{{/if}}'),
        );
        self::assertSame(
            $isTrue ? 'yes' : '',
            $subject->filter('{{if order.getIsVirtual()}}yes{{/if}}'),
        );
    }

    public static function provideConditionValues(): Generator
    {
        yield 'null' => [null, false];
        yield 'false' => [false, false];
        yield 'empty string' => ['', false];
        yield 'int zero' => [0, false];
        yield 'string zero' => ['0', false];
        yield 'float zero' => [0.0, false];
        yield 'empty array' => [[], false];
        yield 'true' => [true, true];
        yield 'int one' => [1, true];
        yield 'string one' => ['1', true];
        yield 'decimal string zero' => ['0.0000', true];
        yield 'whitespace' => [' ', true];
        yield 'non-empty string' => ['abc', true];
        yield 'non-empty array' => [[0], true];
        yield 'object' => [new Varien_Object(), true];
    }
}
