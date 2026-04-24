<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model;

use Generator;

trait PurifierTrait
{
    public static function provideGetAllowedAttributes(): Generator
    {
        yield 'allow all attributes' => [null];
        yield 'allow some attributes' => [['a.href', '*.class']];
        yield 'allow no attributes' => [[]];
    }

    public static function provideGetAllowedElements(): Generator
    {
        yield 'allow all elements' => [null];
        yield 'allow some elements' => [['b', 'i', 'u']];
        yield 'allow no elements' => [[]];
    }

    public static function provideGetAllowedClasses(): Generator
    {
        yield 'allow all classes' => [null];
        yield 'allow some classes' => [['foo-bar__baz']];
        yield 'allow no classes' => [[]];
    }

    public static function provideGetAllowedStyleProperties(): Generator
    {
        yield 'allow all style properties' => [null];
        yield 'allow some style properties' => [['margin', 'font-size']];
        yield 'allow no style properties' => [[]];
    }

    public static function provideGetEscapeInvalidTags(): Generator
    {
        yield 'false' => [false];
        yield 'true' => [true];
    }

    public static function providePurify(): Generator
    {
        yield 'empty string' => [
            '',
            '',
        ];

        yield 'plain text' => [
            'hello world',
            'hello world',
        ];

        yield 'double quotes preserved' => [
            'He said "hello"',
            'He said "hello"',
        ];

        yield 'single quotes preserved' => [
            "it's fine",
            "it's fine",
        ];

        yield 'both quotes preserved' => [
            'She said "it\'s OK"',
            'She said "it\'s OK"',
        ];

        yield 'bare ampersand encoded' => [
            'a &amp; b',
            'a & b',
        ];

        yield '&amp; entity round-trips' => [
            'a &amp; b',
            'a &amp; b',
        ];

        yield '&lt; entity round-trips' => [
            '1 &lt; 2',
            '1 &lt; 2',
        ];

        yield '&gt; entity round-trips' => [
            '2 &gt; 1',
            '2 &gt; 1',
        ];

        yield 'bare > encoded' => [
            'value &gt; 0',
            'value > 0',
        ];

        yield '&quot; entity decoded' => [
            '"hello"',
            '&quot;hello&quot;',
        ];

        yield '&#039; entity decoded' => [
            "'hello'",
            '&#039;hello&#039;',
        ];

        yield '&copy; named entity decoded' => [
            "\u{00A9} 2024",
            '&copy; 2024',
        ];

        yield '&nbsp; named entity decoded' => [
            "hello\u{00A0}world",
            'hello&nbsp;world',
        ];

        yield 'double-encoded &amp;amp; preserves one level' => [
            '&amp;amp;',
            '&amp;amp;',
        ];

        yield 'fast path used regardless of escapeInvalidTags' => [
            'He said "it\'s fine"',
            'He said "it\'s fine"',
            ['escapeInvalidTags' => true],
        ];

        yield 'fast path used regardless of allowedElements' => [
            'He said "it\'s fine"',
            'He said "it\'s fine"',
            ['allowedElements' => []],
        ];

        yield 'fast path used with both options' => [
            'He said "it\'s fine"',
            'He said "it\'s fine"',
            ['escapeInvalidTags' => true, 'allowedElements' => []],
        ];

        yield 'allowed tag preserved' => [
            '<b>bold</b>',
            '<b>bold</b>',
        ];

        yield 'script tag stripped' => [
            '',
            '<script>alert("xss")</script>',
        ];

        yield 'forbidden tag stripped when escapeInvalidTags=false' => [
            'bold',
            '<b>bold</b>',
            ['allowedElements' => []],
        ];

        yield 'forbidden tag escaped when escapeInvalidTags=true' => [
            '&lt;b&gt;bold&lt;/b&gt;',
            '<b>bold</b>',
            ['escapeInvalidTags' => true, 'allowedElements' => []],
        ];
    }
}
