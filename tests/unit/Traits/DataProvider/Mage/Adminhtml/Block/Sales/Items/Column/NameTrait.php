<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Block\Sales\Items\Column;

use Generator;

trait NameTrait
{
    /**
     * @return Generator<string, array{array<string, string>, string}>
     */
    public static function provideGetFormattedOptionData(): Generator
    {
        yield 'plain value' => [
            ['value' => 'Red', 'remainder' => ''],
            'Red',
        ];

        yield 'markup is escaped' => [
            ['value' => '&lt;img src=1 onerror=alert(1)&gt;', 'remainder' => ''],
            '<img src=1 onerror=alert(1)>',
        ];

        yield 'double quote is escaped' => [
            ['value' => 'a &quot;b&quot;', 'remainder' => ''],
            'a "b"',
        ];

        yield 'already escaped value is not double-encoded' => [
            ['value' => 'Fish &amp; Chips &lt;b&gt;', 'remainder' => ''],
            'Fish &amp; Chips &lt;b&gt;',
        ];

        yield 'line breaks are kept' => [
            ['value' => "line1<br />\nline2", 'remainder' => ''],
            "line1\nline2",
        ];

        yield 'remainder is escaped' => [
            ['value' => str_repeat('x', 55), 'remainder' => '&lt;script&gt;alert(1)&lt;/script&gt;'],
            str_repeat('x', 55) . '<script>alert(1)</script>',
        ];

        yield 'empty' => [
            ['value' => '', 'remainder' => ''],
            '',
        ];
    }
}
