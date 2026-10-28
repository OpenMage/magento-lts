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
     * case-insensitive) and whether the path names the field or one of its parents.
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
    }
}
